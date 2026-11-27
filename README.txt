BUKU TAMU - PETUNJUK

Alamat:
  Form tamu      : /index.html
  Login admin    : /login.php
  Daftar tamu    : /admin.php   (hanya setelah login)
  Ganti sandi    : /akun.php    (hanya setelah login)

MEMBUAT AKUN ADMIN (pertama kali, lewat browser, tanpa edit kode)
1. Setelah semua file terpasang di server, buka  /login.php
2. Karena belum ada admin, otomatis diarahkan ke halaman "Buat akun admin".
3. Isi nama pengguna dan kata sandi (2x), lalu tekan "Buat akun & masuk".
4. Selesai. Halaman setup tidak bisa dipakai lagi setelah akun dibuat.

PENTING: lakukan langkah di atas SEGERA setelah memasang, karena siapa
yang pertama membuka halaman setup akan menjadi admin.

Mengganti kata sandi : masuk sebagai admin, lalu buka Ganti kata sandi.

LUPA KATA SANDI / MAU MEMBUAT ULANG AKUN
Hapus file  data/admin.txt  lewat File Manager hosting (atau lewat
folder di komputer). Setelah itu buka /login.php lagi dan buat akun baru.
Data tamu (data/bukutamu.txt) tidak ikut terhapus.
