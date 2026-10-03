<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        header("Location: ../login.php?status=error&message=Email dan password wajib diisi");
        exit();
    }

    try {
        // Cari user berdasarkan email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password (menggunakan password_verify jika di-hash)
        if ($user && password_verify($password, $user['password'])) {
            
            // 1. Simpan data user ke dalam Session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['email']     = $user['email'];

            // 2. Redirect/Arahkan langsung ke halaman Dashboard
            header("Location: ../dashboard.php");
            exit();

        } else {
            header("Location: ../login.php?status=error&message=Email atau password salah");
            exit();
        }

    } catch (PDOException $e) {
        header("Location: ../login.php?status=error&message=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: ../login.php");
    exit();
}