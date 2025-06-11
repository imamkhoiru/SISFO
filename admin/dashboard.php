<?php 
// 1. Panggil header di paling atas. Ini akan memuat semua CSS, session, dan layout atas.
require_once '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Dashboard Admin</h5>
                </div>
                <div class="card-body">
                    <h5>Selamat Datang, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h5>
                    <p>Anda telah login sebagai **Admin**. Anda memiliki akses penuh terhadap sistem.</p>
                    <p>Silakan gunakan menu di sebelah kiri untuk mengelola data master, jadwal, dan melihat laporan.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// 3. Panggil footer di paling bawah untuk menutup semua tag HTML dan memuat JS.
require_once '../includes/footer.php';
?>