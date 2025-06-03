<?php
// db.php
// responsável pra conectar ao SQL Server com as configurações apropriadas
// necessário para todos os endpoints que interagem com o banco

$server_name = 'localhost,1433';
$connection_options = [
    'Database' => 'TesteTransferencia',
    'Uid' => 'sa',
    'PWD' => 'YourPassword123',
    'CharacterSet' => 'UTF-8',
    'Encrypt' => 1, // SSL obrigatório no ODBC Driver 18
    'TrustServerCertificate' => 1 // ignora certificados inválidos
];

$conn = sqlsrv_connect($server_name, $connection_options);

if(!$conn) {
    http_response_code(500);
    die(json_encode(sqlsrv_errors()));
}