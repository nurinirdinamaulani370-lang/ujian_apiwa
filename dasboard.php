<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/database.php';

// Ambil data log WhatsApp
$logs = [];

try {
    $stmt = $pdo->query("
        SELECT *
        FROM log_whatsapp
        ORDER BY id DESC
        LIMIT 50
    ");

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $logs = [];
}

// Hitung statistik
$total = count($logs);

$success = 0;
$failed = 0;

foreach ($logs as $log) {

    if (($log['status'] ?? '') === 'success') {
        $success++;
    }

    if (($log['status'] ?? '') === 'failed') {
        $failed++;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>API WA Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="bg-slate-100 min-h-screen">

    <!-- SIDEBAR -->

    <aside class="fixed left-0 top-0 h-screen w-64 bg-slate-900 text-white">

        <div class="p-6 border-b border-slate-700">

            <h1 class="text-2xl font-bold">
                API WA Panel
            </h1>

            <p class="text-slate-400 text-sm mt-1">
                ASTS ADMIN
            </p>

        </div>


        <nav class="p-4 space-y-2">

            <a href="dashboard.php"
               class="block px-4 py-3 rounded-xl bg-green-600 font-semibold">
                🏠 Dashboard
            </a>

            <a href="kirim_wa.php"
               class="block px-4 py-3 rounded-xl hover:bg-slate-800 transition">
                💬 Kirim Pesan
            </a>

            <a href="#log"
               class="block px-4 py-3 rounded-xl hover:bg-slate-800 transition">
                📋 Riwayat Log
            </a>

        </nav>


        <div class="absolute bottom-0 left-0 right-0 p-4">

            <a href="logout.php"
               class="block text-center bg-red-600 hover:bg-red-700 py-3 rounded-xl transition">
                Logout
            </a>

        </div>

    </aside>


    <!-- MAIN -->

    <main class="ml-64 p-8">

        <!-- HEADER -->

        <div class="flex justify-between items-center mb-8">

            <div>

                <h2 class="text-3xl font-bold text-slate-800">
                    Dashboard
                </h2>

                <p class="text-slate-500 mt-1">
                    Kelola pengiriman WhatsApp melalui API Fonnte.
                </p>

            </div>


            <a href="kirim_wa.php"
               class="bg-green-600 hover:bg-green-700 text-white px-5 py-3 rounded-xl font-semibold transition">

                + Kirim WA Baru

            </a>

        </div>


        <!-- STATISTIK -->

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <!-- TOTAL -->

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-500 text-sm">
                    Total Pesan
                </p>

                <h3 class="text-4xl font-bold text-slate-800 mt-2">
                    <?= $total ?>
                </h3>

            </div>


            <!-- BERHASIL -->

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-500 text-sm">
                    Berhasil
                </p>

                <h3 class="text-4xl font-bold text-green-600 mt-2">
                    <?= $success ?>
                </h3>

            </div>


            <!-- GAGAL -->

            <div class="bg-white rounded-2xl shadow-sm p-6">

                <p class="text-slate-500 text-sm">
                    Gagal
                </p>

                <h3 class="text-4xl font-bold text-red-600 mt-2">
                    <?= $failed ?>
                </h3>

            </div>

        </div>


        <!-- LOG -->

        <div id="log"
             class="bg-white rounded-2xl shadow-sm overflow-hidden">

            <div class="p-6 border-b">

                <h3 class="text-xl font-bold text-slate-800">
                    Riwayat Pengiriman
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    50 pengiriman terakhir
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                No
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Nomor Tujuan
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Pesan
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Status
                            </th>

                            <th class="text-left px-6 py-4 text-sm font-semibold text-slate-600">
                                Waktu
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        <?php if (count($logs) > 0): ?>

                            <?php foreach ($logs as $index => $log): ?>

                                <tr class="hover:bg-slate-50">

                                    <td class="px-6 py-4 text-slate-600">
                                        <?= $index + 1 ?>
                                    </td>

                                    <td class="px-6 py-4 font-medium text-slate-800">
                                        <?= htmlspecialchars($log['no_tujuan'] ?? '-') ?>
                                    </td>

                                    <td class="px-6 py-4 text-slate-600 max-w-md">
                                        <?= htmlspecialchars($log['pesan'] ?? '-') ?>
                                    </td>

                                    <td class="px-6 py-4">

                                        <?php if (($log['status'] ?? '') === 'success'): ?>

                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                                Berhasil
                                            </span>

                                        <?php else: ?>

                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                                Gagal
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="px-6 py-4 text-sm text-slate-500">
                                        <?= htmlspecialchars($log['created_at'] ?? '-') ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="5"
                                    class="px-6 py-12 text-center text-slate-400">

                                    Belum ada riwayat pengiriman WhatsApp.

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</body>

</html>
