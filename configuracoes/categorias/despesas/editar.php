<?php
// configuracoes/categorias/despesas/editar.php - Editar categoria de despesas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM categorias WHERE id = :id AND tipo = 'despesa'");
$stmt->execute(['id' => $id]);
$categoria = $stmt->fetch();

if (!$categoria) {
    die("Categoria não encontrada!");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    $stmt = $pdo->prepare("UPDATE categorias SET nome = :nome WHERE id = :id");
    $stmt->execute(['nome' => $nome, 'id' => $id]);
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Categoria de Despesa</title>
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
    <?php include_once __DIR__ . '/../../../sidebar.php'; ?>
    <div class="container">
        <h2>Editar Categoria de Despesa</h2>
        <form method="POST">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
            </div>
            <button type="submit"><i class="fas fa-save"></i> Salvar</button>
        </form>
    </div>
</body>
</html>
