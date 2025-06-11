<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../../index.php");
    exit;
}

$kelas_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($kelas_id > 0) {
    $sql = "DELETE FROM mst_kelas WHERE id = $kelas_id";
    if (mysqli_query($db, $sql)) {
        header("Location: list.php?status=delete_success");
        exit;

        // ... (setelah query delete berhasil)
        if (mysqli_query($db, $sql)) {
            require_once '../../includes/functions.php';
            log_activity($db, $_SESSION['user_id'], "Menghapus data kelas ID: " . $kelas_id, "kelas/delete.php");

            header("Location: list.php?status=delete_success");
            exit;
        } // ...
    } else {
        // Mungkin akan gagal jika ada foreign key constraint, misal jika kelas masih punya siswa
        header("Location: list.php?status=delete_failed");
        exit;
    }
} else {
    header("Location: list.php");
    exit;
}
