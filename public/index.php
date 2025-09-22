<?php
declare(strict_types=1);

use App\Router;

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../config.php';
$localConfigPath = __DIR__.'/../config.local.php';

if (is_file($localConfigPath)) {
  $localConfig = require $localConfigPath;
  if (is_array($localConfig)) {
    $config = array_replace_recursive($config, $localConfig);
  }
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

date_default_timezone_set($config['app']['timezone']);

$router = new Router($config);

// Rotas contratos
$router->get('/', fn() => header('Location: /contracts'));
$router->get('/contracts', [App\Controllers\ContractsController::class, 'index']);
$router->get('/contracts/create', [App\Controllers\ContractsController::class, 'create']);
$router->post('/contracts', [App\Controllers\ContractsController::class, 'store']);
$router->get('/contracts/{id}', [App\Controllers\ContractsController::class, 'show']);
$router->get('/contracts/{id}/edit', [App\Controllers\ContractsController::class, 'edit']);
$router->post('/contracts/{id}/update', [App\Controllers\ContractsController::class, 'update']);
$router->post('/contracts/{id}/items', [App\Controllers\ContractsController::class, 'addItem']);

// Rotas faturas
$router->get('/invoices', [App\Controllers\InvoicesController::class, 'index']);
$router->get('/invoices/{id}', [App\Controllers\InvoicesController::class, 'show']);
$router->post('/invoices/generate', [App\Controllers\InvoicesController::class, 'generateMonthly']);
$router->post('/invoices/{id}/emit-nfe', [App\Controllers\InvoicesController::class, 'emitNFe']);
$router->post('/invoices/{id}/emit-nfse', [App\Controllers\InvoicesController::class, 'emitNFSe']);
$router->post('/invoices/{id}/settle', [App\Controllers\InvoicesController::class, 'settle']);

// Rotas relatórios
$router->get('/reports/costs', [App\Controllers\ReportsController::class, 'costs']);
$router->get('/reports/contracts', [App\Controllers\ReportsController::class, 'contracts']);
$router->get('/reports/costs/export/pdf', [App\Controllers\ReportsController::class, 'exportCostsPDF']);
$router->get('/reports/costs/export/xls', [App\Controllers\ReportsController::class, 'exportCostsXLS']);

$router->dispatch();
