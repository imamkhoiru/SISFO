<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';
$mapel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($mapel_id <= 0) {
    header("Location: list.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_mapel = mysqli_real_escape_string($db, $_POST['nama_mapel']);
    $status = $_POST['status'];
    $modified_by = $_SESSION['user_id'];

    if (empty($nama_mapel)) {
        $error_message = "Nama Mata Pelajaran tidak boleh kosong.";
    } else {
        $sql_update = "UPDATE mst_mapel SET 
                        nama_mapel = '$nama_mapel', 
                        status = '$status',
                        modified_by = '$modified_by'
                    WHERE id = $mapel_id";

        if (mysqli_query($db, $sql_update)) {
            header("Location: list.php?status=edit_success");
            exit;
        } else {
            $error_message = "Gagal mengubah data: " . mysqli_error($db);
        }
    }
}

$sql_mapel = "SELECT * FROM mst_mapel WHERE id = $mapel_id";
$result_mapel = mysqli_query($db, $sql_mapel);
$mapel = mysqli_fetch_assoc($result_mapel);

if (!$mapel) {
    header("Location: list.php");
    exit;
}

require_once '../../includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Data Mata Pelajaran</h4>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="edit.php?id=<?= $mapel_id; ?>" method="POST">
                            <div class="mb-3">
                                <label for="nama_mapel" class="form-label">Nama Mata Pelajaran</label>
                                <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" value="<?= htmlspecialchars($mapel['nama_mapel']); ?>" required>
                            </div>
                             <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= ($mapel['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="non-active" <?= ($mapel['status'] == 'non-active') ? 'selected' : ''; ?>>Non-Aktif</option>
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