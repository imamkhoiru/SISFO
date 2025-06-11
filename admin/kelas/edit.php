<?php
// 1. Panggil file konfigurasi & session terlebih dahulu. File ini tidak mencetak HTML.
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';
$kelas_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($kelas_id <= 0) {
    header("Location: list.php");
    exit;
}

// 2. Lakukan SEMUA proses form (metode POST) di sini.
// Jika sukses, akan redirect dan script berhenti sebelum mencetak HTML apapun.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kelas = mysqli_real_escape_string($db, $_POST['nama_kelas']);
    $wali_kelas_id = (int)$_POST['wali_kelas_id'];
    $status = $_POST['status'];
    $modified_by = $_SESSION['user_id'];

    if (empty($nama_kelas)) {
        $error_message = "Nama Kelas tidak boleh kosong.";
    } else {
        $sql_update = "UPDATE mst_kelas SET 
                        nama_kelas = '$nama_kelas', 
                        wali_kelas_id = '$wali_kelas_id', 
                        status = '$status',
                        modified_by = '$modified_by'
                    WHERE id = $kelas_id";

        if (mysqli_query($db, $sql_update)) {
            // Panggil fungsi log SEBELUM redirect
            require_once '../../includes/functions.php';
            log_activity($db, $_SESSION['user_id'], "Mengubah data kelas: " . $nama_kelas . " (ID: {$kelas_id})", "kelas/edit.php");

            header("Location: list.php?status=edit_success");
            exit;
        } else {
            $error_message = "Gagal mengubah data: " . mysqli_error($db);
        }
    }
}

// 3. Ambil data dari database untuk ditampilkan di form.
// Bagian ini hanya berjalan jika bukan request POST yang sukses.
$sql_kelas = "SELECT * FROM mst_kelas WHERE id = $kelas_id";
$result_kelas = mysqli_query($db, $sql_kelas);
$kelas = mysqli_fetch_assoc($result_kelas);

if (!$kelas) {
    header("Location: list.php");
    exit;
}

// 4. BARU panggil header.php untuk mulai mencetak tampilan HTML.
require_once '../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Edit Data Kelas</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= $error_message; ?></div>
                    <?php endif; ?>

                    <form action="edit.php?id=<?= $kelas_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" value="<?= htmlspecialchars($kelas['nama_kelas']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="wali_kelas_id" class="form-label">Wali Kelas</label>
                            <select class="form-select" id="wali_kelas_id" name="wali_kelas_id" required>
                                <option value="">-- Pilih Wali Kelas --</option>
                                <?php
                                $guru_sql = "SELECT id, nama_lengkap FROM mst_guru WHERE role_id = 2 AND status = 'active'";
                                $guru_result = mysqli_query($db, $guru_sql);
                                while($guru = mysqli_fetch_assoc($guru_result)){
                                    $selected = ($guru['id'] == $kelas['wali_kelas_id']) ? 'selected' : '';
                                    echo "<option value='{$guru['id']}' $selected>".htmlspecialchars($guru['nama_lengkap'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                         <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" <?= ($kelas['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="non-active" <?= ($kelas['status'] == 'non-active') ? 'selected' : ''; ?>>Non-Aktif</option>
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

<?php 
// Panggil footer di akhir
require_once '../../includes/footer.php'; 
?>