<?php

$start = microtime(true);
$payload = $argv[1];

$ch = curl_init('http://nginx/transfer.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
$end = microtime(true);
$time_ms = round(($end - $start) * 1000, 2);

if($http_code === 200)
    echo "Sucesso ({$time_ms} ms): $response";
else
    echo "Erro HTTP $http_code ({$time_ms} ms): $response";