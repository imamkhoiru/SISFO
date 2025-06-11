<?php
// File ini hanya untuk membuat hash baru.
// Anda bisa mengganti 'password123' jika ingin menggunakan password lain.
$password_untuk_dihash = 'password123';

// Membuat hash menggunakan algoritma default yang paling aman
$hash_baru = password_hash($password_untuk_dihash, PASSWORD_DEFAULT);

// Menampilkan hash agar mudah disalin
echo '<h3>Hash Baru Telah Dibuat</h3>';
echo 'Silakan salin seluruh teks di bawah ini dan gunakan untuk memperbarui database Anda:';
echo '<br><br>';
echo '<textarea rows="3" cols="70" readonly onclick="this.select();">' . htmlspecialchars($hash_baru) . '</textarea>';
?>