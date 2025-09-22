<?php
// configuracoes/logo.php - Configurar logo de entrada

include_once __DIR__ . '/../config.php';
verificarLogin();

$erro = '';
$mensagem = '';
$logo_path = '/in/assets/logo.png'; // Caminho padrão

// Obter logo atual
$stmt = $pdo->prepare("SELECT valor FROM configuracoes WHERE chave = 'logo_path'");
$stmt->execute();
$logo = $stmt->fetchColumn();
if ($logo) {
    $logo_path = $logo;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $upload_dir = '/home/u824192816/domains/infinitystore-ro.com/public_html/in/assets/';
        $file_name = 'logo.' . pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $upload_path = $upload_dir . $file_name;

        // Verificar e criar diretório com permissões
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $erro = 'Erro ao criar diretório /in/assets/. Verifique as permissões do diretório pai.';
            } else {
                // Garantir permissões corretas
                if (!chmod($upload_dir, 0755)) {
                    $erro = 'Erro ao definir permissões do diretório /in/assets/.';
                }
            }
        }

        // Verificar se o diretório é gravável
        if (!$erro && !is_writable($upload_dir)) {
            $erro = 'Diretório /in/assets/ não tem permissões de escrita. Execute: chmod 755 /in/assets/';
        }

        // Validações do arquivo
        if (!$erro && !in_array($_FILES['logo']['type'], $allowed_types)) {
            $erro = 'Formato de arquivo inválido. Use PNG ou JPG.';
        } elseif (!$erro && $_FILES['logo']['size'] > $max_size) {
            $erro = 'Arquivo muito grande. Máximo 2MB.';
        } else {
            try {
                // Mover arquivo
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $logo_path = '/in/assets/' . $file_name;
                    // Atualizar banco
                    $stmt = $pdo->prepare("INSERT INTO configuracoes (chave, valor) VALUES ('logo_path', :valor) ON DUPLICATE KEY UPDATE valor = :valor");
                    $stmt->execute(['valor' => $logo_path]);
                    $mensagem = 'Logo atualizada com sucesso!';
                } else {
                    $erro = 'Erro ao fazer upload do arquivo. Verifique permissões do diretório /in/assets/.';
                }
            } catch (PDOException $e) {
                $erro = 'Erro ao salvar no banco: ' . $e->getMessage();
            }
        }
    } else {
        $erro = 'Nenhum arquivo selecionado ou erro no upload.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Logo</title>
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
        .form-container {
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
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        .logo-preview {
            margin-bottom: 20px;
            text-align: center;
        }
        .logo-preview img {
            max-width: 150px;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .error {
            color: #e74c3c;
            margin-bottom: 10px;
        }
        .success {
            color: #2ecc71;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
            .form-container {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Configurar Logo de Entrada</h2>
        <div class="form-container">
            <?php if ($erro): ?>
                <p class="error"><?php echo htmlspecialchars($erro); ?></p>
            <?php endif; ?>
            <?php if ($mensagem): ?>
                <p class="success"><?php echo htmlspecialchars($mensagem); ?></p>
            <?php endif; ?>
            <div class="logo-preview">
                <h3>Logo Atual</h3>
                <?php
                    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
                    $fileOk = $docRoot ? file_exists($docRoot . $logo_path) : false;
                ?>
                <?php if ($fileOk): ?>
                    <img src="<?php echo htmlspecialchars($logo_path); ?>" alt="Logo Atual">
                <?php else: ?>
                    <p>Nenhuma logo configurada.</p>
                <?php endif; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="logo">Selecionar Nova Logo (PNG ou JPG, máx. 2MB):</label>
                    <input type="file" id="logo" name="logo" accept=".png,.jpg,.jpeg" required>
                </div>
                <button type="submit"><i class="fas fa-upload"></i> Atualizar Logo</button>
            </form>
        </div>
    </div>
</body>
</html>
