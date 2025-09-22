<?php ob_start(); ?>
<h3>Editar Contrato</h3>
<form method="post" action="/contracts/<?=$c['id']?>/update" class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Empresa Pagadora (ID)</label>
    <input name="empresa_pagadora_id" class="form-control" value="<?=htmlspecialchars($c['empresa_pagadora_id'])?>" required>
  </div>
  <div class="col-md-3"><label class="form-label">Número</label><input name="numero" class="form-control" value="<?=htmlspecialchars($c['numero']??'')?>"></div>
  <div class="col-md-3"><label class="form-label">Processo</label><input name="processo" class="form-control" value="<?=htmlspecialchars($c['processo']??'')?>"></div>
  <div class="col-md-3"><label class="form-label">Empenho</label><input name="empenho" class="form-control" value="<?=htmlspecialchars($c['empenho']??'')?>"></div>
  <div class="col-md-3"><label class="form-label">Vigência Início</label><input type="date" name="vigencia_inicio" class="form-control" value="<?=htmlspecialchars($c['vigencia_inicio'])?>" required></div>
  <div class="col-md-3"><label class="form-label">Vigência Fim</label><input type="date" name="vigencia_fim" class="form-control" value="<?=htmlspecialchars($c['vigencia_fim']??'')?>"></div>
  <div class="col-md-3"><label class="form-label">Centro de Custo (ID)</label><input name="centro_custo_id" class="form-control" value="<?=htmlspecialchars($c['centro_custo_id']??'')?>"></div>
  <div class="col-md-3"><label class="form-label">Valor Mensal</label><input type="number" step="0.01" name="valor_mensal" class="form-control" value="<?=htmlspecialchars($c['valor_mensal']??'')?>"></div>
  <div class="col-12"><button class="btn btn-primary">Salvar</button></div>
</form>
<?php $content = ob_get_clean(); include __DIR__.'/../layout.php'; echo str_replace('{{content}}',$content,$layout??file_get_contents(__DIR__.'/../layout.php')); ?>
