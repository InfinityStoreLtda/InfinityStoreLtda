<?php ob_start(); ?>
<h3>Fatura #<?=$f['id']?></h3>
<p><b>Competência:</b> <?=$f['competencia']?> · <b>Valor:</b> R$ <?=number_format((float)$f['valor_total'],2,',','.')?> · <b>Status:</b> <?=$f['status']?></p>
<form method="post" action="/invoices/<?=$f['id']?>/emit-nfe" class="d-inline"><button class="btn btn-success">Emitir NF-e</button></form>
<form method="post" action="/invoices/<?=$f['id']?>/emit-nfse" class="d-inline"><button class="btn btn-warning">Emitir NFS-e</button></form>
<form method="post" action="/invoices/<?=$f['id']?>/settle" class="d-inline"><button class="btn btn-secondary">Dar Baixa</button></form>
<?php $content = ob_get_clean(); include __DIR__.'/../layout.php'; echo str_replace('{{content}}',$content,$layout??file_get_contents(__DIR__.'/../layout.php')); ?>
