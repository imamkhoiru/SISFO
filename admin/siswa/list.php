<?php 
require_once '../../includes/header.php';

// Logika PHP untuk filter tetap sama, tidak ada yang berubah
$kelas_filter = isset($_GET['kelas_id']) && !empty($_GET['kelas_id']) ? (int)$_GET['kelas_id'] : null;
$sql = "SELECT s.*, k.nama_kelas FROM mst_siswa s LEFT JOIN mst_kelas k ON s.kelas_id = k.id";
if ($kelas_filter) {
    $sql .= " WHERE s.kelas_id = $kelas_filter";
}
$sql .= " ORDER BY k.nama_kelas, s.nama_lengkap ASC";
$result = mysqli_query($db, $sql);
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card mb-4">
                <div class="card-body">
                    <form action="list.php" method="GET" class="d-flex justify-content-start align-items-center">
                        <label for="kelas_id" class="form-label me-3 mb-0">Tampilkan Kelas:</label>
                        <div>
                            <select name="kelas_id" id="kelas_id" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Semua Kelas --</option>
                                <?php
                                $kelas_sql = "SELECT id, nama_kelas FROM mst_kelas WHERE status = 'active' ORDER BY nama_kelas";
                                $kelas_result = mysqli_query($db, $kelas_sql);
                                while($kelas = mysqli_fetch_assoc($kelas_result)){
                                    $selected = ($kelas['id'] == $kelas_filter) ? 'selected' : '';
                                    echo "<option value='{$kelas['id']}' $selected>".htmlspecialchars($kelas['nama_kelas'])."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <a href="list.php" class="btn btn-secondary ms-3">Reset</a>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title m-0">Manajemen Data Siswa</h5>
                        <div>
                            <a href="import.php" class="btn btn-info btn-sm"><i class="fa-solid fa-file-import"></i> Import Data</a>
                            <a href="export_excel.php" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel"></i> Export ke Excel</a>
                            <a href="add.php" class="btn btn-primary btn-sm"><i class="fa-solid fa-plus"></i> Tambah Siswa</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php /* Blok Notifikasi tidak berubah */ if (isset($_GET['status'])) { $status = $_GET['status']; $message = ''; $alert_type = 'success'; switch ($status) { case 'add_success': $message = 'Data siswa berhasil ditambahkan!'; break; case 'edit_success': $message = 'Data siswa berhasil diubah!'; break; case 'delete_success': $message = 'Data siswa berhasil dihapus!'; break; case 'delete_failed': $message = '<strong>Gagal Menghapus!</strong> Terjadi kesalahan.'; $alert_type = 'danger'; break; } if ($message) { echo '<div class="alert alert-' . $alert_type . ' alert-dismissible fade show" role="alert">' . $message . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'; }} ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="col-no">No</th>
                                    <th class="col-foto">Foto</th>
                                    <th>Nama Lengkap</th>
                                    <th>NIS</th>
                                    <th>Kelas</th>
                                    <th>Jenis Kelamin</th>
                                    <th class="col-status">Status</th>
                                    <th class="col-aksi">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; while($siswa = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td class="col-no"><?= $no++; ?></td>
                                        <td class="col-foto"><img src="<?= BASE_URL; ?>assets/uploads/foto_siswa/<?= htmlspecialchars($siswa['foto']); ?>" alt="Foto Siswa" width="50" class="img-thumbnail"></td>
                                        <td><?= htmlspecialchars($siswa['nama_lengkap']); ?></td>
                                        <td><?= htmlspecialchars($siswa['nis']); ?></td>
                                        <td><?= htmlspecialchars($siswa['nama_kelas']); ?></td>
                                        <td><?= htmlspecialchars($siswa['jenis_kelamin']); ?></td>
                                        <td class="col-status"><?php if($siswa['status'] == 'active'): ?><span class="badge bg-success">Aktif</span><?php else: ?><span class="badge bg-danger">Non-Aktif</span><?php endif; ?></td>
                                        <td class="col-aksi"><a href="edit.php?id=<?= $siswa['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pencil"></i> Edit</a> <a href="delete.php?id=<?= $siswa['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');"><i class="fa-solid fa-trash"></i> Hapus</a></td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center">Tidak ada data siswa yang cocok dengan filter.</td></tr>
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