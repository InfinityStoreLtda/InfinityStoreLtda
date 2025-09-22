<?php
declare(strict_types=1);
require __DIR__.'/../vendor/autoload.php';
$config = require __DIR__.'/../config.php';
$app = new App\Bootstrap($config);
$invoice = new App\Models\Invoice($app);
$competencia = (new DateTime('first day of this month'))->format('Y-m-01');
$count = $invoice->generateMonthly($competencia);
file_put_contents(__DIR__.'/faturar.log', date('c')." faturas=$count\n", FILE_APPEND);
