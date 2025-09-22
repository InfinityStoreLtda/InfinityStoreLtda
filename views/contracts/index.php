<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Contratos</h3>
  <a href="/contracts/create" class="btn btn-primary">Novo contrato</a>
</div>
<table class="table table-sm table-striped"><thead><tr>
  <th>ID</th><th>Cliente</th><th>Número</th><th>Vigência</th><th>Centro de Custo</th><th></th>
</tr></thead><tbody>
<?php foreach (($rows ?? []) as $r): ?>
<tr>
  <td><?=$r['id']?></td>
  <td><?=htmlspecialchars($r['cliente'])?></td>
  <td><?=htmlspecialchars($r['numero'] ?? '-')?></td>
  <td><?=htmlspecialchars($r['vigencia_inicio'])?> → <?=htmlspecialchars($r['vigencia_fim'] ?? '-')?></td>
  <td><?=htmlspecialchars($r['centro_custo'] ?? '-')?></td>
  <td><a class="btn btn-sm btn-outline-secondary" href="/contracts/<?=$r['id']?>">Abrir</a></td>
</tr>
<?php endforeach; ?>
</tbody></table>
<?php
$content = ob_get_clean();
$layout = file_get_contents(__DIR__.'/../layout.php') ?: '';
echo str_replace('{{content}}', $content, $layout);
