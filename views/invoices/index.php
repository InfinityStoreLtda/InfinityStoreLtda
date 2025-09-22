<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Faturas</h3>
  <form class="d-flex" method="post" action="/invoices/generate">
    <input class="form-control me-2" type="month" name="competencia" value="<?=date('Y-m')?>">
    <button class="btn btn-primary">Gerar mês</button>
  </form>
</div>
<table class="table table-sm table-striped"><thead><tr>
  <th>ID</th><th>Contrato</th><th>Cliente</th><th>Competência</th><th>Valor</th><th>Status</th><th></th>
</tr></thead><tbody>
<?php foreach($list as $f): ?>
<tr>
  <td><?=$f['id']?></td>
  <td><?=htmlspecialchars($f['contrato_numero']??('#'.$f['contrato_id']))?></td>
  <td><?=htmlspecialchars($f['cliente'])?></td>
  <td><?=htmlspecialchars($f['competencia'])?></td>
  <td>R$ <?=number_format((float)$f['valor_total'],2,',','.')?></td>
  <td><?=htmlspecialchars($f['status'])?></td>
  <td>
    <a class="btn btn-sm btn-outline-secondary" href="/invoices/<?=$f['id']?>">Abrir</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php $content = ob_get_clean(); include __DIR__.'/../layout.php'; echo str_replace('{{content}}',$content,$layout??file_get_contents(__DIR__.'/../layout.php')); ?>
