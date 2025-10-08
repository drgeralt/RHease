<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo de Ponto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/registroPonto.css">
</head>
<body>

<header class="topbar">
    <img src="<?php echo BASE_URL; ?>/img/rhease-ease 1.png" alt="Logo da Empresa" class="logo">
</header>

<main class="main-content">
    <div class="clock-widget">
        <div id="initial-view">
            <h1 class="greeting">Olá, Rick Ribeiro!</h1>
            <?php if (isset($horaEntrada) && $horaEntrada): ?>
                <p class="entry-time-info">A sua entrada foi registada às <strong><?php echo $horaEntrada; ?></strong>.</p>
            <?php endif; ?>

            <div id="current-time" class="time-display">--:--</div>
        </div>

        <div id="camera-view" class="hidden">
            <video id="camera-feed" class="camera-feed" autoplay playsinline></video>
            <canvas id="photo-canvas" class="hidden"></canvas>
        </div>

        <div id="feedback-container"></div>

        <button id="register-button" class="register-btn">
            <?php echo (isset($horaEntrada) && $horaEntrada) ? 'REGISTAR SAÍDA' : 'REGISTAR ENTRADA'; ?>
        </button>

        <a href="<?php echo BASE_URL; ?>" id="exit-button" class="exit-btn">VOLTAR AO INÍCIO</a>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const registerButton = document.getElementById('register-button');
        const initialView = document.getElementById('initial-view');
        const cameraView = document.getElementById('camera-view');
        const videoElement = document.getElementById('camera-feed');
        const canvasElement = document.getElementById('photo-canvas');
        const feedbackContainer = document.getElementById('feedback-container');
        const timeElement = document.getElementById('current-time');
        const exitButton = document.getElementById('exit-button');

        let videoStream = null;
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            if (timeElement) timeElement.textContent = `${hours}:${minutes}`;
        }
        updateClock();
        setInterval(updateClock, 1000);
        registerButton.addEventListener('click', handleRegistrationStep);

        async function handleRegistrationStep() {
            if (!videoStream) {
                await startCamera();
            }
            else {
                await captureAndSend();
            }
        }

        async function startCamera() {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                videoElement.srcObject = videoStream;
                initialView.classList.add('hidden');
                cameraView.classList.remove('hidden');
                registerButton.textContent = 'TIRAR FOTO E REGISTAR';
            } catch (error) {
                console.error("Erro ao aceder à câmera:", error);
                showFeedback('Não foi possível aceder à câmera. Verifique as permissões do navegador.', 'error');
            }
        }

        async function captureAndSend() {
            registerButton.disabled = true;
            registerButton.textContent = 'PROCESSANDO...';
            feedbackContainer.innerHTML = '';

            try {
                const location = await getGeoLocation();
                const context = canvasElement.getContext('2d');
                canvasElement.width = videoElement.videoWidth;
                canvasElement.height = videoElement.videoHeight;
                context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
                const imageData = canvasElement.toDataURL('image/jpeg');

                stopCamera();

                const formData = new FormData();
                formData.append('imagem', imageData);
                formData.append('geolocalizacao', location);
                const response = await fetch('<?php echo BASE_URL; ?>/registrarponto/salvar', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erro do servidor: ${response.status}`);
                }

                const result = await response.json();

                if (result.status === 'success') {
                    showFeedback(`Registo de ${result.tipo} efetuado com sucesso às ${result.horario}! A redirecionar...`, 'success');

                    registerButton.classList.add('hidden');
                    exitButton.classList.add('hidden');
                    setTimeout(() => { window.location.href = '<?php echo BASE_URL; ?>'; }, 2500);
                } else {
                    throw new Error(result.message || 'Ocorreu um erro desconhecido no backend.');
                }

            } catch (error) {
                console.error("Falha ao registar ponto:", error);
                showFeedback(`Falha no registo: ${error.message}`, 'error');
                restoreInitialState();
            }
        }

        function getGeoLocation() {
            return new Promise((resolve) => {
                if (!navigator.geolocation) {
                    resolve('Geolocalização não suportada.');
                }
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        resolve(`${position.coords.latitude},${position.coords.longitude}`);
                    },
                    () => {
                        resolve('Permissão de localização negada.');
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
            const originalButtonText = '<?php echo (isset($horaEntrada) && $horaEntrada) ? "REGISTAR SAÍDA" : "REGISTAR ENTRADA"; ?>';
            registerButton.textContent = originalButtonText;
        }
    });
</script>

</body>
</html>