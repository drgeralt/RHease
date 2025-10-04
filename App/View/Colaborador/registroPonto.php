<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ponto</title>

    <!-- Importando a fonte Montserrat do Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

    <!--
      Vinculando o arquivo CSS.
      O caminho deve ser relativo à raiz do seu site (a pasta 'public').
    -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/registroPonto.css">
</head>
<body>

<header class="topbar">
    <!-- Você pode substituir 'public/images/logo.png' pelo caminho real do seu logo -->
    <img src="/RHease/public/images/logo.png" alt="Logo da Empresa" class="logo">
</header>

<main class="main-content">
    <!-- O widget de registro de ponto inspirado no Figma -->
    <div class="clock-widget">
        <h1 class="greeting">Olá, Rick Ribeiro!</h1>

        <!-- O horário atual será inserido aqui pelo JavaScript -->
        <div id="current-time" class="time-display">--:--</div>

        <button id="register-button" class="register-btn">
            REGISTRAR PONTO
        </button>

        <!-- O tipo de batida (entrada/saída) pode ser dinâmico no futuro -->
        <p class="punch-type">SAÍDA</p>
    </div>
</main>

<!--
  Vinculando o arquivo JavaScript no final do body para melhor performance.
  O caminho também é relativo à raiz do site.
-->
<script src="/RHease/public/js/registroPonto.js"></script>

</body>
</html>
