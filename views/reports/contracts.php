<?php ob_start(); ?>
<h3>Contratos Vigentes</h3>
<table class="table table-sm table-striped"><thead><tr>
  <th>Cliente</th><th>Nº</th><th>Processo</th><th>Empenho</th><th>Início</th><th>Fim</th><th>Valor Mensal</th><th>Status</th><th>Centro Custo</th>
</tr></thead><tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><?=htmlspecialchars($r['cliente'])?></td>
  <td><?=htmlspecialchars($r['numero']??'-')?></td>
  <td><?=htmlspecialchars($r['processo']??'-')?></td>
  <td><?=htmlspecialchars($r['empenho']??'-')?></td>
  <td><?=htmlspecialchars($r['vigencia_inicio'])?></td>
  <td><?=htmlspecialchars($r['vigencia_fim']??'-')?></td>
  <td>R$ <?=number_format((float)$r['valor_mensal'],2,',','.')?></td>
  <td><?=htmlspecialchars($r['status'])?></td>
  <td><?=htmlspecialchars($r['centro_custo']??'-')?></td>
</tr>
<?php endforeach; ?></tbody></table>
<?php $content = ob_get_clean(); include __DIR__.'/../layout.php'; echo str_replace('{{content}}',$content,$layout??file_get_contents(__DIR__.'/../layout.php')); ?>
