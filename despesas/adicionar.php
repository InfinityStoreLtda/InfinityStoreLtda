<?php
// despesas/adicionar.php - Adicionar despesa com parcelamento

include_once __DIR__ . '/../config.php';
verificarLogin();

$stmt = $pdo->query("SELECT * FROM categorias WHERE tipo = 'despesa' AND status = 'ativo'");
$categorias = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM empresas_pagaveis WHERE status = 'ativo'");
$empresas = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $categoria_id = $_POST['categoria_id'] ?: null;
    $descricao = $_POST['descricao'];
    $valor_total = floatval($_POST['valor_total']);
    $data_inicio = $_POST['data_inicio'];
    $tipo_pagamento = $_POST['tipo_pagamento'];
    $num_parcelas = ($tipo_pagamento == 'unitario') ? 1 : (int)$_POST['num_parcelas'];
    $empresa_id = $_POST['empresa_id'] ?: null;

    for ($i = 1; $i <= $num_parcelas; $i++) {
        $valor_parcela = $valor_total / $num_parcelas;
        $data_parcela = date('Y-m-d', strtotime($data_inicio . ' + ' . ($i - 1) . ' months'));

        $stmt = $pdo->prepare("INSERT INTO transacoes (tipo, descricao, valor, data, usuario_id, categoria_id, empresa_id, status_pagamento, parcela_numero, parcela_total) VALUES ('despesa', :descricao, :valor, :data, :usuario_id, :categoria_id, :empresa_id, 'pendente', :parcela_numero, :parcela_total)");
        $stmt->execute([
            'descricao' => $categoria_id ? ($categorias[array_search($categoria_id, array_column($categorias, 'id'))]['nome'] . ' - ' . $descricao) : $descricao,
            'valor' => $valor_parcela,
            'data' => $data_parcela,
            'usuario_id' => $_SESSION['usuario_id'],
            'categoria_id' => $categoria_id,
            'empresa_id' => $empresa_id,
            'parcela_numero' => $i,
            'parcela_total' => $num_parcelas
        ]);
    }
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Despesa</title>
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
        #parcelasFields {
            display: none;
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
        <h2>Adicionar Despesa</h2>
        <form method="POST">
            <div class="form-group">
                <label for="empresa_id">Empresa:</label>
                <select id="empresa_id" name="empresa_id" required>
                    <option value="">Selecione uma empresa</option>
                    <?php foreach ($empresas as $empresa): ?>
                        <option value="<?php echo $empresa['id']; ?>"><?php echo htmlspecialchars($empresa['nome_empresa']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoria/Descrição:</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sem categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nome']); ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="descricao" placeholder="Descrição adicional" style="margin-top: 5px;">
            </div>
            <div class="form-group">
                <label for="valor_total">Valor Total:</label>
                <input type="number" id="valor_total" name="valor_total" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="data_inicio">Data da Primeira Parcela:</label>
                <input type="date" id="data_inicio" name="data_inicio" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label for="tipo_pagamento">Tipo de Pagamento:</label>
                <select id="tipo_pagamento" name="tipo_pagamento" onchange="toggleParcelas()" required>
                    <option value="unitario">Unitário</option>
                    <option value="parcelado">Parcelado</option>
                </select>
            </div>
            <div id="parcelasFields" class="form-group">
                <label for="num_parcelas">Número de Parcelas:</label>
                <input type="number" id="num_parcelas" name="num_parcelas" min="2" value="2">
            </div>
            <button type="submit"><i class="fas fa-plus"></i> Adicionar</button>
        </form>
    </div>
    <script>
        function toggleParcelas() {
            const tipo = document.getElementById('tipo_pagamento').value;
            document.getElementById('parcelasFields').style.display = tipo === 'parcelado' ? 'block' : 'none';
        }
        toggleParcelas();
    </script>
</body>
</html>
