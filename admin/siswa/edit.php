<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';
$upload_dir = '../../assets/uploads/foto_siswa/';
$siswa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($siswa_id <= 0) {
    header("Location: list.php");
    exit;
}

// Ambil data siswa saat ini untuk ditampilkan di form dan untuk foto lama
$sql_siswa = "SELECT * FROM mst_siswa WHERE id = $siswa_id";
$result_siswa = mysqli_query($db, $sql_siswa);
$siswa = mysqli_fetch_assoc($result_siswa);
if (!$siswa) {
    header("Location: list.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (Ambil semua data dari POST seperti di add.php)
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $nis = mysqli_real_escape_string($db, $_POST['nis']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $kelas_id = (int)$_POST['kelas_id'];
    $status = $_POST['status'];
    $foto_name = $siswa['foto']; // Gunakan foto lama sebagai default

    // Logika upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // ... (Logika validasi file sama seperti di add.php)
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (in_array($_FILES['foto']['type'], $allowed_types)) {
            // Hapus foto lama jika bukan default.png
            if ($siswa['foto'] != 'default.png' && file_exists($upload_dir . $siswa['foto'])) {
                unlink($upload_dir . $siswa['foto']);
            }
            $foto_name = time() . '_' . basename($_FILES['foto']['name']);
            move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
        } else {
             $error_message = "Format file tidak didukung. Harap upload file JPG, JPEG, atau PNG.";
        }
    }
    
    if (empty($error_message)) {
        $sql_update = "UPDATE mst_siswa SET 
                        nama_lengkap = '$nama_lengkap', nis = '$nis', jenis_kelamin = '$jenis_kelamin', 
                        kelas_id = '$kelas_id', status = '$status', foto = '$foto_name'
                    WHERE id = $siswa_id";

        if (mysqli_query($db, $sql_update)) {
            header("Location: list.php?status=edit_success");
            exit;
        } else {
            $error_message = "Gagal mengubah data: " . mysqli_error($db);
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Edit Data Siswa</h4></div>
                    <div class="card-body">
                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="edit.php?id=<?= $siswa_id; ?>" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($siswa['nama_lengkap']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                                <input type="text" class="form-control" id="nis" name="nis" value="<?= htmlspecialchars($siswa['nis']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki" <?= ($siswa['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($siswa['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
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
                                        $selected = ($kelas['id'] == $siswa['kelas_id']) ? 'selected' : '';
                                        echo "<option value='{$kelas['id']}' $selected>".htmlspecialchars($kelas['nama_kelas'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Foto Saat Ini</label><br>
                                <img src="<?= BASE_URL; ?>assets/uploads/foto_siswa/<?= htmlspecialchars($siswa['foto']); ?>" alt="Foto Siswa" width="100" class="img-thumbnail mb-2">
                                <br><label for="foto" class="form-label">Ganti Foto (Opsional)</label>
                                <input class="form-control" type="file" id="foto" name="foto">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                            </div>
                             <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= ($siswa['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="non-active" <?= ($siswa['status'] == 'non-active') ? 'selected' : ''; ?>>Non-Aktif</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="list.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>