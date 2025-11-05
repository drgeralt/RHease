from flask import Flask, request, jsonify
from flask_cors import CORS
import base64
import numpy as np
import cv2
from insightface.app import FaceAnalysis
import mysql.connector
from mysql.connector import Error
import json
import os
from datetime import datetime

app = Flask(__name__)
CORS(app)

DB_CONFIG = {
    'host': 'localhost',
    'database': 'rhease',
    'user': 'root',
    'password': ''
}

face_app = FaceAnalysis(providers=['CPUExecutionProvider'])
face_app.prepare(ctx_id=0, det_size=(640, 640))

def get_db_connection():
    """Cria e retorna uma conexão com o banco de dados"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        return connection
    except Error as e:
        print(f"Erro ao conectar ao MySQL: {e}")
        return None

def base64_to_image(base64_string):
    """Converte uma string base64 em imagem numpy array"""
    try:
        if ',' in base64_string:
            base64_string = base64_string.split(',')[1]
        img_data = base64.b64decode(base64_string)
        nparr = np.frombuffer(img_data, np.uint8)
        img = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

        return img
    except Exception as e:
        print(f"Erro ao converter base64 para imagem: {e}")
        return None

def extract_face_embedding(image):
    """Extrai o embedding facial de uma imagem"""
    try:
        faces = face_app.get(image)

        if len(faces) == 0:
            return None, "Nenhuma face detectada na imagem"

        if len(faces) > 1:
            return None, "Múltiplas faces detectadas. Por favor, tire uma foto com apenas uma pessoa"

        embedding = faces[0].embedding

        return embedding, None
    except Exception as e:
        return None, f"Erro ao extrair embedding: {str(e)}"

def calculate_similarity(embedding1, embedding2):
    """Calcula a similaridade entre dois embeddings usando cosseno"""
    embedding1 = np.array(embedding1)
    embedding2 = np.array(embedding2)

    embedding1_norm = embedding1 / np.linalg.norm(embedding1)
    embedding2_norm = embedding2 / np.linalg.norm(embedding2)

    similarity = np.dot(embedding1_norm, embedding2_norm)

    return float(similarity)

@app.route('/facial-api/register-face', methods=['POST'])
def register_face():
    """
    Endpoint para registrar uma face no sistema.
    Recebe: imagem (base64) e id_colaborador
    """
    try:
        data = request.get_json()

        if not data or 'imagem' not in data or 'id_colaborador' not in data:
            return jsonify({
                'status': 'error',
                'message': 'Parâmetros inválidos. Necessário: imagem e id_colaborador'
            }), 400

        id_colaborador = data['id_colaborador']
        imagem_base64 = data['imagem']

        image = base64_to_image(imagem_base64)
        if image is None:
            return jsonify({
                'status': 'error',
                'message': 'Falha ao processar a imagem'
            }), 400

        embedding, error = extract_face_embedding(image)
        if error:
            return jsonify({
                'status': 'error',
                'message': error
            }), 400

        # Converte embedding para JSON string
        embedding_json = json.dumps(embedding.tolist())

        # Salva no banco de dados
        connection = get_db_connection()
        if not connection:
            return jsonify({
                'status': 'error',
                'message': 'Erro ao conectar ao banco de dados'
            }), 500

        try:
            cursor = connection.cursor()
            query = """
                UPDATE colaborador
                SET facial_embedding = %s,
                    facial_registered_at = %s
                WHERE id_colaborador = %s
            """
            cursor.execute(query, (embedding_json, datetime.now(), id_colaborador))
            connection.commit()

            if cursor.rowcount == 0:
                return jsonify({
                    'status': 'error',
                    'message': 'Colaborador não encontrado'
                }), 404

            return jsonify({
                'status': 'success',
                'message': 'Face registrada com sucesso',
                'id_colaborador': id_colaborador
            }), 200

        finally:
            cursor.close()
            connection.close()

    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/facial-api/verify', methods=['POST'])
def verify_face():
    """
    Endpoint para verificar uma face e registrar ponto.
    Recebe: imagem (base64), geolocalizacao, ip_address
    """
    try:
        data = request.get_json()

        if not data or 'imagem' not in data:
            return jsonify({
                'status': 'error',
                'message': 'Imagem não fornecida'
            }), 400

        imagem_base64 = data['imagem']
        geolocalizacao = data.get('geolocalizacao', 'Não informada')
        ip_address = data.get('ip_address', 'Não identificado')

        # Converte base64 para imagem
        image = base64_to_image(imagem_base64)
        if image is None:
            return jsonify({
                'status': 'error',
                'message': 'Falha ao processar a imagem'
            }), 400

        # Extrai o embedding da foto de input
        current_embedding, error = extract_face_embedding(image)
        if error:
            return jsonify({
                'status': 'error',
                'message': error
            }), 400

        # Bsusca todos os embeddings cadastrados
        connection = get_db_connection()
        if not connection:
            return jsonify({
                'status': 'error',
                'message': 'Erro ao conectar ao banco de dados'
            }), 500

        try:
            cursor = connection.cursor(dictionary=True)

            query = """
                SELECT id_colaborador, facial_embedding, nome_completo
                FROM colaborador
                WHERE facial_embedding IS NOT NULL
            """
            cursor.execute(query)
            colaboradores = cursor.fetchall()

            if not colaboradores:
                return jsonify({
                    'status': 'error',
                    'message': 'Nenhuma face cadastrada no sistema'
                }), 404

            best_match = None
            best_similarity = -1
            THRESHOLD = 0.6

            for colaborador in colaboradores:
                stored_embedding = np.array(json.loads(colaborador['facial_embedding']))
                similarity = calculate_similarity(current_embedding, stored_embedding)

                if similarity > best_similarity:
                    best_similarity = similarity
                    best_match = colaborador

            if best_similarity < THRESHOLD:
                return jsonify({
                    'status': 'error',
                    'message': 'Face não reconhecida. Por favor, cadastre sua face primeiro.',
                    'similarity': best_similarity
                }), 401

            id_colaborador = best_match['id_colaborador']
            nome_colaborador = best_match['nome_completo']

            # Salva a imagem da verificação
            timestamp = int(datetime.now().timestamp())
            nome_arquivo = f"{id_colaborador}_{timestamp}.jpg"
            caminho_relativo = f"storage/fotos_ponto/{nome_arquivo}"
            caminho_completo = os.path.join('C:/xampp/htdocs/RHease', caminho_relativo)

            os.makedirs(os.path.dirname(caminho_completo), exist_ok=True)

            cv2.imwrite(caminho_completo, image)

            # Regisstra o ponto
            data_hora_atual = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            data_atual = datetime.now().strftime('%Y-%m-%d')

            query_busca = """
                SELECT id_registro_ponto
                FROM folha_ponto
                WHERE id_colaborador = %s
                AND DATE(data_hora_entrada) = %s
                AND data_hora_saida IS NULL
                ORDER BY data_hora_entrada DESC
                LIMIT 1
            """
            cursor.execute(query_busca, (id_colaborador, data_atual))
            registro_aberto = cursor.fetchone()

            if registro_aberto:
                query_update = """
                    UPDATE folha_ponto
                    SET data_hora_saida = %s,
                        geolocalizacao = %s,
                        caminho_foto = %s,
                        ip_address = %s
                    WHERE id_registro_ponto = %s
                """
                cursor.execute(query_update, (
                    data_hora_atual,
                    geolocalizacao,
                    caminho_relativo,
                    ip_address,
                    registro_aberto['id_registro_ponto']
                ))
                tipo_registro = 'saida'
            else:
                query_insert = """
                    INSERT INTO folha_ponto
                    (id_colaborador, data_hora_entrada, geolocalizacao, caminho_foto, ip_address)
                    VALUES (%s, %s, %s, %s, %s)
                """
                cursor.execute(query_insert, (
                    id_colaborador,
                    data_hora_atual,
                    geolocalizacao,
                    caminho_relativo,
                    ip_address
                ))
                tipo_registro = 'entrada'

            connection.commit()

            return jsonify({
                'status': 'success',
                'message': f'Ponto de {tipo_registro} registrado com sucesso',
                'id_colaborador': id_colaborador,
                'nome_colaborador': nome_colaborador,
                'tipo': tipo_registro,
                'horario': datetime.now().strftime('%H:%M'),
                'similarity': best_similarity
            }), 200

        finally:
            cursor.close()
            connection.close()

    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': f'Erro interno: {str(e)}'
        }), 500

@app.route('/facial-api/health', methods=['GET'])
def health_check():
    """Endpoint para verificar se a API está funcionando"""
    return jsonify({
        'status': 'success',
        'message': 'API Facial está funcionando',
        'timestamp': datetime.now().isoformat()
    }), 200

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)