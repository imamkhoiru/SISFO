<?php
require_once '../../includes/config.php';
require_once '../../includes/session.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mapel_id = (int)$_POST['mapel_id'];
    $pengajar_id = (int)$_POST['pengajar_id'];
    $kelas_id = (int)$_POST['kelas_id'];
    $tanggal = $_POST['tanggal'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    
    // Konversi nama hari dari tanggal
    $nama_hari_list = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
    $hari_index = date('w', strtotime($tanggal));
    $hari = $nama_hari_list[$hari_index];

    $created_by = $_SESSION['user_id'];

    if ($mapel_id == 0 || $pengajar_id == 0 || $kelas_id == 0 || empty($tanggal) || empty($jam_mulai) || empty($jam_selesai)) {
        $error_message = "Semua field wajib diisi.";
    } elseif ($jam_mulai >= $jam_selesai) {
        $error_message = "Jam mulai harus lebih awal dari jam selesai.";
    } else {
        // --- VALIDASI JADWAL BENTROK ---

        // 1. Cek bentrok Guru (apakah guru ini sudah ada jadwal di jam yang sama)
        $sql_cek_guru = "SELECT * FROM jadwal WHERE pengajar_id = '$pengajar_id' AND tanggal = '$tanggal' AND 
                         (('$jam_mulai' >= jam_mulai AND '$jam_mulai' < jam_selesai) OR 
                          ('$jam_selesai' > jam_mulai AND '$jam_selesai' <= jam_selesai))";
        $result_cek_guru = mysqli_query($db, $sql_cek_guru);

        // 2. Cek bentrok Kelas (apakah kelas ini sudah ada jadwal di jam yang sama)
        $sql_cek_kelas = "SELECT * FROM jadwal WHERE kelas_id = '$kelas_id' AND tanggal = '$tanggal' AND 
                          (('$jam_mulai' >= jam_mulai AND '$jam_mulai' < jam_selesai) OR 
                           ('$jam_selesai' > jam_mulai AND '$jam_selesai' <= jam_selesai))";
        $result_cek_kelas = mysqli_query($db, $sql_cek_kelas);

        if (mysqli_num_rows($result_cek_guru) > 0) {
            $error_message = "Jadwal bentrok! Guru tersebut sudah memiliki jadwal lain di waktu yang sama.";
        } elseif (mysqli_num_rows($result_cek_kelas) > 0) {
            $error_message = "Jadwal bentrok! Kelas tersebut sudah memiliki jadwal lain di waktu yang sama.";
        } else {
            // Jika tidak ada bentrok, simpan data
            $sql_insert = "INSERT INTO jadwal (mapel_id, pengajar_id, kelas_id, hari, tanggal, jam_mulai, jam_selesai, created_by) 
                           VALUES ('$mapel_id', '$pengajar_id', '$kelas_id', '$hari', '$tanggal', '$jam_mulai', '$jam_selesai', '$created_by')";
            
            if (mysqli_query($db, $sql_insert)) {
                header("Location: list.php?status=add_success");
                exit;
            } else {
                $error_message = "Gagal menyimpan jadwal: " . mysqli_error($db);
            }
        }
    }
}

require_once '../../includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header"><h4 class="card-title">Tambah Jadwal Pelajaran</h4></div>
                    <div class="card-body">
                        <?php if(!empty($error_message)): ?>
                            <div class="alert alert-danger"><?= $error_message; ?></div>
                        <?php endif; ?>

                        <form action="add.php" method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kelas_id" class="form-label">Kelas</label>
                                    <select class="form-select" id="kelas_id" name="kelas_id" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php
                                        $kelas_sql = "SELECT id, nama_kelas FROM mst_kelas WHERE status = 'active'";
                                        $kelas_result = mysqli_query($db, $kelas_sql);
                                        while($kelas = mysqli_fetch_assoc($kelas_result)){
                                            echo "<option value='{$kelas['id']}'>".htmlspecialchars($kelas['nama_kelas'])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="mapel_id" class="form-label">Mata Pelajaran</label>
                                    <select class="form-select" id="mapel_id" name="mapel_id" required>
                                        <option value="">-- Pilih Mata Pelajaran --</option>
                                         <?php
                                        $mapel_sql = "SELECT id, nama_mapel FROM mst_mapel WHERE status = 'active'";
                                        $mapel_result = mysqli_query($db, $mapel_sql);
                                        while($mapel = mysqli_fetch_assoc($mapel_result)){
                                            echo "<option value='{$mapel['id']}'>".htmlspecialchars($mapel['nama_mapel'])."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="pengajar_id" class="form-label">Guru Pengajar</label>
                                <select class="form-select" id="pengajar_id" name="pengajar_id" required>
                                    <option value="">-- Pilih Guru --</option>
                                     <?php
                                    $guru_sql = "SELECT id, nama_lengkap FROM mst_guru WHERE role_id = 2 AND status = 'active'";
                                    $guru_result = mysqli_query($db, $guru_sql);
                                    while($guru = mysqli_fetch_assoc($guru_result)){
                                        echo "<option value='{$guru['id']}'>".htmlspecialchars($guru['nama_lengkap'])."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="tanggal" class="form-label">Tanggal</label>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                            <a href="list.php" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>