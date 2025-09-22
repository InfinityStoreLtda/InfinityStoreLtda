<?php
// usuarios/excluir.php - Excluir usuário

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

try {
    // Verificar dependências (ex.: transações do usuário)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM transacoes WHERE usuario_id = :id");
    $stmt->execute(['id' => $id]);
    if ($stmt->fetchColumn() > 0) {
        header('Location: index.php?erro=Não é possível excluir: usuário possui transações vinculadas');
        exit;
    }

    // Excluir usuário
    $stmt = $pdo->prepare("UPDATE usuarios SET status = 'inativo' WHERE id = :id");
    $stmt->execute(['id' => $id]);
    if ($stmt->rowCount() === 0) {
        header('Location: index.php?erro=Usuário não encontrado');
        exit;
    }
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    header('Location: index.php?erro=Erro ao excluir: ' . urlencode($e->getMessage()));
    exit;
}
?>
