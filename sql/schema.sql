-- schema.sql
-- criação das tabelas principais: contas e transacoes

CREATE TABLE contas (
    id INT PRIMARY KEY IDENTITY(1,1),
    nome NVARCHAR(100) NOT NULL,
    saldo DECIMAL(18,2) NOT NULL DEFAULT 0
);

CREATE TABLE transacoes (
    id INT PRIMARY KEY IDENTITY(1,1),
    conta_origem_id INT NOT NULL,
    conta_destino_id INT NOT NULL,
    valor DECIMAL(18,2) NOT NULL,
    data DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (conta_origem_id) REFERENCES contas(id),
    FOREIGN KEY (conta_destino_id) REFERENCES contas(id)
);