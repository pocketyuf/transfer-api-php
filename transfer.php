<?php
// transfer.php
// endpoint para transferência de valores entre contas com validação e transação segura

require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$origem = (int) $data['origem'];
$destino = (int) $data['destino'];
$valor = (float) $data['valor'];

if($valor <= 0 || $origem === $destino) {
    http_response_code(400);
    echo json_encode(["erro" => "Dados inválidos"]);
    exit;
}

sqlsrv_begin_transaction($conn);

try {
    //ordenação dos IDs evita deadlocks ao bloquear na mesma ordem
    $ids = [$origem, $destino];
    sort($ids);

    foreach ($ids as $id) {
        $stmt = sqlsrv_query($conn, "SELECT saldo FROM Contas WITH (UPDLOCK, ROWLOCK) WHERE id = ?", [$id]);
        if(!$stmt || !sqlsrv_fetch($stmt))
            throw new Exception("Conta $id não encontrada");
    }

    //verifica saldo da origem
    $stmt = sqlsrv_query($conn, "SELECT saldo FROM Contas WHERE id = ?", [$origem]);
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if($row['saldo'] < $valor)
        throw new Exception('Saldo insuficiente');

    //débito e crédito
    sqlsrv_query($conn, "UPDATE Contas SET saldo = saldo - ? WHERE id = ?", [$valor, $origem]);
    sqlsrv_query($conn, "UPDATE Contas SET saldo = saldo + ? WHERE id = ?", [$valor, $destino]);

    //transação
    sqlsrv_query($conn, "INSERT INTO Transacoes (conta_origem_id, conta_destino_id, valor) VALUES (?, ?, ?)", [$origem, $destino, $valor]); //bindings pra prevenir SQL Injection
    sqlsrv_commit($conn);
    echo json_encode(['Valor transferido com sucesso!']);
} catch (Exception $e) {
    sqlsrv_rollback($conn);
    http_response_code(400);
    echo json_encode(["erro" => $e->getMessage()]);
}