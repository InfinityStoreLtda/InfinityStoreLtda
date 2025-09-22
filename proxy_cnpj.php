<?php
// proxy_cnpj.php - Proxy para consultar CNPJ na API ReceitaWS

header('Content-Type: application/json');

$cnpj = isset($_GET['cnpj']) ? preg_replace('/\D/', '', $_GET['cnpj']) : '';
if (strlen($cnpj) !== 14) {
    http_response_code(400);
    echo json_encode(['error' => 'CNPJ inválido']);
    exit;
}

try {
    $url = "https://receitaws.com.br/v1/cnpj/$cnpj";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        http_response_code($httpCode);
        echo json_encode(['error' => 'Erro na API ReceitaWS']);
        exit;
    }

    echo $response;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao consultar API: ' . $e->getMessage()]);
}
?>