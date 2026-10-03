<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kirim WhatsApp - API WA Panel</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen">

    <!-- HEADER -->
    <header class="bg-slate-900 text-white shadow-lg">
        <div class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">

            <div>
                <h1 class="text-2xl font-bold">
                    API WA Panel
                </h1>

                <p class="text-slate-400 text-sm">
                    Kirim Pesan WhatsApp
                </p>
            </div>

            <a href="dashboard(2).php"
               class="bg-slate-700 hover:bg-slate-600 px-4 py-2 rounded-lg transition">
                ← Dashboard
            </a>

        </div>
    </header>


    <!-- CONTENT -->
    <main class="max-w-5xl mx-auto px-6 py-10">

        <!-- JUDUL -->
        <div class="mb-8">

            <h2 class="text-3xl font-bold text-slate-800">
                Kirim WA Baru
            </h2>

            <p class="text-slate-500 mt-2">
                Kirim pesan WhatsApp melalui API Fonnte.
            </p>

        </div>


        <!-- PESAN ERROR -->
        <?php if ($error): ?>

            <div class="mb-6 bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-xl">
                ❌ <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <!-- PESAN SUCCESS -->
        <?php if ($success): ?>

            <div class="mb-6 bg-green-100 border border-green-300 text-green-700 px-5 py-4 rounded-xl">
                ✅ <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <!-- FORM -->
        <div class="bg-white rounded-2xl shadow-lg p-8">

            <form action="api/whatsapp.php" method="POST" id="formKirim">

                <!-- NOMOR -->
                <div class="mb-6">

                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nomor WhatsApp
                    </label>

                    <input
                        type="text"
                        name="target"
                        id="target"
                        placeholder="Contoh: 081234567890"
                        required
                        autocomplete="off"
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-green-500
                               focus:border-transparent">

                    <p class="text-xs text-slate-500 mt-2">
                        Bisa menggunakan format 08xxxxxxxxxx atau 62xxxxxxxxxx.
                    </p>

                </div>


                <!-- PESAN -->
                <div class="mb-6">

                    <div class="flex justify-between items-center mb-2">

                        <label class="block text-sm font-semibold text-slate-700">
                            Pesan WhatsApp
                        </label>

                        <span
                            id="counter"
                            class="text-xs text-slate-500">
                            0 / 1000
                        </span>

                    </div>

                    <textarea
                        name="message"
                        id="message"
                        rows="8"
                        maxlength="1000"
                        placeholder="Tulis pesan WhatsApp di sini..."
                        required
                        class="w-full px-4 py-3 border border-slate-300 rounded-xl
                               focus:outline-none focus:ring-2 focus:ring-green-500
                               focus:border-transparent resize-none"></textarea>

                </div>


                <!-- BUTTON -->
                <div class="flex gap-3">

                    <a
                        href="dashboard(2).php"
                        class="flex-1 text-center bg-slate-200 hover:bg-slate-300
                               text-slate-700 font-semibold py-3 rounded-xl transition">
                        Batal
                    </a>

                    <button
                        type="submit"
                        id="btnKirim"
                        class="flex-1 bg-green-600 hover:bg-green-700
                               text-white font-semibold py-3 rounded-xl transition">

                        📱 Kirim WhatsApp

                    </button>

                </div>

            </form>

        </div>


        <!-- INFO -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-5">

            <h3 class="font-semibold text-blue-800 mb-2">
                ℹ️ Informasi
            </h3>

            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Pastikan nomor WhatsApp sudah benar.</li>
                <li>• Gunakan pesan yang sesuai untuk penerima.</li>
                <li>• Token API tidak ditampilkan pada halaman ini.</li>
                <li>• Setelah dikirim, hasilnya akan dicatat pada log WhatsApp.</li>
            </ul>

        </div>

    </main>


    <script>

        const message = document.getElementById('message');
        const counter = document.getElementById('counter');
        const form = document.getElementById('formKirim');
        const button = document.getElementById('btnKirim');

        // Counter pesan
        message.addEventListener('input', function () {

            counter.textContent =
                this.value.length + ' / 1000';

        });


        // Saat form dikirim
        form.addEventListener('submit', function () {

            button.disabled = true;

            button.innerHTML = '⏳ Mengirim...';

            button.classList.add('opacity-70');
            button.classList.remove('hover:bg-green-700');

        });

    </script>

</body>

</html>
