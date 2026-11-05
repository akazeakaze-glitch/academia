-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/11/2026 às 19:25
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `academia`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sexo` enum('M','F') NOT NULL DEFAULT 'M',
  `telefone` varchar(20) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `objetivo` varchar(50) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `senha` varchar(255) DEFAULT '123456',
  `treino_atual` varchar(2) DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `nome`, `email`, `sexo`, `telefone`, `data_nascimento`, `objetivo`, `data_cadastro`, `senha`, `treino_atual`) VALUES
(1004, 'andreza', 'andreza@gmail.com', 'F', '51980644786', '2000-08-06', 'Emagrecimento', '2026-10-16 22:21:18', '$2y$10$zkb1QNehsmz3Eszuq7xik.jgQtDV56Uk6khcm9cQSlU94nPy87cyW', 'B'),
(1009, 'eduardo', 'eeduardo@gmail.com', 'M', '51980000000', '2000-04-07', 'Reabilitação', '2026-10-21 14:33:14', '$2y$10$2w4.7GdxBL0Cv64nAFn.LeSfAy.ZmT/jLscHgTrh9XF5BH2.YgeL.', 'A'),
(1012, 'carlos', 'carlos@gmail.com', 'M', '51999988888', '2000-02-08', 'Hipertrofia', '2026-10-21 15:05:36', '$2y$10$Ilu9o.mQAMtl5luQsB8ZResZlx61wwdHjAk7VZFumS1rzePI4sn0a', 'A');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `data_avaliacao` date NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(3,2) DEFAULT NULL,
  `percentual_gordura` decimal(4,2) DEFAULT NULL,
  `percepcao_corporal` varchar(255) DEFAULT NULL,
  `doencas_preexistentes` text DEFAULT NULL,
  `limitacoes_fisicas` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `aluno_id`, `data_avaliacao`, `peso`, `altura`, `percentual_gordura`, `percepcao_corporal`, `doencas_preexistentes`, `limitacoes_fisicas`) VALUES
(6, 1009, '2026-10-21', 80.00, 1.75, NULL, 'ganha peso com facilidade', 'asma', 'dores no cotovelo'),
(9, 1012, '2026-10-21', 90.00, 1.77, NULL, 'facilidade em ganhar peso', 'asma', 'dores no joelho');

-- --------------------------------------------------------

--
-- Estrutura para tabela `exercicios`
--

CREATE TABLE `exercicios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `grupo_muscular` varchar(50) NOT NULL,
  `equipamento` varchar(50) DEFAULT NULL,
  `instrucoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `exercicios`
--

INSERT INTO `exercicios` (`id`, `nome`, `grupo_muscular`, `equipamento`, `instrucoes`) VALUES
(1, 'Supino Reto', 'Peito', 'Barra Livre', ''),
(2, 'Agachamento Livre', 'Pernas', 'Barra Livre', ''),
(3, 'Puxada Frontal', 'Costas', 'Polia', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichas`
--

CREATE TABLE `fichas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) NOT NULL,
  `nome_ficha` varchar(100) NOT NULL,
  `data_criacao` date NOT NULL,
  `data_vencimento` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fichas`
--

INSERT INTO `fichas` (`id`, `aluno_id`, `nome_ficha`, `data_criacao`, `data_vencimento`) VALUES
(11, 1004, 'Treino Padrão Inicial', '2026-10-17', '2026-12-01'),
(12, 1009, 'Treino Padrão Inicial', '2026-10-21', '2026-12-05'),
(15, 1012, 'Treino Padrão Inicial', '2026-10-21', '2026-12-05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichas_exercicios`
--

CREATE TABLE `fichas_exercicios` (
  `id` int(11) NOT NULL,
  `ficha_id` int(11) NOT NULL,
  `exercicio_id` int(11) NOT NULL,
  `divisao` varchar(10) DEFAULT 'A',
  `series` varchar(50) DEFAULT '3',
  `repeticoes` varchar(50) DEFAULT '10-12',
  `carga` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ficha_exercicios`
--

CREATE TABLE `ficha_exercicios` (
  `id` int(11) NOT NULL,
  `ficha_id` int(11) NOT NULL,
  `exercicio_id` int(11) NOT NULL,
  `divisao` char(1) NOT NULL,
  `series` varchar(20) DEFAULT NULL,
  `repeticoes` varchar(20) DEFAULT NULL,
  `carga` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `especialidade` varchar(100) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'padrao.png',
  `senha` varchar(255) DEFAULT '123456'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `professores`
--

INSERT INTO `professores` (`id`, `nome`, `email`, `especialidade`, `telefone`, `foto`, `senha`) VALUES
(1, 'Carlos Eduardo', 'admin@academia.com', 'Musculação e Hipertrofia', '51999998888', 'padrao.png', '123456'),
(2, 'Fernanda Lima', NULL, 'Cross Training e Funcional', '51988887777', 'padrao.png', '123456'),
(12, 'eduardo', NULL, 'musculação', '22222222222', 'padrao.png', '123456');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('admin','professor','aluno') NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@academia.com', '$2y$10$Uc3t6qQMxHKqcuQ7KR1Xse3AzM9JBoFnM/ZydZpsMTkXVqQaZnsIe', 'admin', '2026-08-28 20:12:47'),
(8, 'eduardo', 'eduardo@academia.com', '$2y$10$a3hk0yE5pPl4H0ZFXaE6aOmBBg5Zw0RCJq4CzAxhJf5cmMTWEmgOC', 'professor', '2026-10-19 02:01:03');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices de tabela `exercicios`
--
ALTER TABLE `exercicios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fichas`
--
ALTER TABLE `fichas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`);

--
-- Índices de tabela `fichas_exercicios`
--
ALTER TABLE `fichas_exercicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ficha_id` (`ficha_id`),
  ADD KEY `exercicio_id` (`exercicio_id`);

--
-- Índices de tabela `ficha_exercicios`
--
ALTER TABLE `ficha_exercicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ficha_id` (`ficha_id`),
  ADD KEY `exercicio_id` (`exercicio_id`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1013;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `exercicios`
--
ALTER TABLE `exercicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `fichas`
--
ALTER TABLE `fichas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de tabela `fichas_exercicios`
--
ALTER TABLE `fichas_exercicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ficha_exercicios`
--
ALTER TABLE `ficha_exercicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fichas`
--
ALTER TABLE `fichas`
  ADD CONSTRAINT `fichas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `fichas_exercicios`
--
ALTER TABLE `fichas_exercicios`
  ADD CONSTRAINT `fichas_exercicios_ibfk_1` FOREIGN KEY (`ficha_id`) REFERENCES `fichas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fichas_exercicios_ibfk_2` FOREIGN KEY (`exercicio_id`) REFERENCES `exercicios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `ficha_exercicios`
--
ALTER TABLE `ficha_exercicios`
  ADD CONSTRAINT `ficha_exercicios_ibfk_1` FOREIGN KEY (`ficha_id`) REFERENCES `fichas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ficha_exercicios_ibfk_2` FOREIGN KEY (`exercicio_id`) REFERENCES `exercicios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
