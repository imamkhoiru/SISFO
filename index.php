<?php
// Memanggil file config untuk memulai session dan koneksi database
require_once 'includes/config.php';

// Cek apakah pengguna sudah login, jika belum, paksa ke halaman login
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header('Location: login.php');
    exit;
}

// Jika sudah login, cek rolenya dari session
$role_id = $_SESSION['role_id'];

// Arahkan pengguna ke dashboard yang sesuai berdasarkan role
if ($role_id == 1) { // Jika role_id adalah 1 (Admin)
    header('Location: ' . BASE_URL . 'admin/dashboard.php');
    exit;
} elseif ($role_id == 2) { // Jika role_id adalah 2 (Guru)
    header('Location: ' . BASE_URL . 'guru/dashboard.php');
    exit;
} else {
    // Jika role tidak dikenal (sebagai pengaman), hancurkan session dan kembali ke login
    session_unset();
    session_destroy();
    header('Location: login.php?error=invalidrole');
    exit;
}
?>