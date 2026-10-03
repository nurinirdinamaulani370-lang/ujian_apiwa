<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/fonnte.php';

$token = FONNTE_TOKEN;
$url = FONNTE_URL;

// Cek apakah form mengirim POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../kirim_wa.php");
    exit;
}

// Ambil data dari form
$target = trim($_POST['target'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validasi
if ($target === '' || $message === '') {
    header("Location: ../kirim_wa.php?error=Nomor dan pesan wajib diisi");
    exit;
}

// Bersihkan nomor
$target = preg_replace('/[^0-9]/', '', $target);

// Jika nomor diawali 0, ubah menjadi 62
if (substr($target, 0, 1) === '0') {
    $target = '62' . substr($target, 1);
}

// Data yang dikirim ke Fonnte
$data = [
    'target' => $target,
    'message' => $message,
    'countryCode' => '62'
];

// Kirim ke Fonnte menggunakan CURL
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($data),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'Content-Type: application/x-www-form-urlencoded'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Tentukan status
$status = 'failed';

if (!$curlError && $httpCode >= 200 && $httpCode < 300) {

    $result = json_decode($response, true);

    if (isset($result['status']) && $result['status'] === true) {
        $status = 'success';
    }
}

// Simpan ke log database
try {

    $stmt = $pdo->prepare("
        INSERT INTO log_whatsapp 
        (no_tujuan, pesan, status)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $target,
        $message,
        $status
    ]);

} catch (PDOException $e) {

    die("Gagal menyimpan log: " . $e->getMessage());
}

// Jika berhasil
if ($status === 'success') {

    header("Location: ../dashboard(2).php?success=Pesan WhatsApp berhasil dikirim");
    exit;

} else {

    header("Location: ../kirim_wa.php?error=Pesan gagal dikirim");
    exit;
}