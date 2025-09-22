<?php
// despesas/excluir.php - Excluir despesa

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM transacoes WHERE id = :id AND tipo = 'despesa'");
$stmt->execute(['id' => $id]);
header('Location: index.php');
exit;
?>
