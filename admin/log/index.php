<?php 
require_once '../../includes/header.php';

// Ambil data log aktivitas dan join dengan tabel guru untuk mendapatkan nama pengguna
$sql = "SELECT l.*, g.nama_lengkap 
        FROM trs_log_aktivitas l 
        LEFT JOIN mst_guru g ON l.user_id = g.id 
        ORDER BY l.created_at DESC";
$result = mysqli_query($db, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Log Aktivitas Pengguna</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Pengguna</th>
                                        <th>Aktivitas</th>
                                        <th>Halaman</th>
                                        <th>Alamat IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                                        <?php while($log = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= date('d M Y, H:i:s', strtotime($log['created_at'])); ?></td>
                                            <td><?= htmlspecialchars($log['nama_lengkap'] ?? 'User ID: '.$log['user_id']); ?></td>
                                            <td><?= htmlspecialchars($log['aktivitas']); ?></td>
                                            <td><?= htmlspecialchars($log['halaman']); ?></td>
                                            <td><?= htmlspecialchars($log['ip_address']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Belum ada aktivitas yang tercatat.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>