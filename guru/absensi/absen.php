<?php
require_once '../includes/header.php'; // Panggil header milik guru

// Pastikan file functions.php juga dipanggil untuk logging nanti
require_once '../../includes/functions.php';

$jadwal_id = isset($_GET['jadwal_id']) ? (int)$_GET['jadwal_id'] : 0;
$guru_id = $_SESSION['user_id'];

// --- BAGIAN PEMROSESAN FORM (SAAT TOMBOL SUBMIT DIKLIK) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $posted_jadwal_id = isset($_POST['jadwal_id']) ? (int)$_POST['jadwal_id'] : 0;
    $kehadiran_data = isset($_POST['status_kehadiran']) ? $_POST['status_kehadiran'] : [];

    // Pastikan ada data yang disubmit
    if ($posted_jadwal_id > 0 && !empty($kehadiran_data)) {
        
        $hadir = 0; $sakit = 0; $izin = 0; $alpa = 0;

        // 1. Masukkan data kehadiran setiap siswa ke tabel trs_kehadiran
        foreach ($kehadiran_data as $siswa_id => $status) {
            $siswa_id_safe = (int)$siswa_id;
            $status_safe = mysqli_real_escape_string($db, $status);
            $sql_insert_absen = "INSERT INTO trs_kehadiran (jadwal_id, siswa_id, status_kehadiran, created_by) 
                                 VALUES ('$posted_jadwal_id', '$siswa_id_safe', '$status_safe', '$guru_id')";
            mysqli_query($db, $sql_insert_absen);

            // Hitung statistik untuk diupdate ke tabel jadwal
            if ($status == 'Hadir') $hadir++;
            if ($status == 'Sakit') $sakit++;
            if ($status == 'Izin') $izin++;
            if ($status == 'Alpa') $alpa++;
        }

        // 2. Siapkan dan eksekusi query untuk update statistik di tabel jadwal
        $jumlah_siswa = count($kehadiran_data);
        $sql_update_jadwal = "UPDATE jadwal SET 
                                actual_pengajar_id = '$guru_id',
                                actual_pengajar_time = NOW(),
                                jumlah_siswa = '$jumlah_siswa',
                                hadir = '$hadir',
                                sakit = '$sakit',
                                izin = '$izin',
                                alpa = '$alpa'
                              WHERE id = '$posted_jadwal_id'";
        
        // Eksekusi query update
        if(mysqli_query($db, $sql_update_jadwal)){
             // Catat log aktivitas setelah berhasil
             log_activity($db, $_SESSION['user_id'], "Mengisi absensi untuk jadwal ID: " . $posted_jadwal_id, "absensi/absen.php");
             
             // Redirect kembali ke dashboard guru dengan pesan sukses
             header("Location: ../dashboard.php?status=absen_success");
             exit;
        } else {
            // Jika query update gagal, tampilkan error (jarang terjadi)
            $error_message = "Gagal mengupdate statistik jadwal: " . mysqli_error($db);
        }

    } else {
        $error_message = "Tidak ada data siswa untuk diabsen atau ID jadwal tidak valid.";
    }
}

// --- BAGIAN UNTUK MENAMPILKAN FORM (SAAT HALAMAN DIBUKA) ---
if ($jadwal_id <= 0) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>ID Jadwal tidak valid.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Ambil detail jadwal untuk ditampilkan
$sql_jadwal = "SELECT j.*, mapel.nama_mapel, kelas.nama_kelas 
               FROM jadwal j
               JOIN mst_mapel mapel ON j.mapel_id = mapel.id
               JOIN mst_kelas kelas ON j.kelas_id = kelas.id
               WHERE j.id = '$jadwal_id' AND j.pengajar_id = '$guru_id' AND j.actual_pengajar_id IS NULL"; // Hanya tampilkan jika belum diabsen
$result_jadwal = mysqli_query($db, $sql_jadwal);
$jadwal = mysqli_fetch_assoc($result_jadwal);

if (!$jadwal) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Jadwal tidak ditemukan, sudah diabsen, atau Anda tidak berhak mengaksesnya.</div></div>";
    require_once '../includes/footer.php';
    exit;
}

// Ambil daftar siswa dari kelas yang sesuai dengan jadwal
$kelas_id = $jadwal['kelas_id'];
$sql_siswa = "SELECT * FROM mst_siswa WHERE kelas_id = '$kelas_id' AND status = 'active' ORDER BY nama_lengkap ASC";
$result_siswa = mysqli_query($db, $sql_siswa);

?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Formulir Absensi Kehadiran</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message ?></div>
            <?php endif; ?>

            <h5>Detail Jadwal</h5>
            <table class="table table-bordered w-50">
                <tr>
                    <th style="width: 30%;">Mata Pelajaran</th>
                    <td><?= htmlspecialchars($jadwal['nama_mapel']); ?></td>
                </tr>
                <tr>
                    <th>Kelas</th>
                    <td><?= htmlspecialchars($jadwal['nama_kelas']); ?></td>
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

            <?php if ($result_siswa && mysqli_num_rows($result_siswa) > 0): ?>
                <h5>Daftar Siswa</h5>
                <form action="absen.php" method="POST">
                    <input type="hidden" name="jadwal_id" value="<?= $jadwal_id; ?>">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; while($siswa = mysqli_fetch_assoc($result_siswa)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($siswa['nama_lengkap']); ?></td>
                                <td>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_kehadiran[<?= $siswa['id']; ?>]" id="hadir_<?= $siswa['id']; ?>" value="Hadir" checked>
                                        <label class="form-check-label" for="hadir_<?= $siswa['id']; ?>">Hadir</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_kehadiran[<?= $siswa['id']; ?>]" id="sakit_<?= $siswa['id']; ?>" value="Sakit">
                                        <label class="form-check-label" for="sakit_<?= $siswa['id']; ?>">Sakit</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_kehadiran[<?= $siswa['id']; ?>]" id="izin_<?= $siswa['id']; ?>" value="Izin">
                                        <label class="form-check-label" for="izin_<?= $siswa['id']; ?>">Izin</label>
                                    </div>
                                     <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="status_kehadiran[<?= $siswa['id']; ?>]" id="alpa_<?= $siswa['id']; ?>" value="Alpa">
                                        <label class="form-check-label" for="alpa_<?= $siswa['id']; ?>">Alpa</label>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success mt-3"><i class="fa-solid fa-floppy-disk"></i> Submit Absensi</button>
                    <a href="../dashboard.php" class="btn btn-secondary mt-3">Batal</a>
                </form>
            <?php else: ?>
                <div class="alert alert-warning">Tidak ada siswa yang terdaftar di kelas ini. Absensi tidak dapat dilakukan.</div>
                <a href="../dashboard.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>