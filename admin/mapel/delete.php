<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

if ($_SESSION['role_id'] != 1) {
    header("Location: ../../index.php");
    exit;
}

$mapel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($mapel_id > 0) {
    // Hapus data dari database
    $sql = "DELETE FROM mst_mapel WHERE id = $mapel_id";
    if (mysqli_query($db, $sql)) {
        header("Location: list.php?status=delete_success");
        exit;
    } else {
        // Kemungkinan gagal karena foreign key constraint (mapel sudah dipakai di tabel jadwal)
        header("Location: list.php?status=delete_failed");
        exit;
    }
} else {
    header("Location: list.php");
    exit;
}
?>