<?php
namespace App\Controllers;

use App\Bootstrap;
use App\Models\FiscalNote;
use App\Models\Invoice;

class InvoicesController {
  private Invoice $invoices;
  private FiscalNote $notes;

  public function __construct(private Bootstrap $app) {
    $this->invoices = new Invoice($app);
    $this->notes = new FiscalNote($app);
  }

  public function index(): void {
    $list = $this->invoices->list();
    $this->render('invoices/index', compact('list'));
  }

  public function show(string $id): void {
    $f = $this->invoices->find((int) $id);
    if (!$f) {
      http_response_code(404);
      echo 'Fatura não encontrada';
      return;
    }
    $this->render('invoices/show', compact('f'));
  }

  public function generateMonthly(): void {
    $comp = $_POST['competencia'] ?? date('Y-m-01');
    if (!str_ends_with($comp, '-01')) {
      $comp .= '-01';
    }
    $count = $this->invoices->generateMonthly($comp);
    $_SESSION['flash'] = "Faturas geradas: $count";
    header('Location: /invoices');
  }

  public function emitNFe(string $id): void {
    $endpoint = rtrim((string) getenv('NFE_SERVICE_URL'), '/');
    if ($endpoint === '') {
      throw new \RuntimeException('NFE_SERVICE_URL não configurado');
    }
    $payload = ['fatura_id' => (int) $id, 'idempotency_key' => hash('sha256', 'nfe:' . $id)];
    $resp = $this->postJson($endpoint . '/emitir-nfe', $payload);
    $nfId = $this->notes->create([
      'tipo' => 'NFE', 'numero' => $resp['numero'] ?? null, 'serie' => $resp['serie'] ?? null,
      'chave' => $resp['chave'] ?? null, 'protocolo' => $resp['protocolo'] ?? null,
      'data_emissao' => $resp['data_emissao'] ?? null, 'status' => $resp['status'] ?? 'Autorizada',
      'xml_path' => $resp['xml_url'] ?? null, 'pdf_path' => $resp['pdf_url'] ?? null,
      'retorno' => json_encode($resp, JSON_THROW_ON_ERROR)
    ]);
    $this->invoices->markEmitted((int) $id, 'NFE', $nfId);
    header('Location: /invoices/' . $id);
  }

  public function emitNFSe(string $id): void {
    $endpoint = rtrim((string) getenv('NFE_SERVICE_URL'), '/');
    if ($endpoint === '') {
      throw new \RuntimeException('NFE_SERVICE_URL não configurado');
    }
    $payload = ['fatura_id' => (int) $id, 'idempotency_key' => hash('sha256', 'nfse:' . $id)];
    $resp = $this->postJson($endpoint . '/emitir-nfse', $payload);
    $nfId = $this->notes->create([
      'tipo' => 'NFSE', 'numero' => $resp['numero'] ?? null, 'serie' => $resp['serie'] ?? null,
      'chave' => $resp['chave'] ?? null, 'protocolo' => $resp['protocolo'] ?? null,
      'data_emissao' => $resp['data_emissao'] ?? null, 'status' => $resp['status'] ?? 'Autorizada',
      'xml_path' => $resp['xml_url'] ?? null, 'pdf_path' => $resp['pdf_url'] ?? null,
      'retorno' => json_encode($resp, JSON_THROW_ON_ERROR)
    ]);
    $this->invoices->markEmitted((int) $id, 'NFSE', $nfId);
    header('Location: /invoices/' . $id);
  }

  public function settle(string $id): void {
    $this->invoices->settle((int) $id);
    header('Location: /invoices/' . $id);
  }

  private function render(string $view, array $data = []): void {
    extract($data);
    $viewPath = __DIR__ . '/../../views/' . $view . '.php';
    if (!file_exists($viewPath)) {
      throw new \RuntimeException('View não encontrada: ' . $view);
    }
    include $viewPath;
  }

  private function postJson(string $url, array $data): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . ($this->jwt() ?? '')
      ],
      CURLOPT_POSTFIELDS => json_encode($data, JSON_THROW_ON_ERROR),
      CURLOPT_TIMEOUT => 20,
    ]);
    $res = curl_exec($ch);
    if ($res === false) {
      $err = curl_error($ch);
      curl_close($ch);
      throw new \RuntimeException($err ?: 'Erro ao chamar serviço de nota fiscal');
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code >= 300) {
      throw new \RuntimeException('HTTP ' . $code . ': ' . $res);
    }
    $decoded = json_decode($res, true);
    return is_array($decoded) ? $decoded : [];
  }

  private function jwt(): ?string {
    return $_ENV['ERP_JWT'] ?? null;
  }
}
