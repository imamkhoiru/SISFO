<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../../index.php");
    exit;
}

$siswa_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($siswa_id > 0) {
    // 1. Ambil nama file foto dari database sebelum dihapus
    $sql_get_foto = "SELECT foto FROM mst_siswa WHERE id = $siswa_id";
    $result_foto = mysqli_query($db, $sql_get_foto);
    if ($row = mysqli_fetch_assoc($result_foto)) {
        $foto_to_delete = $row['foto'];

        // 2. Hapus record siswa dari database
        $sql_delete = "DELETE FROM mst_siswa WHERE id = $siswa_id";
        if (mysqli_query($db, $sql_delete)) {
            // 3. Jika record berhasil dihapus, hapus file fotonya dari server
            $file_path = '../../assets/uploads/foto_siswa/' . $foto_to_delete;
            if ($foto_to_delete != 'default.png' && file_exists($file_path)) {
                unlink($file_path);
            }
            header("Location: list.php?status=delete_success");
            exit;
        }
    }
} 

// Jika gagal atau ID tidak valid
header("Location: list.php?status=delete_failed");
exit;
?>