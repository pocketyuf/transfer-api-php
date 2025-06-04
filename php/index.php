<?php
// index.php
// arquivo de rotas simples para encaminhar requisições HTTP para os scripts apropriados

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

header('Content-Type: application/json');

$routes = [
    'GET' => ['/' => function() {
            echo json_encode([
                'mensagem' => 'PHP carregado com sucesso!',
                'info' => 'API de Transferência - Acesse /transferir para realizar uma transferência ou execute o teste de estresse.'
            ]);
        },
    ], 'POST' => ['/transfer' => function() {
            require 'transfer.php';
        }
    ]
];

if(isset($routes[$method][$uri]))
    $routes[$method][$uri]();
else {
    http_response_code(404);
    echo json_encode(['erro' => 'Rota inválida']);
}