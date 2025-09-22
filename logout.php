<?php
// logout.php - Encerra a sessão do usuário

include_once __DIR__ . '/config.php';

// Iniciar sessão apenas se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir sessão
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Mensagem de logout (exibida por 2 segundos antes do redirecionamento)
$mensagem = 'Logout realizado com sucesso!';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout - Sistema Financeiro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes fadeUp { from {opacity:0; transform: translateY(6px);} to {opacity:1; transform: translateY(0);} }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #2c3e50, #3498db);
        }
        .logout-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeUp 0.4s ease both;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .success {
            color: #2ecc71;
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 2em;
            color: #3498db;
            margin-bottom: 10px;
        }
        @media (max-width: 480px) {
            .logout-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="logout-container">
        <i class="fas fa-sign-out-alt icon"></i>
        <h2>Logout</h2>
        <p class="success"><?php echo htmlspecialchars($mensagem); ?></p>
    </div>
    <script>
        // Redirecionar para login.php após 2 segundos
        setTimeout(() => {
            window.location.href = '/in/login.php';
        }, 2000);
    </script>
</body>
</html>
