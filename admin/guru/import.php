<?php
// Panggil semua file yang dibutuhkan
require_once '../../includes/header.php';
require_once '../../includes/SimpleXLSX.php';

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
                // Lewati baris header pertama
                if ($is_header) {
                    $is_header = false;
                    continue;
                }

                // Ambil data dari setiap kolom
                $nip = mysqli_real_escape_string($db, $row[0] ?? '');
                $nama_lengkap = mysqli_real_escape_string($db, $row[1] ?? '');
                $username = mysqli_real_escape_string($db, $row[2] ?? '');
                $password = mysqli_real_escape_string($db, $row[3] ?? '');
                $nama_role = mysqli_real_escape_string($db, $row[4] ?? '');
                $jenis_kelamin = mysqli_real_escape_string($db, $row[5] ?? '');
                $nohp = mysqli_real_escape_string($db, $row[6] ?? '');

                // --- VALIDASI DATA ---
                if (empty($nama_lengkap) || empty($username) || empty($password) || empty($nama_role)) {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": Nama, Username, Password, dan Role tidak boleh kosong.";
                    continue;
                }

                $result_username = mysqli_query($db, "SELECT id FROM mst_guru WHERE username = '$username'");
                if (mysqli_num_rows($result_username) > 0) {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": Username '$username' sudah terdaftar.";
                    continue;
                }

                $result_role = mysqli_query($db, "SELECT id FROM mst_role WHERE nama_role = '$nama_role'");
                if (mysqli_num_rows($result_role) > 0) {
                    $role_data = mysqli_fetch_assoc($result_role);
                    $role_id = $role_data['id'];
                } else {
                    $gagal_count++;
                    $errors[] = "Baris " . ($r + 1) . ": Nama Role '$nama_role' tidak ditemukan.";
                    continue;
                }

                // --- INSERT DATA JIKA LOLOS VALIDASI ---
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_insert = "INSERT INTO mst_guru (nip, nama_lengkap, username, password, role_id, jenis_kelamin, nohp, status, created_by) 
                               VALUES ('$nip', '$nama_lengkap', '$username', '$hashed_password', '$role_id', '$jenis_kelamin', '$nohp', 'active', '{$_SESSION['user_id']}')";
                
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
                    <h5 class="card-title m-0">Impor Data Guru dari Excel</h5>
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
                        2. Isi data guru sesuai dengan kolom pada template. Pastikan **Nama Role** sesuai dengan yang ada di sistem (Contoh: 'Guru').<br>
                        3. Unggah file yang sudah diisi ke dalam form di bawah ini.
                    </div>
                    
                    <a href="../../assets/templates/template_import_guru.xlsx" class="btn btn-success mb-3">
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