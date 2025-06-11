<?php
// File ini akan dipanggil di setiap halaman yang terproteksi

// Panggil file config untuk memastikan session sudah dimulai
require_once 'config.php';

// Cek apakah variabel session 'login_status' ada dan bernilai true
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    // Jika tidak, artinya pengguna belum login
    // Arahkan kembali ke halaman login
    header('Location: ' . BASE_URL . 'login.php');
    // Hentikan eksekusi script selanjutnya
    exit;
}
?>