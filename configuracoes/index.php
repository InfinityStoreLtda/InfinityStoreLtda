<?php
// configuracoes/index.php - Submenus para empresas, categorias e logo

include_once __DIR__ . '/../config.php';
verificarLogin();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
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
        .tab {
            overflow: hidden;
            border-bottom: 2px solid #3498db;
            margin-bottom: 20px;
        }
        .tab button {
            background: #ffffff;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 1em;
            color: #2c3e50;
            transition: background 0.3s, color 0.3s;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }
        .tab button:hover {
            background: #ecf0f1;
        }
        .tab button.active {
            background: #3498db;
            color: #ffffff;
        }
        .tabcontent {
            display: none;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .tabcontent.active {
            display: block;
        }
        .menu-btn {
            display: inline-block;
            padding: 10px 15px;
            background: #3498db;
            color: #ffffff;
            border-radius: 5px;
            margin-bottom: 10px;
            margin-right: 10px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .menu-btn:hover {
            background: #2c3e50;
        }
        @media (max-width: 768px) {
            .container {
                margin-left: 90px;
            }
            .tab button {
                padding: 8px 12px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <?php include_once __DIR__ . '/../sidebar.php'; ?>
    <div class="container">
        <h2>Configurações</h2>
        <div class="tab">
            <button class="tablinks active" onclick="openTab(event, 'Empresas')">Empresas</button>
            <button class="tablinks" onclick="openTab(event, 'Categorias')">Categorias</button>
            <button class="tablinks" onclick="openTab(event, 'Geral')">Geral</button>
        </div>
        <div id="Empresas" class="tabcontent active">
            <h3>Gerenciamento de Empresas</h3>
            <a href="/in/configuracoes/empresas/despesas/index.php" class="menu-btn"><i class="fas fa-arrow-down"></i> Empresas de Despesas</a>
            <a href="/in/configuracoes/empresas/receitas/index.php" class="menu-btn"><i class="fas fa-arrow-up"></i> Empresas de Receitas</a>
        </div>
        <div id="Categorias" class="tabcontent">
            <h3>Gerenciamento de Categorias</h3>
            <a href="/in/configuracoes/categorias/receitas/index.php" class="menu-btn"><i class="fas fa-arrow-up"></i> Categorias de Receitas</a>
            <a href="/in/configuracoes/categorias/despesas/index.php" class="menu-btn"><i class="fas fa-arrow-down"></i> Categorias de Despesas</a>
        </div>
        <div id="Geral" class="tabcontent">
            <h3>Configurações Gerais</h3>
            <a href="/in/configuracoes/logo.php" class="menu-btn"><i class="fas fa-image"></i> Configurar Logo de Entrada</a>
        </div>
    </div>
    <script>
        function openTab(evt, tabName) {
            const tabcontent = document.getElementsByClassName("tabcontent");
            for (let i = 0; i < tabcontent.length; i++) {
                tabcontent[i].classList.remove("active");
            }
            const tablinks = document.getElementsByClassName("tablinks");
            for (let i = 0; i < tablinks.length; i++) {
                tablinks[i].classList.remove("active");
            }
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }
        window.onload = function() {
            document.getElementsByClassName("tablinks")[0].click();
        };
    </script>
</body>
</html>
