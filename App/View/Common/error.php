<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Erro</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; color: #333; text-align: center; padding: 50px; }
        .error-container { background-color: #fff; border: 1px solid #ddd; padding: 30px; display: inline-block; border-radius: 8px; }
        h1 { color: #d9534f; }
        p { font-size: 1.2em; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Ops! Ocorreu um erro.</h1>
        <p>A página que você está tentando acessar não foi encontrada ou um erro inesperado ocorreu.</p>
        <p><a href="<?php echo BASE_URL; ?>">Voltar para a página inicial</a></p>
    </div>
</body>
</html>