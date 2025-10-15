-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 12, 2025 at 10:09 PM
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
  `custo_padrao_empresa` decimal(10,2) DEFAULT NULL,
  `status` enum('Ativo','Inativo') NOT NULL DEFAULT 'Ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beneficios_catalogo`
--

INSERT INTO `beneficios_catalogo` (`id_beneficio`, `nome`, `categoria`, `tipo_valor`, `custo_padrao_empresa`, `status`) VALUES
(1, 'YAYAYAYAY', 'Saúde', 'Fixo', 366.00, 'Ativo'),
(2, '3wetrqwert', 'Mobilidade', 'Fixo', 1244.00, 'Ativo'),
(5, 'dassf', 'Saúde', 'Fixo', NULL, 'Ativo'),
(6, 'asdfsdgggg', 'Saúde', 'Fixo', NULL, 'Ativo'),
(7, 'asdfsdgggg', 'Saúde', 'Fixo', 12234.00, 'Ativo'),
(9, 'vale alimentação', 'Alimentação', 'Variável', NULL, 'Ativo');

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

--
-- Dumping data for table `candidato`
--

INSERT INTO `candidato` (`id_candidato`, `nome_completo`, `CPF`, `situacao`, `curriculo`) VALUES
(1, 'Rhyan Nascimento de Sousa', '01794023151', 'em análise', '/uploads/curriculos/68e4410e7526b-Currículo Rhyan.pdf'),
(2, 'Rhyan Nascimento de Sousa', '654.642.354-55', 'em análise', '/uploads/curriculos/68e46c88d613b-Currículo Rhyan.pdf'),
(3, 'Gabriel Rodrigues', '76576556', 'em análise', '/uploads/curriculos/68e479e4967dd-downloadfile.PDF'),
(4, 'Gabriel Rodrigues', '23123312', 'em análise', '/uploads/curriculos/68e47a6d3bff7-downloadfile.PDF'),
(5, 'Rhyan Nascimento de Sousa', '98798878', 'em análise', '/uploads/curriculos/68e47ae8e5e72-Rhyan Nascimento de Sousa.pdf'),
(6, 'Rhyan Nascimento de Sousa', '98787789956', 'em análise', '/uploads/curriculos/68e50a53ef859-Rhyan Nascimento de Sousa.pdf'),
(7, 'Lean Albuquerque', '45612378912', 'em análise', '/uploads/curriculos/68e64e62b4162-Curriculo_novo.pdf');

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

--
-- Dumping data for table `candidaturas`
--

INSERT INTO `candidaturas` (`id_candidatura`, `id_vaga`, `id_candidato`, `data_candidatura`, `pontuacao_aderencia`, `justificativa_ia`, `status_triagem`) VALUES
(1, 1, 1, '2025-10-06 22:22:06', NULL, NULL, 'Recebido'),
(2, 4, 2, '2025-10-07 01:27:36', 3, 'O currículo apresenta uma desconexão significativa com os requisitos técnicos da vaga de Desenvolvedor Full Stack Pleno. O candidato não demonstra experiência formal em desenvolvimento de software nem proficiência nas tecnologias obrigatórias como JavaScript, React.js e Node.js. Embora esteja cursando Ciência da Computação e mencione linguagens como Python e Java, sua experiência profissional e habilidades listadas são focadas em gestão, atendimento e infraestrutura de redes, não aderindo ao perfil procurado.', 'Recebido'),
(4, 4, 4, '2025-10-07 02:26:53', 0, 'Não foi fornecido nenhum currículo para análise. Para que eu possa avaliar a aderência do candidato à vaga de Desenvolvedor Full Stack Pleno – Projeto Orion, por favor, inclua o conteúdo do currículo. Sem essas informações, não é possível gerar um sumário ou calcular uma nota de correspondência.', 'Recebido'),
(5, 4, 5, '2025-10-07 02:28:56', 44, 'Rhyan demonstra solidez em Python, Django e bancos de dados relacionais (PostgreSQL/MySQL), com experiência em desenvolvimento web e automação. Contudo, há uma lacuna significativa nos requisitos obrigatórios de frontend (React.js) e backend (Node.js/TypeScript), tecnologias centrais para esta vaga de Desenvolvedor Full Stack Pleno. Embora exiba competências desejáveis em Machine Learning e alguma experiência em gestão de sistemas, o perfil técnico primário do currículo não se alinha diretamente com o stack principal da vaga. Adicionalmente, seu resumo profissional sugere busca por estágio focado em IA, o que se distancia do escopo e nível da posição oferecida.', 'Recebido'),
(6, 5, 6, '2025-10-07 12:40:51', 57, 'O candidato Rhyan Nascimento de Sousa demonstra uma base sólida em programação Python e SQL, essencial para a vaga de Cientista de Dados Júnior, com experiência prática em automação, desenvolvimento web e projetos de análise de dados que utilizam Pandas e conceitos de IA/PLN. Sua formação em Ciência da Computação, incluindo aprendizado de máquina, complementa os requisitos técnicos da posição. Embora possua conhecimento inferido em estatística e algumas técnicas de machine learning, há lacunas na menção explícita de bibliotecas fundamentais como NumPy, Matplotlib e Scikit-learn, além de ferramentas de BI como Tableau/Power BI. Contudo, seu perfil proativo e a experiência com manipulação de dados em desafios reais o tornam um candidato promissor, com um bom potencial de desenvolvimento na função.', 'Recebido'),
(7, 5, 7, '2025-10-08 11:43:30', 78, 'Lean Albuquerque demonstra um perfil muito promissor para a vaga de Cientista de Dados Júnior, com sólida base em Python, SQL, lógica de programação e experiência prática em Machine Learning através de projetos e estágio. Sua formação em Ciência da Computação e capacidade de comunicação são pontos fortes alinhados aos requisitos. Embora algumas bibliotecas específicas (Pandas, NumPy, Matplotlib) e ferramentas de visualização não sejam explicitamente mencionadas, sua experiência com pré-processamento de dados e Scikit-learn sugere familiaridade. O candidato possui um bom alinhamento com as expectativas para uma posição de entrada, com potencial de crescimento significativo.', 'Recebido');

-- --------------------------------------------------------

--
-- Table structure for table `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `nome_cargo` varchar(100) NOT NULL,
  `salario_base` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cargo`
--

INSERT INTO `cargo` (`id_cargo`, `nome_cargo`, `salario_base`) VALUES
(23, 'calabreso', 0.00),
(27, 'calabresouuu', 0.00),
(28, 'calabres', 0.00);

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

--
-- Dumping data for table `colaborador`
--

INSERT INTO `colaborador` (`id_colaborador`, `matricula`, `nome_completo`, `email_pessoal`, `email_profissional`, `senha`, `perfil`, `status_conta`, `token_verificacao`, `token_recuperacao`, `token_expiracao`, `cpf`, `rg`, `genero`, `data_nascimento`, `telefone`, `data_admissao`, `situacao`, `tipo_contrato`, `numero_dependentes`, `id_cargo`, `id_setor`, `id_endereco`) VALUES
(1, 'skdjfks77', 'Jarineura Gomes Nascimento Chavesss', 'rhyannscanada@gmail.com', 'rhyannscanada@gmail.com', 'heheboi', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '654.642.354-55', '21355122', '', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'ativo', 'CLT', 0, 23, 16, 23),
(12, 'skdjfks3827346', 'Jarineura Gomes Nascimento Chavez', 'rhyannscasdfnada@gmail.com', 's@gmail.cofdfsdm', 'heheboi', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '654.642.354-57', '21355126', '', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'ativo', 'CLT', 0, 27, 17, 28),
(13, 'skdjfks382734648', 'Jarineura Gomes Nascimento Chavei', 'rhyannscasdfdfnada@gmail.com', 's@gmail.cofdfsdsfgm', 'heheboi', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '654.642.354-50', '21355123', '', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'ativo', 'CLT', 0, 28, 18, 29),
(46, 'C1759891155', 'Rhyan Nascimento de Sousa', 'rhyannsousa@gmail.com', 'rhyannsousa@gmail.com', '$2y$10$9ie2djxm/5.Ozfnsh818X.WMo65RNPYXTU/lQlKsyWNrg0NEnwMP.', 'colaborador', 'pendente_verificacao', '579837882c9c0c9914c9f782155e56111a7a45edaeee4267e31a52719ed6629f', NULL, '2025-10-08 05:39:15', '12332112232', NULL, '', '2025-10-04', '', '2025-10-08', 'ativo', 'CLT', 0, NULL, NULL, NULL);

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

--
-- Dumping data for table `endereco`
--

INSERT INTO `endereco` (`id_endereco`, `logradouro`, `CEP`, `numero`, `bairro`, `cidade`, `estado`) VALUES
(23, 'Quadra ARSE 131 Rua 4', '77024-664', '131', 'Plano Diretor Sul', 'Palmas', 'TO'),
(28, 'Quadra ARSE 131 Rua 4', '77024-664', '131', 'Plano Diretor Sul', 'Palmas', 'TO'),
(29, 'Quadra ARSE 131 Rua 4', '77024-664', '131', 'Plano Diretor Sul', 'Palmas', 'TO');

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
  `id_registro_ponto` int(11) NOT NULL,
  `id_colaborador` int(11) NOT NULL,
  `data_hora_entrada` datetime DEFAULT NULL,
  `data_hora_saida` datetime DEFAULT NULL,
  `geolocalizacao` varchar(50) DEFAULT NULL COMMENT 'ADICIONADO: String com "latitude,longitude"',
  `caminho_foto` varchar(255) DEFAULT NULL COMMENT 'ADICIONADO: Caminho para a foto de prova de vida',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ADICIONADO: Para auditoria'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `folha_ponto`
--

INSERT INTO `folha_ponto` (`id_registro_ponto`, `id_colaborador`, `data_hora_entrada`, `data_hora_saida`, `geolocalizacao`, `caminho_foto`, `ip_address`) VALUES
(1, 1, '2025-10-08 02:38:41', '2025-10-08 04:45:42', NULL, NULL, NULL),
(2, 1, '2025-10-08 13:30:12', '2025-10-08 13:47:57', NULL, NULL, NULL);

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

--
-- Dumping data for table `regras_beneficios`
--

INSERT INTO `regras_beneficios` (`id_regra`, `tipo_contrato`, `id_beneficio`) VALUES
(2, 'PJ', 1),
(3, 'Estágio', 5),
(4, 'Estágio', 1),
(5, 'Temporário', 1),
(6, 'CLT', 9);

-- --------------------------------------------------------

--
-- Table structure for table `setor`
--

CREATE TABLE `setor` (
  `id_setor` int(11) NOT NULL,
  `nome_setor` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setor`
--

INSERT INTO `setor` (`id_setor`, `nome_setor`) VALUES
(16, 'toscanetto'),
(17, 'toscanettouuu'),
(18, 'toscanettouuus'),
(19, 'Operacional'),
(20, 'TI'),
(21, 'Tecnologia da Informação – Desenvolvimento de Software'),
(22, 'Business Intelligence (BI) e Analytics');

-- --------------------------------------------------------

--
-- Table structure for table `vaga`
--

CREATE TABLE `vaga` (
  `id_vaga` int(11) NOT NULL,
  `titulo_vaga` varchar(100) NOT NULL,
  `requisitos_necessarios` text DEFAULT NULL,
  `requisitos_recomendados` text DEFAULT NULL,
  `requisitos_desejados` text DEFAULT NULL,
  `descricao_vaga` text DEFAULT NULL COMMENT 'Descrição completa sobre a vaga e a equipe',
  `situacao` enum('aberta','fechada','em processo') DEFAULT NULL,
  `id_setor` int(11) DEFAULT NULL,
  `id_cargo` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaga`
--

INSERT INTO `vaga` (`id_vaga`, `titulo_vaga`, `requisitos_necessarios`, `requisitos_recomendados`, `requisitos_desejados`, `descricao_vaga`, `situacao`, `id_setor`, `id_cargo`, `data_criacao`) VALUES
(1, 'Engenheiro de software', 'gitflow, github, java, sql', NULL, NULL, NULL, 'aberta', 19, NULL, '2025-10-06 20:59:56'),
(2, 'Cientista de dados', 'SQL, Python, Flask, Django', NULL, NULL, NULL, 'aberta', 20, NULL, '2025-10-06 22:44:34'),
(3, 'Arquiteto da Informação', 'design de interação, taxonomias', 'sistemas de organização de conteúdo, metadados', 'habilidades interpessoais (como comunicação clara, análise, resolução de problemas e colaboração com equipes de UX e desenvolvimento)', 'O seu trabalho visa criar uma experiência de usuário (UX) positiva através de sistemas de navegação claros, categorizações lógicas e hierarquias bem definidas, garantindo que os objetivos do negócio e do usuário sejam atendidos. ', 'aberta', 20, NULL, '2025-10-07 01:03:36'),
(4, 'Desenvolvedor Full Stack Pleno – Projeto Orion', 'JavaScript/TypeScript, React.js, Node.js (Express ou NestJS), bancos de dados relacionais (PostgreSQL/MySQL), Git e GitHub/GitLab, arquitetura MVC e REST', 'Docker, CI/CD (GitHub Actions, GitLab CI, etc.), testes automatizados (Jest, Mocha, Cypress), GraphQL, Next.js, design de sistemas escaláveis e microserviços', 'Cloud (AWS, GCP ou Azure), DevOps e observabilidade (Prometheus, Grafana), projetos open source, Machine Learning aplicado a e-commerce', 'A equipe do Projeto Orion, iniciativa interna voltada à criação de soluções inteligentes para e-commerce, busca um Desenvolvedor Full Stack Pleno para atuar no desenvolvimento e manutenção de aplicações web escaláveis. O profissional participará de todo o ciclo de vida do produto, desde o planejamento até a implantação e monitoramento em produção, colaborando com times de UI/UX e Data Science.\r\n\r\n🛠️ Responsabilidades\r\n\r\nDesenvolver novas funcionalidades e manter aplicações existentes.\r\n\r\nIntegrar sistemas via APIs RESTful e GraphQL.\r\n\r\nEscrever código limpo, testável e bem documentado.\r\n\r\nParticipar de revisões de código e decisões arquiteturais.\r\n\r\nColaborar com designers e analistas de dados na melhoria contínua do produto.', 'aberta', 21, NULL, '2025-10-07 01:07:03'),
(5, 'Cientista de Dados Júnior', 'Python,SQL para extração de dados,Pandas,NumPy,Matplotlib,Scikit-learn,Conhecimento em estatística fundamental,Lógica de programação,Capacidade de comunicação', 'Git para versionamento de código,Jupyter Notebooks,Tableau,Power BI,Noções de álgebra linear,Limpeza e pré-processamento de dados,Técnicas de machine learning supervisionado e não supervisionado', 'Computação em nuvem (AWS, Azure ou GCP),PySpark,TensorFlow ou PyTorch,Experiência com APIs REST,Docker,Familiaridade com metodologias ágeis', 'Estamos à procura de um Cientista de Dados Júnior curioso e motivado para se juntar à nossa equipe de Analytics. O candidato ideal terá uma base sólida em estatística, programação e um forte desejo de resolver problemas complexos através dos dados. Nesta posição, você colaborará com equipes multifuncionais para extrair, limpar e analisar grandes conjuntos de dados, desenvolvendo modelos de machine learning iniciais e criando visualizações que gerem insights valiosos para a tomada de decisões estratégicas. Esta é uma oportunidade fantástica para aprender com profissionais experientes e crescer na carreira de ciência de dados, aplicando seu conhecimento teórico em desafios reais do negócio.', 'aberta', 22, NULL, '2025-10-07 12:40:08'),
(6, 'Engenheiro de software', 'gitflow, github, java, sql', 'Criatividade, Proatividade', '', 'desenvolver projetos e sistemas com php', 'aberta', 20, NULL, '2025-10-08 11:42:07');

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
  ADD PRIMARY KEY (`id_registro_ponto`),
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
  MODIFY `id_beneficio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `candidato`
--
ALTER TABLE `candidato`
  MODIFY `id_candidato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `candidaturas`
--
ALTER TABLE `candidaturas`
  MODIFY `id_candidatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `colaborador`
--
ALTER TABLE `colaborador`
  MODIFY `id_colaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
  MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `ferias`
--
ALTER TABLE `ferias`
  MODIFY `id_ferias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folha_ponto`
--
ALTER TABLE `folha_ponto`
  MODIFY `id_registro_ponto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id_regra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `setor`
--
ALTER TABLE `setor`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `vaga`
--
ALTER TABLE `vaga`
  MODIFY `id_vaga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
