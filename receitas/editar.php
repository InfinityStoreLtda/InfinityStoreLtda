<?php
// receitas/editar.php - Editar receita

include_once __DIR__ . '/../config.php';
verificarLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php?erro=ID inválido');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM transacoes WHERE id = :id AND tipo = 'receita'");
$stmt->execute(['id' => $id]);
$receita = $stmt->fetch();

if (!$receita) {
    header('Location: index.php?erro=Receita não encontrada');
    exit;
}

$stmt = $pdo->query("SELECT * FROM categorias WHERE tipo = 'receita' AND status = 'ativo'");
$categorias = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM empresas_pagadoras WHERE status = 'ativo'");
$empresas = $stmt->fetchAll();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $descricao = $_POST['descricao'] ?? '';
    $valor = floatval($_POST['valor'] ?? 0);
    $data = $_POST['data'] ?? '';
    $empresa_id = !empty($_POST['empresa_id']) ? (int)$_POST['empresa_id'] : null;

    if ($valor <= 0 || empty($data)) {
        $erro = 'Valor e data são obrigatórios.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE transacoes SET descricao = :descricao, valor = :valor, data = :data, categoria_id = :categoria_id, empresa_id = :empresa_id WHERE id = :id AND tipo = 'receita'");
            $stmt->execute([
                'descricao' => $categoria_id && isset($categorias[array_search($categoria_id, array_column($categorias, 'id'))]) ? ($categorias[array_search($categoria_id, array_column($categorias, 'id'))]['nome'] . ' - ' . $descricao) : $descricao,
                'valor' => $valor,
                'data' => $data,
                'categoria_id' => $categoria_id,
                'empresa_id' => $empresa_id,
                'id' => $id,
                
            ]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $erro = 'Erro ao editar: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Receita</title>
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
        input, select {
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
        <h2>Editar Receita</h2>
        <?php if ($erro): ?>
            <p class="error"><?php echo htmlspecialchars($erro); ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="empresa_id">Empresa:</label>
                <select id="empresa_id" name="empresa_id">
                    <option value="">Selecione uma empresa (opcional)</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo $empresa['id']; ?>" <?php echo $empresa['id'] == $receita['empresa_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($empresa['nome_empresa']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoria/Descrição:</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sem categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>" <?php echo $categoria['id'] == $receita['categoria_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="descricao" value="<?php echo htmlspecialchars($receita['descricao']); ?>" placeholder="Descrição adicional" style="margin-top: 5px;">
            </div>
            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="number" id="valor" name="valor" step="0.01" value="<?php echo $receita['valor']; ?>" required>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" value="<?php echo $receita['data']; ?>" required>
            </div>
            <button type="submit"><i class="fas fa-save"></i> Salvar</button>
        </form>
    </div>
</body>
</html>
