<?php
session_start();

// Jika sudah login, lempar langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - API WA Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121824] text-gray-200 min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md bg-[#1a2332] rounded-2xl border border-gray-800 p-8 shadow-xl">
    
    <!-- Header Form -->
    <div class="text-center mb-8">
      <h1 class="text-2xl font-bold text-white tracking-wide">Selamat Datang</h1>
      <p class="text-sm text-gray-400 mt-1">Masuk ke akun API WA kamu</p>
    </div>

    <!-- Alert Notifikasi -->
    <?php if ($status === 'error'): ?>
      <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Email atau password salah.'); ?>
      </div>
    <?php elseif ($status === 'success'): ?>
      <div class="mb-6 p-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm rounded-xl">
        <?= htmlspecialchars($message ?? 'Berhasil logout.'); ?>
      </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form action="api/login_process.php" method="POST" class="space-y-5">
      
      <div>
        <label for="email" class="block text-sm font-medium text-gray-400 mb-2">Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          placeholder="contoh@email.com" 
          required 
          class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#22c55e] transition"
        >
      </div>

      <div>
        <div class="flex justify-between items-center mb-2">
          <label for="password" class="block text-sm font-medium text-gray-400">Password</label>
          <a href="#" class="text-xs text-[#22c55e] hover:underline">Lupa password?</a>
        </div>
        <input 
          type="password" 
          id="password" 
          name="password" 
          placeholder="••••••••" 
          required 
          class="w-full bg-[#243044] border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-[#22c55e] transition"
        >
      </div>

      <button 
        type="submit" 
        class="w-full bg-[#22c55e] hover:bg-[#16a34a] text-white font-semibold py-3 rounded-lg transition duration-200 mt-2 shadow-lg shadow-green-500/20"
      >
        Masuk
      </button>

    </form>

    <!-- Footer Link -->
    <div class="text-center mt-6 text-sm text-gray-400">
      Belum punya akun? 
      <a href="index.html" class="text-[#22c55e] font-medium hover:underline">Daftar di sini</a>
    </div>

  </div>

</body>
</html>