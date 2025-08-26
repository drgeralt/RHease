CREATE DATABASE IF NOT EXISTS `rhease` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rhease`;

-- TABELAS DO SEU TIME --
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years_experience` int(11) DEFAULT NULL,
  `expected_salary` decimal(10,2) DEFAULT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `payroll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- (FUNCIONÁRIOS, BENEFÍCIOS, DEMISSÃO) --
CREATE TABLE IF NOT EXISTS `funcionarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(255) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `salario` decimal(10,2) NOT NULL,
  `data_admissao` date NOT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `beneficios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome_beneficio` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `valor_mensal` decimal(10,2) NOT NULL,
  `tipo` enum('desconto','acréscimo') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `funcionario_beneficios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funcionario_id` int(11) NOT NULL,
  `beneficio_id` int(11) NOT NULL,
  `data_adesao` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `funcionario_id` (`funcionario_id`),
  KEY `beneficio_id` (`beneficio_id`),
  CONSTRAINT `funcionario_beneficios_ibfk_1` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `funcionario_beneficios_ibfk_2` FOREIGN KEY (`beneficio_id`) REFERENCES `beneficios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `demissoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `funcionario_id` int(11) NOT NULL,
  `data_demissao` date NOT NULL,
  `tipo_demissao` enum('pedido_demissao','sem_justa_causa','com_justa_causa') NOT NULL,
  `motivo` text DEFAULT NULL,
  `saldo_salario` decimal(10,2) NOT NULL,
  `aviso_previo` decimal(10,2) NOT NULL,
  `ferias_vencidas` decimal(10,2) NOT NULL,
  `ferias_proporcionais` decimal(10,2) NOT NULL,
  `terco_ferias` decimal(10,2) NOT NULL,
  `decimo_terceiro_proporcional` decimal(10,2) NOT NULL,
  `valor_total_rescisao` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `funcionario_id` (`funcionario_id`),
  CONSTRAINT `demissoes_ibfk_1` FOREIGN KEY (`funcionario_id`) REFERENCES `funcionarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--  DADOS INICIAIS --
INSERT INTO `beneficios` (`nome_beneficio`, `descricao`, `valor_mensal`, `tipo`) VALUES
('Vale-Transporte', 'Benefício para custear o deslocamento do funcionário.', 220.00, 'desconto'),
('Vale-Alimentação', 'Benefício para compra de alimentos em supermercados.', 650.00, 'desconto'),
('Plano Odontológico', 'Cobertura de consultas e procedimentos odontológicos.', 45.00, 'acréscimo');

INSERT INTO `funcionarios` (`nome_completo`, `cpf`, `cargo`, `salario`, `data_admissao`, `status`) VALUES
('Ana Maria Teste', '123.456.789-10', 'Analista de Testes', 3500.00, '2023-01-15', 'ativo');
