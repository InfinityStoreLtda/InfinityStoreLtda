<?php
// config.php - Configuracao do banco de dados e funcoes globais

if (defined('CONFIG_PHP')) {
    return;
}
define('CONFIG_PHP', true);

// Iniciar sessao apenas se nao estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuracao do banco de dados
try {
    $pdo = new PDO('mysql:host=localhost;dbname=u824192816_financeiro_inf', 'u824192816_financeiro_inf', 'Lantolan.20');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro de conexao com o banco: ' . $e->getMessage());
}

// Funcao para verificar login
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /in/login.php');
        exit;
    }
}

// Funcoes utilitarias para senhas
function gerarHashSenha(string $senha): string {
    return password_hash($senha, PASSWORD_DEFAULT);
}

function validarSenhaInformada(string $senhaInformada, string $hashArmazenado): array {
    $info = password_get_info($hashArmazenado);
    if ($info['algo'] === 0) {
        $valida = hash_equals($hashArmazenado, $senhaInformada);
        return [$valida, $valida];
    }

    $valida = password_verify($senhaInformada, $hashArmazenado);
    $precisaRehash = $valida && password_needs_rehash($hashArmazenado, PASSWORD_DEFAULT);

    return [$valida, $precisaRehash];
}

function atualizarSenhaHashed(PDO $pdo, int $usuarioId, string $senhaEmTexto): void {
    $stmt = $pdo->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
    $stmt->execute([
        'password' => gerarHashSenha($senhaEmTexto),
        'id' => $usuarioId
    ]);
}
?>
