<?php
// util/cnpj_lookup.php
header('Content-Type: application/json; charset=utf-8');

$cnpj = preg_replace('/\D/', '', $_GET['cnpj'] ?? '');
if (strlen($cnpj) !== 14) {
  http_response_code(400);
  echo json_encode(['error' => 'CNPJ inválido']);
  exit;
}

$endpoint = "https://brasilapi.com.br/api/cnpj/v1/{$cnpj}";
$ch = curl_init($endpoint);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CONNECTTIMEOUT => 8,
  CURLOPT_TIMEOUT => 12,
  CURLOPT_HTTPHEADER => ['Accept: application/json']
]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($res === false || $http >= 400) {
  http_response_code($http ?: 502);
  echo json_encode(['error' => $err ?: 'Falha ao consultar CNPJ']);
  exit;
}

echo $res;
