<?php
// config.php : fungsi bersama untuk akun admin dan sesi
//
// Akun admin TIDAK ditulis di kode. Akun dibuat lewat browser di halaman
// setup.php (sekali saja) dan disimpan di data/admin.txt dengan format:
//     namapengguna|hash_kata_sandi
// Kata sandi hanya disimpan dalam bentuk hash, bukan teks asli.

define("FILE_ADMIN", __DIR__ . "/data/admin.txt");

function mulai_sesi() {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(array(
            "httponly" => true,
            "samesite" => "Lax"
        ));
        session_start();
    }
}

function sudah_login() {
    return !empty($_SESSION["admin"]) && $_SESSION["admin"] === true;
}

// Apakah akun admin sudah pernah dibuat?
function admin_ada() {
    return is_file(FILE_ADMIN) && filesize(FILE_ADMIN) > 0;
}

// Mengembalikan array("user" => ..., "hash" => ...) atau null
function baca_admin() {
    if (!admin_ada()) { return null; }
    $bagian = explode("|", trim(file_get_contents(FILE_ADMIN)), 2);
    if (count($bagian) !== 2) { return null; }
    return array("user" => $bagian[0], "hash" => $bagian[1]);
}

// Membuat akun pertama. Mode 'x' = gagal bila file sudah ada,
// jadi akun admin yang sudah ada tidak mungkin tertimpa.
function buat_admin_pertama($user, $sandi) {
    $dir = dirname(FILE_ADMIN);
    if (!is_dir($dir)) { mkdir($dir, 0755, true); }
    $fh = @fopen(FILE_ADMIN, "x");
    if (!$fh) { return false; }
    flock($fh, LOCK_EX);
    fwrite($fh, $user . "|" . password_hash($sandi, PASSWORD_DEFAULT) . "\n");
    flock($fh, LOCK_UN);
    fclose($fh);
    return true;
}

// Mengganti kata sandi akun yang sudah ada
function ganti_sandi_admin($user, $sandiBaru) {
    $baris = $user . "|" . password_hash($sandiBaru, PASSWORD_DEFAULT) . "\n";
    return file_put_contents(FILE_ADMIN, $baris, LOCK_EX) !== false;
}

// Aturan kata sandi. Mengembalikan pesan kesalahan, atau "" bila benar
function cek_sandi($sandi) {
    if (strlen($sandi) < 6) { return "Kata sandi minimal 6 karakter."; }
    return "";
}
