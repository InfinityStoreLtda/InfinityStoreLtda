<?php ob_start(); ?>
<h3>Contrato #<?=$c['id']?> — <?=htmlspecialchars($c['cliente'])?></h3>
<p><b>Número:</b> <?=htmlspecialchars($c['numero']??'-')?> · <b>Processo:</b> <?=htmlspecialchars($c['processo']??'-')?> · <b>Empenho:</b> <?=htmlspecialchars($c['empenho']??'-')?></p>
<p><b>Vigência:</b> <?=htmlspecialchars($c['vigencia_inicio'])?> → <?=htmlspecialchars($c['vigencia_fim']??'-')?> · <b>Centro de Custo:</b> <?=htmlspecialchars($c['centro_custo']??'-')?></p>

<h5 class="mt-4">Itens</h5>
<table class="table table-sm"><thead><tr><th>Descrição</th><th>Qtd</th><th>Vlr Unit</th><th>Total</th></tr></thead><tbody>
<?php $total=0; foreach($items as $it): $t=(float)$it['qtd']*(float)$it['valor_unit']; $total+=$t; ?>
<tr>
  <td><?=htmlspecialchars($it['descricao'])?></td>
  <td><?=$it['qtd']?></td>
  <td>R$ <?=number_format((float)$it['valor_unit'],2,',','.')?></td>
  <td>R$ <?=number_format($t,2,',','.')?></td>
</tr>
<?php endforeach; ?></tbody>
<tfoot><tr><th colspan="3" class="text-end">Total</th><th>R$ <?=number_format($total,2,',','.')?></th></tr></tfoot>
</table>

<form method="post" action="/contracts/<?=$c['id']?>/items" class="row g-2 mt-3">
  <div class="col-md-5"><input required name="descricao" class="form-control" placeholder="Descrição"></div>
  <div class="col-md-2"><input name="unidade" class="form-control" value="un"></div>
  <div class="col-md-2"><input name="qtd" type="number" step="0.01" class="form-control" value="1"></div>
  <div class="col-md-2"><input required name="valor_unit" type="number" step="0.01" class="form-control" placeholder="0,00"></div>
  <div class="col-md-1"><button class="btn btn-success w-100">+</button></div>
</form>
<?php $content = ob_get_clean(); include __DIR__.'/../layout.php'; echo str_replace('{{content}}',$content,$layout??file_get_contents(__DIR__.'/../layout.php')); ?>
