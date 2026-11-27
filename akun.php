<?php
// akun.php : admin mengganti kata sandinya sendiri
require "config.php";
mulai_sesi();

if (!sudah_login()) {
    header("Location: login.php");
    exit;
}

$admin  = baca_admin();
$error  = array();
$sukses = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && $admin !== null) {
    $lama  = $_POST["lama"] ?? "";
    $baru  = $_POST["baru"] ?? "";
    $ulang = $_POST["ulang"] ?? "";

    if (!password_verify($lama, $admin["hash"])) {
        sleep(1);
        $error[] = "Kata sandi lama salah.";
    }
    $e = cek_sandi($baru);
    if ($e !== "") { $error[] = $e; }
    if ($baru !== $ulang) { $error[] = "Kata sandi baru dan ulangannya tidak sama."; }

    if (count($error) === 0) {
        if (ganti_sandi_admin($admin["user"], $baru)) {
            $sukses = true;
        } else {
            $error[] = "Kata sandi belum bisa disimpan. Pastikan folder data dapat ditulisi.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ganti Kata Sandi</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap sempit">
    <header class="topbar">
      <a class="brand" href="admin.php"><span class="logo">BT</span><span>Buku Tamu</span></a>
      <nav><a href="admin.php">Kembali ke daftar</a></nav>
    </header>

    <main class="panel-card">
      <h1>Ganti kata sandi</h1>
      <p class="lead">Masuk sebagai <b><?php echo htmlspecialchars($admin["user"] ?? ""); ?></b></p>

<?php if ($sukses): ?>
      <div class="notif ok" role="status">Kata sandi berhasil diganti.</div>
<?php endif; ?>
<?php if (count($error) > 0): ?>
      <ul class="galat" role="alert">
<?php foreach ($error as $pesan): ?>
        <li><?php echo htmlspecialchars($pesan); ?></li>
<?php endforeach; ?>
      </ul>
<?php endif; ?>

      <form action="akun.php" method="post">
        <label for="lama">Kata sandi lama</label>
        <input type="password" id="lama" name="lama" autocomplete="current-password" required>

        <label for="baru">Kata sandi baru</label>
        <input type="password" id="baru" name="baru" autocomplete="new-password" required>
        <div class="hitung" style="text-align:left">Minimal 6 karakter</div>

        <label for="ulang">Ulangi kata sandi baru</label>
        <input type="password" id="ulang" name="ulang" autocomplete="new-password" required>

        <div class="aksi">
          <button type="submit" class="btn full">Simpan kata sandi</button>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
