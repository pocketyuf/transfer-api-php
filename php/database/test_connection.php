<?php
$serverName = 'sqlserver'; //nome do container SQL Server no docker-compose
$connectionOptions = [
    'UID' => 'sa',
    'PWD' => 'YourPassword123',
    'Database' => 'TesteTransferencia'
];

// Conecta ao banco
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Consulta a conta com id 1
$sql = "SELECT * FROM contas WHERE id in (1,2)";
$stmt = sqlsrv_query($conn, $sql);

if($stmt === false)
    die(print_r(sqlsrv_errors(), true));

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
if ($row === null)
    echo "Conta 1 não encontrada\n";
else {
    echo "Conta 1 encontrada:\n";
    print_r($row);
}

sqlsrv_close($conn);