<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        header("Location: ../dashboard.php?status=error&message=" . urlencode("ID tidak ditemukan."));
        exit();
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM logs WHERE id = :id");
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            header("Location: ../dashboard.php?status=success&message=" . urlencode("Data berhasil dihapus!"));
        } else {
            header("Location: ../dashboard.php?status=error&message=" . urlencode("Data tidak ditemukan di database."));
        }
        exit();

    } catch (PDOException $e) {
        header("Location: ../dashboard.php?status=error&message=" . urlencode("Error: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}