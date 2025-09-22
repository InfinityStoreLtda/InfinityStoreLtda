-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 22/09/2025 às 01:32
-- Versão do servidor: 11.8.3-MariaDB-log
-- Versão do PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `u824192816_financeiro_inf`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `tipo`, `descricao`, `status`) VALUES
(5, 'Seviços de Tic', 'receita', NULL, 'ativo'),
(6, 'DESPESAS COM SERVIÇOS DE INTERNET', 'despesa', NULL, 'ativo'),
(8, 'TESTES DE SISTEMAS', 'despesa', NULL, 'ativo'),
(22, 'Infraestrutura de TI', 'receita', NULL, 'ativo'),
(23, 'Suporte Técnico', 'receita', NULL, 'ativo'),
(24, 'Segurança da Informação', 'receita', NULL, 'ativo'),
(25, 'Soluções em Telefonia e Comunicação', 'receita', NULL, 'ativo'),
(26, 'Desenvolvimento de Sistemas', 'receita', NULL, 'ativo'),
(27, 'Consultoria e Assessoria', 'receita', NULL, 'ativo'),
(28, 'Segurança Eletrônica', 'receita', NULL, 'ativo'),
(29, 'Serviços de Internet', 'receita', NULL, 'ativo'),
(30, 'Tecnologia da Informação', 'receita', NULL, 'ativo'),
(31, 'Hospedagem de Sistema', 'receita', NULL, 'ativo'),
(32, 'Firewall e Segurança de Rede', 'receita', NULL, 'ativo'),
(33, 'Serviços Oficiais para Prefeituras', 'receita', NULL, 'ativo'),
(34, 'Serviços de E-mail', 'receita', NULL, 'ativo'),
(35, 'Internet e Conectividade', 'receita', NULL, 'ativo'),
(36, 'Hospedagem de Sistemas', 'receita', NULL, 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `centros_custo`
--

CREATE TABLE `centros_custo` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `nome` varchar(180) NOT NULL,
  `descricao` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(50) NOT NULL,
  `valor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`) VALUES
(1, 'logo_path', '/in/assets/logo.png'),
(2, 'logo_path', '/in/assets/logo.png'),
(3, 'logo_path', '/in/assets/logo.png');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos`
--

CREATE TABLE `contratos` (
  `id` int(11) NOT NULL,
  `empresa_pagadora_id` int(11) NOT NULL,
  `numero` varchar(60) DEFAULT NULL,
  `processo` varchar(60) DEFAULT NULL,
  `empenho` varchar(60) DEFAULT NULL,
  `vigencia_inicio` date NOT NULL,
  `vigencia_fim` date DEFAULT NULL,
  `centro_custo_id` int(11) DEFAULT NULL,
  `valor_mensal` decimal(12,2) DEFAULT NULL,
  `reajuste_anual` tinyint(1) DEFAULT 1,
  `status` enum('Vigente','Suspenso','Encerrado') DEFAULT 'Vigente',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos_itens`
--

CREATE TABLE `contratos_itens` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `unidade` varchar(30) DEFAULT 'un',
  `qtd` decimal(10,2) DEFAULT 1.00,
  `valor_unit` decimal(12,2) NOT NULL,
  `ativo_glpi_id` int(11) DEFAULT NULL,
  `zabbix_hostid` int(11) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas_pagadoras`
--

CREATE TABLE `empresas_pagadoras` (
  `id` int(11) NOT NULL,
  `nome_empresa` varchar(100) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `empresas_pagadoras`
--

INSERT INTO `empresas_pagadoras` (`id`, `nome_empresa`, `cnpj`, `endereco`, `contato`, `status`) VALUES
(1, 'INSTITUTO DE PREVIDENCIA SOCIAL DOS SERVIDORES PUBLICOS MUNICIPAIS DE VALE DO ANARI', '05.972.519/0001-55', 'RUA MANAUS, 2460, CENTRO, VALE DO ANARI - RO', '(69) 3525-1450', 'ativo'),
(11, 'PREFEITURA MUNICIPAL DE PARECIS - RO', '84.745.363/0001-46', NULL, NULL, 'ativo'),
(12, 'CONSELHO REGIONAL DE ODONTOLOGIA - CRO-SE', '13083431000100', 'VILA CRISTINA, 589 | CENTRO | ARACAJU - SE | 49015-000', NULL, 'ativo'),
(13, 'CONSELHO REGIONAL DE FISIOTERAPIA E TERAPIA OCUPACIONAL - CREFITO-13', '13593943000117', 'ANTONIO MARIA COELHO, 1400 | CENTRO | CAMPO GRANDE - MS | 79002-221', '(67) 33214-558 / (67) 81700-004', 'ativo'),
(17, 'UNI TELECOM', '49271108000108', 'Av. Irianeópolis, 114, Manaus - AM, 69097-758', '(92) 2020-1818', 'ativo'),
(18, 'MUNICIPIO DE NOVA PALMA', '88488358000156', 'DOM ERICO FERRARI, 145 | CENTRO | NOVA PALMA - RS | 97250-000 | CASA', '(05) 52661-188', 'ativo'),
(19, 'MUNICIPIO DE URANDI', '13982632000140', 'SEBASTIAO ALVES SANTANA, 57 | CENTRO | URANDI - BA | 46350-000 | SALA CENTRO ADMINISTRATIV', '(77) 34562-127 / (77) 34562-505', 'ativo'),
(20, 'CAMARA MUNICIPAL DE VEREADORES DE NOVA GUARITA', '01909326000107', 'DOS IMIGRANTES, S/N | CENTRO | NOVA GUARITA - MT | 78508-000', '(06) 55741-166', 'ativo'),
(21, 'CONSORCIO INTERMUNICIPAL DE DESENVOLVIMENTO SUSTENTAVEL DA REGIAO DOS CAMPOS DE CIMA DA SERRA - COND', '04712762000171', 'MADRE JOANA VITORIA FAVRE, 930 | PARQUE DOS RODEIOS | VACARIA - RS | 95201-227', '(54) 32314-219', 'ativo'),
(22, 'MUNICIPIO DE CORONEL DOMINGOS SOARES', '01614415000118', 'ARAUCARIA, 3120 | CENTRO | CORONEL DOMINGOS SOARES - PR | 85557-000 | PREFEITURA', '(46) 32541-166 / (46) 98125-356', 'ativo'),
(23, 'MUNICIPIO DE ROLIM DE MOURA', '04394805000118', 'JOAO PESSOA, 4478 | CENTRO | ROLIM DE MOURA - RO | 76940-000', '(69) 84315-779 / (69) 34423-100', 'ativo'),
(24, 'MUNICIPIO DE VALE DO ANARI', '84722917000190', 'CAPITAO SILVIO DE FARIAS, 4571 | CENTRO | VALE DO ANARI - RO | 76867-000 | TERREO', '(69) 35251-055 / (69) 35251-057', 'ativo'),
(25, 'LUZ CAMARA MUNICIPAL', '20921664000109', 'DEZ DE ABRIL, 721 | CENTRO | LUZ - MG | 35595-000', '(37) 34213-089', 'ativo'),
(26, 'MUNICIPIO DE CACHOEIRA DO SUL', '87530978000143', 'QUINZE DO NOVEMBRO, 364 | CENTRO | CACHOEIRA DO SUL - RS | 96508-750', '(51) 37246-083', 'ativo'),
(27, 'MUNICIPIO DE CARAMBEI', '01613765000160', 'DO OURO, 1355 | JD EUROPA | CARAMBEI - PR | 84145-000', '(42) 32318-350', 'ativo'),
(28, 'MUNICIPIO DE IMBAU', '01613770000172', 'FRANCISCO SIQUEIRA KORTZ, 471 | SAO CRISTOVAO | IMBAU - PR | 84250-000', '(42) 32788-100 / (42) 32788-116', 'ativo'),
(29, 'PARA MINISTERIO PUBLICO', '05054960000158', 'JOAO DIOGO, 100 | CENTRO | BELEM - PA | 66015-160', NULL, 'ativo'),
(30, 'MUNICIPIO DE MIRANTE DA SERRA', '63787071000104', 'DOM PEDRO I, 2389 | CENTRO | MIRANTE DA SERRA - RO | 76926-000', '(69) 34632-143 / (69) 34632-248', 'ativo'),
(31, 'FUNDACAO NACIONAL DOS POVOS INDIGENAS - FUNAI', '00059311001874', 'DOS MAMOEIROS, 25 | ITAPERAPUAN - SEDE | PORTO SEGURO - BA | 45810-000', '(61) 32476-717 / (61) 32476-580', 'ativo'),
(32, 'MUNICIPIO DE PRESIDENTE PRUDENTE', '55356653000108', 'CORONEL JOSE SOARES MARCONDES, 1200 | CENTRO | PRESIDENTE PRUDENTE - SP | 19010-081', '(18) 39024-400 / (18) 39024-445', 'ativo'),
(33, 'MUNICIPIO DE TEIXEIROPOLIS', '84722933000182', 'AFONSO PENA, 2280 | CENTRO | TEIXEIROPOLIS - RO | 78954-000', '694218392', 'ativo'),
(34, 'CAMARA DE VEREADORES DO MUNICIPIO DE ARIQUEMES', '04797247000131', 'CASSITERITA, 1369 | SETOR INSTITUCIONAL | ARIQUEMES - RO | 76872-874', '(69) 35353-261', 'ativo'),
(35, 'MUNICIPIO DE CONQUISTA', '18428888000123', 'CORONEL TANCREDO FRANCA, 181 | CENTRO | CONQUISTA - MG | 38195-000', NULL, 'ativo'),
(36, 'FUNDACAO DE SAUDE DE LAURO MULLER', '27611852000171', 'PADRE HERCILIO CAPELLER, SN | CAIRU | LAURO MULLER - SC | 88880-000 | EDIF HOSPITAL', '(48) 34643-222 / (48) 34643-136', 'ativo'),
(37, 'MUNICIPIO DE MONTE NEGRO', '63761985000198', 'JUSCELINO KUBITSCHEK, 2272 | SETOR 02 | MONTE NEGRO - RO | 76888-000', '(69) 99446-030', 'ativo'),
(38, 'CAMARA MUNICIPAL DE CORREIA PINTO', '75438689000130', 'DUQUE DE CAXIAS, S/N | CENTRO | CORREIA PINTO - SC | 88535-000', NULL, 'ativo'),
(39, 'MUNICIPIO DE ECOPORANGA', '27167311000104', 'SUELON DIAS DE MENDONCA, 20 | CENTRO | ECOPORANGA - ES | 29850-000', '(27) 37552-900', 'ativo'),
(40, 'CAMARA MUNICIPAL DE SERINGUEIRAS', '84580224000100', 'CAPITAO SILVIO, SN | CRISTO REI | SERINGUEIRAS - RO | 76934-000', '(69) 84954-562', 'ativo'),
(41, 'MUNICIPIO DE MATA DE SAO JOAO', '13805528000180', 'LUIZ ANTONIO GARCEZ, S/N | CENTRO | MATA DE SAO JOAO - BA | 48280-000', '(71) 96094-471', 'ativo'),
(42, 'MUNICIPIO DE PARECIS', '84745363000146', 'JAIR DIAS, 150 | CENTRO | PARECIS - RO | 76979-000', '(69) 34471-051 / (69) 34471-129', 'ativo'),
(43, 'CAIXA DE ASSISTENCIA AO SERVIDOR PUBLICO MUNICIPAL DE SANTOS - CAPEP-SAUDE', '58197948000169', 'GENERAL FRANCISCO GLICERIO, 479 | JOSE MENINO | SANTOS - SP | 11065-403 | CASA RUA CEARA N 11', '(13) 32055-020 / (13) 32055-026', 'ativo'),
(44, 'MUNICIPIO DE ASTORGA', '75743377000130', 'DR JOSE SOARES AZEVEDO, 48 | CENTRO | ASTORGA - PR | 86730-000', NULL, 'ativo'),
(45, 'INSTITUTO FEDERAL DE EDUCACAO,CIENCIA E TECNOLOGIA DO PARA', '10763998000563', 'PORTO COLOMBO, 12 | VILA PERMANENTE | TUCURUI - PA | 68455-695 | TERREO', '(94) 37781-029 / (94) 37783-131', 'ativo'),
(46, 'MUNICIPIO DE COSTA MARQUES', '04100020000195', 'CHIANCA, S/N | CENTRO | COSTA MARQUES - RO | 76937-000', NULL, 'ativo'),
(47, 'FUNDACAO NACIONAL DOS POVOS INDIGENAS - FUNAI', '00059311007562', 'FLORIANO PEIXOTO, 234 | CENTRO | CRUZEIRO DO SUL - AC | 69980-000', '(68) 33226-666', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresas_pagaveis`
--

CREATE TABLE `empresas_pagaveis` (
  `id` int(11) NOT NULL,
  `nome_empresa` varchar(100) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `contato` varchar(100) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `empresas_pagaveis`
--

INSERT INTO `empresas_pagaveis` (`id`, `nome_empresa`, `cnpj`, `endereco`, `contato`, `status`) VALUES
(2, 'UNI TELECOM LTDA', '49271108000108', 'RUA MANOEL FRANCO, 809, NOVA BRASILIA, JI-PARANA - RO', 'adm@souuni.com', 'ativo'),
(6, 'HOUSE TECNOLOGIA DA INFORMACAO LTDA', '18941423000171', 'CARD ARCOVERDE, 1749 | PINHEIROS | SAO PAULO - SP | 05407-002 | CONJ 21 BLOCO A', '(11) 43058-915', 'ativo'),
(7, 'ONE TELECOM TELECOMUNICACOES LTDA', '12488125000191', 'SALDANHA MARINHO, 383 | PATRIA NOVA | NOVO HAMBURGO - RS | 93320-060', '(51) 36000-001', 'ativo'),
(8, 'MICROTELL SCM LTDA', '22457970000153', 'FRANCISCO GAETANI, 960 | MAJOR PRATES | MONTES CLAROS - MG | 39403-202 | LETRA B', '(38) 32219-004', 'ativo'),
(9, 'POLUX NET SERVICOS ELETRICOS LTDA', '11090539000103', 'RS 149, SN | SAO JOSE ZONA RURAL | RESTINGA SECA - RS | 97200-000', '(51) 35611-844 / (51) 81870-061', 'ativo'),
(10, 'DBUG TELECOM LTDA', '09385611000170', 'FELIPE JUSTUS, 410 | BOA VISTA | PONTA GROSSA - PR | 84070-480 | SALA E', '(41) 96048-504 / (42) 84037-479', 'ativo'),
(11, 'UNIFIQUE TELECOMUNICACOES S/A', '02255187005088', 'ALEXANDRE PEDRON, 1460 | APARECIDA | FLORES DA CUNHA - RS | 95270-000 | SALA 03', '(47) 33800-800', 'ativo'),
(12, 'ROTA SUL TELECOM LTDA', '10284020000195', 'VER. ANTONIO FRANCISCO CORREIA DA SILVA, 2125 | CENTRO | CORONEL DOMINGOS SOARES - PR | 85557-000 | SALA 01', '(36) 32141-030', 'ativo'),
(13, 'ULTRA TELECOM LTDA', '55156421000106', 'RAUL POMPEIA, 1774 | CIDADE INDUSTRIAL | CURITIBA - PR | 81250-320 | LOJA 01', '(41) 84240-202', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `faturas`
--

CREATE TABLE `faturas` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `competencia` date NOT NULL,
  `valor_total` decimal(12,2) NOT NULL,
  `status` enum('Pendente','Emitida','Cancelada','Paga') DEFAULT 'Pendente',
  `nf_tipo` enum('NFE','NFSE') DEFAULT NULL,
  `nf_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas_fiscais`
--

CREATE TABLE `notas_fiscais` (
  `id` int(11) NOT NULL,
  `tipo` enum('NFE','NFSE') NOT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `serie` varchar(5) DEFAULT NULL,
  `chave` varchar(60) DEFAULT NULL,
  `protocolo` varchar(60) DEFAULT NULL,
  `data_emissao` datetime DEFAULT NULL,
  `status` enum('Autorizada','Rejeitada','Cancelada','Pendente') DEFAULT 'Pendente',
  `xml_path` varchar(255) DEFAULT NULL,
  `pdf_path` varchar(255) DEFAULT NULL,
  `retorno` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `slas_contrato`
--

CREATE TABLE `slas_contrato` (
  `id` int(11) NOT NULL,
  `contrato_id` int(11) NOT NULL,
  `competencia` date NOT NULL,
  `disponibilidade` decimal(5,2) DEFAULT NULL,
  `observacao` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `tipo` enum('receita','despesa') NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data` date NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `empresa_pagadora_id` int(11) DEFAULT NULL,
  `contrato_id` int(11) DEFAULT NULL,
  `centro_custo_id` int(11) DEFAULT NULL,
  `status_pagamento` enum('pendente','pago') DEFAULT 'pendente',
  `parcela_numero` int(11) DEFAULT 1,
  `parcela_total` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `transacoes`
--

INSERT INTO `transacoes` (`id`, `tipo`, `descricao`, `valor`, `data`, `usuario_id`, `categoria_id`, `empresa_id`, `empresa_pagadora_id`, `contrato_id`, `centro_custo_id`, `status_pagamento`, `parcela_numero`, `parcela_total`) VALUES
(1133, 'despesa', 'DESPESAS COM SERVIÇOS DE INTERNET - Boleto pago ', 147.30, '2025-09-19', 3, 6, 13, NULL, NULL, NULL, 'pago', 1, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome_completo` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `cargo` varchar(50) DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `nome_completo`, `email`, `cpf`, `data_nascimento`, `cargo`, `status`) VALUES
(1, 'mailon', '123456', 'Mailon Roger Sátimo', 'mailon@infinitystore-ro.com', '01767582242', '1992-10-01', 'Gestor de Tecnologia', 'ativo'),
(3, 'dafne', '123456', 'Dafne', 'dafne@infinitystore-ro.com', '01767582242', '1992-10-01', 'N1', 'ativo'),
(5, 'matheus', '123456', 'Matheus Garcia Klug', 'matheus@infinitystore-ro.com', '01767582242', '1992-10-01', 'N1', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_contratos_vigentes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_contratos_vigentes` (
`contrato_id` int(11)
,`cliente` varchar(100)
,`numero` varchar(60)
,`processo` varchar(60)
,`empenho` varchar(60)
,`vigencia_inicio` date
,`vigencia_fim` date
,`valor_mensal` decimal(12,2)
,`status` enum('Vigente','Suspenso','Encerrado')
,`centro_custo` varchar(180)
);

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_custos_por_cliente_mes`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_custos_por_cliente_mes` (
`ano` int(5)
,`mes` int(3)
,`empresa_pagadora_id` int(11)
,`cliente` varchar(100)
,`centro_custo_id` int(11)
,`centro_custo` varchar(180)
,`total_receita` decimal(32,2)
,`total_despesa` decimal(32,2)
);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `centros_custo`
--
ALTER TABLE `centros_custo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contratos_emp_pag` (`empresa_pagadora_id`),
  ADD KEY `fk_contratos_cc` (`centro_custo_id`);

--
-- Índices de tabela `contratos_itens`
--
ALTER TABLE `contratos_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_itens_contrato` (`contrato_id`);

--
-- Índices de tabela `empresas_pagadoras`
--
ALTER TABLE `empresas_pagadoras`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `empresas_pagaveis`
--
ALTER TABLE `empresas_pagaveis`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `faturas`
--
ALTER TABLE `faturas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_faturas_contrato_competencia` (`contrato_id`,`competencia`),
  ADD KEY `fk_faturas_nf` (`nf_id`);

--
-- Índices de tabela `notas_fiscais`
--
ALTER TABLE `notas_fiscais`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `slas_contrato`
--
ALTER TABLE `slas_contrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sla_contrato_competencia` (`contrato_id`,`competencia`);

--
-- Índices de tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_empresa_pagavel` (`empresa_id`),
  ADD KEY `idx_tx_emp_pag` (`empresa_pagadora_id`),
  ADD KEY `idx_tx_contrato` (`contrato_id`),
  ADD KEY `idx_tx_cc` (`centro_custo_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `centros_custo`
--
ALTER TABLE `centros_custo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contratos_itens`
--
ALTER TABLE `contratos_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresas_pagadoras`
--
ALTER TABLE `empresas_pagadoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT de tabela `empresas_pagaveis`
--
ALTER TABLE `empresas_pagaveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `faturas`
--
ALTER TABLE `faturas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notas_fiscais`
--
ALTER TABLE `notas_fiscais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `slas_contrato`
--
ALTER TABLE `slas_contrato`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1134;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_contratos_vigentes`
--
DROP TABLE IF EXISTS `vw_contratos_vigentes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u824192816_financeiro_inf`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_contratos_vigentes`  AS SELECT `c`.`id` AS `contrato_id`, `ep`.`nome_empresa` AS `cliente`, `c`.`numero` AS `numero`, `c`.`processo` AS `processo`, `c`.`empenho` AS `empenho`, `c`.`vigencia_inicio` AS `vigencia_inicio`, `c`.`vigencia_fim` AS `vigencia_fim`, `c`.`valor_mensal` AS `valor_mensal`, `c`.`status` AS `status`, `cc`.`nome` AS `centro_custo` FROM ((`contratos` `c` join `empresas_pagadoras` `ep` on(`ep`.`id` = `c`.`empresa_pagadora_id`)) left join `centros_custo` `cc` on(`cc`.`id` = `c`.`centro_custo_id`)) WHERE `c`.`status` = 'Vigente' AND (`c`.`vigencia_fim` is null OR `c`.`vigencia_fim` >= curdate()) ORDER BY `ep`.`nome_empresa` ASC, `c`.`vigencia_inicio` ASC ;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_custos_por_cliente_mes`
--
DROP TABLE IF EXISTS `vw_custos_por_cliente_mes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u824192816_financeiro_inf`@`127.0.0.1` SQL SECURITY DEFINER VIEW `vw_custos_por_cliente_mes`  AS SELECT year(`t`.`data`) AS `ano`, month(`t`.`data`) AS `mes`, `ep`.`id` AS `empresa_pagadora_id`, `ep`.`nome_empresa` AS `cliente`, `cc`.`id` AS `centro_custo_id`, `cc`.`nome` AS `centro_custo`, sum(case when `t`.`tipo` = 'receita' then `t`.`valor` else 0 end) AS `total_receita`, sum(case when `t`.`tipo` = 'despesa' then `t`.`valor` else 0 end) AS `total_despesa` FROM ((`transacoes` `t` left join `empresas_pagadoras` `ep` on(`ep`.`id` = `t`.`empresa_pagadora_id`)) left join `centros_custo` `cc` on(`cc`.`id` = `t`.`centro_custo_id`)) GROUP BY year(`t`.`data`), month(`t`.`data`), `ep`.`id`, `cc`.`id` ;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `fk_contratos_cc` FOREIGN KEY (`centro_custo_id`) REFERENCES `centros_custo` (`id`),
  ADD CONSTRAINT `fk_contratos_emp_pag` FOREIGN KEY (`empresa_pagadora_id`) REFERENCES `empresas_pagadoras` (`id`);

--
-- Restrições para tabelas `contratos_itens`
--
ALTER TABLE `contratos_itens`
  ADD CONSTRAINT `fk_itens_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`);

--
-- Restrições para tabelas `faturas`
--
ALTER TABLE `faturas`
  ADD CONSTRAINT `fk_faturas_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`),
  ADD CONSTRAINT `fk_faturas_nf` FOREIGN KEY (`nf_id`) REFERENCES `notas_fiscais` (`id`);

--
-- Restrições para tabelas `slas_contrato`
--
ALTER TABLE `slas_contrato`
  ADD CONSTRAINT `fk_sla_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`);

--
-- Restrições para tabelas `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `fk_tx_cc` FOREIGN KEY (`centro_custo_id`) REFERENCES `centros_custo` (`id`),
  ADD CONSTRAINT `fk_tx_contrato` FOREIGN KEY (`contrato_id`) REFERENCES `contratos` (`id`),
  ADD CONSTRAINT `fk_tx_emp_pag` FOREIGN KEY (`empresa_pagadora_id`) REFERENCES `empresas_pagadoras` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
