<?php
// login.php - Tela de login sem uso de hash (senha em texto puro)
// AVISO: usar senhas em texto puro não é seguro. Utilize apenas para testes/ambientes controlados.

declare(strict_types=1);

session_start();

include_once __DIR__ . '/config.php';

$erro = '';
$logo_path = '/in/assets/logo.png';

// Carregar logo configurada, se houver
try {
    if (isset($pdo)) {
        $stmtLogo = $pdo->query("SELECT valor FROM configuracoes WHERE chave = 'logo_path' LIMIT 1");
        $val = $stmtLogo ? $stmtLogo->fetchColumn() : null;
        if (!empty($val)) {
            $logo_path = $val;
        }
    }
} catch (Exception $e) {
    // ignora erro de tabela ausente
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura segura dos campos
    $username = isset($_POST['username']) ? trim((string)$_POST['username']) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if ($username === '' || $password === '') {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        try {
            // Busca o usuário ativo
            $stmt = $pdo->prepare(
                "SELECT id, password 
                 FROM usuarios 
                 WHERE username = :username AND status = 'ativo' 
                 LIMIT 1"
            );
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Comparação direta (sem hash) — NÃO RECOMENDADO PARA PRODUÇÃO
            if ($user && $password === $user['password']) {
                $_SESSION['usuario_id'] = (int)$user['id'];
                header('Location: dashboard.php');
                exit;
            }

            $erro = 'Credenciais inválidas ou usuário inativo.';
        } catch (PDOException $e) {
            $erro = 'Erro ao fazer login: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Financeiro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes bgShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #2c3e50, #3498db);
            background-size: 200% 200%;
            animation: bgShift 12s ease infinite;
        }
        .login-container {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeUp 0.5s ease both;
        }
        .logo { margin-bottom: 20px; }
        .logo img { max-width: 150px; height: auto; }
        h2 { color: #2c3e50; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group input {
            width: 100%;
            padding: 12px 40px 12px 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .form-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #3498db;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.15s ease;
        }
        button:hover { background: #2c3e50; transform: translateY(-1px); }
        .error { color: #e74c3c; margin-bottom: 15px; font-size: 0.9em; }
        .toggle-pass {
            position: absolute;
            right: 42px;
            top: 50%;
            transform: translateY(-50%);
            color: #7f8c8d;
            cursor: pointer;
        }
        @media (max-width: 480px) {
            .login-container { margin: 20px; padding: 20px; }
            .logo img { max-width: 120px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="<?php echo htmlspecialchars($logo_path, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo da Empresa">
        </div>
        <h2>Login - Sistema Financeiro</h2>
        <?php if (!empty($erro)): ?>
            <p class="error"><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <input
                    type="text"
                    name="username"
                    placeholder="Username"
                    required
                    value="<?php echo htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >
                <i class="fas fa-user"></i>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Senha" required>
                <i class="fas fa-lock"></i>
                <i class="fas fa-eye toggle-pass" id="togglePass" title="Mostrar senha" aria-label="Mostrar senha"></i>
            </div>
            <button type="submit"><i class="fas fa-sign-in-alt"></i> Entrar</button>
        </form>
    </div>
    <script>
        const toggle = document.getElementById('togglePass');
        const input = document.getElementById('password');
        if (toggle && input) {
            toggle.addEventListener('click', () => {
                const isPass = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPass ? 'text' : 'password');
                toggle.classList.toggle('fa-eye');
                toggle.classList.toggle('fa-eye-slash');
                toggle.title = isPass ? 'Ocultar senha' : 'Mostrar senha';
            });
        }
    </script>
</body>
</html>
