<?php
// receitas/excluir.php - Excluir receita

    include_once __DIR__ . '/../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM transacoes WHERE id = :id AND tipo = 'receita'");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
        header('Location: index.php?erro=Receita não encontrada');
        exit;
    }
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro ao excluir: ' . urlencode($e->getMessage()));
    exit;
}
?>
