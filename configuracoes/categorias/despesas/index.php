<?php
// configuracoes/categorias/despesas/index.php - Lista categorias de despesas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$stmt = $pdo->query("SELECT * FROM categorias WHERE tipo = 'despesa' AND status = 'ativo' ORDER BY nome");
$categorias = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias de Despesas</title>
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
        .add-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #3498db;
            color: #ffffff;
            border-radius: 5px;
            margin-bottom: 15px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .add-btn:hover {
            background: #2c3e50;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background: #3498db;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background: #f9fafb;
        }
        a {
            color: #3498db;
            text-decoration: none;
            margin-right: 10px;
        }
        a:hover {
            color: #2c3e50;
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../../../sidebar.php'; ?>
    <div class="container">
        <h2>Categorias de Despesas</h2>
        <a href="adicionar.php" class="add-btn"><i class="fas fa-plus"></i> Adicionar Categoria</a>
        <table>
            <tr>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['nome']); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $categoria['id']; ?>"><i class="fas fa-edit"></i> Editar</a>
                        <a href="excluir.php?id=<?php echo $categoria['id']; ?>" onclick="return confirm('Confirmar exclusão?')"><i class="fas fa-trash"></i> Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
