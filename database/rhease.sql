-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 26, 2025 at 05:55 AM
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
                                                                                                                            (6, 'asdfsdgggg', 'Saúde', 'Fixo', NULL, 'Ativo'),
                                                                                                                            (9, 'vale alimentação', 'Alimentação', 'Variável', NULL, 'Ativo'),
                                                                                                                            (10, 'rocambole', 'Alimentação', 'Fixo', 15.00, 'Ativo'),
                                                                                                                            (11, 'heyhey', 'Saúde', 'Descritivo', NULL, 'Ativo'),
                                                                                                                            (12, 'heyhey', 'Saúde', 'Descritivo', NULL, 'Ativo'),
                                                                                                                            (13, 'heyhey', 'Saúde', 'Descritivo', NULL, 'Ativo');

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
                                                                                              (7, 'Lean Albuquerque', '45612378912', 'em análise', '/uploads/curriculos/68e64e62b4162-Curriculo_novo.pdf'),
                                                                                              (8, 'Rhyan Nascimento de Sousa', '654.642.354-50', 'em análise', '/uploads/curriculos/68f8291352445-Rhyan Nascimento de Sousa.pdf'),
                                                                                              (9, 'teste', '12454365776', 'em análise', '/uploads/curriculos/68f8618bf334a-Notes_251022_014502.pdf'),
                                                                                              (10, 'Catarina', '12443246775', 'em análise', '/uploads/curriculos/68f86538da0f1-Rhyan Nascimento de Sousa.pdf'),
                                                                                              (11, 'Rhyan Nascimento de Sousa', '65785423615', 'em análise', '/uploads/curriculos/68f8c2285d2dd-Rhyan Nascimento de Sousa.pdf'),
                                                                                              (12, 'Rhyan Teste', '12454676587', 'em análise', '/uploads/curriculos/6926612e63f63-Rhyan Nascimento de Sousa.pdf');

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
                                                                                                                                                              (7, 5, 7, '2025-10-08 11:43:30', 78, 'Lean Albuquerque demonstra um perfil muito promissor para a vaga de Cientista de Dados Júnior, com sólida base em Python, SQL, lógica de programação e experiência prática em Machine Learning através de projetos e estágio. Sua formação em Ciência da Computação e capacidade de comunicação são pontos fortes alinhados aos requisitos. Embora algumas bibliotecas específicas (Pandas, NumPy, Matplotlib) e ferramentas de visualização não sejam explicitamente mencionadas, sua experiência com pré-processamento de dados e Scikit-learn sugere familiaridade. O candidato possui um bom alinhamento com as expectativas para uma posição de entrada, com potencial de crescimento significativo.', 'Recebido'),
                                                                                                                                                              (8, 7, 8, '2025-10-22 00:45:07', 34, 'O candidato Rhyan Nascimento de Sousa possui experiência com desenvolvimento web em Python/Django e PHP, além de sólido conhecimento em bancos de dados relacionais (MySQL, PostgreSQL) e controle de versão com Git. No entanto, o currículo não demonstra proficiência nos requisitos obrigatórios da vaga, como JavaScript (ES6+), React/Vue.js e Node.js. Há uma ausência significativa dos requisitos recomendados e desejáveis, incluindo TypeScript, testes e ambientes Cloud. O resumo profissional, focado em IA e busca por estágio, sugere um desalinhamento com a posição de Analista de Desenvolvimento Web Pleno.', 'Recebido'),
                                                                                                                                                              (9, 7, 9, '2025-10-22 04:46:04', 0, 'O currículo fornecido está vazio, impossibilitando qualquer análise ou avaliação da aderência do candidato aos requisitos da vaga de Analista de Desenvolvimento Web Pleno. Não há informações para comparar com os requisitos obrigatórios, recomendados ou desejáveis. Portanto, não é possível determinar se o candidato possui as qualificações necessárias para a posição.', 'Recebido'),
                                                                                                                                                              (10, 8, 10, '2025-10-22 05:01:44', 0, 'O candidato Rhyan Nascimento de Sousa possui formação e experiência exclusivamente na área de Ciência da Computação e Tecnologia da Informação, focando em desenvolvimento de software e inteligência artificial. Não há qualquer evidência de formação em Medicina Veterinária, registro no CRMV, ou experiência clínica/cirúrgica com pequenos animais, que são requisitos mandatórios para a vaga. Seu perfil está totalmente desalinhado com as qualificações e responsabilidades exigidas para um Médico Veterinário.', 'Recebido'),
                                                                                                                                                              (11, 7, 11, '2025-10-22 11:38:16', 35, 'O candidato Rhyan demonstra experiência em desenvolvimento web com Python, Django e PHP, além de sólido conhecimento em bancos de dados relacionais (MySQL, PostgreSQL) e Git. Contudo, há lacunas significativas nos requisitos obrigatórios da vaga, como a ausência de proficiência em React/Vue.js e experiência com Node.js, que são tecnologias-chave para a posição de Analista de Desenvolvimento Web Pleno. O currículo também não evidencia os requisitos recomendados ou desejáveis, e o resumo profissional expressa busca por estágio com foco em IA, desalinhado com o perfil da vaga. Embora mostre potencial com outras stacks, a aderência às tecnologias específicas da vaga é baixa.', 'Recebido'),
                                                                                                                                                              (12, 6, 12, '2025-11-26 02:08:46', 77, 'Rhyan Nascimento possui forte aderência à descrição geral da vaga de Engenheiro de Software, com experiência prática em PHP e SQL em projetos notáveis. Ele também atende bem aos requisitos de GitHub, Criatividade e Proatividade. Contudo, a ausência de qualquer menção a Java, um requisito obrigatório crucial, é uma lacuna significativa. Apesar dessa ressalva, seu perfil proativo e experiência com tecnologias avançadas sugerem um candidato com grande potencial de aprendizado.', 'Recebido');

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
                                                                   (23, 'Dono do Félix', 4000.00),
                                                                   (27, 'calabresouuu', 0.00),
                                                                   (28, 'calabres', 9999.00),
                                                                   (29, 'Analista de Sistemas', 10000.00);

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
                               `id_endereco` int(11) DEFAULT NULL,
                               `failed_login_attempts` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Contador de tentativas de login falhadas consecutivas',
                               `last_failed_login_at` timestamp NULL DEFAULT NULL COMMENT 'Timestamp da última tentativa de login falhada',
                               `facial_embedding` longtext DEFAULT NULL COMMENT 'Embedding facial em formato JSON',
                               `facial_registered_at` datetime DEFAULT NULL COMMENT 'Data e hora do cadastro facial',
                               `face_registered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colaborador`
--

INSERT INTO `colaborador` (`id_colaborador`, `matricula`, `nome_completo`, `email_pessoal`, `email_profissional`, `senha`, `perfil`, `status_conta`, `token_verificacao`, `token_recuperacao`, `token_expiracao`, `cpf`, `rg`, `genero`, `data_nascimento`, `telefone`, `data_admissao`, `situacao`, `tipo_contrato`, `numero_dependentes`, `id_cargo`, `id_setor`, `id_endereco`, `failed_login_attempts`, `last_failed_login_at`, `facial_embedding`, `facial_registered_at`, `face_registered_at`) VALUES
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (1, 'skdjfks77', 'Rhyanzinho do Meu Coraçãozinho', 'rhyannscanada@gmail.com', 'rhyannscanada@gmail.com', '$2y$10$9ie2djxm/5.Ozfnsh818X.WMo65RNPYXTU/lQlKsyWNrg0NEnwMP.', 'colaborador', 'ativo', NULL, NULL, NULL, '654.642.354-55', '21355122', 'masculino', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'ativo', 'CLT', 0, 23, 16, 23, 0, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (12, 'skdjfks3827346', 'Jarineura Gomes Nascimento Chavez', 'rhyannscasdfnada@gmail.com', 's@gmail.cofdfsdm', 'heheboi', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '654.642.354-57', '21355126', '', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'ativo', 'CLT', 0, 27, 17, 28, 0, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (13, 'skdjfks382734648', 'Jarineura Gomes Nascimento Chav', 'rhyannscasdfdfnada@gmail.com', 's@gmail.cofdfsdsfgm', 'heheboi', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '654.642.354-50', '21355123', '', '1998-06-15', '(71) 99137-0403', '2000-04-05', 'inativo', 'CLT', 0, 28, 18, 29, 0, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (46, 'C1759891155', 'Rhyan Nascimento de Sousa', 'rhyannsousa@gmail.com', 'rhyannsousa@gmail.com', '$2y$10$9ie2djxm/5.Ozfnsh818X.WMo65RNPYXTU/lQlKsyWNrg0NEnwMP.', 'gestor_rh', 'ativo', '579837882c9c0c9914c9f782155e56111a7a45edaeee4267e31a52719ed6629f', NULL, '2025-10-08 05:39:15', '12332112232', NULL, '', '2025-10-04', '', '2025-10-08', 'ativo', 'CLT', 0, 28, 18, 29, 0, NULL, '[1.2938919067382812, 2.0251786708831787, 0.5684653520584106, -0.2288495898246765, 1.2141815423965454, -0.04247573763132095, -0.3135140538215637, -0.237853541970253, 0.7057810425758362, -0.3853028118610382, -0.43267589807510376, 0.014377158135175705, -1.8958055973052979, 1.2165274620056152, 0.18609915673732758, -1.1063668727874756, -0.8850295543670654, 1.3139599561691284, -0.1460546851158142, 0.7806971669197083, -1.3216532468795776, 0.07190532982349396, 0.5318453311920166, -0.6432156562805176, 0.0818559005856514, 0.49871352314949036, 0.5756704211235046, -1.4470049142837524, 0.5177498459815979, -1.4041799306869507, -0.5940220952033997, -0.6485917568206787, -0.36315274238586426, 1.0849363803863525, 1.6918548345565796, 1.090396523475647, 1.8906255960464478, -0.1224050372838974, 0.4279983639717102, -0.01164159830659628, -0.29822754859924316, -0.8470929265022278, -0.937736988067627, -0.47666263580322266, -1.0948872566223145, -1.9973598718643188, -1.7620351314544678, 1.279409646987915, -0.9153900146484375, 0.1814359575510025, 0.5305883288383484, -0.5813024640083313, -1.6271206140518188, 1.316870927810669, -0.592427134513855, -0.0031173769384622574, -2.016662359237671, 0.9042550921440125, -0.066968634724617, -0.3884802460670471, 0.8530223369598389, 0.8606948256492615, 0.47459933161735535, 0.6586917638778687, -0.40326374769210815, -1.5141350030899048, 0.6386816501617432, 1.0490766763687134, 2.6905479431152344, 0.14662271738052368, -0.29794785380363464, -0.6132585406303406, -0.20961952209472656, -0.1485241949558258, 0.23200586438179016, -1.9018620252609253, -0.3617386519908905, -0.7980623245239258, 0.6747579574584961, -0.07193520665168762, 1.2221097946166992, 1.1449333429336548, 0.05346214026212692, 0.15381675958633423, -0.915044367313385, 1.8990567922592163, 1.0234119892120361, -0.7070415616035461, -0.043990831822156906, 1.6007312536239624, 0.5373092889785767, 0.7245332598686218, 0.4228416979312897, -0.6947753429412842, -0.6186723113059998, 0.2786846458911896, -0.12222578376531601, 0.8746001720428467, -2.3076982498168945, -2.0995266437530518, 0.8784922957420349, 0.5805615186691284, -0.8021043539047241, -0.2905712425708771, -0.33400458097457886, -0.47248929738998413, 1.4334349632263184, 0.05169787257909775, -0.41870322823524475, -1.6671600341796875, 1.0111440420150757, -0.8357082009315491, 0.20043931901454926, 1.2436283826828003, 1.149665355682373, -0.12714563310146332, 2.238769769668579, -0.6511625051498413, -0.956494927406311, -0.4884316921234131, -1.0861036777496338, 1.31111478805542, 0.7444546818733215, 0.5675682425498962, -1.8370161056518555, -1.221316933631897, -1.3611990213394165, -0.6803463697433472, -2.033989191055298, 2.220614433288574, -1.4172934293746948, 1.0654534101486206, -0.00278504379093647, -2.7842628955841064, -0.4149181544780731, -1.409364938735962, -0.40107670426368713, -0.9451999068260193, -0.09522424638271332, 1.6173559427261353, 1.2051596641540527, 0.1481800675392151, 0.2193889319896698, 1.8110994100570679, -0.8815642595291138, -0.6115224361419678, 0.3864770233631134, 1.0564818382263184, 0.16293099522590637, -1.3167580366134644, 1.1091917753219604, -0.5234769582748413, -0.5735318660736084, -0.23751255869865417, 0.007024026475846767, -0.22489811480045319, 0.023442383855581284, 0.9884958267211914, -0.6433583498001099, -0.6042283177375793, 0.9615083336830139, 1.0578014850616455, -0.17719750106334686, -0.30261582136154175, 0.8587509989738464, 0.8182197213172913, -2.0445058345794678, 0.35521450638771057, -0.4861716330051422, 2.337289333343506, -0.4710140824317932, -0.7575010061264038, -1.7323278188705444, 0.45273369550704956, 0.9915226101875305, -1.4785284996032715, -1.1188310384750366, -0.3124200105667114, -0.8047114014625549, 1.7427171468734741, -0.6843227744102478, 0.6351736187934875, -0.6604052782058716, -0.7244318127632141, 0.3613373935222626, -0.8328818678855896, 0.2500467300415039, -0.11879577487707138, 0.9558670520782471, 1.5551631450653076, 0.9188369512557983, 1.0740723609924316, -0.14403808116912842, -0.5882576107978821, 0.033906176686286926, -1.217938780784607, -0.9777287840843201, 0.372550368309021, 1.7654181718826294, 0.7730445861816406, 0.5671777129173279, -1.3885550498962402, -0.39420270919799805, 1.8492140769958496, 2.0191895961761475, 0.1625206023454666, 0.2967553436756134, 1.201187252998352, -0.24465471506118774, 1.5371763706207275, -1.9429328441619873, -3.0506579875946045, 0.052427876740694046, -0.8242821097373962, -1.2167543172836304, -1.4087005853652954, -0.005105283111333847, 2.0281102657318115, -0.7612220048904419, 1.6394152641296387, -2.0297088623046875, -0.7085066437721252, 0.8979865908622742, -0.41585659980773926, 0.5721040368080139, 2.417332649230957, 0.7166981101036072, 0.40655088424682617, -1.0969243049621582, -0.8436115384101868, 2.0408809185028076, 0.0906417965888977, -0.46350374817848206, -0.44086840748786926, 1.2481426000595093, -0.13571926951408386, -1.1590032577514648, -0.5564945340156555, 0.09697505086660385, 0.5380165576934814, -1.0624074935913086, -1.196273922920227, 1.2199100255966187, 2.028714895248413, 0.5750004649162292, 1.0222352743148804, 1.261359691619873, -0.43997910618782043, -0.33900752663612366, 0.6163075566291809, 0.07439723610877991, -0.7406630516052246, 0.9090335369110107, 1.1509896516799927, -0.7455238103866577, -2.071136474609375, -0.4774610102176666, 2.060145139694214, -0.3248315453529358, 0.5655614137649536, 1.1428256034851074, 0.8971670269966125, 0.9685800075531006, -2.030268669128418, -0.3145672380924225, 0.75617516040802, 1.592185378074646, 0.5269389152526855, -0.3688100576400757, 0.6866894364356995, 0.17101068794727325, -1.497295618057251, -0.01782253012061119, 0.402021586894989, 0.690995991230011, 0.4192647635936737, -0.4680596590042114, -0.6390615701675415, 1.5186598300933838, -0.4059446156024933, -2.449995279312134, 0.7285377979278564, -1.3773034811019897, 0.10159379243850708, -1.7891149520874023, -1.8819056749343872, -0.6487541198730469, 1.7275784015655518, 1.442358136177063, 0.6834203600883484, -1.1512843370437622, -1.3510346412658691, -0.1522262692451477, -0.8965049982070923, -0.24061551690101624, -0.28983473777770996, -0.8904274106025696, 0.6400189399719238, -1.2326527833938599, -0.8093716502189636, -0.8742511868476868, -0.3018453121185303, -0.81643146276474, 2.504336357116699, 1.005629539489746, -2.284026861190796, -0.9456119537353516, -0.21474123001098633, -0.2230701595544815, 0.4666335880756378, 1.0272728204727173, 1.490006446838379, 0.9571896195411682, 1.3831026554107666, 2.536322593688965, -0.37887129187583923, 0.04995887726545334, -0.23482352495193481, -0.8386731147766113, 0.326482355594635, 1.1865907907485962, 0.5275903344154358, 0.1753845363855362, 2.879753589630127, -1.4842394590377808, 1.14559006690979, 0.6473908424377441, 0.9014424085617065, -0.40940529108047485, 0.06822048127651215, 0.09753615409135818, -2.006699323654175, -1.493412971496582, 0.8115985989570618, 0.4178417921066284, -0.6956997513771057, 1.3682197332382202, 0.13363930583000183, 1.0626585483551025, -0.5482444167137146, 1.170738697052002, -0.14655537903308868, -0.5113286375999451, -1.9636032581329346, 0.06197277456521988, 0.03859144449234009, -1.0914695262908936, 0.6943049430847168, 0.2106175273656845, -0.31783902645111084, -1.1730895042419434, 0.0005004992708563805, 0.4631365239620209, 0.6330061554908752, -0.87730473279953, 1.038644552230835, 1.1621991395950317, 0.33822834491729736, -0.01354843471199274, 0.331957072019577, 0.11798182129859924, 0.409259170293808, 0.5386164784431458, 0.22508172690868378, -0.898300051689148, -0.8507010340690613, 0.9387043118476868, 0.4439644515514374, 1.1234313249588013, 0.24402780830860138, -1.6337621212005615, -0.41420093178749084, 0.7687472105026245, 0.4862597584724426, -0.31501010060310364, 2.1693568229675293, -0.22324027121067047, 0.7537294030189514, 0.5389354825019836, 1.7957638502120972, 1.2530723810195923, 1.1406636238098145, -0.006015419028699398, 1.1267156600952148, 3.183854103088379, -1.4196994304656982, -1.5795849561691284, 2.13845157623291, -0.2074800282716751, 0.8836700320243835, -1.2958993911743164, 1.2107417583465576, -0.622880220413208, 0.4821105897426605, -2.295968770980835, -0.37556952238082886, -0.16821664571762085, 0.5817139744758606, -1.190666913986206, 0.7361713647842407, -0.6083030700683594, -1.99183988571167, -0.1992875039577484, -0.2943141460418701, -0.3124006986618042, -0.4777618944644928, -0.4310387670993805, -1.6722972393035889, 1.9592329263687134, 1.0605207681655884, 0.30473193526268005, 0.5573604702949524, -0.2916925251483917, 1.2737044095993042, -1.397781252861023, -1.5296807289123535, -0.7425923943519592, -0.12010855972766876, -0.5147261619567871, -2.1474475860595703, 0.2966206967830658, 0.6331596970558167, -1.128808617591858, -0.16013243794441223, 0.6422491073608398, 1.4586769342422485, -0.4479571282863617, -0.049273159354925156, -0.9442945122718811, -1.164221167564392, 1.3339601755142212, -0.08516941964626312, -0.4281732738018036, -1.2144848108291626, -1.263880968093872, 0.8123886585235596, -1.175217866897583, -1.4708985090255737, -0.8463122844696045, 0.5187057852745056, -0.5125414729118347, 0.5615410804748535, -0.9794544577598572, 0.5575408935546875, -1.8811453580856323, 1.4592347145080566, -1.4276669025421143, 0.7605273723602295, 1.36599600315094, -0.7080062627792358, 1.1543481349945068, 0.6380182504653931, -0.6292986273765564, -0.6817926168441772, -0.6700359582901001, -0.3434330224990845, -1.247218132019043, 1.2678911685943604, 1.8537896871566772, 0.4458634555339813, 0.8645411133766174, 0.3301413655281067, 0.5795009732246399, 0.5723889470100403, -1.9017152786254883, -0.3327717185020447, 0.6885658502578735, -0.09234966337680817, 0.7644262313842773, 1.0404572486877441, -0.25221937894821167, 0.0294216126203537, 0.46525371074676514, -2.160458564758301, 0.023312095552682877, 0.5125712752342224, 0.7168111205101013, 0.03866603225469589, -1.5733191967010498, -1.0955601930618286, 1.0390143394470215, 2.213589668273926, 0.981850266456604, 1.4266932010650635, 0.1691204011440277, 1.0065592527389526, -0.3066626191139221, 0.24532902240753174, 0.7767351865768433, -0.9258300065994263, -1.3858113288879395, -0.861993670463562, -1.269823670387268, 0.6360999345779419, 1.3831058740615845, -0.4337540864944458, -1.8011531829833984, 1.0900819301605225, -0.7512149214744568, -0.5615105628967285, 0.5759336352348328, 0.6335285305976868, -0.8934805393218994, -0.06514853239059448, 1.6142584085464478, -1.1739178895950317, -0.6656908392906189, 0.41429224610328674, -0.5210093855857849, -1.3764454126358032, 2.135681629180908, -0.03480564057826996]', '2025-11-24 13:18:15', NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (61, '', 'Rhyan Nascimento de Sousa', 'undefined@gmail.com', 'rhyannsdeutsch@gmail.com', '$2y$10$qcqMb8qQsZQBOA0.nQcpqufbQGVj/u.LSk72fLCvqq5.rdrwwp.5u', 'colaborador', 'ativo', NULL, 'e2829bdfbe58d3f3ff0f4b4f1328debdcab11247d76bf3473afca54a3fa18f98', '2025-10-22 14:05:47', '65476896412', NULL, NULL, '2025-10-31', '', '2025-10-22', 'inativo', 'CLT', 0, NULL, NULL, NULL, 1, '2025-10-22 11:50:18', NULL, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (63, '42345323687', 'Rhyan Nascimento de Sousa', 'rhyannsdeutsch@gmail.com', 'rhyannsdeutsch@gmail.com', '$2y$10$yD2Une30O84LIWNMqId72uCg6lpyUwQaRLN47TAzCvg6Yddd/xc9q', 'colaborador', 'pendente_verificacao', 'ff9e9d8429144cc089acbd57437a20cd86ac7ed75905c634b7b8020f457983e9', NULL, '2025-11-17 19:25:35', '42345323687', NULL, NULL, '2025-11-07', '', '2025-11-17', 'ativo', 'CLT', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          (64, 'C17598911342', 'teste', 'sdlfkjsldkf@gmail.com', 'dfgdsfgdf@gmail.com', '', 'colaborador', 'pendente_verificacao', NULL, NULL, NULL, '123.443.211-43', '3423124', 'masculino', '2025-11-06', '', '2025-11-24', 'ativo', 'PJ', 0, 29, 19, 39, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `colaborador_beneficio`
--

CREATE TABLE `colaborador_beneficio` (
                                         `id_colaborador` int(11) NOT NULL,
                                         `id_beneficio` int(11) NOT NULL,
                                         `valor_especifico` decimal(10,2) DEFAULT NULL COMMENT 'NULL = Usa valor do catálogo; Preenchido = Valor personalizado',
                                         `data_atribuicao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colaborador_beneficio`
--

INSERT INTO `colaborador_beneficio` (`id_colaborador`, `id_beneficio`, `valor_especifico`, `data_atribuicao`) VALUES
                                                                                                                  (46, 10, NULL, '2025-11-24 23:32:13'),
                                                                                                                  (64, 9, NULL, '2025-11-24 17:33:02'),
                                                                                                                  (64, 13, NULL, '2025-11-24 17:33:02');

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
-- Table structure for table `empresa_perfil`
--

CREATE TABLE `empresa_perfil` (
                                  `id_empresa` int(11) NOT NULL,
                                  `razao_social` varchar(255) NOT NULL,
                                  `cnpj` varchar(20) NOT NULL,
                                  `endereco` varchar(255) DEFAULT NULL,
                                  `cidade_uf` varchar(100) DEFAULT NULL,
                                  `padrao` tinyint(1) DEFAULT 0 COMMENT '1 = Empresa Principal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `empresa_perfil`
--

INSERT INTO `empresa_perfil` (`id_empresa`, `razao_social`, `cnpj`, `endereco`, `cidade_uf`, `padrao`) VALUES
    (1, 'Minha Empresa Padrão Ltda', '00.000.000/0001-00', 'Rua Exemplo, 123', 'São Paulo - SP', 1);

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
                                                                                                        (29, 'Quadra ARSE 131 Rua 4', '77024-664', '131', 'Plano Diretor Sul', 'Palmas', 'TO'),
                                                                                                        (39, 'Avenida Paraná', '69945-970', '3', 'Centro', 'Acrelândia', 'AC');

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
                                                                                                                                                              (2, 1, '2025-10-08 13:30:12', '2025-10-08 13:47:57', NULL, NULL, NULL),
                                                                                                                                                              (3, 1, '2025-10-20 09:00:00', '2025-10-20 17:00:00', NULL, NULL, NULL),
                                                                                                                                                              (4, 1, '2025-10-21 09:00:00', '2025-10-21 16:00:00', NULL, NULL, NULL),
                                                                                                                                                              (5, 1, '2025-10-22 08:30:00', '2025-10-22 17:00:00', NULL, NULL, NULL),
                                                                                                                                                              (6, 1, '2025-10-22 05:52:11', '2025-10-22 06:03:54', '-10.2627145,-48.3280588', 'storage/fotos_ponto/1_1761105834.jpg', '::1'),
                                                                                                                                                              (7, 61, '2025-10-22 06:50:32', '2025-10-22 06:50:44', '-10.262715,-48.3280585', 'storage/fotos_ponto/61_1761108644.jpg', '::1'),
                                                                                                                                                              (8, 1, '2025-10-22 13:37:12', '2025-10-22 13:49:22', '-10.1810176,-48.3622912', 'storage/fotos_ponto/1_1761133762.jpg', '::1'),
                                                                                                                                                              (9, 46, '2025-11-24 12:28:27', '2025-11-24 16:28:27', '-10.2624146,-48.3290049', 'caminho/para/foto/temp.jpg', '::1'),
                                                                                                                                                              (10, 46, '2025-11-24 12:28:34', '2025-11-24 16:28:34', '-10.2624146,-48.3290049', 'caminho/para/foto/temp.jpg', '::1');

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
                             `data_processamento` timestamp NOT NULL DEFAULT current_timestamp(),
                             `base_calculo_inss` decimal(10,2) NOT NULL DEFAULT 0.00,
                             `base_calculo_fgts` decimal(10,2) NOT NULL DEFAULT 0.00,
                             `valor_fgts` decimal(10,2) NOT NULL DEFAULT 0.00,
                             `base_calculo_irrf` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holerites`
--

INSERT INTO `holerites` (`id_holerite`, `id_colaborador`, `mes_referencia`, `ano_referencia`, `total_proventos`, `total_descontos`, `salario_liquido`, `data_processamento`, `base_calculo_inss`, `base_calculo_fgts`, `valor_fgts`, `base_calculo_irrf`) VALUES
                                                                                                                                                                                                                                                              (41, 13, 10, '2024', 0.00, 0.00, 0.00, '2025-10-20 04:38:14', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (42, 1, 10, '2024', 0.00, 0.00, 0.00, '2025-10-20 04:38:14', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (43, 12, 10, '2024', 0.00, 0.00, 0.00, '2025-10-20 04:38:14', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (44, 46, 10, '2024', 0.00, 0.00, 0.00, '2025-10-20 04:38:14', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (70, 12, 11, '2025', 0.00, 0.00, 0.00, '2025-10-22 01:42:10', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (137, 1, 9, '2025', 0.00, 0.00, 0.00, '2025-10-22 02:00:49', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (138, 12, 9, '2025', 0.00, 0.00, 0.00, '2025-10-22 02:00:49', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (139, 13, 9, '2025', 0.00, 0.00, 0.00, '2025-10-22 02:00:49', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (140, 46, 9, '2025', 0.00, 0.00, 0.00, '2025-10-22 02:00:49', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (191, 1, 10, '2025', 4000.00, 360.00, 3640.00, '2025-10-22 11:48:08', 4000.00, 4000.00, 320.00, 3640.00),
                                                                                                                                                                                                                                                              (192, 12, 10, '2025', 0.00, 0.00, 0.00, '2025-10-22 11:48:08', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (193, 13, 10, '2025', 10000.00, 900.00, 9100.00, '2025-10-22 11:48:08', 10000.00, 10000.00, 800.00, 9100.00),
                                                                                                                                                                                                                                                              (194, 46, 10, '2025', 10000.00, 900.00, 9100.00, '2025-10-22 11:48:08', 10000.00, 10000.00, 800.00, 9100.00),
                                                                                                                                                                                                                                                              (195, 61, 10, '2025', 0.00, 0.00, 0.00, '2025-10-22 11:48:08', 0.00, 0.00, 0.00, 0.00),
                                                                                                                                                                                                                                                              (240, 1, 11, '2025', 4000.00, 3269.09, 730.91, '2025-11-26 04:29:34', 4000.00, 4000.00, 320.00, 3640.00),
                                                                                                                                                                                                                                                              (241, 13, 11, '2025', 9999.00, 8171.91, 1827.09, '2025-11-26 04:29:34', 9999.00, 9999.00, 799.92, 9099.09),
                                                                                                                                                                                                                                                              (242, 46, 11, '2025', 9999.00, 7808.31, 2190.69, '2025-11-26 04:29:34', 9999.00, 9999.00, 799.92, 9099.09),
                                                                                                                                                                                                                                                              (243, 64, 11, '2025', 10000.00, 8172.73, 1827.27, '2025-11-26 04:29:34', 10000.00, 10000.00, 800.00, 9100.00);

-- --------------------------------------------------------

--
-- Table structure for table `holerite_itens`
--

CREATE TABLE `holerite_itens` (
                                  `id_item` int(11) NOT NULL,
                                  `id_holerite` int(11) NOT NULL,
                                  `descricao` varchar(100) NOT NULL COMMENT 'Ex: Salário Base, INSS, Horas Extras 50%',
                                  `tipo` enum('provento','desconto') NOT NULL,
                                  `valor` decimal(10,2) NOT NULL,
                                  `codigo_evento` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holerite_itens`
--

INSERT INTO `holerite_itens` (`id_item`, `id_holerite`, `descricao`, `tipo`, `valor`, `codigo_evento`) VALUES
                                                                                                           (1, 1, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (2, 1, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (3, 2, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (4, 2, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (5, 3, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (6, 3, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (7, 4, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (8, 4, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (9, 5, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (10, 5, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (11, 6, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (12, 6, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (13, 7, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (14, 7, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (15, 8, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (16, 8, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (17, 9, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (18, 9, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (19, 10, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (20, 10, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (21, 11, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (22, 11, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (23, 12, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (24, 12, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (25, 13, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (26, 13, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (27, 14, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (28, 14, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (29, 15, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (30, 15, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (31, 16, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (32, 16, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (33, 17, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (34, 17, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (35, 18, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (36, 18, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (37, 19, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (38, 19, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (39, 20, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (40, 20, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (41, 21, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (42, 21, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (43, 22, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (44, 22, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (45, 23, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (46, 23, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (47, 24, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (48, 24, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (49, 25, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (50, 25, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (51, 26, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (52, 26, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (53, 27, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (54, 27, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (55, 28, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (56, 28, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (57, 29, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (58, 29, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (59, 30, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (60, 30, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (61, 31, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (62, 31, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (63, 32, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (64, 32, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (65, 33, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (66, 33, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (67, 34, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (68, 34, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (69, 35, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (70, 35, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (71, 36, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (72, 36, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (73, 37, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (74, 37, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (75, 38, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (76, 38, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (77, 39, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (78, 39, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (79, 40, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (80, 40, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (81, 41, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (82, 41, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (83, 42, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (84, 42, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (85, 43, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (86, 43, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (87, 44, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (88, 44, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (89, 45, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (90, 45, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (91, 46, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (92, 46, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (93, 47, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (94, 47, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (95, 48, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (96, 48, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (97, 49, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (98, 49, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (99, 50, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (100, 50, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (101, 51, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (102, 51, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (103, 52, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (104, 52, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (105, 53, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (106, 53, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (107, 54, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (108, 54, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (109, 55, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (110, 55, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (111, 56, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (112, 56, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (113, 57, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (114, 57, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (115, 58, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (116, 58, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (117, 59, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (118, 59, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (119, 60, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (120, 60, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (121, 61, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (122, 61, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (123, 62, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (124, 62, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (125, 63, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (126, 63, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (127, 64, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (128, 64, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (129, 65, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (130, 65, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (131, 66, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (132, 66, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (133, 67, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (134, 67, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (135, 68, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (136, 68, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (137, 69, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (138, 69, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (139, 70, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (140, 70, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (141, 71, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (142, 71, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (143, 72, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (144, 72, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (145, 73, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (146, 73, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (147, 74, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (148, 74, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (149, 75, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (150, 75, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (151, 76, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (152, 76, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (153, 77, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (154, 77, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (155, 78, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (156, 78, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (157, 79, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (158, 79, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (159, 80, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (160, 80, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (161, 81, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (162, 81, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (163, 82, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (164, 82, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (165, 83, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (166, 83, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (167, 84, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (168, 84, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (169, 85, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (170, 85, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (171, 86, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (172, 86, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (173, 87, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (174, 87, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (175, 88, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (176, 88, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (177, 89, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (178, 89, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (179, 90, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (180, 90, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (181, 91, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (182, 91, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (183, 92, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (184, 92, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (185, 93, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (186, 93, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (187, 94, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (188, 94, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (189, 95, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (190, 95, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (191, 96, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (192, 96, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (193, 97, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (194, 97, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (195, 98, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (196, 98, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (197, 99, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (198, 99, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (199, 100, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (200, 100, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (201, 101, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (202, 101, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (203, 102, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (204, 102, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (205, 103, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (206, 103, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (207, 104, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (208, 104, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (209, 105, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (210, 105, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (211, 106, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (212, 106, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (213, 107, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (214, 107, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (215, 108, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (216, 108, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (217, 109, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (218, 109, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (219, 110, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (220, 110, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (221, 111, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (222, 111, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (223, 112, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (224, 112, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (225, 113, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (226, 113, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (227, 114, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (228, 114, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (229, 115, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (230, 115, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (231, 116, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (232, 116, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (233, 117, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (234, 117, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (235, 118, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (236, 118, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (237, 119, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (238, 119, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (239, 120, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (240, 120, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (241, 121, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (242, 121, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (243, 122, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (244, 122, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (245, 123, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (246, 123, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (247, 124, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (248, 124, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (249, 125, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (250, 125, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (251, 126, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (252, 126, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (253, 127, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (254, 127, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (255, 128, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (256, 128, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (257, 129, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (258, 129, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (259, 130, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (260, 130, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (261, 131, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (262, 131, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (263, 132, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (264, 132, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (265, 133, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (266, 133, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (267, 134, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (268, 134, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (269, 135, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (270, 135, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (271, 136, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (272, 136, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (273, 137, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (274, 137, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (275, 138, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (276, 138, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (277, 139, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (278, 139, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (279, 140, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (280, 140, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (281, 141, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (282, 141, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (283, 142, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (284, 142, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (285, 143, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (286, 143, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (287, 144, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (288, 144, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (289, 145, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (290, 145, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (291, 146, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (292, 146, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (293, 147, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (294, 147, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (295, 148, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (296, 148, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (297, 149, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (298, 149, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (299, 150, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (300, 150, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (301, 151, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (302, 151, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (303, 152, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (304, 152, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (305, 153, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (306, 153, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (307, 154, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (308, 154, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (309, 155, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (310, 155, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (311, 156, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (312, 156, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (313, 157, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (314, 157, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (315, 158, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (316, 158, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (317, 159, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (318, 159, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (319, 160, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (320, 160, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (321, 161, 'Salário Base', 'provento', 3500.00, '101'),
                                                                                                           (322, 161, 'Vale Refeição', 'provento', 500.00, '115'),
                                                                                                           (323, 161, 'INSS', 'desconto', 400.00, '501'),
                                                                                                           (324, 162, 'Salário Base', 'provento', 3500.00, NULL),
                                                                                                           (325, 162, 'Vale Refeição', 'provento', 500.00, NULL),
                                                                                                           (326, 162, 'INSS', 'desconto', 400.00, NULL),
                                                                                                           (327, 163, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (328, 163, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (329, 164, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (330, 164, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (331, 165, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (332, 165, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (333, 166, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (334, 166, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (335, 167, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (336, 167, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (337, 168, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (338, 168, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (339, 169, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (340, 169, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (341, 170, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (342, 170, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (343, 171, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (344, 171, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (345, 172, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (346, 172, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (347, 173, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (348, 173, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (349, 174, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (350, 174, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (351, 175, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (352, 175, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (353, 176, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (354, 176, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (355, 177, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (356, 177, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (357, 178, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (358, 178, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (359, 179, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (360, 179, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (361, 180, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (362, 180, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (363, 181, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (364, 181, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (365, 182, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (366, 182, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (367, 183, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (368, 183, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (369, 184, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (370, 184, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (371, 185, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (372, 185, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (373, 186, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (374, 186, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (375, 187, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (376, 187, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (377, 188, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (378, 188, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (379, 189, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (380, 189, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (381, 190, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (382, 190, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (383, 191, 'Salário Base', 'provento', 4000.00, '101'),
                                                                                                           (384, 191, 'INSS', 'desconto', 360.00, '501'),
                                                                                                           (385, 192, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (386, 192, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (387, 193, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (388, 193, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (389, 194, 'Salário Base', 'provento', 10000.00, '101'),
                                                                                                           (390, 194, 'INSS', 'desconto', 900.00, '501'),
                                                                                                           (391, 195, 'Salário Base', 'provento', 0.00, '101'),
                                                                                                           (392, 195, 'INSS', 'desconto', 0.00, '501'),
                                                                                                           (393, 196, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (394, 196, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (395, 196, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (396, 197, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (397, 197, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (398, 197, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (399, 198, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (400, 198, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (401, 198, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (402, 199, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (403, 199, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (404, 199, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (405, 200, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (406, 200, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (407, 200, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (408, 201, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (409, 201, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (410, 201, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (411, 202, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (412, 202, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (413, 202, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (414, 203, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (415, 203, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (416, 203, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (417, 204, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (418, 204, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (419, 204, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (420, 205, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (421, 205, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (422, 205, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (423, 206, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (424, 206, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (425, 206, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (426, 207, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (427, 207, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (428, 207, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (429, 208, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (430, 208, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (431, 208, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (432, 209, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (433, 209, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (434, 209, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (435, 210, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (436, 210, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (437, 210, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (438, 211, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (439, 211, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (440, 211, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (441, 212, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (442, 212, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (443, 212, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (444, 213, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (445, 213, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (446, 213, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (447, 214, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (448, 214, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (449, 214, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (450, 215, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (451, 215, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (452, 215, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (453, 216, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (454, 216, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (455, 216, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (456, 217, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (457, 217, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (458, 217, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (459, 218, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (460, 218, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (461, 218, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (462, 219, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (463, 219, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (464, 219, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (465, 220, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (466, 220, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (467, 220, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (468, 221, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (469, 221, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (470, 221, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (471, 222, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (472, 222, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (473, 222, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (474, 223, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (475, 223, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (476, 223, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (477, 224, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (478, 224, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (479, 224, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (480, 225, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (481, 225, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (482, 225, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (483, 226, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (484, 226, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (485, 226, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (486, 227, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (487, 227, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (488, 227, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (489, 228, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (490, 228, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (491, 228, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (492, 229, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (493, 229, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (494, 229, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (495, 230, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (496, 230, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (497, 230, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (498, 231, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (499, 231, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (500, 231, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (501, 232, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (502, 232, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (503, 232, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (504, 233, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (505, 233, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (506, 233, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 72727272.72, '205'),
                                                                                                           (507, 234, 'Salario Base', 'provento', 99999999.99, '101'),
                                                                                                           (508, 234, 'Desconto INSS', 'desconto', 9000000.00, '201'),
                                                                                                           (509, 234, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 69090909.08, '205'),
                                                                                                           (510, 235, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (511, 235, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (512, 235, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (513, 236, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (514, 236, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (515, 236, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (516, 237, 'Salario Base', 'provento', 9999.00, '101'),
                                                                                                           (517, 237, 'Desconto INSS', 'desconto', 899.91, '201'),
                                                                                                           (518, 237, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.00, '205'),
                                                                                                           (519, 238, 'Salario Base', 'provento', 9999.00, '101'),
                                                                                                           (520, 238, 'Desconto INSS', 'desconto', 899.91, '201'),
                                                                                                           (521, 238, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 6908.40, '205'),
                                                                                                           (522, 239, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (523, 239, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (524, 239, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205'),
                                                                                                           (525, 240, 'Salario Base', 'provento', 4000.00, '101'),
                                                                                                           (526, 240, 'Desconto INSS', 'desconto', 360.00, '201'),
                                                                                                           (527, 240, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 2909.09, '205'),
                                                                                                           (528, 241, 'Salario Base', 'provento', 9999.00, '101'),
                                                                                                           (529, 241, 'Desconto INSS', 'desconto', 899.91, '201'),
                                                                                                           (530, 241, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.00, '205'),
                                                                                                           (531, 242, 'Salario Base', 'provento', 9999.00, '101'),
                                                                                                           (532, 242, 'Desconto INSS', 'desconto', 899.91, '201'),
                                                                                                           (533, 242, 'Faltas e Atrasos (152,00 Horas)', 'desconto', 6908.40, '205'),
                                                                                                           (534, 243, 'Salario Base', 'provento', 10000.00, '101'),
                                                                                                           (535, 243, 'Desconto INSS', 'desconto', 900.00, '201'),
                                                                                                           (536, 243, 'Faltas e Atrasos (160,00 Horas)', 'desconto', 7272.73, '205');

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

--
-- Dumping data for table `parametros_folha`
--

INSERT INTO `parametros_folha` (`id_parametro`, `nome`, `valor`, `ano_vigencia`) VALUES
                                                                                     (1, 'INSS_FAIXA_1', '{\"aliquota\": 7.50, \"de\": 0.00, \"ate\": 1412.00, \"deduzir\": 0.00}', '2024'),
                                                                                     (2, 'INSS_FAIXA_2', '{\"aliquota\": 9.00, \"de\": 1412.01, \"ate\": 2666.68, \"deduzir\": 21.18}', '2024'),
                                                                                     (3, 'INSS_FAIXA_3', '{\"aliquota\": 12.00, \"de\": 2666.69, \"ate\": 4000.03, \"deduzir\": 101.18}', '2024'),
                                                                                     (4, 'INSS_FAIXA_4', '{\"aliquota\": 14.00, \"de\": 4000.04, \"ate\": 7786.02, \"deduzir\": 181.18}', '2024'),
                                                                                     (5, 'IRRF_FAIXA_1', '{\"aliquota\": 0.00, \"de\": 0.00, \"ate\": 2259.20, \"deduzir\": 0.00}', '2024'),
                                                                                     (6, 'IRRF_FAIXA_2', '{\"aliquota\": 7.50, \"de\": 2259.21, \"ate\": 2826.65, \"deduzir\": 169.44}', '2024'),
                                                                                     (7, 'IRRF_FAIXA_3', '{\"aliquota\": 15.00, \"de\": 2826.66, \"ate\": 3751.05, \"deduzir\": 381.44}', '2024'),
                                                                                     (8, 'IRRF_FAIXA_4', '{\"aliquota\": 22.50, \"de\": 3751.06, \"ate\": 4664.68, \"deduzir\": 662.77}', '2024'),
                                                                                     (9, 'IRRF_FAIXA_5', '{\"aliquota\": 27.50, \"de\": 4664.69, \"ate\": 999999.99, \"deduzir\": 896.00}', '2024');

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
                                                                                  (15, 'CLT', 6),
                                                                                  (16, 'CLT', 11),
                                                                                  (17, 'Estágio', 10);

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
                                                   (22, 'Business Intelligence (BI) e Analytics'),
                                                   (23, 'Tecnologia e Sistemas'),
                                                   (24, 'Atendimento Clínico e Cirúrgico'),
                                                   (25, 'asdf');

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
                                                                                                                                                                                                     (3, 'Arquiteto da Informação', 'design de interação, taxonomias', 'sistemas de organização de conteúdo, metadados', 'habilidades interpessoais (como comunicação clara, análise, resolução de problemas e colaboração com equipes de UX e desenvolvimento)', 'O seu trabalho visa criar uma experiência de usuário (UX) positiva através de sistemas de navegação claros, categorizações lógicas e hierarquias bem definidas, garantindo que os objetivos do negócio e do usuário sejam atendidos. ', 'aberta', 20, NULL, '2025-10-07 01:03:36'),
                                                                                                                                                                                                     (4, 'Desenvolvedor Full Stack Pleno – Projeto Orion', 'JavaScript/TypeScript, React.js, Node.js (Express ou NestJS), bancos de dados relacionais (PostgreSQL/MySQL), Git e GitHub/GitLab, arquitetura MVC e REST', 'Docker, CI/CD (GitHub Actions, GitLab CI, etc.), testes automatizados (Jest, Mocha, Cypress), GraphQL, Next.js, design de sistemas escaláveis e microserviços', 'Cloud (AWS, GCP ou Azure), DevOps e observabilidade (Prometheus, Grafana), projetos open source, Machine Learning aplicado a e-commerce', 'A equipe do Projeto Orion, iniciativa interna voltada à criação de soluções inteligentes para e-commerce, busca um Desenvolvedor Full Stack Pleno para atuar no desenvolvimento e manutenção de aplicações web escaláveis. O profissional participará de todo o ciclo de vida do produto, desde o planejamento até a implantação e monitoramento em produção, colaborando com times de UI/UX e Data Science.\r\n\r\n🛠️ Responsabilidades\r\n\r\nDesenvolver novas funcionalidades e manter aplicações existentes.\r\n\r\nIntegrar sistemas via APIs RESTful e GraphQL.\r\n\r\nEscrever código limpo, testável e bem documentado.\r\n\r\nParticipar de revisões de código e decisões arquiteturais.\r\n\r\nColaborar com designers e analistas de dados na melhoria contínua do produto.', 'aberta', 21, NULL, '2025-10-07 01:07:03'),
                                                                                                                                                                                                     (5, 'Cientista de Dados Júnior', 'Python,SQL para extração de dados,Pandas,NumPy,Matplotlib,Scikit-learn,Conhecimento em estatística fundamental,Lógica de programação,Capacidade de comunicação', 'Git para versionamento de código,Jupyter Notebooks,Tableau,Power BI,Noções de álgebra linear,Limpeza e pré-processamento de dados,Técnicas de machine learning supervisionado e não supervisionado', 'Computação em nuvem (AWS, Azure ou GCP),PySpark,TensorFlow ou PyTorch,Experiência com APIs REST,Docker,Familiaridade com metodologias ágeis', 'Estamos à procura de um Cientista de Dados Júnior curioso e motivado para se juntar à nossa equipe de Analytics. O candidato ideal terá uma base sólida em estatística, programação e um forte desejo de resolver problemas complexos através dos dados. Nesta posição, você colaborará com equipes multifuncionais para extrair, limpar e analisar grandes conjuntos de dados, desenvolvendo modelos de machine learning iniciais e criando visualizações que gerem insights valiosos para a tomada de decisões estratégicas. Esta é uma oportunidade fantástica para aprender com profissionais experientes e crescer na carreira de ciência de dados, aplicando seu conhecimento teórico em desafios reais do negócio.', 'aberta', 22, NULL, '2025-10-07 12:40:08'),
                                                                                                                                                                                                     (6, 'Engenheiro de software', 'gitflow, github, java, sql', 'Criatividade, Proatividade', '', 'desenvolver projetos e sistemas com php', 'aberta', 20, NULL, '2025-10-08 11:42:07'),
                                                                                                                                                                                                     (7, 'Analista de Desenvolvimento Web Pleno', 'Domínio de JavaScript (ES6+), Proficiência em React ou Vue.js, Experiência com Node.js e RESTful APIs, Conhecimento sólido em HTML5/CSS3 e pré-processadores (ex: SASS/LESS), Experiência com bancos de dados relacionais (ex: PostgreSQL ou MySQL), Controle de versão com Git', 'Experiência com TypeScript, Conhecimento em Testes (unitários/integração - ex: Jest, Cypress), Familiaridade com ambientes Cloud (ex: AWS, Google Cloud ou Azure), Experiência em desenvolvimento e consumo de Microsserviços', 'Conhecimento de metodologias DevOps e ferramentas de CI/CD (ex: Docker, Jenkins), Experiência em otimização de performance de aplicações (Web Vitals), Certificações relevantes na área', 'Estamos em busca de um(a) Analista de Desenvolvimento Web Pleno para integrar nossa equipe de Engenharia de Software. O profissional será responsável pelo desenvolvimento, manutenção e otimização de sistemas e aplicações web, garantindo a performance, segurança e usabilidade.\r\nAs responsabilidades incluem:\r\n\r\nDesenvolver novas funcionalidades e APIs (Application Programming Interfaces) utilizando práticas de código limpo e metodologias ágeis.\r\n\r\nColaborar com as equipes de Produto e Design (UX/UI) para traduzir requisitos de negócio em soluções técnicas eficientes.\r\n\r\nRealizar testes unitários e de integração, além de participar ativamente das revisões de código (code reviews).\r\n\r\nMonitorar e corrigir bugs e falhas em produção.\r\n\r\nContribuir para a melhoria contínua da arquitetura e das ferramentas de desenvolvimento.', 'aberta', 23, NULL, '2025-10-21 22:56:38'),
                                                                                                                                                                                                     (8, 'Médico Veterinário (Clínica Geral e Cirurgia de Pequenos Animais)', 'Formação em Medicina Veterinária e registro ativo no CRMV, Experiência comprovada em Clínica e Cirurgia de Pequenos Animais, Conhecimento sólido em Farmacologia Veterinária, Habilidade em Diagnóstico por Imagem (interpretação básica de raio-x e ultrassom), Ética profissional e empatia no atendimento aos tutores.', 'Experiência em Anestesiologia e monitoramento intensivo, Habilidade em Odontologia Veterinária, Conhecimento e prática em Medicina de Felinos, Capacidade de lidar com situações de emergência e tomar decisões rápidas.', 'Pós-graduação ou especialização em áreas clínicas ou cirúrgicas, Fluência em Inglês técnico (para leitura de artigos científicos), Experiência com softwares de gestão de clínicas veterinárias, Experiência em Atendimento Silencioso/Low Stress.', 'Procuramos um Médico Veterinário dedicado e experiente para se juntar à nossa equipe. O profissional será responsável pela prestação de cuidados médicos de alta qualidade a pequenos animais (cães e gatos), abrangendo desde consultas de rotina e preventivas até procedimentos de emergência, diagnósticos e cirurgias.\r\n\r\nAs responsabilidades incluem:\r\n\r\nRealizar consultas clínicas gerais, exames físicos e coletas de materiais para exames laboratoriais.\r\n\r\nInterpretar exames de imagem e laboratoriais para formular diagnósticos precisos.\r\n\r\nExecutar procedimentos cirúrgicos de rotina (castrações, limpeza de tártaro) e emergenciais, garantindo a segurança anestésica.\r\n\r\nGerenciar casos de internação, acompanhando a evolução dos pacientes e ajustando planos de tratamento.\r\n\r\nOrientar tutores sobre cuidados preventivos, vacinação, nutrição e bem-estar animal.\r\n\r\nManter registros clínicos detalhados e organizados.', 'aberta', 24, NULL, '2025-10-22 05:00:58'),
                                                                                                                                                                                                     (9, 'asdf', 'asdf', 'asdf', 'asdf', 'asdf', 'aberta', 25, NULL, '2025-11-24 14:04:38');

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
-- Indexes for table `empresa_perfil`
--
ALTER TABLE `empresa_perfil`
    ADD PRIMARY KEY (`id_empresa`);

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
    MODIFY `id_beneficio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `candidato`
--
ALTER TABLE `candidato`
    MODIFY `id_candidato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `candidaturas`
--
ALTER TABLE `candidaturas`
    MODIFY `id_candidatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cargo`
--
ALTER TABLE `cargo`
    MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `colaborador`
--
ALTER TABLE `colaborador`
    MODIFY `id_colaborador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

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
-- AUTO_INCREMENT for table `empresa_perfil`
--
ALTER TABLE `empresa_perfil`
    MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `endereco`
--
ALTER TABLE `endereco`
    MODIFY `id_endereco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `ferias`
--
ALTER TABLE `ferias`
    MODIFY `id_ferias` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `folha_ponto`
--
ALTER TABLE `folha_ponto`
    MODIFY `id_registro_ponto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `holerites`
--
ALTER TABLE `holerites`
    MODIFY `id_holerite` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `holerite_itens`
--
ALTER TABLE `holerite_itens`
    MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=537;

--
-- AUTO_INCREMENT for table `parametros_folha`
--
ALTER TABLE `parametros_folha`
    MODIFY `id_parametro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `regras_beneficios`
--
ALTER TABLE `regras_beneficios`
    MODIFY `id_regra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `setor`
--
ALTER TABLE `setor`
    MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `vaga`
--
ALTER TABLE `vaga`
    MODIFY `id_vaga` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
    ADD CONSTRAINT `fk_cb_beneficio` FOREIGN KEY (`id_beneficio`) REFERENCES `beneficios_catalogo` (`id_beneficio`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_cb_colaborador` FOREIGN KEY (`id_colaborador`) REFERENCES `colaborador` (`id_colaborador`) ON DELETE CASCADE ON UPDATE CASCADE;

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
