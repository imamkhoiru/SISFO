<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

// Pastikan hanya admin yang bisa mengakses
if ($_SESSION['role_id'] != 1) {
    header("Location: ../../index.php");
    exit;
}

$guru_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($guru_id > 0) {
    // Jangan biarkan admin menghapus dirinya sendiri
    if ($guru_id == $_SESSION['user_id']) {
        header("Location: list.php?status=delete_failed_self");
        exit;
    }
    // ... (di dalam blok if (guru_id > 0), setelah query delete berhasil)
    if (mysqli_query($db, $sql)) {
        require_once '../../includes/functions.php';
        log_activity($db, $_SESSION['user_id'], "Menghapus data guru ID: " . $guru_id, "guru/delete.php");

        header("Location: list.php?status=delete_success");
        exit;
    } // ...

    $sql = "DELETE FROM mst_guru WHERE id = $guru_id";
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
