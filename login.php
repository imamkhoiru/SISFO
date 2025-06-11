<?php
// Memanggil file-file yang diperlukan
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Jika pengguna sudah login, langsung arahkan ke index utama
if (isset($_SESSION['login_status']) && $_SESSION['login_status'] === true) {
    header("Location: index.php");
    exit;
}

$error_message = '';

// --- BLOK LOGIKA LOGIN YANG DIKEMBALIKAN ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan lakukan sanitasi
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $password = $_POST['password'];

    // Query untuk mencari user berdasarkan username
    $sql = "SELECT id, nama_lengkap, username, password, role_id, status FROM mst_guru WHERE username = '$username'";
    $result = mysqli_query($db, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password dan pastikan status user 'active'
        if (password_verify($password, $user['password']) && $user['status'] == 'active') {
            
            // Jika berhasil, set session
            $_SESSION['login_status'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role_id'] = $user['role_id'];

            // Catat log aktivitas
            log_activity($db, $_SESSION['user_id'], "Berhasil login ke sistem.", "login.php");

            // Arahkan ke halaman index utama
            header("Location: index.php");
            exit;

        } else {
            // Jika password tidak cocok atau user tidak aktif
            $error_message = "Username atau password salah, atau akun Anda tidak aktif.";
        }
    } else {
        // Jika username tidak ditemukan
        $error_message = "Username atau password salah.";
    }
}
// --- AKHIR BLOK LOGIKA LOGIN ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Informasi Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            background-color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="card-body p-0">
                <div class="text-center mb-4">
                    <img src="<?= BASE_URL; ?>assets/images/logo.png" alt="Logo Sekolah" width="60" class="mb-3">
                    <h3 class="card-title fw-bold">Sistem Informasi Sekolah</h3>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert"><?= $error_message; ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>