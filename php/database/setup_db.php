<?php
$serverName = "sqlserver,1433"; // nome do container + porta
$connectionOptions = [
    "UID" => "sa",
    "PWD" => "YourPassword123",
    "Database" => "master"
];

//conectar no SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn === false)
    die(print_r(sqlsrv_errors(), true));

//criar banco TesteTransferencia se não existir
$sqlCreateDB = "IF DB_ID('TesteTransferencia') IS NULL CREATE DATABASE TesteTransferencia;";
$stmt = sqlsrv_query($conn, $sqlCreateDB);
if($stmt === false)
    die(print_r(sqlsrv_errors(), true));
sqlsrv_close($conn);

//conectar no banco TesteTransferencia
$connectionOptions["Database"] = "TesteTransferencia";
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn === false)
    die(print_r(sqlsrv_errors(), true));

//criar tabela contas
$sqlCreateContas = "IF OBJECT_ID('contas', 'U') IS NULL
    CREATE TABLE contas (
        id INT IDENTITY(1,1) PRIMARY KEY,
        nome NVARCHAR(100) NOT NULL,
        saldo DECIMAL(15,2) NOT NULL DEFAULT 0 -- não utilizar o tipo FLOAT para valores monetários
    );";
$stmt = sqlsrv_query($conn, $sqlCreateContas);
if($stmt === false)
    die(print_r(sqlsrv_errors(), true));

//inserção de contas de teste
$sqlInsertContas = "INSERT INTO contas (nome, saldo) VALUES
    ('Carlos Silva', 1000.00),
    ('João Pereira', 1500.00),
    ('Maria Oliveira', 2000.00),
    ('Ana Souza', 1200.00);";
$stmt = sqlsrv_query($conn, $sqlInsertContas);
if ($stmt === false)
    die(print_r(sqlsrv_errors(), true));

//criar tabela transferencias
$sqlCreateTransferencias = "IF OBJECT_ID('transacoes', 'U') IS NULL
    CREATE TABLE transacoes (
        id INT PRIMARY KEY IDENTITY(1,1),
        conta_origem_id INT NOT NULL,
        conta_destino_id INT NOT NULL,
        valor DECIMAL(18,2) NOT NULL,
        data DATETIME DEFAULT GETDATE(),
        FOREIGN KEY (conta_origem_id) REFERENCES contas(id),
        FOREIGN KEY (conta_destino_id) REFERENCES contas(id)
    );";
$stmt = sqlsrv_query($conn, $sqlCreateTransferencias);
if($stmt === false)
    die(print_r(sqlsrv_errors(), true));

echo "Banco, tabelas e dados iniciais criados com sucesso.\n";
sqlsrv_close($conn);