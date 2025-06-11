<?php
// Panggil config.php untuk koneksi database dan session
require_once '../../includes/config.php';
// Panggil session.php untuk memastikan user sudah login
require_once '../../includes/session.php';

$error_message = '';

// Proses form jika disubmit (LOGIKA INI HARUS DI ATAS SEBELUM ADA OUTPUT HTML)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_lengkap = mysqli_real_escape_string($db, $_POST['nama_lengkap']);
    $nip = mysqli_real_escape_string($db, $_POST['nip']);
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $nohp = mysqli_real_escape_string($db, $_POST['nohp']);
    $status = $_POST['status'];
    $created_by = $_SESSION['user_id'];

    // ... (di dalam blok if POST, setelah query insert berhasil)
    if (mysqli_query($db, $sql)) {
        require_once '../../includes/functions.php';
        $new_user_id = mysqli_insert_id($db);
        log_activity($db, $_SESSION['user_id'], "Menambah guru baru: " . $nama_lengkap . " (ID: {$new_user_id})", "guru/add.php");

        header("Location: list.php?status=add_success");
        exit;
    } // ...
    // Validasi dasar
    if (empty($nama_lengkap) || empty($username) || empty($password) || empty($role_id)) {
        $error_message = "Nama Lengkap, Username, Password, dan Role tidak boleh kosong.";
    } else {
        // Cek apakah username sudah ada
        $check_sql = "SELECT id FROM mst_guru WHERE username = '$username'";
        $check_result = mysqli_query($db, $check_sql);
        if (mysqli_num_rows($check_result) > 0) {
            $error_message = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Query untuk insert data
            $sql = "INSERT INTO mst_guru (nama_lengkap, nip, username, password, role_id, jenis_kelamin, nohp, status, created_by) 
                    VALUES ('$nama_lengkap', '$nip', '$username', '$hashed_password', '$role_id', '$jenis_kelamin', '$nohp', '$status', '$created_by')";

            if (mysqli_query($db, $sql)) {
                // Redirect ke halaman list dengan status sukses
                header("Location: list.php?status=add_success");
                exit; // Penting untuk menghentikan eksekusi setelah redirect
            } else {
                $error_message = "Gagal menyimpan data: " . mysqli_error($db);
            }
        }
    }
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
                        <h4 class="card-title">Tambah Data Guru</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="add.php" method="POST">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            <div class="mb-3">
                                <label for="nip" class="form-label">NIP</label>
                                <input type="text" class="form-control" id="nip" name="nip">
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Role</label>
                                <select class="form-select" id="role_id" name="role_id" required>
                                    <option value="">-- Pilih Role --</option>
                                    <?php
                                    $role_sql = "SELECT * FROM mst_role";
                                    $role_result = mysqli_query($db, $role_sql);
                                    while ($role = mysqli_fetch_assoc($role_result)) {
                                        echo "<option value='{$role['id']}'>" . htmlspecialchars($role['nama_role']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                                <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nohp" class="form-label">No. Handphone</label>
                                <input type="text" class="form-control" id="nohp" name="nohp">
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