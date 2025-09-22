<?php
// configuracoes/empresas/despesas/excluir.php - Excluir empresa pagável (despesas)

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

// Verificar dependências em transações de despesa
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transacoes WHERE empresa_id = :id AND tipo = 'despesa'");
$stmt->execute(['id' => $id]);
$dep = $stmt->fetchColumn();
if ($dep > 0) {
    header('Location: index.php?erro=Não é possível excluir: empresa vinculada a ' . $dep . ' transação(ões) de despesa');
    exit;
}

try {
    $del = $pdo->prepare('DELETE FROM empresas_pagaveis WHERE id = :id');
    $del->execute(['id' => $id]);
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro ao excluir: ' . urlencode($e->getMessage()));
    exit;
}
?>

