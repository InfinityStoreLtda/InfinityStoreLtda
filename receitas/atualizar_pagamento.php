<?php
// receitas/atualizar_pagamento.php - Atualizar status de pagamento

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) && in_array($_GET['status'], ['pago', 'pendente']) ? $_GET['status'] : 'pendente';

if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE transacoes SET status_pagamento = :status WHERE id = :id AND tipo = 'receita'");
    $stmt->execute([
        'status' => $status,
        'id' => $id,
        
    ]);
    if ($stmt->rowCount() === 0) {
        header('Location: index.php?erro=Receita não encontrada');
        exit;
    }
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro ao atualizar status: ' . urlencode($e->getMessage()));
    exit;
}
?>
