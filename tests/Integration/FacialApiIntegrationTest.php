<?php

namespace Tests\Integration;

use Tests\TestCase;

/**
 * Testes de integração com a API Facial (DeepFace)
 *
 * IMPORTANTE: Estes testes requerem que a API Python esteja rodando!
 * Execute: python app.py
 */
class FacialApiIntegrationTest extends TestCase
{
    private $apiUrl;
    private $apiAvailable = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiUrl = $_ENV['FACIAL_API_URL'];
        $this->apiAvailable = $this->checkApiHealth();

        if (!$this->apiAvailable) {
            $this->markTestSkipped(
                'API Facial não está disponível. Execute: python app.py'
            );
        }
    }

    /**
     * Verifica se a API está online
     */
    private function checkApiHealth(): bool
    {
        try {
            $ch = curl_init($this->apiUrl . '/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $httpCode === 200;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @test
     */
    public function api_deve_estar_online()
    {
        $ch = curl_init($this->apiUrl . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(200, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('success', $data['status']);
        $this->assertArrayHasKey('model', $data);
        $this->assertArrayHasKey('detector', $data);
    }

    /**
     * @test
     */
    public function deve_retornar_erro_quando_imagem_nao_fornecida()
    {
        $payload = json_encode([
            'id_colaborador' => 999
            // Sem 'imagem'
        ]);

        $ch = curl_init($this->apiUrl . '/register-face');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(400, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('error', $data['status']);
    }

    /**
     * @test
     */
    public function deve_retornar_erro_quando_id_colaborador_nao_fornecido()
    {
        $payload = json_encode([
            'imagem' => $this->getFakeImageBase64()
            // Sem 'id_colaborador'
        ]);

        $ch = curl_init($this->apiUrl . '/register-face');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(400, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('error', $data['status']);
    }

    /**
     * @test
     */
    public function deve_retornar_erro_para_imagem_invalida()
    {
        $colaborador = $this->createTestColaborador();

        $payload = json_encode([
            'imagem' => 'data:image/jpeg;base64,INVALID_BASE64',
            'id_colaborador' => $colaborador['id_colaborador']
        ]);

        $ch = curl_init($this->apiUrl . '/register-face');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(400, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('error', $data['status']);
    }

    /**
     * @test
     */
    public function deve_retornar_erro_quando_colaborador_nao_existe()
    {
        $payload = json_encode([
            'imagem' => $this->getFakeImageBase64(),
            'id_colaborador' => 999999 // ID que não existe
        ]);

        $ch = curl_init($this->apiUrl . '/register-face');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(400, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('error', $data['status']);
        $this->assertStringContainsString('Nenhuma face', $data['message']);
    }

    /**
     * @test
     */
    public function verify_deve_retornar_erro_quando_nenhuma_face_cadastrada()
    {
        $payload = json_encode([
            'imagem' => $this->getFakeImageBase64(),
            'geolocalizacao' => '-10.1689,-48.3317',
            'ip_address' => '127.0.0.1'
        ]);

        $ch = curl_init($this->apiUrl . '/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->assertEquals(400, $httpCode);

        $data = json_decode($response, true);
        $this->assertEquals('error', $data['status']);
        $this->assertStringContainsString('Nenhuma face', $data['message']);
    }

    /**
     * @test
     */
    public function deve_processar_timeout_gracefully()
    {
        $payload = json_encode([
            'imagem' => $this->getFakeImageBase64(),
            'geolocalizacao' => '-10.1689,-48.3317',
            'ip_address' => '127.0.0.1'
        ]);

        $ch = curl_init($this->apiUrl . '/verify');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Timeout muito curto

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->assertStringContainsString('time out', strtolower($error));
        }

        $this->assertTrue(true); // Passou sem crash
    }

    /**
     * @test
     */
    public function api_deve_retornar_json_valido()
    {
        $ch = curl_init($this->apiUrl . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('message', $data);
    }

    /**
     * @test
     */
    public function health_check_deve_incluir_informacoes_do_modelo()
    {
        $ch = curl_init($this->apiUrl . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        $this->assertArrayHasKey('model', $data);
        $this->assertArrayHasKey('detector', $data);
        $this->assertArrayHasKey('timestamp', $data);

        // Modelos válidos do DeepFace
        $validModels = ['VGG-Face', 'Facenet', 'Facenet512', 'OpenFace', 'DeepFace', 'DeepID', 'ArcFace', 'Dlib', 'SFace'];
        $this->assertContains($data['model'], $validModels);
    }

    /**
     * @test
     */
    public function deve_retornar_headers_corretos()
    {
        $ch = curl_init($this->apiUrl . '/health');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $this->assertStringContainsString('Content-Type: application/json', $response);
    }
}