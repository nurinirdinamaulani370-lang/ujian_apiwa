<?php
session_start();

// Cek session login - redirect ke login jika belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';

// Ambil pesan status dari URL (hasil redirect dari update_log.php / delete_log.php)
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;

// ========================================
// AMBIL DATA LOG DARI DATABASE (Disesuaikan ke log_whatsapp)
// ========================================
try {
    $stmt = $pdo->query("SELECT * FROM log_whatsapp ORDER BY id DESC LIMIT 50");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $logs = [];
    $status = 'error';
    $message = "Gagal mengambil data log: " . $e->getMessage();
}

// ========================================
// HITUNG STATISTIK RINGKASAN (Disesuaikan ke log_whatsapp)
// ========================================
try {
    $totalSukses = $pdo->query("SELECT COUNT(*) FROM log_whatsapp WHERE status = 'success'")->fetchColumn();
    $totalGagal  = $pdo->query("SELECT COUNT(*) FROM log_whatsapp WHERE status = 'failed'")->fetchColumn();
} catch (PDOException $e) {
    $totalSukses = 0;
    $totalGagal  = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - API WA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121824] text-gray-200 min-h-screen flex">

  <!-- Sidebar -->
  <aside class="w-64 bg-[#1a2332] border-r border-gray-800 p-5 flex flex-col justify-between hidden md:flex">
    <div>
      <h2 class="text-xl font-bold text-white mb-8 tracking-wide">API WA Panel</h2>
      <nav class="space-y-2">
        <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-[#22c55e] text-white font-medium rounded-lg transition">
          <span>Dashboard</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Kirim Pesan</span>
        </a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Riwayat Log</span>
        </a>
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:bg-[#243044] hover:text-white rounded-lg transition">
          <span>Profil Saya</span>
        </a>
      </nav>
    </div>

    <div class="border-t border-gray-800 pt-4 flex justify-between items-center">
      <div>
        <div class="text-sm font-medium text-white"><?= htmlspecialchars($_SESSION['user_nama'] ?? 'User'); ?></div>
        <div class="text-xs text-gray-400">Status: Aktif</div>
      </div>
      <a href="logout.php" class="text-xs text-red-400 hover:underline">Keluar</a>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6 md:p-10 overflow-y-auto">

    <!-- Top Bar -->
    <header class="flex justify-between items-center mb-8">
      <div>
        <h1 class="text-2xl font-bold text-white">Ringkasan Sistem</h1>
        <p class="text-sm text-gray-400">Monitoring pengiriman WhatsApp & Fonnte API</p>
      </div>
      <button onclick="openAddModal()" class="bg-[#22c55e] hover:bg-[#16a34a] text-white font-semibold px-4 py-2 rounded-lg transition cursor-pointer">
        + Kirim WA Baru
      </button>
    </header>

    <!-- Notifikasi Alert -->
    <?php if ($status === 'success'): ?>
      <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Berhasil!'); ?>
      </div>
    <?php elseif ($status === 'error'): ?>
      <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Terjadi kesalahan.'); ?>
      </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800">
        <p class="text-sm text-gray-400 mb-1">Pesan Terkirim</p>
        <p class="text-2xl font-bold text-white"><?= number_format($totalSukses, 0, ',', '.'); ?></p>
        <span class="text-xs text-green-400 mt-2 inline-block">Berhasil dikirim</span>
      </div>

      <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800">
        <p class="text-sm text-gray-400 mb-1">Pesan Gagal</p>
        <p class="text-2xl font-bold text-white"><?= number_format($totalGagal, 0, ',', '.'); ?></p>
        <span class="text-xs text-red-400 mt-2 inline-block">Cek koneksi Fonnte</span>
      </div>

      <div class="bg-[#1a2332] p-6 rounded-xl border border-gray-800">
        <p class="text-sm text-gray-400 mb-1">Status API Fonnte</p>
        <p class="text-2xl font-bold text-green-400">Connected</p>
        <span class="text-xs text-gray-400 mt-2 inline-block">Token terverifikasi</span>
      </div>
    </section>

    <!-- Table Section -->
    <section class="bg-[#1a2332] rounded-xl border border-gray-800 p-6">
      <h3 class="text-lg font-bold text-white mb-4">Log Pengiriman Terbaru</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-300">
          <thead class="bg-[#243044] text-gray-200 uppercase text-xs">
            <tr>
              <th class="p-3 rounded-l-lg">No. WA</th>
              <th class="p-3">Pesan</th>
              <th class="p-3">Status</th>
              <th class="p-3">Waktu</th>
              <th class="p-3 rounded-r-lg text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-800">
            <?php if (count($logs) === 0): ?>
              <tr>
                <td colspan="5" class="p-6 text-center text-gray-500">Belum ada data log.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($logs as $log): ?>
                <tr>
                  <td class="p-3 font-medium text-white"><?= htmlspecialchars($log['no_tujuan'] ?? '-'); ?></td>
                  <td class="p-3 truncate max-w-xs"><?= htmlspecialchars($log['pesan'] ?? '-'); ?></td>
                  <td class="p-3">
                    <?php if (($log['status'] ?? '') === 'success'): ?>
                      <span class="bg-green-500/20 text-green-400 px-2.5 py-1 rounded-full text-xs font-semibold">Sukses</span>
                    <?php else: ?>
                      <span class="bg-red-500/20 text-red-400 px-2.5 py-1 rounded-full text-xs font-semibold">Gagal</span>
                    <?php endif; ?>
                  </td>
                  <td class="p-3"><?= htmlspecialchars($log['created_at'] ?? '-'); ?></td>
                  <td class="p-3 text-right whitespace-nowrap">
                    <!-- Tombol Edit: Menggunakan data-attribute agar aman dari enter / kutip -->
                    <button
                      type="button"
                      data-id="<?= (int) $log['id']; ?>"
                      data-target="<?= htmlspecialchars($log['no_tujuan'] ?? '', ENT_QUOTES); ?>"
                      data-pesan="<?= htmlspecialchars($log['pesan'] ?? '', ENT_QUOTES); ?>"
                      onclick="openEditModalFromButton(this)"
                      class="text-blue-400 hover:text-blue-300 text-xs font-semibold mr-3 cursor-pointer"
                    >
                      Edit
                    </button>

                    <!-- Tombol Delete -->
                    <button
                      type="button"
                      onclick="openDeleteModal('<?= (int) $log['id']; ?>')"
                      class="text-red-400 hover:text-red-300 text-xs font-semibold cursor-pointer"
                    >
                      Hapus
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

  </main>

  <!-- ======================================== -->
  <!-- MODAL EDIT LOG -->
  <!-- ======================================== -->
  <div id="editModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
    <div class="w-full max-w-md bg-[#1a2332] rounded-2xl border border-gray-800 p-6 shadow-xl">
      <h3 class="text-lg font-bold text-white mb-4">Edit Data Log</h3>
      <form action="api/update_log.php" method="POST" class="space-y-4">
        <input type="hidden" name="id" id="edit_id">

        <div>
          <label class="block text-sm text-gray-400 mb-1">Nomor WhatsApp</label>
          <input
            type="text"
            name="target"
            id="edit_target"
            required
            class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]"
          >
        </div>

        <div>
          <label class="block text-sm text-gray-400 mb-1">Pesan</label>
          <textarea
            name="pesan"
            id="edit_pesan"
            rows="3"
            required
            class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-[#22c55e]"
          ></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-400 hover:text-white text-sm cursor-pointer">Batal</button>
          <button type="submit" class="bg-[#22c55e] hover:bg-[#16a34a] text-white font-semibold px-5 py-2 rounded-lg transition cursor-pointer">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <!-- ======================================== -->
  <!-- MODAL KONFIRMASI DELETE -->
  <!-- ======================================== -->
  <div id="deleteModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 p-4">
    <div class="w-full max-w-sm bg-[#1a2332] rounded-2xl border border-gray-800 p-6 shadow-xl text-center">
      <h3 class="text-lg font-bold text-white mb-2">Hapus Data?</h3>
      <p class="text-sm text-gray-400 mb-6">Data log ini akan dihapus permanen dan tidak bisa dikembalikan.</p>
      <form action="api/delete_log.php" method="POST">
        <input type="hidden" name="id" id="delete_id">
        <div class="flex justify-center gap-3">
          <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 text-gray-400 hover:text-white text-sm cursor-pointer">Batal</button>
          <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-5 py-2 rounded-lg transition cursor-pointer">Ya, Hapus</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Fungsi baru untuk mengambil data dari atribut HTML tombol
    function openEditModalFromButton(btn) {
      const id = btn.getAttribute('data-id');
      const target = btn.getAttribute('data-target');
      const pesan = btn.getAttribute('data-pesan');

      document.getElementById('edit_id').value = id;
      document.getElementById('edit_target').value = target;
      document.getElementById('edit_pesan').value = pesan;

      const modal = document.getElementById('editModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeEditModal() {
      const modal = document.getElementById('editModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function openDeleteModal(id) {
      document.getElementById('delete_id').value = id;
      const modal = document.getElementById('deleteModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeDeleteModal() {
      const modal = document.getElementById('deleteModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function openAddModal() {
      alert('Hubungkan tombol ini ke form pengiriman WA baru (api/whatsapp.php).');
    }
  </script>

</body>
</html>