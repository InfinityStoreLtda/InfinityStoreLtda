-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 16/09/2025 às 01:17
-- Versão do servidor: 10.11.10-MariaDB-log
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
(5, 'SERVIÇOS DE INSTALAÇÃO E ATIVAÇÃO DE LINK DE INTERNET CABEADA ', 'receita', NULL, 'ativo'),
(6, 'DESPESAS COM SERVIÇOS DE INTERNET', 'despesa', NULL, 'ativo'),
(8, 'TESTES DE SISTEMAS', 'despesa', NULL, 'ativo');

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
(1, 'INSTITUTO DE PREVIDENCIA SOCIAL DOS SERVIDORES PUBLICOS MUNICIPAIS DE VALE DO ANARI', '05.972.519/0001-55', 'RUA MANAUS, 2460, CENTRO, VALE DO ANARI - RO', '(69) 3525-1450', 'ativo');

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
(2, 'UNI TELECOM LTDA', ' 49271108000108', 'RUA MANOEL FRANCO, 809, NOVA BRASILIA, JI-PARANA - RO', 'adm@souuni.com', 'ativo');

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
  `status_pagamento` enum('pendente','pago') DEFAULT 'pendente',
  `parcela_numero` int(11) DEFAULT 1,
  `parcela_total` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(3, 'dafne', '123456', 'Mailon Roger Sátimo', 'mailon@infinitystore-ro.com', '01767582242', '1992-10-01', 'Gestor de Tecnologia', 'ativo');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`);

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
-- Índices de tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_empresa_pagavel` (`empresa_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `empresas_pagadoras`
--
ALTER TABLE `empresas_pagadoras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `empresas_pagaveis`
--
ALTER TABLE `empresas_pagaveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
