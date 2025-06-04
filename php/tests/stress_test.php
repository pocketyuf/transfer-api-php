<?php
$payload = json_encode([
    'origem' => 1,
    'destino' => 2,
    'valor' => 1.00
]);

$threads = 50;

for ($i = 0; $i < $threads; $i++) {
    $cmd = "php tests/stress_worker.php '$payload' &";
    echo shell_exec($cmd) . PHP_EOL;
}
