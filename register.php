<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/fonnte.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nama) || empty($email) || empty($no_hp) || empty($alamat) || empty($password)) {
        die("Semua field wajib diisi!");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid!");
    }

    try {
        $stmtCek = $pdo->prepare("SELECT id FROM users WHERE email = :email OR no_hp = :no_hp");
        $stmtCek->execute([
            ':email' => $email, 
            ':no_hp' => $no_hp
        ]);

        if ($stmtCek->rowCount() > 0) {
            die("Email atau Nomor WhatsApp sudah terdaftar di sistem!");
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmtInsert = $pdo->prepare("INSERT INTO users (nama, email, no_hp, alamat, password) VALUES (:nama, :email, :no_hp, :alamat, :password)");
        $stmtInsert->execute([
            ':nama'     => $nama,
            ':email'    => $email,
            ':no_hp'    => $no_hp,
            ':alamat'   => $alamat,
            ':password' => $hashedPassword
        ]);

        $pesanWA = "🎉 *REGISTRASI BERHASIL*\n\n" .
                   "Selamat! Kak *" . $nama . "* 👋\n\n" .
                   "Akun Anda telah berhasil terdaftar.\n" .
                   "----------------------------------\n" .
                   "📧 *Email:* " . $email . "\n" .
                   "📱 *WhatsApp:* " . $no_hp . "\n" .
                   "🏠 *Alamat:* " . $alamat . "\n" .
                   "----------------------------------\n\n" .
                   "Terima kasih telah bergabung bersama kami.";

        $waResult = kirimWhatsApp($no_hp, $pesanWA);

        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Status Registrasi</title>
        </head>
        <body>
            <h2>Registrasi Berhasil!</h2>
            <p>Data akun Anda telah disimpan ke sistem.</p>

            <?php if (!empty($waResult['status'])): ?>
                <p style="color: green;">Notifikasi WhatsApp telah berhasil dikirim ke <strong><?= htmlspecialchars($no_hp); ?></strong></p>
            <?php else: ?>
                <p style="color: orange;">Registrasi sukses, namun notifikasi WhatsApp gagal dikirim.</p>
            <?php endif; ?>

            <br>
            <a href="../index.html">Kembali ke Form Registrasi</a>
        </body>
        </html>
        <?php

    } catch (PDOException $e) {
        die("Detail Error Database: " . $e->getMessage());
    }
} else {
    header("Location: ../index.html");
    exit();
}