<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_mapel = mysqli_real_escape_string($db, $_POST['nama_mapel']);
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];

    if (empty($nama_mapel)) {
        $error_message = "Nama Mata Pelajaran tidak boleh kosong.";
    } else {
        $sql = "INSERT INTO mst_mapel (nama_mapel, status, created_by) 
                VALUES ('$nama_mapel', '$status', '$created_by')";
        
        if (mysqli_query($db, $sql)) {
            header("Location: list.php?status=add_success");
            exit;
        } else {
            $error_message = "Gagal menyimpan data: " . mysqli_error($db);
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
                    <div class="card-header">
                        <h4 class="card-title">Tambah Data Mata Pelajaran</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="add.php" method="POST">
                            <div class="mb-3">
                                <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
                                <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" required>
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