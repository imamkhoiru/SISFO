<?php
require_once '../../includes/header.php';

// --- PENGATURAN FILTER ---
// Set default tanggal: 1 bulan terakhir
$tanggal_mulai = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$tanggal_selesai = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// --- LOGIKA UNTUK TABEL REKAPITULASI GURU ---
// Query untuk menghitung jumlah pertemuan dan total durasi mengajar per guru
// Kita hanya menghitung jadwal yang sudah diisi absensinya (actual_pengajar_id IS NOT NULL)
$sql_rekap_guru = "SELECT 
                        g.nama_lengkap, g.nip,
                        COUNT(j.id) AS jumlah_pertemuan,
                        SUM(TIME_TO_SEC(TIMEDIFF(j.jam_selesai, j.jam_mulai))) AS total_detik_mengajar
                    FROM jadwal j
                    JOIN mst_guru g ON j.pengajar_id = g.id
                    WHERE 
                        j.actual_pengajar_id IS NOT NULL 
                        AND j.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'
                    GROUP BY g.id, g.nama_lengkap, g.nip
                    ORDER BY g.nama_lengkap ASC";

$result_rekap_guru = mysqli_query($db, $sql_rekap_guru);

// Fungsi helper untuk mengubah detik menjadi format Jam dan Menit
function format_durasi($total_detik) {
    if ($total_detik <= 0) {
        return '0 Jam 0 Menit';
    }
    $jam = floor($total_detik / 3600);
    $sisa_detik = $total_detik % 3600;
    $menit = floor($sisa_detik / 60);
    return "{$jam} Jam {$menit} Menit";
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Filter Laporan Aktivitas Guru</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-5">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $tanggal_mulai ?>">
                                </div>
                                <div class="col-md-5">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $tanggal_selesai ?>">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title float-start">Rekapitulasi Jam Mengajar Guru</h4>
                        </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Guru</th>
                                        <th>NIP</th>
                                        <th>Jumlah Pertemuan</th>
                                        <th>Total Jam Mengajar Efektif</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result_rekap_guru && mysqli_num_rows($result_rekap_guru) > 0): ?>
                                        <?php $no = 1; while($rekap = mysqli_fetch_assoc($result_rekap_guru)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($rekap['nama_lengkap']); ?></td>
                                            <td><?= htmlspecialchars($rekap['nip']); ?></td>
                                            <td><?= $rekap['jumlah_pertemuan']; ?> Sesi</td>
                                            <td><?= format_durasi($rekap['total_detik_mengajar']); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data aktivitas guru pada periode yang dipilih.</td>
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