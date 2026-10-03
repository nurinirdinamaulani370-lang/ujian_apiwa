<?php
session_start();
require_once 'config/database.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil data user dari database
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Data pengguna tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Data - API WA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121824] text-gray-200 min-h-screen flex items-center justify-center p-4">

  <!-- Card Form Edit -->
  <div class="w-full max-w-lg bg-[#181d24] rounded-2xl border border-gray-800/60 p-8 shadow-2xl">
    
    <!-- Header -->
    <div class="text-center mb-8">
      <h1 class="text-3xl font-bold text-white tracking-wide">Edit Data</h1>
      <p class="text-sm text-gray-400 mt-2">Silakan perbarui data akun Anda</p>
    </div>

    <!-- Alert Notifikasi -->
    <?php if ($status === 'success'): ?>
      <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Data berhasil diperbarui!'); ?>
      </div>
    <?php elseif ($status === 'error'): ?>
      <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Gagal memperbarui data.'); ?>
      </div>
    <?php endif; ?>

    <!-- Form Edit Data -->
    <form action="api/update_profile.php" method="POST" class="space-y-5">
      
      <!-- Input Nama -->
      <div>
        <label for="nama" class="block text-sm font-semibold text-gray-300 mb-2">Nama</label>
        <input 
          type="text" 
          id="nama" 
          name="nama" 
          value="<?= htmlspecialchars($user['nama'] ?? ''); ?>"
          placeholder="Masukkan nama lengkap" 
          required 
          class="w-full bg-[#33393e] border border-transparent focus:border-[#22c55e] rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none transition"
        >
      </div>

      <!-- Input Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-300 mb-2">Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          value="<?= htmlspecialchars($user['email'] ?? ''); ?>"
          placeholder="contoh@email.com" 
          required 
          class="w-full bg-[#33393e] border border-transparent focus:border-[#22c55e] rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none transition"
        >
      </div>

      <!-- Input WhatsApp -->
      <div>
        <label for="whatsapp" class="block text-sm font-semibold text-gray-300 mb-2">Nomor WhatsApp</label>
        <input 
          type="text" 
          id="whatsapp" 
          name="whatsapp" 
          value="<?= htmlspecialchars($user['whatsapp'] ?? ''); ?>"
          placeholder="628123456789" 
          required 
          class="w-full bg-[#33393e] border border-transparent focus:border-[#22c55e] rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none transition"
        >
        <p class="text-xs text-gray-400 mt-1.5">Gunakan format 62, contoh: 628123456789</p>
      </div>

      <!-- Input Alamat -->
      <div>
        <label for="alamat" class="block text-sm font-semibold text-gray-300 mb-2">Alamat</label>
        <textarea 
          id="alamat" 
          name="alamat" 
          rows="3" 
          placeholder="Masukkan alamat lengkap"
          class="w-full bg-[#33393e] border border-transparent focus:border-[#22c55e] rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none transition resize-none"
        ><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
      </div>

      <!-- Input Password Baru (Opsional) -->
      <div>
        <label for="password" class="block text-sm font-semibold text-gray-300 mb-2">Password Baru (Opsional)</label>
        <div class="relative">
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="Kosongkan jika tidak ingin diubah" 
            class="w-full bg-[#33393e] border border-transparent focus:border-[#22c55e] rounded-xl pl-4 pr-16 py-3 text-white placeholder-gray-500 focus:outline-none transition"
          >
          <button 
            type="button" 
            onclick="togglePassword()" 
            class="absolute right-4 top-1/2 -translate-y-1/2 text-sm font-semibold text-[#22c55e] hover:text-green-400 transition"
          >
            Lihat
          </button>
        </div>
        <p class="text-xs text-gray-400 mt-1.5">Password minimal 6 karakter jika diisi.</p>
      </div>

      <!-- Tombol Simpan -->
      <div class="pt-2">
        <button 
          type="submit" 
          class="w-full bg-[#15803d] hover:bg-[#16a34a] text-white font-bold py-3.5 rounded-xl transition duration-200 shadow-lg shadow-green-900/20"
        >
          Simpan Perubahan
        </button>
      </div>

    </form>

    <!-- Navigation Back -->
    <div class="text-center mt-6">
      <a href="dashboard.php" class="text-sm text-gray-400 hover:text-white transition">
        &larr; Kembali ke Dashboard
      </a>
    </div>

  </div>

  <!-- Script Fitur Toggle Lihat Password -->
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleBtn = event.target;
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleBtn.textContent = 'Sembunyi';
      } else {
        passwordInput.type = 'password';
        toggleBtn.textContent = 'Lihat';
      }
    }
  </script>

</body>
</html>