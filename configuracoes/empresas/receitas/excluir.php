<?php
// configuracoes/empresas/receitas/excluir.php - Excluir empresa de receitas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

// Verificar ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

// Verificar dependências
$stmt = $pdo->prepare("SELECT COUNT(*) FROM transacoes WHERE empresa_id = :id AND tipo = 'receita'");
$stmt->execute(['id' => $id]);
$dependencias = $stmt->fetchColumn();

if ($dependencias > 0) {
    header('Location: index.php?erro=Não é possível excluir: empresa vinculada a ' . $dependencias . ' transação(ões) de receita');
    exit;
}

// Excluir empresa
try {
    $stmt = $pdo->prepare("DELETE FROM empresas_pagadoras WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro ao excluir: ' . urlencode($e->getMessage()));
    exit;
}
?>
