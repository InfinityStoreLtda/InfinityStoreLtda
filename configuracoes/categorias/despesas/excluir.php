<?php
// configuracoes/categorias/despesas/excluir.php - Excluir categoria de despesas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id AND tipo = 'despesa'");
$stmt->execute(['id' => $id]);
header('Location: index.php');
exit;
?>
