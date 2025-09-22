<?php
// despesas/index.php - Lista despesas com filtro de mês, quantidade por página e paginação

include_once __DIR__ . '/../config.php';
verificarLogin();

// Filtro de mês
$mes_atual = date('Y-m');
$mes = isset($_GET['mes']) ? $_GET['mes'] : $mes_atual;
list($ano, $mes_num) = explode('-', $mes);

// Mês anterior e próximo
$mes_anterior = date('Y-m', strtotime('-1 month', strtotime($mes . '-01')));
$mes_proximo = date('Y-m', strtotime('+1 month', strtotime($mes . '-01')));

// Filtro de quantidade por página
$itens_por_pagina = isset($_GET['itens']) && in_array((int)$_GET['itens'], [10, 100, 1000]) ? (int)$_GET['itens'] : 10;

// Paginação
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina - 1) * $itens_por_pagina;

$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM transacoes WHERE tipo = 'despesa' AND DATE_FORMAT(data, '%Y-%m') = :mes");
$stmt_total->execute(['mes' => $mes]);
$total = $stmt_total->fetchColumn();
$paginas = ceil($total / $itens_por_pagina);

$stmt = $pdo->prepare("SELECT t.*, c.nome as categoria, e.nome_empresa as empresa, DATEDIFF(t.data, CURDATE()) as dias_vencimento FROM transacoes t LEFT JOIN categorias c ON t.categoria_id = c.id LEFT JOIN empresas_pagaveis e ON t.empresa_id = e.id WHERE t.tipo = 'despesa' AND DATE_FORMAT(data, '%Y-%m') = :mes ORDER BY t.data DESC LIMIT :offset, :limit");
$stmt->bindValue(':mes', $mes, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$despesas = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas</title>
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
        .filtro {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filtro a, .filtro select, .filtro input, .filtro button {
            padding: 10px;
            background: #3498db;
            color: #ffffff;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
            border: none;
        }
        .filtro a:hover, .filtro select:hover, .filtro button:hover {
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
        tr.pago-row {
            background: #e6f7e6; /* Verde claro para despesas pagas */
        }
        tr.pendente-row {
            background: #f7e6e6; /* Vermelho claro para despesas pendentes */
        }
        .pago {
            color: green;
            font-weight: bold;
        }
        .pendente {
            color: red;
            font-weight: bold;
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
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .action-btn.editar {
            background: #3498db;
            color: #ffffff;
        }
        .action-btn.excluir {
            background: #e74c3c;
            color: #ffffff;
        }
        .action-btn.status-pago {
            background: linear-gradient(135deg, #3498db, #2ecc71);
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .action-btn.status-pendente {
            background: linear-gradient(135deg, #3498db, #e74c3c);
            color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .action-btn i {
            margin-right: 5px;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
            .filtro {
                flex-direction: column;
                align-items: flex-start;
            }
            .action-btn {
                padding: 5px 10px;
                font-size: 0.8em;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Despesas</h2>
        <div class="filtro">
            <a href="?mes=<?php echo $mes_anterior; ?>&itens=<?php echo $itens_por_pagina; ?>"><i class="fas fa-arrow-left"></i> Mês Anterior</a>
            <form method="GET" style="display: inline;">
                <input type="month" name="mes" value="<?php echo htmlspecialchars($mes); ?>" onchange="this.form.submit()">
                <select name="itens" onchange="this.form.submit()">
                    <option value="10" <?php echo $itens_por_pagina == 10 ? 'selected' : ''; ?>>10 por página</option>
                    <option value="100" <?php echo $itens_por_pagina == 100 ? 'selected' : ''; ?>>100 por página</option>
                    <option value="1000" <?php echo $itens_por_pagina == 1000 ? 'selected' : ''; ?>>1000 por página</option>
                </select>
            </form>
            <a href="?mes=<?php echo $mes_proximo; ?>&itens=<?php echo $itens_por_pagina; ?>"><i class="fas fa-arrow-right"></i> Mês Próximo</a>
            <a href="adicionar.php">Adicionar Despesa</a>
        </div>
        <table>
            <tr>
                <th>Data</th>
                <th>Categoria/Descrição</th>
                <th>Valor</th>
                <th>Empresa</th>
                <th>Parcela</th>
                <th>Status</th>
                <th>Dias para Vencer</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($despesas as $despesa): ?>
                <tr class="<?php echo $despesa['status_pagamento'] == 'pago' ? 'pago-row' : 'pendente-row'; ?>">
                    <td><?php echo date('d/m/Y', strtotime($despesa['data'])); ?></td>
                    <td><?php echo htmlspecialchars($despesa['categoria'] ? ($despesa['categoria'] . ' - ' . $despesa['descricao']) : $despesa['descricao']); ?></td>
                    <td>R$ <?php echo number_format($despesa['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($despesa['empresa'] ?: '-'); ?></td>
                    <td><?php echo $despesa['parcela_numero'] . '/' . $despesa['parcela_total']; ?></td>
                    <td class="<?php echo $despesa['status_pagamento'] == 'pago' ? 'pago' : 'pendente'; ?>">
                        <?php echo $despesa['status_pagamento'] == 'pago' ? 'Pago' : 'Pendente'; ?>
                    </td>
                    <td><?php echo $despesa['dias_vencimento'] >= 0 ? $despesa['dias_vencimento'] . ' dias' : 'Vencido'; ?></td>
                    <td>
                        <a href="editar.php?id=<?php echo $despesa['id']; ?>" class="action-btn editar"><i class="fas fa-edit"></i> Editar</a>
                        <a href="excluir.php?id=<?php echo $despesa['id']; ?>" class="action-btn excluir" onclick="return confirm('Confirmar exclusão?')"><i class="fas fa-trash"></i> Excluir</a>
                        <a href="atualizar_pagamento.php?id=<?php echo $despesa['id']; ?>&status=<?php echo $despesa['status_pagamento'] == 'pago' ? 'pendente' : 'pago'; ?>" 
                           class="action-btn status-<?php echo $despesa['status_pagamento'] == 'pago' ? 'pendente' : 'pago'; ?>">
                            <i class="fas <?php echo $despesa['status_pagamento'] == 'pago' ? 'fa-times' : 'fa-check'; ?>"></i> 
                            Marcar como <?php echo $despesa['status_pagamento'] == 'pago' ? 'Pendente' : 'Pago'; ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="paginacao">
            <?php if ($pagina > 1): ?>
                <a href="?mes=<?php echo $mes; ?>&pagina=<?php echo $pagina - 1; ?>&itens=<?php echo $itens_por_pagina; ?>">Anterior</a>
            <?php endif; ?>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginas; ?></span>
            <?php if ($pagina < $paginas): ?>
                <a href="?mes=<?php echo $mes; ?>&pagina=<?php echo $pagina + 1; ?>&itens=<?php echo $itens_por_pagina; ?>">Próximo</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
