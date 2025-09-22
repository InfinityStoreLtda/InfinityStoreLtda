<?php
// usuarios/editar.php - Editar usuário

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id AND status = 'ativo'");
$stmt->execute(['id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: index.php?erro=Usuário não encontrado');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_completo = $_POST['nome_completo'] ?? '';
    $email = $_POST['email'] ?? '';
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $data_nascimento = $_POST['data_nascimento'] ?? '';
    $cargo = $_POST['cargo'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Validações
    if (empty($nome_completo) || empty($email) || empty($cpf) || empty($username)) {
        $erro = 'Todos os campos obrigatórios devem ser preenchidos.';
    } elseif (strlen($cpf) != 11) {
        $erro = 'CPF deve ter 11 dígitos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif ($password && strlen($password) < 6) {
        $erro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        try {
            // Verificar se username ou CPF já existem (exceto para o próprio usuário)
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE (username = :username OR cpf = :cpf) AND id != :id");
            $stmt->execute(['username' => $username, 'cpf' => $cpf, 'id' => $id]);
            if ($stmt->fetch()) {
                $erro = 'Username ou CPF já cadastrado.';
            } else {
                $sql = "UPDATE usuarios SET nome_completo = :nome_completo, email = :email, cpf = :cpf, data_nascimento = :data_nascimento, cargo = :cargo, username = :username";
                $params = [
                    'nome_completo' => $nome_completo,
                    'email' => $email,
                    'cpf' => $cpf,
                    'data_nascimento' => $data_nascimento ?: null,
                    'cargo' => $cargo,
                    'username' => $username,
                    'id' => $id
                ];
                if ($password) {
                    $sql .= ", password = :password";
                    $params['password'] = gerarHashSenha($password);
                }
                $sql .= " WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao editar usuário: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f7fc;
        }
        .container {
            margin-left: 270px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        h2 {
            color: #2c3e50;
        }
        form {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #2c3e50;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
            form {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Editar Usuário</h2>
        <?php if ($erro): ?>
            <p class="error"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="nome_completo">Nome Completo:</label>
                <input type="text" id="nome_completo" name="nome_completo" value="<?php echo htmlspecialchars($usuario['nome_completo'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" value="<?php echo htmlspecialchars($usuario['cpf'] ?? ''); ?>" placeholder="xxx.xxx.xxx-xx" required>
            </div>
            <div class="form-group">
                <label for="data_nascimento">Data de Nascimento:</label>
                <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo htmlspecialchars($usuario['data_nascimento'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="cargo">Cargo:</label>
                <input type="text" id="cargo" name="cargo" value="<?php echo htmlspecialchars($usuario['cargo'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Nova Senha (opcional):</label>
                <input type="password" id="password" name="password" placeholder="Deixe em branco para manter a senha atual">
            </div>
            <button type="submit"><i class="fas fa-save"></i> Salvar</button>
        </form>
    </div>
</body>
</html>
