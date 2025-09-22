<?php
// despesas/atualizar_pagamento.php - Atualizar status de pagamento

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = $_GET['id'];
$status = ($_GET['status'] == 'pago') ? 'pago' : 'pendente';
$stmt = $pdo->prepare("UPDATE transacoes SET status_pagamento = :status WHERE id = :id AND tipo = 'despesa'");
$stmt->execute(['status' => $status, 'id' => $id]);
header('Location: index.php');
exit;
?>
