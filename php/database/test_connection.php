<?php
$serverName = 'sqlserver'; //nome do container SQL Server no docker-compose
$connectionOptions = [
    'UID' => 'sa',
    'PWD' => 'YourPassword123',
    'Database' => 'TesteTransferencia'
];

//conecta ao banco
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn === false)
    die(print_r(sqlsrv_errors(), true));

//consulta todas as contas
$sql = "SELECT id, nome, saldo FROM contas";
$stmt = sqlsrv_query($conn, $sql);

if($stmt === false)
    die(print_r(sqlsrv_errors(), true));

echo 'Lista de contas:' . PHP_EOL;
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "ID: {$row['id']} | Nome: {$row['nome']} | Saldo: R$ " . number_format($row['saldo'], 2, ',', '.') . PHP_EOL;
}

sqlsrv_close($conn);