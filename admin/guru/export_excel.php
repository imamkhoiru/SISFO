<?php
require_once '../../includes/config.php';
// Path diperbaiki untuk menunjuk langsung ke file di folder includes
require_once '../../includes/SimpleXLSXGen.php'; 

use Shuchkin\SimpleXLSXGen;

// Query untuk mengambil data guru beserta nama rolenya
$sql = "SELECT g.nip, g.nama_lengkap, g.username, r.nama_role, g.jenis_kelamin, g.nohp, g.status 
        FROM mst_guru g 
        LEFT JOIN mst_role r ON g.role_id = r.id 
        ORDER BY g.nama_lengkap ASC";
$result = mysqli_query($db, $sql);

// Siapkan data dalam bentuk array
$data_for_excel = [
    ['<b>NIP</b>', '<b>Nama Lengkap</b>', '<b>Username</b>', '<b>Role</b>', '<b>Jenis Kelamin</b>', '<b>No. HP</b>', '<b>Status</b>']
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_for_excel[] = [
            $row['nip'],
            $row['nama_lengkap'],
            $row['username'],
            $row['nama_role'],
            $row['jenis_kelamin'],
            $row['nohp'],
            ucfirst($row['status'])
        ];
    }
}

// Buat dan unduh file Excel
$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data_for_excel);
$xlsx->downloadAs('data_guru_'.date('Ymd').'.xlsx');

exit;
?>