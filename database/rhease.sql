-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 01, 2025 at 01:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rhease`
--

-- --------------------------------------------------------

--
-- Table structure for table `avaliacao_desempenho`
--

CREATE TABLE `avaliacao_desempenho` (
  `id_avaliacao` int(11) NOT NULL,
  `data_avaliacao` date DEFAULT NULL,
  `nota` decimal(10,2) NOT NULL,
  `feedback` varchar(500) DEFAULT NULL,
  `id_avaliado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficios_catalogo`
--

CREATE TABLE `beneficios_catalogo` (
  `id_beneficio` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `tipo_valor` enum('Fixo','Variável','Descritivo') NOT NULL,
  `status` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidato`
--

CREATE TABLE `candidato` (
  `id_candidato` int(11) NOT NULL,
  `nome_completo` varchar(100) DEFAULT NULL,
  `CPF` varchar(14) NOT NULL,
  `situacao` enum('em análise','aprovado','rejeitado','contratado') NOT NULL DEFAULT 'em análise',
  `curriculo` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidaturas`
--

CREATE TABLE `candidaturas` (
  `id_candidatura` int(11) NOT NULL,
  `id_vaga` int(11) NOT NULL,
  `id_candidato` int(11) NOT NULL,
  `data_candidatura` timestamp NOT NULL DEFAULT current_timestamp(),
  `pontuacao_aderencia` int(11) DEFAULT NULL COMMENT 'Score de 0-100 gerado pela IA',
  `justificativa_ia` text DEFAULT NULL COMMENT 'Justificativa da pontuação gerada pela IA',
  `status_triagem` enum('Recebido','Em Análise','Aprovado para Entrevista','Rejeitado') NOT NULL DEFAULT 'Recebido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `nome_cargo` varchar(100) NOT NULL,
  `salario_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colaborador`
--

CREATE TABLE `colaborador` (
  `id_colaborador` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nome_completo` varchar(100) NOT NULL,
  `email_pessoal` varchar(100) NOT NULL,
  `email_profissional` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL COMMENT 'ADICIONADO: Hashing com password_hash()',
  `perfil` enum('colaborador','gestor_rh','diretor') NOT NULL DEFAULT 'colaborador' COMMENT 'ADICIONADO: Para controle de acesso',
  `status_conta` enum('ativo','inativo','pendente_verificacao') NOT NULL DEFAULT 'pendente_verificacao' COMMENT 'ADICIONADO: Para fluxo de registro',
  `token_verificacao` varchar(255) DEFAULT NULL COMMENT 'ADICIONADO: Hash do token para verificação de e-mail',
  `token_recuperacao` varchar(255) DEFAULT NULL COMMENT 'ADICIONADO: Hash do token para recuperação de senha',
  `token_expiracao` datetime DEFAULT NULL COMMENT 'ADICIONADO: Expiração dos tokens',
  `cpf` varchar(14) NOT NULL,
  `rg` varchar(15) DEFAULT NULL,
  `genero` enum('feminino','masculino','outro','prefiro nao informar') DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_admissao` date DEFAULT NULL,
  `situacao` enum('ativo','inativo','ferias','licença') DEFAULT NULL,
  `tipo_contrato` enum('CLT','PJ','Estágio','Temporário') DEFAULT 'CLT' COMMENT 'ADICIONADO: Para regras de benefícios',
  `numero_dependentes` int(11) DEFAULT 0 COMMENT 'ADICIONADO: Para cálculo de IRRF',
  `id_cargo` int(11) DEFAULT NULL,
  `id_setor` int(11) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `colaborador_beneficio`
--

CREATE TABLE `colaborador_beneficio` (
  `id_colaborador` int(11) NOT NULL,
  `id_beneficio` int(11) NOT NULL,
  `valor_especifico` decimal(10,2) DEFAULT NULL COMMENT 'ADICIONADO: Para benefícios com valor variável ou exceções'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contratacao`
--

CREATE TABLE `contratacao` (
  `id_contratacao` int(11) NOT NULL,
  `data_contratacao` date NOT NULL,
  `tipo_contrato` enum('CLT','PJ','estagio') NOT NULL,
  `salario_inicial` decimal(10,2) DEFAULT NULL,
  `id_candidato` int(11) DEFAULT NULL,
  `id_colaborador` int(11) DEFAULT NULL,
  `id_vaga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demissao`
--

CREATE TABLE `demissao` (
  `id_demissao` int(11) NOT NULL,
  `data_demissa` date NOT NULL,
  `tipo_demissao` enum('pedido de demissão','justa causa','sem justa causa') NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `id_colaborador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `endereco`
--

CREATE TABLE `endereco` (
  `id_endereco` int(11) NOT NULL,
  `logradouro` varchar(50) DEFAULT NULL,
  `CEP` varchar(9) DEFAULT NULL,
  `numero` varchar(5) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ferias`
--

CREATE TABLE `ferias` (
  `id_ferias` int(11) NOT NULL,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `situacao` enum('aprovada','pendente','rejeitada') DEFAULT NULL,
  `id_colaborador` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `folha_ponto`
--

CREATE TABLE `folha_ponto` (
  `id_registro` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `timestamp_batida` datetime NOT NULL COMMENT 'MODIFICADO: Armazena cada batida individualmente',
  `geolocalizacao` varchar(50) DEFAULT NULL COMMENT 'ADICIONADO: String com "latitude,longitude"',
  `caminho_foto` varchar(255) DEFAULT NULL COMMENT 'ADICIONADO: Caminho para a foto de prova de vida',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ADICIONADO: Para auditoria'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holerites`
--

CREATE TABLE `holerites` (
  `id_holerite` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `mes_referencia` int(11) NOT NULL,
  `ano_referencia` year(4) NOT NULL,
  `total_proventos` decimal(10,2) NOT NULL,
  `total_descontos` decimal(10,2) NOT NULL,
  `salario_liquido` decimal(10,2) NOT NULL,
  `data_processamento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `holerite_itens`
--

CREATE TABLE `holerite_itens` (
  `id_item` int(11) NOT NULL,
  `id_holerite` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL COMMENT 'Ex: Salário Base, INSS, Horas Extras 50%',
  `tipo` enum('provento','desconto') NOT NULL,
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parametros_folha`
--

CREATE TABLE `parametros_folha` (
  `id_parametro` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL COMMENT 'Ex: INSS_FAIXA_1, DEDUCAO_DEPENDENTE_IRRF',
  `valor` varchar(255) NOT NULL,
  `ano_vigencia` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regras_beneficios`
--

CREATE TABLE `regras_beneficios` (
  `id_regra` int(11) NOT NULL,
  `tipo_contrato` enum('CLT','PJ','Estágio','Temporário') NOT NULL,
  `id_beneficio` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setor`
--

CREATE TABLE `setor` (
  `id_setor` int(11) NOT NULL,
  `nome_setor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vaga`
--

CREATE TABLE `vaga` (
  `id_vaga` int(11) NOT NULL,
  `titulo_vaga` varchar(100) NOT NULL,
  `requisitos` varchar(500) DEFAULT NULL,
  `situacao` enum('aberta','fechada','em processo') DEFAULT NULL,
  `id_setor` int(11) DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `avaliacao_desempenho`
--
ALTER TABLE `avaliacao_desempenho`
  ADD PRIMARY KEY (`id_avaliacao`);

--
-- Indexes for table `beneficios_catalogo`
--
ALTER TABLE `beneficios_catalogo`
  ADD PRIMARY KEY (`id_beneficio`);

--
-- Indexes for table `candidato`
--
ALTER TABLE `candidato`
  ADD PRIMARY KEY (`id_candidato`),
  ADD UNIQUE KEY `CPF` (`CPF`);

--
-- Indexes for table `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD PRIMARY KEY (`id_candidatura`),
  ADD KEY `fk_candidaturas_vaga` (`id_vaga`),
  ADD KEY `fk_candidaturas_candidato` (`id_candidato`);

--
-- Indexes for table `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id_cargo`);

--
-- Indexes for table `colaborador`
--
ALTER TABLE `colaborador`
  ADD PRIMARY KEY (`id_colaborador`),
  ADD UNIQUE KEY `email` (`email_pessoal`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `matricula` (`matricula`),
  ADD KEY `fk_colaborador_cargo` (`id_cargo`),
  ADD KEY `fk_colaborador_setor` (`id_setor`),
  ADD KEY `fk_colaborador_endereco` (`id_endereco`);

--
-- Indexes for table `colaborador_beneficio`
--
ALTER TABLE `colaborador_beneficio`
  ADD PRIMARY KEY (`id_colaborador`,`id_beneficio`),
  ADD KEY `fk_cb_beneficio` (`id_beneficio`);

--
-- Indexes for table `contratacao`
--
ALTER TABLE `contratacao`
  ADD PRIMARY KEY (`id_contratacao`);

--
-- Indexes for table `demissao`
--
ALTER TABLE `demissao`
  ADD PRIMARY KEY (`id_demissao`);

--
-- Indexes for table `endereco`
--
ALTER TABLE `endereco`
  ADD PRIMARY KEY (`id_endereco`);

--
-- Indexes for table `ferias`
--
ALTER TABLE `ferias`
  ADD PRIMARY KEY (`id_ferias`);

--
-- Indexes for table `folha_ponto`
--
ALTER TABLE `folha_ponto`
  ADD PRIMARY KEY (`id_registro`),
  ADD KEY `fk_folha_ponto_colaborador` (`id_colaborador`);

--
-- Indexes for table `holerites`
--
ALTER TABLE `holerites`
  ADD PRIMARY KEY (`id_holerite`),
  ADD KEY `fk_holerites_colaborador` (`id_colaborador`);

--
-- Indexes for table `holerite_itens`
--
ALTER TABLE `holerite_itens`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `fk_holerite_itens_holerite` (`id_holerite`);

--
-- Indexes for table `parametros_folha`
--
ALTER TABLE `parametros_folha`
  ADD PRIMARY KEY (`id_parametro`);

--
-- Indexes for table `regras_beneficios`
--
ALTER TABLE `regras_beneficios`
  ADD PRIMARY KEY (`id_regra`),
  ADD KEY `fk_regras_beneficios_catalogo` (`id_beneficio`);

--
-- Indexes for table `setor`
--
ALTER TABLE `setor`
  ADD PRIMARY KEY (`id_setor`);

--
-- Indexes for table `vaga`
--
ALTER TABLE `vaga`
  ADD PRIMARY KEY (`id_vaga`),
  ADD KEY `fk_vaga_setor` (`id_setor`),
  ADD KEY `fk_vaga_cargo` (`id_cargo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `avaliacao_desempenho`
--
ALTER TABLE `avaliacao_desempenho`
  MODIFY `id_avaliacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficios_catalogo`
--
ALTER TABLE `beneficios_catalogo`
  MODIFY `id_beneficio` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidato`
--
ALTER TABLE `candidato`
  MODIFY `id_candidato` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidaturas`
--
ALTER TABLE `candidaturas`
  MODIFY `id_candidatura` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `id_colaborador` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contratacao`
--
ALTER TABLE `contratacao`
  MODIFY `id_contratacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `demissao`
--
ALTER TABLE `demissao`
  MODIFY `id_demissao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `endereco`
--
ALTER TABLE `endereco`
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ferias`
--
ALTER TABLE `ferias`
  MODIFY `id_ferias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folha_ponto`
--
ALTER TABLE `folha_ponto`
  MODIFY `id_registro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holerites`
--
ALTER TABLE `holerites`
  MODIFY `id_holerite` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `holerite_itens`
--
ALTER TABLE `holerite_itens`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parametros_folha`
--
ALTER TABLE `parametros_folha`
  MODIFY `id_parametro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regras_beneficios`
--
ALTER TABLE `regras_beneficios`
  MODIFY `id_regra` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `setor`
--
ALTER TABLE `setor`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vaga`
--
ALTER TABLE `vaga`
  MODIFY `id_vaga` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD CONSTRAINT `fk_candidaturas_candidato_1` FOREIGN KEY (`id_candidato`) REFERENCES `candidato` (`id_candidato`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_candidaturas_vaga_1` FOREIGN KEY (`id_vaga`) REFERENCES `vaga` (`id_vaga`) ON DELETE CASCADE;

--
-- Constraints for table `colaborador_beneficio`
--
ALTER TABLE `colaborador_beneficio`
  ADD CONSTRAINT `fk_cb_beneficio_1` FOREIGN KEY (`id_beneficio`) REFERENCES `beneficios_catalogo` (`id_beneficio`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cb_colaborador_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE;

--
-- Constraints for table `folha_ponto`
--
ALTER TABLE `folha_ponto`
  ADD CONSTRAINT `fk_folha_ponto_colaborador_1` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE;

--
-- Constraints for table `regras_beneficios`
--
ALTER TABLE `regras_beneficios`
  ADD CONSTRAINT `fk_regras_beneficios_catalogo_1` FOREIGN KEY (`id_beneficio`) REFERENCES `beneficios_catalogo` (`id_beneficio`) ON DELETE CASCADE;

--
-- Constraints for table `vaga`
--
ALTER TABLE `vaga`
  ADD CONSTRAINT `fk_vaga_cargo` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`),
  ADD CONSTRAINT `fk_vaga_cargo_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargo` (`id_cargo`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vaga_setor` FOREIGN KEY (`id_setor`) REFERENCES `setor` (`id_setor`),
  ADD CONSTRAINT `fk_vaga_setor_1` FOREIGN KEY (`id_setor`) REFERENCES `setor` (`id_setor`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
