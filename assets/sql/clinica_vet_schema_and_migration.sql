CREATE DATABASE clinicavet;


SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS consulta_servicos;
DROP TABLE IF EXISTS consultas;
DROP TABLE IF EXISTS animais;
DROP TABLE IF EXISTS veterinarios;
DROP TABLE IF EXISTS servicos;
DROP TABLE IF EXISTS clientes;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE clientes (
  id_cliente INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  cpf VARCHAR(20) NOT NULL UNIQUE,
  telefone VARCHAR(20) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  cep VARCHAR(10) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE veterinarios (
  id_vet INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  crmv VARCHAR(40),
  especialidade VARCHAR(120),
  telefone VARCHAR(20),
  email VARCHAR(120),
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE servicos (
  id_servico INT AUTO_INCREMENT PRIMARY KEY,
  nome_servico VARCHAR(150) NOT NULL,
  valor_padrao DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
  ativo BOOLEAN NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE animais (
  id_animal INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  especie VARCHAR(50),
  raca VARCHAR(80),
  idade INT,
  sexo ENUM('M','F','I') DEFAULT 'I',
  id_cliente INT NOT NULL,
  
  foto_url VARCHAR(500) NULL,
  status ENUM('Liberado', 'Em Tratamento', 'Internado') NOT NULL DEFAULT 'Liberado',
  
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  CONSTRAINT fk_animais_cliente 
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) 
    ON DELETE RESTRICT 
    ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE consultas (
  id_consulta INT AUTO_INCREMENT PRIMARY KEY,
  data_consulta DATE NOT NULL,
  hora_consulta TIME NOT NULL,
  motivo VARCHAR(255),
  observacoes TEXT,
  id_animal INT NOT NULL,
  id_vet INT NOT NULL,
  
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  CONSTRAINT fk_consulta_animal 
    FOREIGN KEY (id_animal) REFERENCES animais(id_animal) ON DELETE RESTRICT,
  CONSTRAINT fk_consulta_vet 
    FOREIGN KEY (id_vet) REFERENCES veterinarios(id_vet) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE consulta_servicos (
  id_consulta_servico INT AUTO_INCREMENT PRIMARY KEY,
  id_consulta INT NOT NULL,
  id_servico INT NOT NULL,
  quantidade INT NOT NULL DEFAULT 1,
  valor_cobrado DECIMAL(10, 2) NOT NULL,
  
  CONSTRAINT fk_cs_consulta 
    FOREIGN KEY (id_consulta) REFERENCES consultas(id_consulta) ON DELETE CASCADE,

  CONSTRAINT fk_cs_servico 
    FOREIGN KEY (id_servico) REFERENCES servicos(id_servico) ON DELETE RESTRICT
) ENGINE=InnoDB;

INSERT INTO servicos (nome_servico, valor_padrao) VALUES 
('Consulta Geral', 150.00),
('Retorno', 0.00),
('Vacina V10 (Importada)', 120.00),
('Vacina Antirrábica', 80.00),
('Exame de Sangue (Hemograma)', 90.00),
('Banho e Tosa (P)', 70.00),
('Banho e Tosa (M)', 90.00),
('Banho e Tosa (G)', 120.00),
('Castração (Macho)', 400.00),
('Castração (Fêmea)', 550.00),
('Internação (Diária)', 250.00);