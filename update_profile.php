<?php
session_start();
// Sesuaikan dengan file koneksi database kamu
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    $alamat   = trim($_POST['alamat'] ?? '');

    if (empty($nama) || empty($email) || empty($whatsapp)) {
        header("Location: ../profile.php?status=error&message=Data tidak boleh kosong");
        exit();
    }

    // Sesuaikan ID user dari session login kamu (misal: $_SESSION['user_id'])
    $user_id = $_SESSION['user_id'] ?? 1;

    try {
        // Eksekusi query update ke tabel users
        $stmt = $pdo->prepare("UPDATE users SET nama = ?, email = ?, whatsapp = ?, alamat = ? WHERE id = ?");
        $executed = $stmt->execute([$nama, $email, $whatsapp, $alamat, $user_id]);

        if ($executed) {
            header("Location: ../profile.php?status=success&message=Profil berhasil diperbarui!");
            exit();
        } else {
            header("Location: ../profile.php?status=error&message=Gagal memperbarui database.");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../profile.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../profile.php");
    exit();
}