<?php
// Panggil file koneksi dan library dari lokasi baru yang lebih sederhana
require_once '../../includes/config.php';
require_once '../../includes/SimpleXLSXGen.php'; // <--- PERUBAHAN UTAMA DI SINI

// Logika lainnya tetap sama persis

// Tangkap filter dari URL
$tanggal_mulai = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$tanggal_selesai = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');
$kelas_filter = isset($_GET['kelas_id']) && $_GET['kelas_id'] != '' ? (int)$_GET['kelas_id'] : null;

// Query untuk mengambil data rekap
$sql_rekap = "SELECT 
                s.nama_lengkap, k.nama_kelas,
                COUNT(CASE WHEN t.status_kehadiran = 'Hadir' THEN 1 END) AS hadir,
                COUNT(CASE WHEN t.status_kehadiran = 'Izin' THEN 1 END) AS izin,
                COUNT(CASE WHEN t.status_kehadiran = 'Sakit' THEN 1 END) AS sakit,
                COUNT(CASE WHEN t.status_kehadiran = 'Alpa' THEN 1 END) AS alpa
              FROM trs_kehadiran t
              JOIN mst_siswa s ON t.siswa_id = s.id
              JOIN jadwal j ON t.jadwal_id = j.id
              JOIN mst_kelas k ON s.kelas_id = k.id
              WHERE j.tanggal BETWEEN '$tanggal_mulai' AND '$tanggal_selesai'";

if ($kelas_filter) {
    $sql_rekap .= " AND s.kelas_id = $kelas_filter";
}
$sql_rekap .= " GROUP BY s.id ORDER BY k.nama_kelas, s.nama_lengkap";
$result_rekap = mysqli_query($db, $sql_rekap);

// Siapkan data dalam bentuk array
$data_for_excel = [
    ['<b>No</b>', '<b>Nama Siswa</b>', '<b>Kelas</b>', '<b>Hadir</b>', '<b>Izin</b>', '<b>Sakit</b>', '<b>Alpa</b>']
];

// Isi data dari database
$no = 1;
if ($result_rekap) {
    while ($row = mysqli_fetch_assoc($result_rekap)) {
        $data_for_excel[] = [
            $no,
            $row['nama_lengkap'],
            $row['nama_kelas'],
            (int)$row['hadir'],
            (int)$row['izin'],
            (int)$row['sakit'],
            (int)$row['alpa']
        ];
        $no++;
    }
}

// Panggil class dengan nama lengkapnya
$xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data_for_excel);
$xlsx->downloadAs('laporan_kehadiran_siswa_'.date('Ymd').'.xlsx');

exit;
?>