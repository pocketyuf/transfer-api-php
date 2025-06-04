<?php
// transfer.php
// endpoint para transferência de valores entre contas com validação e transação segura

require 'database/sql_server.php';

if (!$conn) {
    http_response_code(500);
    echo json_encode(["erro" => "Conexão ao banco falhou"]);
    exit;
}

$ambiente = 'dev'; // mudar para 'prod' ao subir ou puxar de arquivo de ambiente (.env)

$data = json_decode(file_get_contents("php://input"), true);
$id_conta_origem = (int)$data['origem'];
$id_conta_destino = (int)$data['destino'];
$valor_transferencia = (float)$data['valor'];

if($valor_transferencia <= 0 || $id_conta_origem === $id_conta_destino) {
    http_response_code(400);
    echo json_encode(["erro" => "Dados inválidos"]);
    exit;
}

sqlsrv_begin_transaction($conn);

try {
    //ordenação dos IDs evita deadlocks ao bloquear na mesma ordem
    $ids = [$id_conta_origem, $id_conta_destino];
    sort($ids);

    $saldo_origem_atual = null;

    foreach ($ids as $id) {
        //aplica UPDLOCK + ROWLOCK para travar a linha durante a transação
        $stmt = sqlsrv_query($conn, "SELECT saldo FROM Contas WITH (UPDLOCK, ROWLOCK) WHERE id = ?", [$id]);
        if(!$stmt || !sqlsrv_fetch($stmt))
            throw new Exception("Conta $id não encontrada");

        //guarda o saldo da conta de origem já sob lock
        if($id === $id_conta_origem)
            $saldo_origem_atual = (float)sqlsrv_get_field($stmt, 0);
    }

    if($saldo_origem_atual === null)
        throw new Exception('Erro ao obter saldo da conta de origem');

    if($saldo_origem_atual < $valor_transferencia)
        throw new Exception('Saldo insuficiente');

    //débito e crédito
    sqlsrv_query($conn, "UPDATE contas SET saldo = saldo - ? WHERE id = ?", [$valor_transferencia, $id_conta_origem]);
    sqlsrv_query($conn, "UPDATE contas SET saldo = saldo + ? WHERE id = ?", [$valor_transferencia, $id_conta_destino]);

    //registro de transação
    sqlsrv_query($conn, "INSERT INTO transacoes (conta_origem_id, conta_destino_id, valor) VALUES (?, ?, ?)", [$id_conta_origem, $id_conta_destino, $valor_transferencia]); //bindings pra prevenir SQL Injection
    sqlsrv_commit($conn);
    sqlsrv_close($conn);
    echo json_encode(['mensagem' => 'Valor transferido com sucesso!']);
} catch (Exception $e) {
    sqlsrv_rollback($conn);
    sqlsrv_close($conn);
    http_response_code(400);
    $msg_erro = $ambiente === 'dev' ? $e->getMessage() : 'Erro ao processar a transferência';
    echo json_encode(["erro" => $msg_erro]);
}