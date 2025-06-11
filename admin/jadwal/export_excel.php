<?php
require_once '../../includes/config.php';
require_once '../../includes/vendor/simplexlsxgen/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

// Query untuk mengambil semua data jadwal dengan nama lengkapnya
$sql = "SELECT j.tanggal, j.hari, j.jam_mulai, j.jam_selesai, mapel.nama_mapel, kelas.nama_kelas, guru.nama_lengkap AS nama_guru 
        FROM jadwal j
        LEFT JOIN mst_mapel mapel ON j.mapel_id = mapel.id
        LEFT JOIN mst_guru guru ON j.pengajar_id = guru.id
        LEFT JOIN mst_kelas kelas ON j.kelas_id = kelas.id
        ORDER BY j.tanggal DESC, j.jam_mulai ASC";
$result = mysqli_query($db, $sql);

// Siapkan data dalam bentuk array, baris pertama adalah header
$data_for_excel = [
    ['<b>Tanggal</b>', '<b>Hari</b>', '<b>Jam Mulai</b>', '<b>Jam Selesai</b>', '<b>Mata Pelajaran</b>', '<b>Kelas</b>', '<b>Guru Pengajar</b>']
];

// Isi data dari database
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data_for_excel[] = [
            date('d-m-Y', strtotime($row['tanggal'])),
            $row['hari'],
            date('H:i', strtotime($row['jam_mulai'])),
            date('H:i', strtotime($row['jam_selesai'])),
            $row['nama_mapel'],
            $row['nama_kelas'],
            $row['nama_guru']
        ];
    }
}

// Buat dan unduh file Excel
$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data_for_excel);
$xlsx->downloadAs('data_jadwal_pelajaran_'.date('Ymd').'.xlsx'); 

exit;
?>