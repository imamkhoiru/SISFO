<?php 
require_once '../../includes/header.php';

// Ambil data jadwal dan join dengan tabel master lainnya untuk mendapatkan nama
$sql = "SELECT j.*, mapel.nama_mapel, guru.nama_lengkap AS nama_guru, kelas.nama_kelas 
        FROM jadwal j
        LEFT JOIN mst_mapel mapel ON j.mapel_id = mapel.id
        LEFT JOIN mst_guru guru ON j.pengajar_id = guru.id
        LEFT JOIN mst_kelas kelas ON j.kelas_id = kelas.id
        ORDER BY j.tanggal DESC, j.jam_mulai ASC";
$result = mysqli_query($db, $sql);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">Manajemen Jadwal Pelajaran</h5>
                        <div>
                            <a href="export_excel.php" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-file-excel"></i> Export ke Excel
                            </a>
                            <a href="add.php" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Tambah Jadwal
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    // Blok Notifikasi
                    if (isset($_GET['status'])) {
                        $status = $_GET['status'];
                        $message = '';
                        $alert_type = 'success';

                        switch ($status) {
                            case 'add_success': $message = 'Jadwal pelajaran berhasil ditambahkan!'; break;
                            case 'edit_success': $message = 'Jadwal pelajaran berhasil diubah!'; break;
                            case 'delete_success': $message = 'Jadwal pelajaran berhasil dihapus!'; break;
                            case 'delete_failed': $message = '<strong>Gagal Menghapus!</strong> Terjadi kesalahan.'; $alert_type = 'danger'; break;
                        }

                        if ($message) {
                            echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show" role="alert">
                                    ' . $message . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                  </div>';
                        }
                    }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Guru Pengajar</th>
                                    <th>Statistik Kehadiran</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; while($jadwal = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= date('d M Y', strtotime($jadwal['tanggal'])); ?> (<?= htmlspecialchars($jadwal['hari']); ?>)</td>
                                        <td><?= date('H:i', strtotime($jadwal['jam_mulai'])); ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])); ?></td>
                                        <td><?= htmlspecialchars($jadwal['nama_mapel']); ?></td>
                                        <td><?= htmlspecialchars($jadwal['nama_kelas']); ?></td>
                                        <td><?= htmlspecialchars($jadwal['nama_guru']); ?></td>
                                        <td>
                                            <span class="badge bg-success">H: <?= $jadwal['hadir']; ?></span>
                                            <span class="badge bg-info">I: <?= $jadwal['izin']; ?></span>
                                            <span class="badge bg-warning text-dark">S: <?= $jadwal['sakit']; ?></span>
                                            <span class="badge bg-danger">A: <?= $jadwal['alpa']; ?></span>
                                        </td>
                                        <td>
                                            <a href="../absensi/view_absensi.php?id=<?= $jadwal['id']; ?>" class="btn btn-info btn-sm">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>
                                            <a href="edit.php?id=<?= $jadwal['id']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pencil"></i> Edit
                                            </a>
                                            <a href="delete.php?id=<?= $jadwal['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?');">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data jadwal.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div> </div> </div> </div> </div> <?php 
require_once '../../includes/footer.php'; 
?>