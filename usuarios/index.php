<?php
// usuarios/index.php - Lista usuários com paginação

include_once __DIR__ . '/../config.php';
verificarLogin();

// Paginação
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$itens_por_pagina = 10;
$offset = ($pagina - 1) * $itens_por_pagina;

$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'");
$stmt_total->execute();
$total = $stmt_total->fetchColumn();
$paginas = ceil($total / $itens_por_pagina);

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE status = 'ativo' ORDER BY nome_completo LIMIT :offset, :limit");
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f7fc;
        }
        .container {
            margin-left: 270px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            animation: fadeIn 0.4s ease both;
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
        .paginacao {
            margin-top: 20px;
            text-align: center;
        }
        .paginacao a {
            padding: 10px;
            background: #3498db;
            color: #ffffff;
            border-radius: 5px;
            text-decoration: none;
            margin: 0 5px;
            transition: background 0.3s;
        }
        .paginacao a:hover {
            background: #2c3e50;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Usuários</h2>
        <a href="adicionar.php" class="add-btn"><i class="fas fa-plus"></i> Adicionar Usuário</a>
        <table>
            <tr>
                <th>Nome Completo</th>
                <th>Email</th>
                <th>CPF</th>
                <th>Data de Nascimento</th>
                <th>Cargo</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($usuarios as $usuario): ?>
                <?php
                // Formatar CPF para mostrar apenas 3 primeiros e 2 últimos dígitos
                $cpf = $usuario['cpf'] ? substr($usuario['cpf'], 0, 3) . '.***.***-' . substr($usuario['cpf'], -2) : '-';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['nome_completo'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email'] ?: '-'); ?></td>
                    <td><?php echo $cpf; ?></td>
                    <td><?php echo $usuario['data_nascimento'] ? date('d/m/Y', strtotime($usuario['data_nascimento'])) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($usuario['cargo'] ?: '-'); ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $usuario['id']; ?>"><i class="fas fa-edit"></i> Editar</a>
                        <a href="excluir.php?id=<?php echo $usuario['id']; ?>" onclick="return confirm('Confirmar exclusão?')"><i class="fas fa-trash"></i> Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="paginacao">
            <?php if ($pagina > 1): ?>
                <a href="?pagina=<?php echo $pagina - 1; ?>">Anterior</a>
            <?php endif; ?>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginas; ?></span>
            <?php if ($pagina < $paginas): ?>
                <a href="?pagina=<?php echo $pagina + 1; ?>">Próximo</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
