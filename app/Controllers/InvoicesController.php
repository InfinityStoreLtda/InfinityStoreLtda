<?php
namespace App\Controllers;
use App\Bootstrap; use App\Models\Invoice; use App\Models\FiscalNote;

class InvoicesController {
  private Invoice $invoices; private FiscalNote $notes;
  public function __construct(private Bootstrap $app){
    $this->invoices = new Invoice($app); $this->notes = new FiscalNote($app);
  }
  public function index(){ $list=$this->invoices->list(); include __DIR__.'/../../views/invoices/index.php'; }
  public function show($id){ $f=$this->invoices->find((int)$id); include __DIR__.'/../../views/invoices/show.php'; }
  public function generateMonthly(){
    $comp = $_POST['competencia'] ?? date('Y-m-01');
    $count = $this->invoices->generateMonthly($comp);
    $_SESSION['flash'] = "Faturas geradas: $count"; header('Location: /invoices');
  }
  public function emitNFe($id){
    // Chamada ao microserviço NFe/NFSe via cURL (exemplo simplificado)
    $endpoint = getenv('NFE_SERVICE_URL');
    $payload = ['fatura_id'=>(int)$id, 'idempotency_key'=>hash('sha256', 'nfe:'.$id)];
    $resp = $this->postJson($endpoint.'/emitir-nfe', $payload);
    $nfId = $this->notes->create([
      'tipo'=>'NFE','numero'=>$resp['numero']??null,'serie'=>$resp['serie']??null,
      'chave'=>$resp['chave']??null,'protocolo'=>$resp['protocolo']??null,
      'data_emissao'=>$resp['data_emissao']??null,'status'=>$resp['status']??'Autorizada',
      'xml_path'=>$resp['xml_url']??null,'pdf_path'=>$resp['pdf_url']??null,
      'retorno'=>json_encode($resp)
    ]);
    $this->invoices->markEmitted((int)$id,'NFE',$nfId);
    header('Location: /invoices/'.$id);
  }
  public function emitNFSe($id){ /* igual ao emitNFe mas endpoint diferente */ }
  public function settle($id){ $this->invoices->settle((int)$id); header('Location: /invoices/'.$id); }

  private function postJson(string $url,array $data){
    $ch=curl_init($url);
    curl_setopt_array($ch,[CURLOPT_POST=>true,CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>[
      'Content-Type: application/json',
      'Authorization: Bearer '.($this->jwt() ?? '')
    ],CURLOPT_POSTFIELDS=>json_encode($data),CURLOPT_TIMEOUT=>20]);
    $res=curl_exec($ch); if($res===false) throw new \Exception(curl_error($ch));
    $code=curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    if($code>=300) throw new \Exception("HTTP $code: $res");
    return json_decode($res,true) ?? [];
  }
  private function jwt(){ return $_ENV['ERP_JWT'] ?? null; }
}
