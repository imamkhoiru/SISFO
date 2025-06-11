<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';
$guru_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($guru_id <= 0) {
    header("Location: list.php");
    exit;
}

// Proses form jika disubmit (LOGIKA INI HARUS DI ATAS)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $nip = mysqli_real_escape_string($db, $_POST['nip']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $nohp = mysqli_real_escape_string($db, $_POST['nohp']);
    $status = $_POST['status'];
    $modified_by = $_SESSION['user_id'];

    // ... (di dalam blok if POST, setelah query update berhasil)
    if (mysqli_query($db, $sql)) {
        require_once '../../includes/functions.php';
        log_activity($db, $_SESSION['user_id'], "Mengubah data guru ID: " . $guru_id, "guru/edit.php");

        header("Location: list.php?status=edit_success");
        exit;
    } // ...

    if (empty($nama_lengkap) || empty($username) || empty($role_id)) {
        $error_message = "Nama Lengkap, Username, dan Role tidak boleh kosong.";
    } else {
        $guru_current_data_sql = "SELECT username FROM mst_guru WHERE id = $guru_id";
        $guru_current_data_result = mysqli_query($db, $guru_current_data_sql);
        $guru_current_data = mysqli_fetch_assoc($guru_current_data_result);

        if ($username != $guru_current_data['username']) {
            $check_sql = "SELECT id FROM mst_guru WHERE username = '$username'";
            $check_result = mysqli_query($db, $check_sql);
            if (mysqli_num_rows($check_result) > 0) {
                $error_message = "Username sudah digunakan. Silakan pilih username lain.";
            }
        }

        if (empty($error_message)) {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $password_update_sql = ", password = '$hashed_password'";
            } else {
                $password_update_sql = "";
            }

            $sql_update = "UPDATE mst_guru SET 
                        nama_lengkap = '$nama_lengkap', 
                        nip = '$nip', 
                        username = '$username', 
                        role_id = '$role_id', 
                        jenis_kelamin = '$jenis_kelamin', 
                        nohp = '$nohp', 
                        status = '$status',
                        modified_by = '$modified_by'
                        $password_update_sql
                    WHERE id = $guru_id";

            if (mysqli_query($db, $sql_update)) {
                header("Location: list.php?status=edit_success");
                exit;
            } else {
                $error_message = "Gagal mengubah data: " . mysqli_error($db);
            }
        }
    }
}

// Ambil data guru yang akan diedit untuk ditampilkan di form
$sql_guru = "SELECT * FROM mst_guru WHERE id = $guru_id";
$result_guru = mysqli_query($db, $sql_guru);
$guru = mysqli_fetch_assoc($result_guru);

if (!$guru) {
    header("Location: list.php");
    exit;
}

// BARU CETAK TAMPILAN SETELAH SEMUA LOGIKA SELESAI
require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Data Guru</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="edit.php?id=<?= $guru_id; ?>" method="POST">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($guru['nama_lengkap']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip" value="<?= htmlspecialchars($guru['nip']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($guru['username']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">-- Pilih Role --</option>
                                    <?php
                                    $role_sql = "SELECT * FROM mst_role";
                                    $role_result = mysqli_query($db, $role_sql);
                                    while ($role = mysqli_fetch_assoc($role_result)) {
                                        $selected = ($role['id'] == $guru['role_id']) ? 'selected' : '';
                                        echo "<option value='{$role['id']}' $selected>" . htmlspecialchars($role['nama_role']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki" <?= ($guru['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($guru['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label">No. Handphone</label>
                                <input type="text" class="form-control" id="nohp" name="nohp" value="<?= htmlspecialchars($guru['nohp']); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?= ($guru['status'] == 'active') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="non-active" <?= ($guru['status'] == 'non-active') ? 'selected' : ''; ?>>Non-Aktif</option>
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