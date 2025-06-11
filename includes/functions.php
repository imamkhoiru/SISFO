<?php
// File: sekolah/includes/functions.php

/**
 * Fungsi untuk mencatat aktivitas pengguna ke dalam database.
 *
 * @param mysqli $db Objek koneksi database.
 * @param int $user_id ID pengguna yang melakukan aksi.
 * @param string $aktivitas Deskripsi aktivitas yang dilakukan.
 * @param string $halaman Nama halaman tempat aksi dilakukan.
 */
function log_activity($db, $user_id, $aktivitas, $halaman) {
    // Ambil data tambahan dari server
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    // Lakukan sanitasi untuk keamanan
    $user_id_safe = (int)$user_id;
    $aktivitas_safe = mysqli_real_escape_string($db, $aktivitas);
    $halaman_safe = mysqli_real_escape_string($db, $halaman);
    $ip_address_safe = mysqli_real_escape_string($db, $ip_address);
    $user_agent_safe = mysqli_real_escape_string($db, $user_agent);

    // Query untuk memasukkan log
    $sql = "INSERT INTO trs_log_aktivitas (user_id, aktivitas, halaman, ip_address, user_agent) 
            VALUES ('$user_id_safe', '$aktivitas_safe', '$halaman_safe', '$ip_address_safe', '$user_agent_safe')";
    
    // Eksekusi query tanpa menghiraukan hasilnya (fire and forget)
    mysqli_query($db, $sql);
}

?>