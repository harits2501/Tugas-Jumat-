<?php
// admin.php : daftar buku tamu, hanya bisa dibuka setelah login
require "config.php";
mulai_sesi();

if (!sudah_login()) {
    header("Location: login.php");
    exit;
}

$fileData = "data/bukutamu.txt";
$daftar   = array();

if (file_exists($fileData)) {
    $fh = fopen($fileData, "r") or die("File tidak bisa dibuka");
    while (!feof($fh)) {                    // ulangi sampai akhir file
        $baris = trim(fgets($fh));          // baca satu baris
        if ($baris === "") { continue; }    // lewati baris kosong
        $bagian = explode("|", $baris, 4);  // pecah menjadi 4 bagian
        if (count($bagian) === 4) {
            $daftar[] = array(
                "tanggal"  => $bagian[0],
                "nama"     => $bagian[1],
                "email"    => $bagian[2],
                "komentar" => $bagian[3]
            );
        }
    }
    fclose($fh);
}

$daftar = array_reverse($daftar);   // tamu terbaru tampil paling atas

function aman($teks) {
    return nl2br(htmlspecialchars(str_replace("[br]", "\n", $teks)));
}

// Huruf pertama nama untuk avatar
function inisial($nama) {
    $nama = trim($nama);
    $huruf = function_exists("mb_substr")
        ? mb_strtoupper(mb_substr($nama, 0, 1, "UTF-8"), "UTF-8")
        : strtoupper(substr($nama, 0, 1));
    return htmlspecialchars($huruf);
}

// Warna avatar (0-5) ditentukan dari nama, jadi nama yang sama selalu berwarna sama
function warna($nama) {
    return crc32(strtolower($nama)) % 6;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Daftar Buku Tamu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrap">
    <header class="topbar">
      <a class="brand" href="index.html"><span class="logo">BT</span><span>Buku Tamu</span></a>
      <nav><a href="akun.php">Ganti kata sandi</a></nav>
    </header>

    <div class="judul-daftar">
      <div>
        <h1>Daftar tamu</h1>
        <p><span class="jumlah"><?php echo count($daftar); ?></span> pesan, terbaru di atas</p>
      </div>
      <a class="btn ghost" href="logout.php">Keluar</a>
    </div>

<?php if (count($daftar) === 0): ?>
    <div class="kosong">
      <b>Belum ada pesan</b>
      Pesan dari pengunjung akan muncul di sini.
    </div>
<?php else: ?>
<?php foreach ($daftar as $tamu): ?>
    <article class="kartu">
      <div class="avatar a<?php echo warna($tamu["nama"]); ?>"><?php echo inisial($tamu["nama"]); ?></div>
      <div>
        <div class="atas">
          <b><?php echo htmlspecialchars($tamu["nama"]); ?></b>
          <time><?php echo htmlspecialchars($tamu["tanggal"]); ?></time>
        </div>
        <div class="surel"><?php echo htmlspecialchars($tamu["email"]); ?></div>
        <p><?php echo aman($tamu["komentar"]); ?></p>
      </div>
    </article>
<?php endforeach; ?>
<?php endif; ?>
  </div>
</body>
</html>
