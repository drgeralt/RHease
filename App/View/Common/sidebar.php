<?php
// 1. Lógica de Permissão
// Verifica o perfil na sessão. Se não existir, assume 'colaborador' (menor privilégio)
$perfilUsuario = $_SESSION['user_perfil'] ?? 'colaborador';
$isGestor = in_array($perfilUsuario, ['gestor_rh', 'diretor', 'admin']);

// 2. Função para marcar o link ativo (Highlight no menu)
function isActive($rota) {
    // Pega a URI atual (ex: /RHease/public/colaboradores)
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    // Se a rota passada (ex: /colaboradores) estiver na URI, retorna 'active'
    return strpos($uri, $rota) !== false ? 'active' : '';
}
?>

<div class="sidebar">
    <ul class="menu">

        <li class="<?= isActive('/inicio') ?>">
            <a href="<?= BASE_URL ?>/inicio">
                <i class="bi bi-clipboard-data-fill"></i> Painel
            </a>
        </li>

        <li class="<?= isActive('/dados') ?>">
            <a href="<?= BASE_URL ?>/dados">
                <i class="bi bi-person-vcard-fill"></i> Meus Dados
            </a>
        </li>

        <li class="<?= isActive('/registrarponto') ?>">
            <a href="<?= BASE_URL ?>/registrarponto">
                <i class="bi bi-calendar2-check-fill"></i> Frequência
            </a>
        </li>

        <li class="mt-3 mb-1 text-uppercase small text-muted px-3" style="font-size: 0.75rem; font-weight: 700;">
            Pessoal & Financeiro
        </li>

        <li class="<?= isActive('/meus-holerites') ?>">
            <a href="<?= BASE_URL ?>/meus-holerites">
                <i class="bi bi-wallet-fill"></i> Meus Holerites
            </a>
        </li>

        <li class="<?= isActive('/meus-beneficios') ?>">
            <a href="<?= BASE_URL ?>/meus-beneficios">
                <i class="bi bi-heart-pulse-fill"></i> Meus Benefícios
            </a>
        </li>

        <li class="<?= isActive('/vagas') && !isActive('/vagas/listar') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/vagas">
                <i class="bi bi-search"></i> Vagas Internas
            </a>
        </li>

        <?php if ($isGestor): ?>
            <li class="mt-3 mb-1 text-uppercase small text-muted px-3" style="font-size: 0.75rem; font-weight: 700;">
                Gestão & RH
            </li>

            <li class="<?= isActive('/colaboradores') ?>">
                <a href="<?= BASE_URL ?>/colaboradores">
                    <i class="bi bi-people-fill"></i> Colaboradores
                </a>
            </li>

            <li class="<?= isActive('/gestao-facial') ?>">
                <a href="<?= BASE_URL ?>/gestao-facial">
                    <i class="bi bi-person-bounding-box"></i> Biometria Facial
                </a>
            </li>

            <li class="<?= isActive('/folha/processar') ?>">
                <a href="<?= BASE_URL ?>/folha/processar">
                    <i class="bi bi-cash-coin"></i> Processar Folha
                </a>
            </li>

            <li class="<?= isActive('/beneficios') ?>">
                <a href="<?= BASE_URL ?>/beneficios">
                    <i class="bi bi-shield-fill-check"></i> Gestão Benefícios
                </a>
            </li>

            <li class="<?= isActive('/vagas/listar') ?>">
                <a href="<?= BASE_URL ?>/vagas/listar">
                    <i class="bi bi-briefcase-fill"></i> Gestão de Vagas
                </a>
            </li>
        <?php endif; ?>

        <li class="mt-3 mb-1 text-uppercase small text-muted px-3" style="font-size: 0.75rem; font-weight: 700;">
            Sistema
        </li>

        <li class="<?= isActive('/contato') ?>">
            <a href="<?= BASE_URL ?>/contato">
                <i class="bi bi-person-lines-fill"></i> Contato / Suporte
            </a>
        </li>

        <li class="mt-2 pt-2 border-top">
            <a href="<?= BASE_URL ?>/logout" class="text-danger">
                <i class="bi bi-box-arrow-left"></i> Sair
            </a>
        </li>
    </ul>
</div>