<?php
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($jadwal_id <= 0) {
    echo "<div class='main-content'><div class='alert alert-danger'>ID Jadwal tidak valid.</div></div>";
    require_once '../../includes/footer.php';
    exit;
}

// Query 1: Ambil detail jadwal
$sql_jadwal = "SELECT j.*, mapel.nama_mapel, guru.nama_lengkap AS nama_guru, kelas.nama_kelas 
               FROM jadwal j
               LEFT JOIN mst_mapel mapel ON j.mapel_id = mapel.id
               LEFT JOIN mst_guru guru ON j.pengajar_id = guru.id
               LEFT JOIN mst_kelas kelas ON j.kelas_id = kelas.id
               WHERE j.id = $jadwal_id";
$result_jadwal = mysqli_query($db, $sql_jadwal);
$jadwal = mysqli_fetch_assoc($result_jadwal);

if (!$jadwal) {
    echo "<div class='main-content'><div class='alert alert-danger'>Data Jadwal tidak ditemukan.</div></div>";
    require_once '../../includes/footer.php';
    exit;
}

// Query 2: Ambil data kehadiran untuk jadwal ini
$sql_kehadiran = "SELECT s.nis, s.nama_lengkap, t.status_kehadiran 
                  FROM trs_kehadiran t
                  JOIN mst_siswa s ON t.siswa_id = s.id
                  WHERE t.jadwal_id = $jadwal_id
                  ORDER BY s.nama_lengkap ASC";
$result_kehadiran = mysqli_query($db, $sql_kehadiran);

// Fungsi untuk memberikan warna pada status kehadiran
function get_badge_class($status) {
    switch ($status) {
        case 'Hadir': return 'bg-success';
        case 'Izin': return 'bg-info';
        case 'Sakit': return 'bg-warning text-dark';
        case 'Alpa': return 'bg-danger';
        default: return 'bg-secondary';
    }
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Detail Kehadiran Siswa</h4>
                    </div>
                    <div class="card-body">
                        <h5>Detail Sesi</h5>
                        <table class="table table-bordered table-sm w-50">
                            <tr>
                                <th style="width: 30%;">Mata Pelajaran</th>
                                <td><?= htmlspecialchars($jadwal['nama_mapel']); ?></td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td><?= htmlspecialchars($jadwal['nama_kelas']); ?></td>
                            </tr>
                            <tr>
                                <th>Guru Pengajar</th>
                                <td><?= htmlspecialchars($jadwal['nama_guru']); ?></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td><?= date('d M Y', strtotime($jadwal['tanggal'])); ?></td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td><?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?></td>
                            </tr>
                        </table>

                        <hr>
                        <h5>Daftar Kehadiran</h5>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result_kehadiran && mysqli_num_rows($result_kehadiran) > 0): ?>
                                        <?php $no = 1; while($kehadiran = mysqli_fetch_assoc($result_kehadiran)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
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
                                            <td colspan="4" class="text-center">Data absensi untuk sesi ini belum diisi atau tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <a href="../jadwal/list.php" class="btn btn-secondary mt-3">Kembali ke Daftar Jadwal</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>