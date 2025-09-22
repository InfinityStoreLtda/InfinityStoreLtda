<?php
// configuracoes/empresas/receitas/index.php - Lista empresas de receitas

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$stmt = $pdo->query("SELECT * FROM empresas_pagadoras WHERE status = 'ativo' ORDER BY nome_empresa");
$empresas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas de Receitas</title>
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
        .error {
            color: #e74c3c;
            margin-bottom: 15px;
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
        <h2>Empresas de Receitas</h2>
        <a href="adicionar.php" class="add-btn"><i class="fas fa-plus"></i> Adicionar Empresa</a>
        <?php if (isset($_GET['erro'])): ?>
            <p class="error"><?php echo htmlspecialchars($_GET['erro']); ?></p>
        <?php endif; ?>
        <table>
            <tr>
                <th>Nome</th>
                <th>CNPJ</th>
                <th>Contato</th>
                <th>Endereço</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($empresas as $empresa): ?>
                <tr>
                    <td><?php echo htmlspecialchars($empresa['nome_empresa']); ?></td>
                    <td><?php echo htmlspecialchars($empresa['cnpj'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($empresa['contato'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($empresa['endereco'] ?: '-'); ?></td>
                    <td><?php echo $empresa['status']; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $empresa['id']; ?>"><i class="fas fa-edit"></i> Editar</a>
                        <a href="excluir.php?id=<?php echo $empresa['id']; ?>" onclick="return confirm('Confirmar exclusão?')"><i class="fas fa-trash"></i> Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
