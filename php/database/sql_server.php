<?php
$serverName = 'sqlserver'; // nome do container do SQL Server no docker-compose
$connectionOptions = [
    'UID' => 'sa',
    'PWD' => 'YourPassword123',
    'Database' => 'TesteTransferencia'
];

// conexão global
$conn = sqlsrv_connect($serverName, $connectionOptions);

if($conn === false)
    die(json_encode(['erro' => 'Falha na conexão ao banco', 'detalhes' => sqlsrv_errors()]));