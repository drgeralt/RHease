document.addEventListener('DOMContentLoaded', () => {
    // Referências aos elementos do DOM
    const registerButton = document.getElementById('register-button');
    const initialView = document.getElementById('initial-view');
    const cameraView = document.getElementById('camera-view');
    const videoElement = document.getElementById('camera-feed');
    const canvasElement = document.getElementById('photo-canvas');
    const feedbackContainer = document.getElementById('feedback-container');
    const timeElement = document.getElementById('current-time');
    const exitButton = document.getElementById('exit-button'); // Se existir

    let videoStream = null;

    // --- Relógio em Tempo Real ---
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        if (timeElement) timeElement.textContent = `${hours}:${minutes}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // --- Event Listeners ---
    if(registerButton) {
        registerButton.addEventListener('click', handleRegistrationStep);
    }

    // --- Lógica Principal ---
    async function handleRegistrationStep() {
        if (!videoStream) {
            await startCamera();
        } else {
            await captureAndSend();
        }
    }

    async function startCamera() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            videoElement.srcObject = videoStream;

            // Troca a visualização
            initialView.classList.add('hidden');
            cameraView.classList.remove('hidden');

            // Define o texto do botão baseado na config vinda do PHP
            let textoAcao = "";

            if (PontoConfig.precisaCadastrarFace) {
                textoAcao = "CADASTRAR FACE (OBRIGATÓRIO)";
                registerButton.style.backgroundColor = "#d35400"; // Cor de alerta opcional
            } else {
                const acao = PontoConfig.isSaida ? "CONFIRMAR SAÍDA" : "CONFIRMAR ENTRADA";
                textoAcao = `TIRAR FOTO E ${acao}`;
            }

            registerButton.textContent = textoAcao;

        } catch (error) {
            console.error("Erro ao aceder à câmera:", error);
            showFeedback('Não foi possível aceder à câmera. Verifique as permissões.', 'error');
        }
    }

    async function captureAndSend() {
        registerButton.disabled = true;
        registerButton.textContent = 'PROCESSANDO...';
        feedbackContainer.innerHTML = '';

        try {
            const location = await getGeoLocation();

            // Captura frame do vídeo
            const context = canvasElement.getContext('2d');
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            const imageData = canvasElement.toDataURL('image/jpeg');
            stopCamera();

            const formData = new FormData();
            formData.append('imagem', imageData);

            let urlDestino = '';

            // Decide a rota com base na necessidade de cadastro
            if (PontoConfig.precisaCadastrarFace) {
                // Rota para APENAS cadastrar a face (Criar no Controller)
                urlDestino = `${PontoConfig.baseUrl}/ponto/registrar-face-api`;
            } else {
                // Rota padrão de bater ponto
                formData.append('geolocalizacao', location);
                urlDestino = `${PontoConfig.baseUrl}/registrarponto/salvar`;
            }

            const response = await fetch(urlDestino, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Erro no servidor: ${response.status}`);
            }

            const result = await response.json();

            if (result.status === 'success') {
                if (PontoConfig.precisaCadastrarFace) {
                    showFeedback("Face cadastrada com sucesso! A página será recarregada para bater o ponto.", 'success');
                } else {
                    showFeedback(`Registo de ${result.tipo} às ${result.horario}! Processando...`, 'success');
                }

                registerButton.classList.add('hidden');
                if (exitButton) exitButton.classList.add('hidden');

                setTimeout(() => { window.location.reload(); }, 2500);
            } else {
                throw new Error(result.message || 'Erro desconhecido no backend.');
            }

        } catch (error) {
            console.error("Falha:", error);
            showFeedback(`Falha: ${error.message}`, 'error');
            restoreInitialState();
        }
    }

    function getGeoLocation() {
        return new Promise((resolve) => {
            if (!navigator.geolocation) {
                resolve('Geolocalização não suportada.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                (position) => resolve(`${position.coords.latitude},${position.coords.longitude}`),
                (err) => {
                    console.warn('Geolocalização negada ou erro:', err);
                    resolve('Localização não permitida/indisponível.');
                }
            );
        });
    }

    function stopCamera() {
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
    }

    function showFeedback(message, type) {
        feedbackContainer.innerHTML = `<div class="feedback-message ${type}">${message}</div>`;
    }

    function restoreInitialState() {
        stopCamera();
        cameraView.classList.add('hidden');
        initialView.classList.remove('hidden');
        registerButton.disabled = false;
        registerButton.classList.remove('hidden');

        // Restaura texto original
        if (PontoConfig.precisaCadastrarFace) {
            registerButton.textContent = "CADASTRAR FACE";
        } else {
            registerButton.textContent = PontoConfig.isSaida ? "REGISTAR SAÍDA" : "REGISTAR ENTRADA";
        }
    }
});