<?php
// index.php
// arquivo de rotas simples para encaminhar requisições HTTP para os scripts apropriados

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

if ($uri === '/transferir' && $method === 'POST') {
    require 'transfer.php';
} else {
    echo json_encode(["erro" => "Rota inválida"]);
}