<?php 
require_once '../../includes/header.php';
$sql = "SELECT k.*, g.nama_lengkap AS nama_wali_kelas FROM mst_kelas k LEFT JOIN mst_guru g ON k.wali_kelas_id = g.id ORDER BY k.nama_kelas ASC";
$result = mysqli_query($db, $sql);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><div class="d-flex justify-content-between align-items-center"><h5 class="card-title m-0">Manajemen Data Kelas</h5><div><a href="add.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Tambah Kelas</a></div></div></div>
                <div class="card-body">
                    <?php /* Blok Notifikasi */ if (isset($_GET['status'])) { $status = $_GET['status']; $message = ''; $alert_type = 'success'; switch ($status) { case 'add_success': $message = 'Data kelas berhasil ditambahkan!'; break; case 'edit_success': $message = 'Data kelas berhasil diubah!'; break; case 'delete_success': $message = 'Data kelas berhasil dihapus!'; break; case 'delete_failed': $message = '<strong>Gagal Menghapus!</strong> Data mungkin terhubung ke siswa atau jadwal.'; $alert_type = 'danger'; break; } if ($message) { echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'; }} ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th>Nama Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; while($kelas = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="col-no"><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($kelas['nama_kelas']); ?></td>
                                        <td><?= htmlspecialchars($kelas['nama_wali_kelas'] ?? 'Belum Diatur'); ?></td>
                                        <td class="col-status"><?php if($kelas['status'] == 'active'): ?><span class="badge bg-success">Aktif</span><?php else: ?><span class="badge bg-danger">Non-Aktif</span><?php endif; ?></td>
                                        <td class="col-aksi"><a href="edit.php?id=<?= $kelas['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pencil"></i> Edit</a> <a href="delete.php?id=<?= $kelas['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fa-solid fa-trash"></i> Hapus</a></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center">Tidak ada data kelas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../../includes/footer.php'; ?>