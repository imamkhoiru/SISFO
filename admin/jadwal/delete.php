<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../../index.php");
    exit;
}

$jadwal_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($jadwal_id > 0) {
    // Karena ada ON DELETE CASCADE pada tabel trs_kehadiran,
    // maka data kehadiran yang terkait akan otomatis terhapus.
    $sql = "DELETE FROM jadwal WHERE id = $jadwal_id";
    if (mysqli_query($db, $sql)) {
        header("Location: list.php?status=delete_success");
        exit;
    } else {
        header("Location: list.php?status=delete_failed");
        exit;
    }
} else {
    header("Location: list.php");
    exit;
}
?>