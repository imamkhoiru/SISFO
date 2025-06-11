<?php
// Mulai session di paling atas file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pengaturan Timezone
date_default_timezone_set('Asia/Jakarta');

// --- PENGATURAN KONEKSI DATABASE ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Ganti dengan username database Anda
define('DB_PASS', '');     // Ganti dengan password database Anda
define('DB_NAME', 'db_sekolah'); // Ganti dengan nama database Anda

// --- KONEKSI KE DATABASE MENGGUNAKAN MYSQLI ---
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if (mysqli_connect_errno()) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Menonaktifkan reporting error internal MySQLi agar tidak menjadi fatal error
// Ini memungkinkan kita menangani error secara manual (contoh: di file delete.php)
mysqli_report(MYSQLI_REPORT_OFF);


// --- PENGATURAN DASAR APLIKASI ---

// BASE_URL adalah alamat dasar dari aplikasi Anda.
// Pengaturan Anda di sini sudah benar jika folder proyek Anda bernama 's_sekolah'.
define('BASE_URL', 'http://localhost/s_sekolah/');

?>