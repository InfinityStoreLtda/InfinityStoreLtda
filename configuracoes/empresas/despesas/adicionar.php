<?php
// configuracoes/empresas/despesas/adicionar.php - Adicionar empresa pagável (despesas)

include_once __DIR__ . '/../../../config.php';
verificarLogin();

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome_empresa'] ?? '');
    $cnpj     = preg_replace('/\D/', '', $_POST['cnpj'] ?? '');
    $contato  = trim($_POST['contato'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $status   = in_array($_POST['status'] ?? 'ativo', ['ativo','inativo']) ? $_POST['status'] : 'ativo';

    if ($nome === '') {
        $erro = 'Informe o nome da empresa.';
    } else {
        try {
            if ($cnpj) {
                $stmt = $pdo->prepare('SELECT id FROM empresas_pagaveis WHERE cnpj = :cnpj');
                $stmt->execute(['cnpj' => $cnpj]);
                if ($stmt->fetch()) { $erro = 'CNPJ já cadastrado.'; }
            }
            if (!$erro) {
                $stmt = $pdo->prepare('INSERT INTO empresas_pagaveis (nome_empresa, cnpj, contato, endereco, status) 
                                       VALUES (:nome, :cnpj, :contato, :endereco, :status)');
                $stmt->execute([
                    'nome'     => $nome,
                    'cnpj'     => $cnpj ?: null,
                    'contato'  => $contato ?: null,
                    'endereco' => $endereco ?: null,
                    'status'   => $status,
                ]);
                header('Location: index.php');
                exit;
            }
        } catch (PDOException $e) {
            $erro = 'Erro ao salvar: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Adicionar Empresa de Despesas</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<style>
@keyframes fadeIn { from { opacity: 0; transform: translateY(6px);} to { opacity: 1; transform: translateY(0);} }
body { font-family: Arial, sans-serif; margin: 0; background: #f4f7fc; }
.container { margin-left: 270px; padding: 20px; transition: margin-left 0.3s ease; animation: fadeIn .35s ease both; }
h2 { color: #2c3e50; }
form { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,.1); max-width: 560px; animation: fadeIn .4s ease both; }
.form-group { margin-bottom: 12px; }
label { display: block; margin-bottom: 6px; color: #2c3e50; }
input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
button[type="submit"] { width: 100%; padding: 12px; background: #3498db; color: #fff; border: 0; border-radius: 6px; cursor: pointer; transition: transform .15s ease, background .2s ease; }
button[type="submit"]:hover { background: #2c3e50; transform: translateY(-1px); }
.error { color: #e74c3c; margin-bottom: 10px; }
.inline { display:flex; gap:8px; align-items:center; }
.row { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
.btn-outlined { white-space:nowrap; padding: 10px 14px; border-radius:6px; border:1px solid #3498db; background:#ecf5fe; color:#0d5aa7; cursor:pointer; }
.btn-outlined:disabled{ opacity:.6; cursor:not-allowed; }
.hint { font-size: .9rem; color:#415a6b; margin:-4px 0 8px; min-height:1.2em; }
.badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:.78rem; background:#eef2f7; color:#2c3e50; margin-left:6px; border:1px solid transparent; }
.checkline { display:flex; gap:8px; align-items:center; margin-top:6px; }
@media (max-width: 768px) { .container { margin-left: 90px; } form { max-width: 100%; } .inline, .row { flex-direction:column; align-items:stretch; } }
</style>
</head>
<body>
<?php include_once __DIR__ . '/../../../sidebar.php'; ?>
<div class="container">
  <h2>Adicionar Empresa de Despesas</h2>
  <?php if ($erro): ?><p class="error"><?php echo htmlspecialchars($erro); ?></p><?php endif; ?>

  <form method="post" autocomplete="off" id="form-empresa">
    <div class="form-group">
      <label for="cnpj">CNPJ (opcional)
        <span class="badge" id="status-cnpj"></span>
      </label>
      <div class="row">
        <input
          type="text"
          id="cnpj"
          name="cnpj"
          placeholder="00.000.000/0000-00"
          maxlength="18"
          inputmode="numeric"
          value="<?php echo htmlspecialchars($_POST['cnpj'] ?? ''); ?>"
        >
        <button type="button" id="btn-buscar-cnpj" class="btn-outlined">
          <i class="fa-solid fa-magnifying-glass"></i> Buscar dados
        </button>
      </div>
      <div class="checkline">
        <input type="checkbox" id="sobrescrever" style="width:auto;">
        <label for="sobrescrever" style="margin:0;">Sobrescrever campos ao buscar</label>
      </div>
      <div class="hint" id="hint-cnpj">Digite o CNPJ e clique em “Buscar dados”.</div>
    </div>

    <div class="form-group">
      <label for="nome_empresa">Nome</label>
      <input type="text" id="nome_empresa" name="nome_empresa" required
             value="<?php echo htmlspecialchars($_POST['nome_empresa'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="contato">Contato (telefones)</label>
      <input type="text" id="contato" name="contato"
             value="<?php echo htmlspecialchars($_POST['contato'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="endereco">Endereço</label>
      <input type="text" id="endereco" name="endereco"
             value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>">
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="ativo"   <?php echo (($_POST['status'] ?? 'ativo')==='ativo')?'selected':''; ?>>Ativo</option>
        <option value="inativo" <?php echo (($_POST['status'] ?? '')==='inativo')?'selected':''; ?>>Inativo</option>
      </select>
    </div>

    <button type="submit"><i class="fas fa-save"></i> Salvar</button>
  </form>
</div>

<script>
// ===== Utilidades =====
const $ = (sel) => document.querySelector(sel);

const cnpjInput    = $('#cnpj');
const nomeInput    = $('#nome_empresa');
const contatoInput = $('#contato');
const endInput     = $('#endereco');
const statusSel    = $('#status');
const btnBuscar    = $('#btn-buscar-cnpj');
const hint         = $('#hint-cnpj');
const badge        = $('#status-cnpj');
const chkOverwrite = $('#sobrescrever');

function onlyDigits(v){ return (v||'').replace(/\D/g,''); }

// Máscara progressiva (não corta)
function formatCNPJ(v){
  const d = onlyDigits(v).slice(0,14);
  if (d.length <= 2)  return d;
  if (d.length <= 5)  return d.replace(/(\d{2})(\d+)/, '$1.$2');
  if (d.length <= 8)  return d.replace(/(\d{2})(\d{3})(\d+)/, '$1.$2.$3');
  if (d.length <= 12) return d.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, '$1.$2.$3/$4');
  return d.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2}).*/, '$1.$2.$3/$4-$5');
}

// Validação por dígitos verificadores
function isValidCNPJ(raw){
  const cnpj = onlyDigits(raw);
  if (cnpj.length !== 14) return false;
  if (/^(\d)\1{13}$/.test(cnpj)) return false;

  const calcDV = (base, pesos) => {
    let soma = 0;
    for (let i=0;i<pesos.length;i++) soma += parseInt(base[i],10) * pesos[i];
    const mod = soma % 11;
    return (mod < 2) ? 0 : 11 - mod;
  };
  const dv1 = calcDV(cnpj.slice(0,12), [5,4,3,2,9,8,7,6,5,4,3,2]);
  const dv2 = calcDV(cnpj.slice(0,12) + dv1, [6,5,4,3,2,9,8,7,6,5,4,3,2]);
  return cnpj.endsWith(`${dv1}${dv2}`);
}

function setBadge(text, ok=true){
  if(!text){ badge.textContent=''; badge.style.borderColor='transparent'; return; }
  badge.textContent = text;
  badge.style.background = ok ? '#e8fff1' : '#fff0f0';
  badge.style.color = ok ? '#0f6f3d' : '#b42318';
  badge.style.border = ok ? '1px solid #b7f0cf' : '1px solid #ffd1d1';
}
function setHint(text, mode='info'){
  hint.textContent = text;
  const colors = { info:'#415a6b', wait:'#0d5aa7', ok:'#0f6f3d', warn:'#b45309', err:'#b42318' };
  hint.style.color = colors[mode] || colors.info;
}

// Eventos do CNPJ
if (cnpjInput){
  cnpjInput.addEventListener('input', () => {
    const caret = cnpjInput.selectionStart;
    const prev  = cnpjInput.value;
    cnpjInput.value = formatCNPJ(prev);
    setBadge('');
    try { cnpjInput.setSelectionRange(caret, caret); } catch(e){}
  });
  cnpjInput.addEventListener('blur', () => {
    const raw = cnpjInput.value;
    const d   = onlyDigits(raw);

    if(!d){ cnpjInput.setCustomValidity(''); setHint('CNPJ é opcional.', 'info'); return; }

    if(d.length !== 14){
      cnpjInput.setCustomValidity('CNPJ inválido. Deve conter 14 dígitos.');
      setHint('CNPJ inválido. Deve conter 14 dígitos.', 'err');
    } else if (!isValidCNPJ(raw)) {
      cnpjInput.setCustomValidity('CNPJ inválido (dígitos verificadores não conferem).');
      setHint('CNPJ inválido (dígitos verificadores não conferem).', 'err');
    } else {
      cnpjInput.setCustomValidity('');
      setHint('CNPJ válido. Você pode buscar os dados.', 'ok');
    }
  });
}

// Consulta BrasilAPI e preenche
async function buscarCNPJ(){
  const cnpj = onlyDigits(cnpjInput.value);

  if(!cnpj){ setHint('Informe um CNPJ para buscar.', 'warn'); cnpjInput.focus(); return; }
  if(cnpj.length !== 14){ setHint('CNPJ inválido. Deve conter 14 dígitos.', 'err'); cnpjInput.focus(); return; }
  if(!isValidCNPJ(cnpj)){ setHint('CNPJ inválido (dígitos verificadores não conferem).', 'err'); cnpjInput.focus(); return; }

  try{
    btnBuscar.disabled = true;
    setBadge('consultando…', true);
    setHint('Consultando dados do CNPJ…', 'wait');

    // Direto na BrasilAPI (CORS liberado). Se preferir, use um proxy no servidor.
    const url = `https://brasilapi.com.br/api/cnpj/v1/${cnpj}`;
    // const url = `/util/cnpj_lookup.php?cnpj=${cnpj}`; // (se usar proxy)

    const resp = await fetch(url, { headers: { 'Accept':'application/json' }});
    if(!resp.ok) throw new Error(`Falha na consulta (HTTP ${resp.status})`);
    const data = await resp.json();

    const nome = data.razao_social || data.nome_fantasia || '';
    const telefones = [data.ddd_telefone_1, data.ddd_telefone_2]
      .filter(Boolean).map(String).map(t => t.replace(/\D/g,''))
      .map(t => t.length>=10 ? `(${t.slice(0,2)}) ${t.slice(2,7)}-${t.slice(7,11)}` : t)
      .join(' / ');

    const partesEndereco = [
      [data.logradouro, data.numero].filter(Boolean).join(', '),
      data.bairro,
      [data.municipio, data.uf].filter(Boolean).join(' - '),
      data.cep ? data.cep.replace(/^(\d{5})(\d{3})$/,'$1-$2') : '',
      data.complemento
    ].filter(Boolean);
    const enderecoFmt = partesEndereco.join(' | ');

    const overwrite = !!(chkOverwrite && chkOverwrite.checked);

    if (nome && (overwrite || !nomeInput.value)) nomeInput.value = nome;
    if (telefones && (overwrite || !contatoInput.value)) contatoInput.value = telefones;
    if (enderecoFmt && (overwrite || !endInput.value)) endInput.value = enderecoFmt;

    const situacao = (data.descricao_situacao_cadastral || '').toUpperCase();
    if(situacao.includes('ATIVA')){
      statusSel.value = 'ativo';
      setBadge('Situação: ATIVA', true);
    } else if (situacao){
      statusSel.value = 'inativo';
      setBadge(`Situação: ${situacao}`, false);
    } else {
      setBadge('');
    }

    setHint(overwrite ? 'Campos atualizados com os dados do CNPJ.' : 'Dados buscados. Campos vazios foram preenchidos.', 'ok');
  } catch(err){
    console.error(err);
    setBadge('erro', false);
    setHint('Não foi possível consultar o CNPJ agora. Verifique o número ou tente mais tarde.', 'err');
  } finally {
    btnBuscar.disabled = false;
  }
}

if (btnBuscar){
  btnBuscar.addEventListener('click', buscarCNPJ);
}
</script>
</body>
</html>
