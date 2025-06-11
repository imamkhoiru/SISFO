<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';
$upload_dir = '../../assets/uploads/foto_siswa/';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $nis = mysqli_real_escape_string($db, $_POST['nis']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas_id = (int)$_POST['kelas_id'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];
    $foto_name = 'default.png'; // Nama file default jika tidak ada foto diupload

    if (empty($nama_lengkap) || empty($nis) || $kelas_id == 0) {
        $error_message = "Nama, NIS, dan Kelas tidak boleh kosong.";
    } else {
        // Logika untuk upload foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            $file_type = $_FILES['foto']['type'];

            if (in_array($file_type, $allowed_types)) {
                $foto_tmp_name = $_FILES['foto']['tmp_name'];
                // Buat nama file unik untuk mencegah duplikasi
                $foto_name = time() . '_' . basename($_FILES['foto']['name']);
                $target_file = $upload_dir . $foto_name;

                if (!move_uploaded_file($foto_tmp_name, $target_file)) {
                    $error_message = "Gagal mengupload file foto.";
                    $foto_name = 'default.png'; // Kembali ke default jika gagal
                }
            } else {
                $error_message = "Format file tidak didukung. Harap upload file JPG, JPEG, atau PNG.";
            }
        }

        if (empty($error_message)) {
            $sql = "INSERT INTO mst_siswa (nama_lengkap, nis, jenis_kelamin, kelas_id, status, foto, created_by) 
                    VALUES ('$nama_lengkap', '$nis', '$jenis_kelamin', '$kelas_id', '$status', '$foto_name', '$created_by')";
            
            if (mysqli_query($db, $sql)) {
                header("Location: list.php?status=add_success");
                exit;
            } else {
                $error_message = "Gagal menyimpan data: " . mysqli_error($db);
            }
        }
    }
}

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Tambah Data Siswa</h4></div>
                    <div class="card-body">
                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>
                        
                        <form action="add.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                                <input type="text" class="form-control" id="nis" name="nis" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="kelas_id" class="form-label">Kelas</label>
                                <select class="form-select" id="kelas_id" name="kelas_id" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php
                                    $kelas_sql = "SELECT id, nama_kelas FROM mst_kelas WHERE status = 'active'";
                                    $kelas_result = mysqli_query($db, $kelas_sql);
                                    while($kelas = mysqli_fetch_assoc($kelas_result)){
                                        echo "<option value='{$kelas['id']}'>".htmlspecialchars($kelas['nama_kelas'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Siswa</label>
                                <input class="form-control" type="file" id="foto" name="foto">
                                <small class="form-text text-muted">Format: JPG, PNG. Ukuran maks: 2MB.</small>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active">Aktif</option>
                                    <option value="non-active">Non-Aktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="list.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>