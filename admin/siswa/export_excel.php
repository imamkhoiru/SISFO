<?php
require_once '../../includes/config.php';
require_once '../../includes/vendor/simplexlsxgen/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

// Query untuk mengambil semua data siswa beserta nama kelasnya
$sql = "SELECT s.nis, s.nama_lengkap, k.nama_kelas, s.jenis_kelamin, s.status 
        FROM mst_siswa s 
        LEFT JOIN mst_kelas k ON s.kelas_id = k.id 
        ORDER BY k.nama_kelas, s.nama_lengkap ASC";
$result = mysqli_query($db, $sql);

// Siapkan data dalam bentuk array, baris pertama adalah header
$data_for_excel = [
    ['<b>NIS</b>', '<b>Nama Lengkap</b>', '<b>Kelas</b>', '<b>Jenis Kelamin</b>', '<b>Status</b>']
];

// Isi data dari database
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_for_excel[] = [
            $row['nis'],
            $row['nama_lengkap'],
            $row['nama_kelas'],
            $row['jenis_kelamin'],
            ucfirst($row['status']) // Mengubah 'active' menjadi 'Active'
        ];
    }
}

// Buat dan unduh file Excel
$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data_for_excel);
$xlsx->downloadAs('data_siswa_'.date('Ymd').'.xlsx'); 

exit;
?>