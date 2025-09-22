<?php
// configuracoes/categorias/receitas/excluir.php - Excluir categoria de receitas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM categorias WHERE id = :id AND tipo = 'receita'");
$stmt->execute(['id' => $id]);
header('Location: index.php');
exit;
?>
