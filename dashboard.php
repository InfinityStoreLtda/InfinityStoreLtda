<?php
// dashboard.php - Exibe gráficos e listas de empresas com dívidas/receitas atrasadas

include_once __DIR__ . '/config.php';
verificarLogin();

// Consulta para despesas atrasadas
$stmt_despesas = $pdo->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'despesa' AND status_pagamento = 'pendente' AND data < CURDATE()");
$stmt_despesas->execute();
$total_despesas_atrasadas = $stmt_despesas->fetchColumn() ?: 0;

// Consulta para receitas atrasadas
$stmt_receitas = $pdo->prepare("SELECT SUM(valor) as total FROM transacoes WHERE tipo = 'receita' AND status_pagamento = 'pendente' AND data < CURDATE()");
$stmt_receitas->execute();
$total_receitas_atrasadas = $stmt_receitas->fetchColumn() ?: 0;

// Consulta para empresas com despesas atrasadas
$stmt_empresas_despesas = $pdo->prepare("SELECT DISTINCT ep.nome_empresa FROM transacoes t JOIN empresas_pagaveis ep ON t.empresa_id = ep.id WHERE t.tipo = 'despesa' AND t.status_pagamento = 'pendente' AND t.data < CURDATE() ORDER BY ep.nome_empresa");
$stmt_empresas_despesas->execute();
$empresas_despesas_atrasadas = $stmt_empresas_despesas->fetchAll(PDO::FETCH_COLUMN);

// Consulta para empresas com receitas atrasadas
$stmt_empresas_receitas = $pdo->prepare("SELECT DISTINCT ep.nome_empresa FROM transacoes t JOIN empresas_pagadoras ep ON t.empresa_id = ep.id WHERE t.tipo = 'receita' AND t.status_pagamento = 'pendente' AND t.data < CURDATE() ORDER BY ep.nome_empresa");
$stmt_empresas_receitas->execute();
$empresas_receitas_atrasadas = $stmt_empresas_receitas->fetchAll(PDO::FETCH_COLUMN);

// Dados para o gráfico
$data = [
    'labels' => ['Despesas Atrasadas', 'Receitas Atrasadas'],
    'datasets' => [
        [
            'label' => 'Valores Atrasados (R$)',
            'data' => [$total_despesas_atrasadas, $total_receitas_atrasadas],
            'backgroundColor' => ['#e74c3c', '#2ecc71'],
            'borderColor' => ['#c0392b', '#27ae60'],
            'borderWidth' => 1
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        h2, h3 {
            color: #2c3e50;
        }
        .resumo {
            margin-bottom: 20px;
            padding: 15px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        .resumo p {
            margin: 5px 0;
            font-size: 1.1em;
        }
        .resumo .despesas {
            color: #e74c3c;
        }
        .resumo .receitas {
            color: #2ecc71;
        }
        .grafico {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 300px;
            max-width: 600px;
        }
        .empresas {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 300px;
            max-width: 400px;
        }
        .empresas h3 {
            margin-top: 0;
        }
        .empresas ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .empresas ul li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .empresas ul li:last-child {
            border-bottom: none;
        }
        .no-data {
            color: #7f8c8d;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
                flex-direction: column;
            }
            .grafico, .empresas {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/sidebar.php'; ?>
    <div class="container">
        <div class="resumo">
            <h2>Dashboard</h2>
            <h3>Resumo de Atrasos</h3>
            <p class="despesas">Despesas Atrasadas: R$ <?php echo number_format($total_despesas_atrasadas, 2, ',', '.'); ?></p>
            <p class="receitas">Receitas Atrasadas: R$ <?php echo number_format($total_receitas_atrasadas, 2, ',', '.'); ?></p>
        </div>
        <div class="grafico">
            <canvas id="graficoAtrasos"></canvas>
        </div>
        <div class="empresas">
            <h3>Empresas com Dívidas Atrasadas</h3>
            <h4>Despesas a Pagar</h4>
            <ul>
                <?php if (empty($empresas_despesas_atrasadas)): ?>
                    <li class="no-data">Nenhuma empresa com despesas atrasadas</li>
                <?php else: ?>
                    <?php foreach ($empresas_despesas_atrasadas as $empresa): ?>
                        <li><?php echo htmlspecialchars($empresa); ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <h4>Receitas a Receber</h4>
            <ul>
                <?php if (empty($empresas_receitas_atrasadas)): ?>
                    <li class="no-data">Nenhuma empresa com receitas atrasadas</li>
                <?php else: ?>
                    <?php foreach ($empresas_receitas_atrasadas as $empresa): ?>
                        <li><?php echo htmlspecialchars($empresa); ?></li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <script>
        const ctx = document.getElementById('graficoAtrasos').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: <?php echo json_encode($data); ?>,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>
</body>
</html>
