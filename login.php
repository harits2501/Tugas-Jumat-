<?php
// login.php : halaman masuk untuk admin
require "config.php";
mulai_sesi();

// Belum ada akun admin -> buat dulu
if (!admin_ada()) {
    header("Location: setup.php");
    exit;
}

if (sudah_login()) {
    header("Location: admin.php");
    exit;
}

$admin   = baca_admin();
$pesan   = "";
$userIsi = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $admin !== null) {
    $user = trim($_POST["username"] ?? "");
    $pass = $_POST["password"] ?? "";
    $userIsi = $user;

    // Selalu jalankan kedua pemeriksaan agar waktu prosesnya sama
    $userBenar = hash_equals($admin["user"], $user);
    $passBenar = password_verify($pass, $admin["hash"]);

    if ($userBenar && $passBenar) {
        session_regenerate_id(true);     // ganti ID sesi setelah login
        $_SESSION["admin"] = true;
        header("Location: admin.php");
        exit;
    }

    sleep(1);                            // memperlambat tebak-tebakan sandi
    $pesan = "Nama pengguna atau kata sandi salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap sempit">
    <header class="topbar">
      <a class="brand" href="index.html"><span class="logo">BT</span><span>Buku Tamu</span></a>
      <nav><a href="index.html">Kembali ke form</a></nav>
    </header>

    <main class="panel-card">
      <h1>Masuk admin</h1>
      <p class="lead">Hanya admin yang dapat melihat daftar tamu.</p>

<?php if ($pesan !== ""): ?>
      <div class="notif" role="alert"><?php echo htmlspecialchars($pesan); ?></div>
<?php endif; ?>

      <form action="login.php" method="post">
        <label for="username">Nama pengguna</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userIsi); ?>" autocomplete="username" autocapitalize="none" required autofocus>

        <label for="password">Kata sandi</label>
        <input type="password" id="password" name="password" autocomplete="current-password" required>

        <div class="aksi">
          <button type="submit" class="btn full">Masuk</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
