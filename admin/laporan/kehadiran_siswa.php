<?php
require_once '../../includes/header.php';

// --- PENGATURAN FILTER ---
$tanggal_mulai = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$tanggal_selesai = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$kelas_filter = isset($_GET['kelas_id']) && $_GET['kelas_id'] != '' ? (int)$_GET['kelas_id'] : null;

// --- LOGIKA UNTUK TABEL REKAPITULASI ---
$sql_rekap = "SELECT 
                s.id, s.nama_lengkap, k.nama_kelas,
                COUNT(CASE WHEN t.status_kehadiran = 'Hadir' THEN 1 END) AS hadir,
                COUNT(CASE WHEN t.status_kehadiran = 'Izin' THEN 1 END) AS izin,
                COUNT(CASE WHEN t.status_kehadiran = 'Sakit' THEN 1 END) AS sakit,
                COUNT(CASE WHEN t.status_kehadiran = 'Alpa' THEN 1 END) AS alpa
              FROM trs_kehadiran t
              JOIN mst_siswa s ON t.siswa_id = s.id
              JOIN jadwal j ON t.jadwal_id = j.id
              JOIN mst_kelas k ON s.kelas_id = k.id
              WHERE j.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
if ($kelas_filter) {
    $sql_rekap .= " AND s.kelas_id = $kelas_filter";
}
$sql_rekap .= " GROUP BY s.id ORDER BY k.nama_kelas, s.nama_lengkap";
$result_rekap = mysqli_query($db, $sql_rekap);

// --- LOGIKA UNTUK GRAFIK KESELURUHAN ---
$sql_grafik = "SELECT 
                 status_kehadiran, COUNT(*) as jumlah
               FROM trs_kehadiran t
               JOIN jadwal j ON t.jadwal_id = j.id
               WHERE j.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";
if ($kelas_filter) {
    $sql_grafik .= " AND j.kelas_id = $kelas_filter";
}
$sql_grafik .= " GROUP BY status_kehadiran";
$result_grafik = mysqli_query($db, $sql_grafik);

$data_grafik = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
while($row = mysqli_fetch_assoc($result_grafik)){
    if (isset($data_grafik[$row['status_kehadiran']])) {
        $data_grafik[$row['status_kehadiran']] = (int)$row['jumlah'];
    }
}
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Filter Laporan Kehadiran Siswa</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $tanggal_mulai ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $tanggal_selesai ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select name="kelas_id" id="kelas_id" class="form-select">
                                        <option value="">Semua Kelas</option>
                                        <?php
                                        $kelas_sql = "SELECT id, nama_kelas FROM mst_kelas WHERE status = 'active' ORDER BY nama_kelas";
                                        $kelas_result = mysqli_query($db, $kelas_sql);
                                        while($kelas = mysqli_fetch_assoc($kelas_result)){
                                            $selected = ($kelas['id'] == $kelas_filter) ? 'selected' : '';
                                            echo "<option value='{$kelas['id']}' $selected>".htmlspecialchars($kelas['nama_kelas'])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Terapkan Filter</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12">
                 <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Grafik Kehadiran Keseluruhan</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-kehadiran"></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title float-start">Tabel Rekapitulasi Kehadiran</h4>
                        
                        <a href="export_excel_siswa.php?start_date=<?= $tanggal_mulai ?>&end_date=<?= $tanggal_selesai ?>&kelas_id=<?= $kelas_filter ?>" class="btn btn-success btn-sm float-end">
                            <i class="fa-solid fa-file-excel"></i> Export ke Excel
                        </a>
                        
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Hadir</th>
                                        <th>Izin</th>
                                        <th>Sakit</th>
                                        <th>Alpa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($result_rekap) > 0): ?>
                                        <?php $no = 1; while($rekap = mysqli_fetch_assoc($result_rekap)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($rekap['nama_lengkap']); ?></td>
                                            <td><?= htmlspecialchars($rekap['nama_kelas']); ?></td>
                                            <td><?= $rekap['hadir']; ?></td>
                                            <td><?= $rekap['izin']; ?></td>
                                            <td><?= $rekap['sakit']; ?></td>
                                            <td><?= $rekap['alpa']; ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data kehadiran pada periode yang dipilih.</td>
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

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const dataGrafik = <?= json_encode(array_values($data_grafik)); ?>;
    const kategoriGrafik = <?= json_encode(array_keys($data_grafik)); ?>;

    var options = {
        chart: {
            type: 'donut',
            height: 350
        },
        series: dataGrafik,
        labels: kategoriGrafik,
        colors: ['#28a745', '#ffc107', '#17a2b8', '#dc3545'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#chart-kehadiran"), options);
    chart.render();
});
</script>

<?php require_once '../../includes/footer.php'; ?>