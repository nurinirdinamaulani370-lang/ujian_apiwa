<?php
session_start();

// Cek autentikasi user
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/fonnte.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $target = trim($_POST['target'] ?? '');
    $pesan  = trim($_POST['pesan'] ?? '');

    // Validasi data
    if (!$id || empty($target) || empty($pesan)) {
        header("Location: ../dashboard.php?status=error&message=" . urlencode("Semua field wajib diisi!"));
        exit();
    }

    try {
        // 1. Update data log di database log_whatsapp
        $stmtUpdate = $pdo->prepare("UPDATE log_whatsapp SET no_tujuan = :no_tujuan, pesan = :pesan WHERE id = :id");
        $stmtUpdate->execute([
            ':no_tujuan' => $target,
            ':pesan'     => $pesan,
            ':id'        => $id
        ]);

        // 2. Susun pesan notifikasi perubahan data
        $pesanNotif = "🔔 *NOTIFIKASI PERUBAHAN DATA LOG*\n\n" .
                     "Halo, data log Anda di sistem telah diperbarui.\n" .
                     "----------------------------------\n" .
                     "📱 *No. Tujuan:* " . $target . "\n" .
                     "💬 *Pesan Baru:* " . $pesan . "\n" .
                     "----------------------------------\n" .
                     "Terima kasih.";

        // 3. Kirim notifikasi WA ke nomor tujuan
        kirimWhatsApp($target, $pesanNotif);

        // 4. Redirect kembali ke dashboard dengan status sukses
        header("Location: ../dashboard.php?status=success&message=" . urlencode("Data log berhasil diperbarui dan notifikasi WA telah dikirim!"));
        exit();

    } catch (PDOException $e) {
        // Handle jika terjadi kesalahan database
        header("Location: ../dashboard.php?status=error&message=" . urlencode("Gagal memperbarui data: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}