CREATE DATABASE IF NOT EXISTS integra_odonto;
USE integra_odonto;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL UNIQUE,
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
    cpf VARCHAR(14) NOT NULL UNIQUE,
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
    tipo_agendamento ENUM('Unica', 'Tratamento') NOT NULL,
    nome_tratamento VARCHAR(100),
    data_hora_inicio DATETIME NOT NULL,
    data_hora_fim DATETIME NOT NULL,
    status ENUM('Agendado', 'Confirmado', 'Aguardando', 'Em Atendimento', 'Concluido', 'Faltou') NOT NULL,
    id_dentista INT NOT NULL,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_paciente) REFERENCES pacientes(id),
    FOREIGN KEY (id_dentista) REFERENCES usuarios(id)
);

CREATE TABLE financeiro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_consulta INT NOT NULL,
    valor DECIMAL(10,2) NOT NULL,
    status_pagamento ENUM('Pendente', 'Pago') NOT NULL,
    forma_recebimento ENUM('Dinheiro', 'PIX', 'Cartao de Credito', 'Cartao de Debito', 'Boleto'),
    data_pagamento DATETIME,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (id_consulta) REFERENCES consultas(id)
);

CREATE TABLE logs_auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    acao_realizada VARCHAR(255) NOT NULL,
    tabela_afetada VARCHAR(50) NOT NULL,
    ip_origem VARCHAR(45) NOT NULL,
    data_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

INSERT INTO usuarios (login, senha, nivel_acesso) 
VALUES ('admin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 1);
