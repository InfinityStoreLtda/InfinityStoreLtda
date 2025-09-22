<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../config.php';
$localConfigPath = __DIR__.'/../config.local.php';

if (is_file($localConfigPath)) {
  $localConfig = require $localConfigPath;
  if (is_array($localConfig)) {
    $config = array_replace_recursive($config, $localConfig);
  }
}
$app = new App\Bootstrap($config);
$invoice = new App\Models\Invoice($app);
$competencia = (new DateTime('first day of this month'))->format('Y-m-01');
$count = $invoice->generateMonthly($competencia);
file_put_contents(__DIR__.'/faturar.log', date('c') . " faturas=$count\n", FILE_APPEND);
