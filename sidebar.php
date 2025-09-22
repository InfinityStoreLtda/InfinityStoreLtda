<?php
// sidebar.php - Menu lateral inspirado no Bitrix24

include_once __DIR__ . '/config.php';
verificarLogin();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2c3e50, #3498db);
            color: #ffffff;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-x: hidden;
            transition: width 0.3s ease;
            box-shadow: 3px 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: linear-gradient(180deg, #1e3a8a, #2980b9);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .sidebar-header h3 {
            margin: 0;
            font-size: 1.4em;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-header .toggle-btn {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.2em;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .sidebar-header .toggle-btn:hover {
            transform: rotate(90deg);
        }
        .sidebar-title {
            transition: opacity 0.3s;
        }
        .sidebar.collapsed .sidebar-title {
            opacity: 0;
            width: 0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin: 5px 0;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ffffff;
            text-decoration: none;
            transition: background 0.3s ease, padding-left 0.3s ease;
        }
        .sidebar-menu li a:hover {
            background: #ecf0f1;
            color: #2c3e50;
            padding-left: 25px;
        }
        .sidebar-menu li a i {
            width: 30px;
            text-align: center;
            font-size: 1.2em;
        }
        .sidebar-menu li a span {
            transition: opacity 0.3s;
        }
        .sidebar.collapsed .sidebar-menu li a span {
            opacity: 0;
            width: 0;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar.collapsed {
                width: 70px;
            }
            .sidebar-title, .sidebar-menu li a span {
                opacity: 0;
                width: 0;
            }
            .sidebar-header .toggle-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-wallet"></i> <span class="sidebar-title">Sistema Financeiro</span></h3>
            <button class="toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="/in/dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li><a href="/in/despesas/index.php"><i class="fas fa-arrow-down"></i> <span>Despesas</span></a></li>
            <li><a href="/in/receitas/index.php"><i class="fas fa-arrow-up"></i> <span>Receitas</span></a></li>
            <li><a href="/in/relatorios/index.php"><i class="fas fa-chart-bar"></i> <span>Relatórios</span></a></li>
            <li><a href="/in/configuracoes/index.php"><i class="fas fa-cog"></i> <span>Configurações</span></a></li>
            <li><a href="/in/usuarios/index.php"><i class="fas fa-users"></i> <span>Usuários</span></a></li>
            <li><a href="/in/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a></li>
        </ul>
    </nav>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const container = document.querySelector('.container');
            sidebar.classList.toggle('collapsed');
            if (sidebar.classList.contains('collapsed')) {
                container.style.marginLeft = '90px';
            } else {
                container.style.marginLeft = '270px';
            }
        }
    </script>
</body>
</html>
