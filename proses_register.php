<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/fonnte.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.html");
    exit;
}

$nama     = trim($_POST['nama'] ?? '');
$email    = trim($_POST['email'] ?? '');
$no_hp    = trim($_POST['no_hp'] ?? '');
$alamat   = trim($_POST['alamat'] ?? '');
$password = $_POST['password'] ?? '';

if (
    empty($nama) ||
    empty($email) ||
    empty($no_hp) ||
    empty($alamat) ||
    empty($password)
) {
    die("Semua field wajib diisi!");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Format email tidak valid!");
}

try {

    // Cek email / nomor WhatsApp
    $stmtCek = $pdo->prepare("
        SELECT id
        FROM users
        WHERE email = :email OR no_hp = :no_hp
    ");

    $stmtCek->execute([
        ':email' => $email,
        ':no_hp' => $no_hp
    ]);

    if ($stmtCek->fetch()) {
        die("Email atau Nomor WhatsApp sudah terdaftar!");
    }

    // Hash password
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // Simpan user
    $stmtInsert = $pdo->prepare("
        INSERT INTO users
        (nama, email, no_hp, alamat, password)
        VALUES
        (:nama, :email, :no_hp, :alamat, :password)
    ");

    $stmtInsert->execute([
        ':nama'     => $nama,
        ':email'    => $email,
        ':no_hp'    => $no_hp,
        ':alamat'   => $alamat,
        ':password' => $hashedPassword
    ]);

    // Pesan WhatsApp
    $pesanWA =
        "🎉 *REGISTRASI BERHASIL*\n\n" .
        "Halo *" . $nama . "* 👋\n\n" .
        "Akun Anda berhasil didaftarkan.\n\n" .
        "📧 Email: " . $email . "\n" .
        "📱 WhatsApp: " . $no_hp . "\n" .
        "🏠 Alamat: " . $alamat . "\n\n" .
        "Terima kasih telah bergabung bersama kami.";

    // Kirim WhatsApp
    $waResult = kirimWhatsApp($no_hp, $pesanWA);

    $waBerhasil =
        isset($waResult['status']) &&
        $waResult['status'] === true;

    ?>

    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Registrasi Berhasil</title>

        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                font-family: Arial, sans-serif;
                background: #f1f5f9;
            }

            .card {
                width: 90%;
                max-width: 500px;
                background: white;
                padding: 35px;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(0,0,0,.12);
                text-align: center;
            }

            .icon {
                font-size: 60px;
                margin-bottom: 10px;
            }

            h2 {
                margin-bottom: 10px;
                color: #16a34a;
            }

            p {
                color: #475569;
                line-height: 1.6;
            }

            .success {
                background: #dcfce7;
                color: #166534;
                padding: 15px;
                border-radius: 10px;
                margin-top: 20px;
            }

            .warning {
                background: #fef3c7;
                color: #92400e;
                padding: 15px;
                border-radius: 10px;
                margin-top: 20px;
            }

            .button {
                display: inline-block;
                margin-top: 25px;
                padding: 12px 20px;
                background: #2563eb;
                color: white;
                text-decoration: none;
                border-radius: 10px;
            }

            .button:hover {
                background: #1d4ed8;
            }
        </style>
    </head>

    <body>

        <div class="card">

            <div class="icon">
                🎉
            </div>

            <h2>Registrasi Berhasil!</h2>

            <p>
                Data akun <strong><?= htmlspecialchars($nama); ?></strong>
                telah berhasil disimpan ke sistem.
            </p>

            <?php if ($waBerhasil): ?>

                <div class="success">
                    ✅ Notifikasi WhatsApp berhasil dikirim ke
                    <strong><?= htmlspecialchars($no_hp); ?></strong>
                </div>

            <?php else: ?>

                <div class="warning">
                    ⚠️ Registrasi berhasil, tetapi notifikasi
                    WhatsApp gagal dikirim.
                </div>

            <?php endif; ?>

            <a href="index.html" class="button">
                ← Kembali ke Registrasi
            </a>

        </div>

    </body>

    </html>

    <?php

} catch (PDOException $e) {

    die(
        "Terjadi kesalahan database: " .
        htmlspecialchars($e->getMessage())
    );
}