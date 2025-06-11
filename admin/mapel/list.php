<?php 
require_once '../../includes/header.php';

// Ambil data mata pelajaran dari database
$sql = "SELECT * FROM mst_mapel ORDER BY nama_mapel ASC";
$result = mysqli_query($db, $sql);
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Manajemen Data Mata Pelajaran</h4>
                        <a href="add.php" class="btn btn-primary btn-sm float-end">
                            <i class="fa-solid fa-plus"></i> Tambah Mapel
                        </a>
                    </div>
                    <div class="card-body">
                        <?php
                        // --- BLOK NOTIFIKASI YANG DIPERBAIKI ---
                        if (isset($_GET['status'])) {
                            $status = $_GET['status'];
                            if ($status == 'add_success' || $status == 'edit_success' || $status == 'delete_success') {
                                $message = '';
                                if ($status == 'add_success') $message = "Data mata pelajaran berhasil ditambahkan!";
                                if ($status == 'edit_success') $message = "Data mata pelajaran berhasil diubah!";
                                if ($status == 'delete_success') $message = "Data mata pelajaran berhasil dihapus!";
                                
                                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                        ' . $message . '
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                      </div>';
                            } 
                            elseif ($status == 'delete_failed') {
                                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Gagal Menghapus!</strong> Data mata pelajaran ini mungkin sedang digunakan di tabel jadwal.
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
                                        <th>Nama Mata Pelajaran</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($result) > 0): ?>
                                        <?php $no = 1; while($mapel = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td><?= $no++; ?></td>
                                            <td><?= htmlspecialchars($mapel['nama_mapel']); ?></td>
                                            <td>
                                                <?php if($mapel['status'] == 'active'): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Non-Aktif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit.php?id=<?= $mapel['id']; ?>" class="btn btn-warning btn-sm">
                                                    <i class="fa-solid fa-pencil"></i> Edit
                                                </a>
                                                <a href="delete.php?id=<?= $mapel['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                    <i class="fa-solid fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Tidak ada data mata pelajaran.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>