DROP DATABASE IF EXISTS integra_odonto;
CREATE DATABASE integra_odonto;
USE integra_odonto;

CREATE TABLE clinica_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    tipo_documento ENUM('CNPJ', 'CPF') NOT NULL,
    documento VARCHAR(20) NOT NULL,
    endereco VARCHAR(255) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_completo VARCHAR(255) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    login VARCHAR(50) NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel_acesso INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    sexo ENUM('M', 'F', 'O') NOT NULL,
    rg VARCHAR(20),
    cpf VARCHAR(14) NOT NULL,
    email VARCHAR(100),
    telefone VARCHAR(20) NOT NULL,
    cep VARCHAR(10),
    logradouro VARCHAR(100),
    numero VARCHAR(10),
    complemento VARCHAR(50),
    bairro VARCHAR(50),
    cidade VARCHAR(50),
    uf CHAR(2),
    responsavel_nome VARCHAR(100),
    responsavel_cpf VARCHAR(14),
    foto_path VARCHAR(255),
    deleted_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE consultas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_paciente INT NOT NULL,
    nome_tratamento VARCHAR(100) NOT NULL,
    data_consulta DATE NOT NULL,
    hora_consulta TIME NOT NULL,
    status ENUM('Agendado', 'Aguardando', 'Concluido', 'Faltou', 'Cancelado') NOT NULL DEFAULT 'Agendado',
    id_dentista INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_dentista) REFERENCES usuarios(id)
);

CREATE TABLE financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status_pagamento ENUM('Pendente', 'Pago') NOT NULL DEFAULT 'Pendente',
    forma_recebimento ENUM('Dinheiro', 'PIX', 'Cartao de Credito', 'Cartao de Debito', 'Boleto') NULL,
    data_pagamento DATETIME NULL,
    incluir_ir TINYINT(1) DEFAULT 0,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_consulta) REFERENCES consultas(id)
);

CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    acao VARCHAR(255) NOT NULL,
    tabela VARCHAR(50) NOT NULL,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome_completo, cpf, login, senha, nivel_acesso) 
VALUES ('Administrador', '00000000000', 'admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 1);
