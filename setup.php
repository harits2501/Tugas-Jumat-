<?php
// setup.php : membuat akun admin pertama kali (hanya bisa dipakai sekali)
require "config.php";
mulai_sesi();

// Jika admin sudah ada, halaman ini tidak boleh dipakai lagi
if (admin_ada()) {
    header("Location: login.php");
    exit;
}

$error   = array();
$userIsi = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user   = trim($_POST["username"] ?? "");
    $sandi  = $_POST["password"] ?? "";
    $ulang  = $_POST["password2"] ?? "";
    $userIsi = $user;

    if (!preg_match('/^[A-Za-z0-9_.]{3,30}$/', $user)) {
        $error[] = "Nama pengguna 3–30 karakter: huruf, angka, titik, atau garis bawah.";
    }
    $e = cek_sandi($sandi);
    if ($e !== "") { $error[] = $e; }
    if ($sandi !== $ulang) { $error[] = "Kata sandi dan ulangannya tidak sama."; }

    if (count($error) === 0) {
        if (buat_admin_pertama($user, $sandi)) {
            session_regenerate_id(true);
            $_SESSION["admin"] = true;      // langsung masuk sebagai admin
            header("Location: admin.php");
            exit;
        }
        if (admin_ada()) {                  // keduluan orang lain
            header("Location: login.php");
            exit;
        }
        $error[] = "Akun belum bisa dibuat. Pastikan folder data dapat ditulisi oleh server.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Buat Akun Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap sempit">
    <header class="topbar">
      <a class="brand" href="index.html"><span class="logo">BT</span><span>Buku Tamu</span></a>
    </header>

    <main class="panel-card">
      <h1>Buat akun admin</h1>
      <p class="lead">Langkah awal. Tentukan nama pengguna dan kata sandi untuk admin. Setelah dibuat, halaman ini tidak bisa dipakai lagi.</p>

<?php if (count($error) > 0): ?>
      <ul class="galat" role="alert">
<?php foreach ($error as $pesan): ?>
        <li><?php echo htmlspecialchars($pesan); ?></li>
<?php endforeach; ?>
      </ul>
<?php endif; ?>

      <form action="setup.php" method="post">
        <label for="username">Nama pengguna</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($userIsi); ?>" maxlength="30" autocomplete="username" autocapitalize="none" required autofocus>

        <label for="password">Kata sandi</label>
        <input type="password" id="password" name="password" autocomplete="new-password" required>
        <div class="hitung" style="text-align:left">Minimal 6 karakter</div>

        <label for="password2">Ulangi kata sandi</label>
        <input type="password" id="password2" name="password2" autocomplete="new-password" required>

        <div class="aksi">
          <button type="submit" class="btn full">Buat akun &amp; masuk</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
