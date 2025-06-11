<?php 
require_once '../../includes/header.php';

// Ambil data guru dari database dan join dengan tabel role
$sql = "SELECT g.*, r.nama_role FROM mst_guru g LEFT JOIN mst_role r ON g.role_id = r.id ORDER BY g.nama_lengkap ASC";
$result = mysqli_query($db, $sql);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">Manajemen Data Guru</h5>
                        <div>
                            <a href="import.php" class="btn btn-info btn-sm">
                                <i class="fa-solid fa-file-import"></i> Import Data
                            </a>
                            <a href="export_excel.php" class="btn btn-success btn-sm">
                                <i class="fa-solid fa-file-excel"></i> Export ke Excel
                            </a>
                            <a href="add.php" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-plus"></i> Tambah Guru
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
                            case 'add_success': $message = 'Data guru berhasil ditambahkan!'; break;
                            case 'edit_success': $message = 'Data guru berhasil diubah!'; break;
                            case 'delete_success': $message = 'Data guru berhasil dihapus!'; break;
                            case 'delete_failed_self': $message = '<strong>Gagal Menghapus!</strong> Anda tidak dapat menghapus akun Anda sendiri.'; $alert_type = 'danger'; break;
                            case 'delete_failed': $message = '<strong>Gagal Menghapus!</strong> Terjadi kesalahan atau data terkait.'; $alert_type = 'danger'; break;
                        }
                        if ($message) {
                            echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                        }
                    }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIP</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; while($guru = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="col-no"><?= $no++; ?></td>
                                        <td><?= htmlspecialchars($guru['nama_lengkap']); ?></td>
                                        <td><?= htmlspecialchars($guru['nip']); ?></td>
                                        <td><?= htmlspecialchars($guru['username']); ?></td>
                                        <td><span class="badge bg-info"><?= htmlspecialchars($guru['nama_role']); ?></span></td>
                                        <td class="col-status"><?php if($guru['status'] == 'active'): ?><span class="badge bg-success">Aktif</span><?php else: ?><span class="badge bg-danger">Non-Aktif</span><?php endif; ?></td>
                                        <td class="col-aksi"><a href="edit.php?id=<?= $guru['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pencil"></i> Edit</a> <a href="delete.php?id=<?= $guru['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fa-solid fa-trash"></i> Hapus</a></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="7" class="text-center">Tidak ada data guru.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once '../../includes/footer.php'; 
?>