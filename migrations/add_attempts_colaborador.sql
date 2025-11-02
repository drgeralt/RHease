-- Migration: Adiciona colunas para controle de tentativas de login falhadas
-- Adicionar esse sql no banco para a função funcionar corretamente

ALTER TABLE `colaborador`
    ADD COLUMN `failed_login_attempts` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Contador de tentativas de login falhadas consecutivas' AFTER `token_expiracao`,
ADD COLUMN `last_failed_login_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Timestamp da última tentativa de login falhada' AFTER `failed_login_attempts`;

-- Fim da Migration