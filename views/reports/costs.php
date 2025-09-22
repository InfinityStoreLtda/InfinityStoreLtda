<?php ob_start(); ?>
<h3>Custos por Cliente / Centro de Custo</h3>
<form class="row g-2 mb-3">
  <div class="col-auto"><input type="number" name="ano" class="form-control" value="<?=htmlspecialchars($_GET['ano'] ?? date('Y'))?>"></div>
  <div class="col-auto"><input type="number" name="mes" class="form-control" value="<?=htmlspecialchars($_GET['mes'] ?? date('n'))?>"></div>
  <div class="col-auto"><button class="btn btn-primary">Filtrar</button></div>
  <div class="col-auto"><a class="btn btn-outline-secondary" href="/reports/costs/export/pdf?ano=<?=htmlspecialchars($_GET['ano'] ?? date('Y'))?>&mes=<?=htmlspecialchars($_GET['mes'] ?? date('n'))?>">PDF</a></div>
  <div class="col-auto"><a class="btn btn-outline-secondary" href="/reports/costs/export/xls?ano=<?=htmlspecialchars($_GET['ano'] ?? date('Y'))?>&mes=<?=htmlspecialchars($_GET['mes'] ?? date('n'))?>">Excel</a></div>
</form>
<table class="table table-sm table-striped"><thead><tr><th>Cliente</th><th>Centro de Custo</th><th>Receita</th><th>Despesa</th></tr></thead><tbody>
<?php foreach (($rows ?? []) as $r): ?>
<tr>
  <td><?=htmlspecialchars($r['cliente'] ?? '-')?></td>
  <td><?=htmlspecialchars($r['centro_custo'] ?? '-')?></td>
  <td>R$ <?=number_format((float) $r['total_receita'], 2, ',', '.')?></td>
  <td>R$ <?=number_format((float) $r['total_despesa'], 2, ',', '.')?></td>
</tr>
<?php endforeach; ?></tbody></table>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/../layout.php') ?: '';
echo str_replace('{{content}}', $content, $layout);
