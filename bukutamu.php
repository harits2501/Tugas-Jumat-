<?php
// bukutamu.php : memproses dan menyimpan data buku tamu ke file teks

date_default_timezone_set("Asia/Jakarta");
$fileData = "data/bukutamu.txt";

// 1. Halaman ini hanya boleh dibuka lewat pengiriman form (POST)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

// 2. Fungsi pembersih: menghapus spasi berlebih, mengganti pemisah "|"
//    dan mengubah baris baru menjadi penanda [br] agar 1 tamu = 1 baris
function bersihkan($teks) {
    $teks = trim($teks);
    $teks = str_replace("|", "/", $teks);
    $teks = str_replace(array("\r\n", "\r", "\n"), "[br]", $teks);
    return $teks;
}

$nama     = bersihkan($_POST["nama"] ?? "");
$email    = bersihkan($_POST["email"] ?? "");
$komentar = bersihkan($_POST["komentar"] ?? "");

// 3. Validasi data
$error = array();
if ($nama === "")     { $error[] = "Nama wajib diisi."; }
if ($email === "")    { $error[] = "Email wajib diisi."; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = "Format email tidak benar.";
}
if ($komentar === "") { $error[] = "Komentar wajib diisi."; }

// 4. Jika tidak ada error, simpan ke file
$berhasil = false;
if (count($error) === 0) {
    if (!is_dir("data")) { mkdir("data", 0755, true); }

    $tanggal = date("d-m-Y H:i:s");
    $baris   = $tanggal . "|" . $nama . "|" . $email . "|" . $komentar . "\n";

    // mode 'a' = append: isi lama tetap ada, data baru ditambah di akhir
    $fh = fopen($fileData, "a") or die("File tidak bisa dibuka");
    flock($fh, LOCK_EX);      // kunci file agar tidak tabrakan antar pengguna
    fwrite($fh, $baris);
    flock($fh, LOCK_UN);
    fclose($fh);              // file wajib ditutup
    $berhasil = true;
}

// Fungsi untuk menampilkan teks dengan aman di HTML
function aman($teks) {
    return nl2br(htmlspecialchars(str_replace("[br]", "\n", $teks)));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hasil Simpan Buku Tamu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap">
    <header class="topbar">
      <a class="brand" href="index.html"><span class="logo">BT</span><span>Buku Tamu</span></a>
      <nav><a href="index.html">Kembali ke form</a></nav>
    </header>

    <main class="panel-card center">
<?php if ($berhasil): ?>
      <div class="ikon ok">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>
      </div>
      <h1>Pesan tersimpan</h1>
      <p class="lead">Terima kasih, pesanmu sudah kami terima.</p>

      <dl class="ringkasan">
        <div><dt>Nama</dt><dd><?php echo aman($nama); ?></dd></div>
        <div><dt>Email</dt><dd><?php echo aman($email); ?></dd></div>
        <div><dt>Komentar</dt><dd><?php echo aman($komentar); ?></dd></div>
      </dl>

      <a class="btn" href="index.html">Kembali ke beranda</a>
<?php else: ?>
      <div class="ikon bad">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" aria-hidden="true"><path d="M12 7v6M12 17h.01"/></svg>
      </div>
      <h1>Pesan belum tersimpan</h1>
      <p class="lead">Perbaiki hal berikut, lalu kirim lagi.</p>

      <ul class="galat">
<?php foreach ($error as $pesan): ?>
        <li><?php echo htmlspecialchars($pesan); ?></li>
<?php endforeach; ?>
      </ul>

      <a class="btn" href="javascript:history.back()">Kembali ke form</a>
<?php endif; ?>
    </main>
  </div>
</body>
</html>
