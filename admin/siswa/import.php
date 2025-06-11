<?php
// Panggil semua file yang dibutuhkan
require_once '../../includes/header.php';
// require_once '../../includes/config.php'; // Tidak perlu, sudah ada di header
require_once '../../includes/SimpleXLSX.php'; // <-- PATH SUDAH DIPERBAIKI

use Shuchkin\SimpleXLSX;

$sukses_count = 0;
$gagal_count = 0;
$errors = [];

// Logika pemrosesan file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['file_excel'])) {
    if ($_FILES['file_excel']['error'] === UPLOAD_ERR_OK) {
        $file_path = $_FILES['file_excel']['tmp_name'];

        if ($xlsx = SimpleXLSX::parse($file_path)) {
            $is_header = true;
            foreach ($xlsx->rows() as $r => $row) {
                if ($is_header) {
                    $is_header = false;
                    continue;
                }
                $nis = mysqli_real_escape_string($db, $row[0] ?? '');
                $nama_lengkap = mysqli_real_escape_string($db, $row[1] ?? '');
                $jenis_kelamin = mysqli_real_escape_string($db, $row[2] ?? '');
                $nama_kelas = mysqli_real_escape_string($db, $row[3] ?? '');

                // Validasi data...
                if (empty($nis) || empty($nama_lengkap) || empty($nama_kelas)) {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": NIS, Nama Lengkap, dan Nama Kelas tidak boleh kosong.";
                    continue;
                }
                $result_nis = mysqli_query($db, "SELECT id FROM mst_siswa WHERE nis = '$nis'");
                if (mysqli_num_rows($result_nis) > 0) {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": NIS '$nis' sudah terdaftar.";
                    continue;
                }
                $result_kelas = mysqli_query($db, "SELECT id FROM mst_kelas WHERE nama_kelas = '$nama_kelas'");
                if (mysqli_num_rows($result_kelas) > 0) {
                    $kelas_data = mysqli_fetch_assoc($result_kelas);
                    $kelas_id = $kelas_data['id'];
                } else {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": Nama Kelas '$nama_kelas' tidak ditemukan.";
                    continue;
                }

                // Insert data jika lolos validasi
                $sql_insert = "INSERT INTO mst_siswa (nis, nama_lengkap, jenis_kelamin, kelas_id, status, foto, created_by) 
                               VALUES ('$nis', '$nama_lengkap', '$jenis_kelamin', '$kelas_id', 'active', 'default.png', '{$_SESSION['user_id']}')";
                if (mysqli_query($db, $sql_insert)) {
                    $sukses_count++;
                } else {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": Gagal menyimpan ke database. Error: " . mysqli_error($db);
                }
            }
        } else {
            $errors[] = 'Gagal membuka file Excel: ' . SimpleXLSX::parseError();
        }
    } else {
        $errors[] = 'Error saat mengunggah file. Kode: ' . $_FILES['file_excel']['error'];
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Impor Data Siswa dari Excel</h5>
                </div>
                <div class="card-body">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="alert alert-info">
                            <h4>Hasil Impor</h4>
                            <p>
                                <span class="badge bg-success">Berhasil: <?= $sukses_count ?> data</span><br>
                                <span class="badge bg-danger">Gagal: <?= $gagal_count ?> data</span>
                            </p>
                            <?php if (!empty($errors)): ?>
                                <strong>Detail Kegagalan:</strong>
                                <ul class="list-unstyled" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($errors as $error): ?>
                                        <li><small><?= htmlspecialchars($error) ?></small></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="alert alert-primary">
                        <strong>Petunjuk:</strong><br>
                        1. Unduh file template Excel yang sudah disediakan.<br>
                        2. Isi data siswa sesuai dengan kolom pada template. Pastikan **Nama Kelas** sesuai dengan data yang ada di sistem.<br>
                        3. Unggah file yang sudah diisi ke dalam form di bawah ini.
                    </div>
                    
                    <a href="../../assets/templates/template_import_siswa.xlsx" class="btn btn-success mb-3">
                        <i class="fa-solid fa-file-excel"></i> Unduh Template
                    </a>

                    <form action="import.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="file_excel" class="form-label">Pilih File Excel (.xlsx)</label>
                            <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".xlsx" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-upload"></i> Mulai Proses Impor
                        </button>
                        <a href="list.php" class="btn btn-secondary">Kembali</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>