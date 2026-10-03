<?php
// session_start();
// include 'config/database.php';
// include 'config/fonnte.php';

// Simulasi data pengguna
$user = [
    'nama' => 'Admin Utama',
    'email' => 'admin@email.com',
    'whatsapp' => '628123456789',
    'alamat' => 'Jl. Merdeka No. 45, Jakarta',
    'fonnte_token' => 'HsQdJefj2ncCwWuoNCWa'
];

// Menangkap parameter pesan dari URL jika ada
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profil Pengguna - API WA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121824] text-gray-200 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-[#1a2332] border-r border-gray-800 p-5 flex flex-col justify-between hidden md:flex">
    <div>
      <h2 class="text-xl font-bold text-white mb-8 tracking-wide">API WA Panel</h2>
      <nav class="space-y-2">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Kirim Pesan</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Riwayat Log</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Pengaturan Fonnte</span>
        </a>
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 bg-[#22c55e] text-white font-medium rounded-lg transition">
          <span>Profil Saya</span>
        </a>
      </nav>
    </div>
    
    <div class="border-t border-gray-800 pt-4 flex justify-between items-center">
      <div>
        <div class="text-sm font-medium text-white"><?= htmlspecialchars($user['nama']); ?></div>
        <div class="text-xs text-gray-400">Status: Aktif</div>
      </div>
      <a href="logout.php" class="text-xs text-red-400 hover:underline">Keluar</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10 overflow-y-auto">
    
    <header class="mb-6">
      <h1 class="text-2xl font-bold text-white">Pengaturan Profil</h1>
      <p class="text-sm text-gray-400">Kelola informasi akun dan konfigurasi token API WhatsApp kamu.</p>
    </header>

    <!-- Notifikasi Alert -->
    <?php if ($status === 'success'): ?>
      <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Data berhasil diperbarui!'); ?>
      </div>
    <?php elseif ($status === 'error'): ?>
      <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Terjadi kesalahan. Silakan coba lagi.'); ?>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Ringkasan Profil (Kiri) -->
      <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800 flex flex-col items-center text-center h-fit">
        <div class="w-24 h-24 bg-[#243044] text-green-400 rounded-full flex items-center justify-center text-3xl font-bold mb-4 border-2 border-[#22c55e]">
          <?= strtoupper(substr($user['nama'], 0, 1)); ?>
        </div>
        <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($user['nama']); ?></h2>
        <p class="text-sm text-gray-400 mb-4"><?= htmlspecialchars($user['email']); ?></p>
        <span class="bg-green-500/20 text-green-400 text-xs px-3 py-1 rounded-full font-medium">Akun Terverifikasi</span>
      </div>

      <!-- Form Detail Profil & API (Kanan) -->
      <div class="lg:col-span-2 space-y-6">
        
        <!-- Form Data Diri -->
        <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800">
          <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-3">Informasi Pribadi</h3>
          
          <form action="api/update_profile.php" method="POST" class="space-y-4">
            <div>
              <label class="block text-sm text-gray-400 mb-1">Nama Lengkap</label>
              <input type="text" name="nama" value="<?= htmlspecialchars($user['nama']); ?>" class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]">
              </div>
              <div>
                <label class="block text-sm text-gray-400 mb-1">Nomor WhatsApp</label>
                <input type="text" name="whatsapp" value="<?= htmlspecialchars($user['whatsapp']); ?>" class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]">
              </div>
            </div>

            <div>
              <label class="block text-sm text-gray-400 mb-1">Alamat</label>
              <textarea name="alamat" rows="3" class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]"><?= htmlspecialchars($user['alamat']); ?></textarea>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="bg-[#22c55e] hover:bg-[#16a34a] text-white font-semibold px-5 py-2 rounded-lg transition">
                Simpan Perubahan
              </button>
            </div>
          </form>
        </div>

        <!-- Form Konfigurasi Fonnte API -->
        <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800">
          <h3 class="text-lg font-bold text-white mb-4 border-b border-gray-800 pb-3">Integrasi Fonnte API</h3>
          
          <form action="api/update_token.php" method="POST" class="space-y-4">
            <div>
              <label class="block text-sm text-gray-400 mb-1">Token Fonnte</label>
              <input type="password" name="fonnte_token" value="<?= htmlspecialchars($user['fonnte_token']); ?>" class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]">
              <p class="text-xs text-gray-500 mt-1">Token ini digunakan untuk menghubungkan script kamu dengan gateway Fonnte.</p>
            </div>

            <div class="flex justify-end">
              <button type="submit" class="bg-[#243044] hover:bg-gray-700 text-white font-semibold px-5 py-2 rounded-lg transition border border-gray-700">
                Update Token
              </button>
            </div>
          </form>
        </div>

      </div>

    </div>

  </main>

</body>
</html>