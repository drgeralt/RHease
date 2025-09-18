-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: rhease
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `avaliacao_desempenho`
--

DROP TABLE IF EXISTS `avaliacao_desempenho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `avaliacao_desempenho` (
  `id_avaliacao` int(11) NOT NULL AUTO_INCREMENT,
  `data_avaliacao` date DEFAULT NULL,
  `nota` decimal(10,2) NOT NULL,
  `feedback` varchar(500) DEFAULT NULL,
  `ID_Avaliado` int(11) NOT NULL,
  PRIMARY KEY (`id_avaliacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `beneficio`
--

DROP TABLE IF EXISTS `beneficio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `beneficio` (
  `id_beneficio` int(11) NOT NULL AUTO_INCREMENT,
  `nome_beneficio` varchar(100) DEFAULT NULL,
  `custo_padrao_empresa` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_beneficio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `candidato`
--

DROP TABLE IF EXISTS `candidato`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidato` (
  `id_candidato` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(100) DEFAULT NULL,
  `CPF` varchar(14) NOT NULL,
  `situacao` enum('em análise','aprovado','rejeitado','contratado') NOT NULL DEFAULT 'em análise',
  `curriculo` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_candidato`),
  UNIQUE KEY `CPF` (`CPF`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `candidato_vaga`
--

DROP TABLE IF EXISTS `candidato_vaga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidato_vaga` (
  `ID_Candidato` int(11) NOT NULL,
  `ID_Vaga` int(11) NOT NULL,
  PRIMARY KEY (`ID_Candidato`,`ID_Vaga`),
  KEY `fk_cv_vaga` (`ID_Vaga`),
  CONSTRAINT `fk_cv_candidato` FOREIGN KEY (`ID_Candidato`) REFERENCES `candidato` (`id_candidato`) ON DELETE CASCADE,
  CONSTRAINT `fk_cv_vaga` FOREIGN KEY (`ID_Vaga`) REFERENCES `vaga` (`id_vaga`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cargo`
--

DROP TABLE IF EXISTS `cargo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL AUTO_INCREMENT,
  `nome_cargo` varchar(100) NOT NULL,
  `salario_base` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_cargo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `colaborador`
--

DROP TABLE IF EXISTS `colaborador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaborador` (
  `id_colaborador` int(11) NOT NULL AUTO_INCREMENT,
  `nome_completo` varchar(100) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `CPF` varchar(14) NOT NULL,
  `RG` varchar(7) NOT NULL,
  `genero` enum('feminino','masculino','outro','prefiro não informar') DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` varchar(11) DEFAULT NULL,
  `data_admissao` date NOT NULL,
  `situacao` enum('ativo','inativo','ferias','licença') DEFAULT NULL,
  `ID_Cargo` int(11) DEFAULT NULL,
  `ID_Setor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_colaborador`),
  UNIQUE KEY `CPF` (`CPF`),
  UNIQUE KEY `RG` (`RG`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_colaborador_cargo` (`ID_Cargo`),
  KEY `fk_colaborador_setor` (`ID_Setor`),
  CONSTRAINT `fk_colaborador_cargo` FOREIGN KEY (`ID_Cargo`) REFERENCES `cargo` (`id_cargo`) ON DELETE SET NULL,
  CONSTRAINT `fk_colaborador_setor` FOREIGN KEY (`ID_Setor`) REFERENCES `setor` (`id_setor`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `colaborador_beneficio`
--

DROP TABLE IF EXISTS `colaborador_beneficio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `colaborador_beneficio` (
  `ID_Colaborador` int(11) NOT NULL,
  `ID_Beneficio` int(11) NOT NULL,
  PRIMARY KEY (`ID_Colaborador`,`ID_Beneficio`),
  KEY `fk_cb_beneficio` (`ID_Beneficio`),
  CONSTRAINT `fk_cb_beneficio` FOREIGN KEY (`ID_Beneficio`) REFERENCES `beneficio` (`id_beneficio`) ON DELETE CASCADE,
  CONSTRAINT `fk_cb_colaborador` FOREIGN KEY (`ID_Colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contratacao`
--

DROP TABLE IF EXISTS `contratacao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contratacao` (
  `id_contratacao` int(11) NOT NULL AUTO_INCREMENT,
  `data_contratacao` date NOT NULL,
  `tipo_contrato` enum('CLT','PJ','estagio') NOT NULL,
  `salario_inicial` decimal(10,2) DEFAULT NULL,
  `ID_Candidato` int(11) NOT NULL,
  `ID_Colaborador` int(11) NOT NULL,
  `ID_Vaga` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_contratacao`),
  KEY `fk_contratacao_colaborador` (`ID_Colaborador`),
  CONSTRAINT `fk_contratacao_colaborador` FOREIGN KEY (`ID_Colaborador`) REFERENCES `colaborador` (`id_colaborador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `demissao`
--

DROP TABLE IF EXISTS `demissao`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `demissao` (
  `id_demissao` int(11) NOT NULL AUTO_INCREMENT,
  `data_demissa` date NOT NULL,
  `tipo_demissao` enum('pedido de demissão','justa causa','sem justa causa') NOT NULL,
  `motivo` varchar(200) DEFAULT NULL,
  `ID_Colaborador` int(11) NOT NULL,
  PRIMARY KEY (`id_demissao`),
  KEY `fk_demissao_colaborador` (`ID_Colaborador`),
  CONSTRAINT `fk_demissao_colaborador` FOREIGN KEY (`ID_Colaborador`) REFERENCES `colaborador` (`id_colaborador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ferias`
--

DROP TABLE IF EXISTS `ferias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ferias` (
  `id_ferias` int(11) NOT NULL AUTO_INCREMENT,
  `data_inicio` date DEFAULT NULL,
  `data_fim` date DEFAULT NULL,
  `situacao` enum('aprovada','pendente','rejeitada') DEFAULT NULL,
  `ID_Colaborador` int(11) NOT NULL,
  PRIMARY KEY (`id_ferias`),
  KEY `fk_ferias_colaborador` (`ID_Colaborador`),
  CONSTRAINT `fk_ferias_colaborador` FOREIGN KEY (`ID_Colaborador`) REFERENCES `colaborador` (`id_colaborador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `folha_ponto`
--

DROP TABLE IF EXISTS `folha_ponto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `folha_ponto` (
  `id_registro_ponto` int(11) NOT NULL AUTO_INCREMENT,
  `data_hora_entrada` datetime DEFAULT NULL,
  `data_hora_saida` datetime DEFAULT NULL,
  `ID_Colaborador` int(11) NOT NULL,
  PRIMARY KEY (`id_registro_ponto`),
  KEY `fk_ponto_colaborador` (`ID_Colaborador`),
  CONSTRAINT `fk_ponto_colaborador` FOREIGN KEY (`ID_Colaborador`) REFERENCES `colaborador` (`id_colaborador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pagamento`
--

DROP TABLE IF EXISTS `pagamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL AUTO_INCREMENT,
  `data_referencia` date NOT NULL,
  `salario_bruto` decimal(10,2) NOT NULL,
  `total_desconto` decimal(10,2) DEFAULT NULL,
  `salario_liquido` decimal(10,2) DEFAULT NULL,
  `ID_Colaborador` int(11) NOT NULL,
  PRIMARY KEY (`id_pagamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setor`
--

DROP TABLE IF EXISTS `setor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setor` (
  `id_setor` int(11) NOT NULL AUTO_INCREMENT,
  `nome_setor` varchar(100) NOT NULL,
  PRIMARY KEY (`id_setor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vaga`
--

DROP TABLE IF EXISTS `vaga`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vaga` (
  `id_vaga` int(11) NOT NULL AUTO_INCREMENT,
  `titulo_vaga` varchar(100) NOT NULL,
  `requisitos` varchar(500) DEFAULT NULL,
  `situacao` enum('aberta','fechada','em processo') DEFAULT NULL,
  `ID_Setor` int(11) DEFAULT NULL,
  `ID_Cargo` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_vaga`),
  KEY `fk_vaga_setor` (`ID_Setor`),
  KEY `fk_vaga_cargo` (`ID_Cargo`),
  CONSTRAINT `fk_vaga_cargo` FOREIGN KEY (`ID_Cargo`) REFERENCES `cargo` (`id_cargo`),
  CONSTRAINT `fk_vaga_setor` FOREIGN KEY (`ID_Setor`) REFERENCES `setor` (`id_setor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-18 14:47:10
