-- Criar banco de dados
-- DROP DATABASE IF EXISTS sistema_esportes; -- Descomente se precisar recriar
CREATE DATABASE IF NOT EXISTS sistema_esportes;

-- Expressão SQL para informar à IDE que este é o banco que estará em uso.
USE sistema_esportes;

-- Expressão SQL para criar a tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- Expressão SQL para criar a tabela de fornecedores (Marcas)
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20),
    imagem VARCHAR(255), -- Coluna para miniatura (thumbnail) - usada no backend
    imagem_full VARCHAR(255) -- Coluna para imagem em alta resolução - usada no frontend
);

-- Expressão SQL para criar a tabela de produtos (Artigos Esportivos)
-- relacionada via FK com a tabela de fornecedores (Marcas)
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fornecedor_id INT, -- ID da Marca
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10, 2),
    imagem VARCHAR(255), -- Coluna para miniatura (thumbnail) - usada no backend
    imagem_full VARCHAR(255), -- Coluna para imagem em alta resolução - usada no frontend
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

-- Expressão SQL para cadastrar usuários de exemplo
INSERT INTO usuarios (usuario, senha) VALUES ('admin', MD5('123'));
INSERT INTO usuarios (usuario, senha) VALUES ('artur', MD5('123'));
INSERT INTO usuarios (usuario, senha) VALUES ('isa', MD5('123'));