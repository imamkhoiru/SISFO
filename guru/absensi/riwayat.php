<?php
// Panggil file-file yang dibutuhkan
require_once '../includes/header.php';
require_once '../../includes/functions.php';

$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;
$guru_id = $_SESSION['user_id'];
$error_message = '';
$jadwal = null;
$result_kehadiran = null;

if ($jadwal_id <= 0) {
    $error_message = "ID Jadwal tidak valid atau tidak ditemukan.";
} else {
    // Query 1: Ambil detail jadwal dan pastikan jadwal ini milik guru yang login
    $sql_jadwal = "SELECT j.*, mapel.nama_mapel, guru.nama_lengkap AS nama_guru, kelas.nama_kelas 
                   FROM jadwal j
                   LEFT JOIN mst_mapel mapel ON j.mapel_id = mapel.id
                   LEFT JOIN mst_guru guru ON j.pengajar_id = guru.id
                   LEFT JOIN mst_kelas kelas ON j.kelas_id = kelas.id
                   WHERE j.id = $jadwal_id AND j.pengajar_id = $guru_id"; // Validasi kepemilikan
    $result_jadwal = mysqli_query($db, $sql_jadwal);
    $jadwal = mysqli_fetch_assoc($result_jadwal);

    if (!$jadwal) {
        $error_message = "Data Jadwal tidak ditemukan atau Anda tidak berhak mengaksesnya.";
    } else {
        // Query 2: Ambil data kehadiran untuk jadwal ini
        $sql_kehadiran = "SELECT s.nis, s.nama_lengkap, t.status_kehadiran 
                          FROM trs_kehadiran t
                          JOIN mst_siswa s ON t.siswa_id = s.id
                          WHERE t.jadwal_id = $jadwal_id
                          ORDER BY s.nama_lengkap ASC";
        $result_kehadiran = mysqli_query($db, $sql_kehadiran);
    }
}

// Fungsi untuk memberikan warna pada status kehadiran
function get_badge_class($status) {
    switch ($status) {
        case 'Hadir': return 'bg-success';
        case 'Izin': return 'bg-info text-dark';
        case 'Sakit': return 'bg-warning text-dark';
        case 'Alpa': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">Detail Riwayat Absensi</h4>
                <a href="../dashboard.php" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="col-12">
                <div class="alert alert-danger"><?= $error_message; ?></div>
            </div>
        <?php elseif ($jadwal): ?>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Detail Sesi</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="fw-bold" style="width: 40%;">Mata Pelajaran</td>
                                <td>: <?= htmlspecialchars($jadwal['nama_mapel']); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Kelas</td>
                                <td>: <?= htmlspecialchars($jadwal['nama_kelas']); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Tanggal</td>
                                <td>: <?= date('d M Y', strtotime($jadwal['tanggal'])); ?></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Waktu</td>
                                <td>: <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Daftar Kehadiran Siswa</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-no">No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result_kehadiran && mysqli_num_rows($result_kehadiran) > 0): ?>
                                        <?php $no = 1; while($kehadiran = mysqli_fetch_assoc($result_kehadiran)): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($kehadiran['nis']); ?></td>
                                            <td><?= htmlspecialchars($kehadiran['nama_lengkap']); ?></td>
                                            <td>
                                                <span class="badge <?= get_badge_class($kehadiran['status_kehadiran']); ?>">
                                                    <?= htmlspecialchars($kehadiran['status_kehadiran']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Data absensi untuk sesi ini belum diisi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Panggil footer di akhir
require_once '../includes/footer.php'; 
?>