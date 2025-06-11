<?php
// 1. Panggil file konfigurasi & session terlebih dahulu
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';

// 2. Lakukan SEMUA proses form (metode POST) di sini.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_kelas = mysqli_real_escape_string($db, $_POST['nama_kelas']);
    $wali_kelas_id = (int)$_POST['wali_kelas_id'];
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];

    if (empty($nama_kelas)) {
        $error_message = "Nama Kelas tidak boleh kosong.";
    } else {
        // Jika validasi lolos, baru buat dan eksekusi query SQL
        $sql = "INSERT INTO mst_kelas (nama_kelas, wali_kelas_id, status, created_by) 
                VALUES ('$nama_kelas', '$wali_kelas_id', '$status', '$created_by')";
        
        if (mysqli_query($db, $sql)) {
            // Panggil fungsi log SEBELUM redirect
            require_once '../../includes/functions.php';
            $new_kelas_id = mysqli_insert_id($db);
            log_activity($db, $_SESSION['user_id'], "Menambah kelas baru: " . $nama_kelas . " (ID: {$new_kelas_id})", "kelas/add.php");
            
            // Jika berhasil, redirect dan hentikan script
            header("Location: list.php?status=add_success");
            exit;
        } else {
            $error_message = "Gagal menyimpan data: " . mysqli_error($db);
        }
    }
}

// 3. BARU panggil header.php untuk mulai mencetak tampilan HTML.
require_once '../../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Tambah Data Kelas</h5>
                </div>
                <div class="card-body">
                    <?php if(!empty($error_message)): ?>
                        <div class="alert alert-danger"><?= $error_message; ?></div>
                    <?php endif; ?>

                    <form action="add.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_kelas" class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" required>
                        </div>
                        <div class="mb-3">
                            <label for="wali_kelas_id" class="form-label">Wali Kelas</label>
                            <select class="form-select" id="wali_kelas_id" name="wali_kelas_id" required>
                                <option value="">-- Pilih Wali Kelas --</option>
                                <?php
                                $guru_sql = "SELECT id, nama_lengkap FROM mst_guru WHERE role_id = 2 AND status = 'active'";
                                $guru_result = mysqli_query($db, $guru_sql);
                                while($guru = mysqli_fetch_assoc($guru_result)){
                                    echo "<option value='{$guru['id']}'>".htmlspecialchars($guru['nama_lengkap'])."</option>";
                                }
                                ?>
                            </select>
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

<?php
// Panggil footer di akhir
require_once '../../includes/footer.php'; 
?>