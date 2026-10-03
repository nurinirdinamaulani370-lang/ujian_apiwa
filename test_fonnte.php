<?php

require_once __DIR__ . '/config/fonnte.php';

$nomor = '628XXXXXXXXXX';

$pesan = 'Tes WhatsApp dari aplikasi Ujian ASTS.';

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => FONNTE_URL,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'target' => $nomor,
        'message' => $pesan,
        'countryCode' => '62'
    ],
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . FONNTE_TOKEN
    ],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($curl);
$error = curl_error($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

curl_close($curl);

echo '<h2>Hasil Tes Fonnte</h2>';

echo '<p><strong>HTTP Code:</strong> ';
echo htmlspecialchars($httpCode);
echo '</p>';

if ($error) {
    echo '<p><strong>Error:</strong> ';
    echo htmlspecialchars($error);
    echo '</p>';
}

echo '<h3>Response Fonnte:</h3>';

echo '<pre>';
echo htmlspecialchars($response);
echo '</pre>';