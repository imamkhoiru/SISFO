<?php 
require_once 'includes/header.php';

$guru_id = $_SESSION['user_id'];
$today_date = date('Y-m-d'); 

// Ambil jadwal guru untuk hari ini
$sql_today = "SELECT j.*, mapel.nama_mapel, kelas.nama_kelas FROM jadwal j JOIN mst_mapel mapel ON j.mapel_id = mapel.id JOIN mst_kelas kelas ON j.kelas_id = kelas.id WHERE j.pengajar_id = '$guru_id' AND j.tanggal = '$today_date' ORDER BY j.jam_mulai ASC";
$result_today = mysqli_query($db, $sql_today);

// Ambil riwayat 20 jadwal sebelumnya yang sudah diisi absensinya
$sql_history = "SELECT j.*, mapel.nama_mapel, kelas.nama_kelas FROM jadwal j JOIN mst_mapel mapel ON j.mapel_id = mapel.id JOIN mst_kelas kelas ON j.kelas_id = kelas.id WHERE j.pengajar_id = '$guru_id' AND j.tanggal < '$today_date' AND j.actual_pengajar_id IS NOT NULL ORDER BY j.tanggal DESC, j.jam_mulai DESC LIMIT 20";
$result_history = mysqli_query($db, $sql_history);
?>

<div class="container py-4">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'absen_success'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Absensi berhasil disubmit!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h4>Jadwal Mengajar Hari Ini (<?= date('d M Y', strtotime($today_date)); ?>)</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php if(mysqli_num_rows($result_today) > 0): ?>
                    <?php while($jadwal = mysqli_fetch_assoc($result_today)): ?>
                    <div class="col-lg-4 col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($jadwal['nama_mapel']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">Kelas: <?= htmlspecialchars($jadwal['nama_kelas']); ?></h6>
                                <p class="card-text"><i class="fa-solid fa-clock"></i> <?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?></p>
                                <div class="mt-auto">
                                    <?php if(is_null($jadwal['actual_pengajar_id'])): ?>
                                        <a href="absensi/absen.php?jadwal_id=<?= $jadwal['id']; ?>" class="btn btn-primary w-100"><i class="fa-solid fa-user-check"></i> Lakukan Absensi</a>
                                    <?php else: ?>
                                        <a href="absensi/riwayat.php?jadwal_id=<?= $jadwal['id']; ?>" class="btn btn-success w-100"><i class="fa-solid fa-check"></i> Absensi Sudah Diisi</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12"><p class="text-center">Tidak ada jadwal mengajar hari ini.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h4>Riwayat Mengajar (20 Terakhir)</h4>
        </div>
        <div class="card-body">
           <div class="table-responsive">
               <table class="table table-hover">
                   <thead><tr><th>Tanggal</th><th>Jam</th><th>Mata Pelajaran</th><th>Kelas</th><th>Aksi</th></tr></thead>
                   <tbody>
                        <?php if($result_history && mysqli_num_rows($result_history) > 0): ?>
                            <?php while($history = mysqli_fetch_assoc($result_history)): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($history['tanggal'])); ?></td>
                                <td><?= date('H:i', strtotime($history['jam_mulai'])); ?></td>
                                <td><?= htmlspecialchars($history['nama_mapel']); ?></td>
                                <td><?= htmlspecialchars($history['nama_kelas']); ?></td>
                                <td><a href="absensi/riwayat.php?jadwal_id=<?= $history['id']; ?>" class="btn btn-info btn-sm"><i class="fa-solid fa-eye"></i> View Riwayat</a></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Tidak ada riwayat mengajar.</td></tr>
                        <?php endif; ?>
                   </tbody>
               </table>
           </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>