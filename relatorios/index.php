<?php
// relatorios/index.php - Resumo financeiro com filtro de mês e paginação

include_once __DIR__ . '/../config.php';
verificarLogin();

// Filtro de mês
$mes_atual = date('Y-m');
$mes = isset($_GET['mes']) ? $_GET['mes'] : $mes_atual;
list($ano, $mes_num) = explode('-', $mes);

// Mês anterior e próximo
$mes_anterior = date('Y-m', strtotime('-1 month', strtotime($mes . '-01')));
$mes_proximo = date('Y-m', strtotime('+1 month', strtotime($mes . '-01')));

// Paginação
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$itens_por_pagina = 10;
$offset = ($pagina - 1) * $itens_por_pagina;

$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM transacoes WHERE DATE_FORMAT(data, '%Y-%m') = :mes");
$stmt_total->execute(['mes' => $mes]);
$total = $stmt_total->fetchColumn();
$paginas = ceil($total / $itens_por_pagina);

// Consulta de transações
$stmt = $pdo->prepare("
    SELECT t.*, c.nome as categoria, 
           CASE 
               WHEN t.tipo = 'receita' THEN ep.nome_empresa 
               WHEN t.tipo = 'despesa' THEN epg.nome_empresa 
               ELSE NULL 
           END as empresa,
           DATEDIFF(t.data, CURDATE()) as dias_vencimento
    FROM transacoes t
    LEFT JOIN categorias c ON t.categoria_id = c.id
    LEFT JOIN empresas_pagadoras ep ON t.empresa_id = ep.id AND t.tipo = 'receita'
    LEFT JOIN empresas_pagaveis epg ON t.empresa_id = epg.id AND t.tipo = 'despesa'
    WHERE DATE_FORMAT(t.data, '%Y-%m') = :mes
    ORDER BY t.data DESC
    LIMIT :offset, :limit
");
$stmt->bindValue(':mes', $mes, PDO::PARAM_STR);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$transacoes = $stmt->fetchAll();

// Resumo financeiro
$stmt_resumo = $pdo->prepare("SELECT tipo, SUM(valor) as total FROM transacoes WHERE DATE_FORMAT(data, '%Y-%m') = :mes GROUP BY tipo");
$stmt_resumo->execute(['mes' => $mes]);
$totais = $stmt_resumo->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios</title>
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
        h2, h3 {
            color: #2c3e50;
        }
        .filtro {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filtro a, .filtro input, .filtro button {
            padding: 10px;
            background: #3498db;
            color: #ffffff;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .filtro a:hover, .filtro button:hover {
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
        .resumo {
            margin-bottom: 20px;
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
            .filtro {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Relatórios</h2>
        <div class="filtro">
            <a href="?mes=<?php echo $mes_anterior; ?>"><i class="fas fa-arrow-left"></i> Mês Anterior</a>
            <form method="GET" style="display: inline;">
                <input type="month" name="mes" value="<?php echo $mes; ?>" onchange="this.form.submit()">
            </form>
            <a href="?mes=<?php echo $mes_proximo; ?>"><i class="fas fa-arrow-right"></i> Mês Próximo</a>
        </div>
        <div class="resumo">
            <h3>Resumo Financeiro</h3>
            <table>
                <tr>
                    <th>Tipo</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($totais as $total): ?>
                    <tr>
                        <td><?php echo ucfirst($total['tipo']); ?></td>
                        <td>R$ <?php echo number_format($total['total'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <h3>Transações</h3>
        <table>
            <tr>
                <th>Data</th>
                <th>Categoria/Descrição</th>
                <th>Valor</th>
                <th>Empresa</th>
                <th>Parcela</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Dias para Vencer</th>
            </tr>
            <?php foreach ($transacoes as $transacao): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($transacao['data'])); ?></td>
                    <td><?php echo htmlspecialchars($transacao['categoria'] ? ($transacao['categoria'] . ' - ' . $transacao['descricao']) : $transacao['descricao']); ?></td>
                    <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo htmlspecialchars($transacao['empresa'] ?: '-'); ?></td>
                    <td><?php echo $transacao['parcela_numero'] . '/' . $transacao['parcela_total']; ?></td>
                    <td><?php echo ucfirst($transacao['tipo']); ?></td>
                    <td class="<?php echo $transacao['status_pagamento'] == 'pago' ? 'pago' : 'pendente'; ?>">
                        <?php echo $transacao['status_pagamento'] == 'pago' ? 'Pago' : 'Pendente'; ?>
                    </td>
                    <td><?php echo $transacao['dias_vencimento'] >= 0 ? $transacao['dias_vencimento'] . ' dias' : 'Vencido'; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div class="paginacao">
            <?php if ($pagina > 1): ?>
                <a href="?mes=<?php echo $mes; ?>&pagina=<?php echo $pagina - 1; ?>">Anterior</a>
            <?php endif; ?>
            <span>Página <?php echo $pagina; ?> de <?php echo $paginas; ?></span>
            <?php if ($pagina < $paginas): ?>
                <a href="?mes=<?php echo $mes; ?>&pagina=<?php echo $pagina + 1; ?>">Próximo</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
