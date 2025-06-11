<?php
require_once __DIR__ . '/session.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sistem Informasi Sekolah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* (Seluruh CSS tetap sama seperti sebelumnya, tidak ada perubahan di sini) */
        body{font-family:'Poppins',sans-serif;background-color:#f4f7f6}.sidebar{position:fixed;top:0;left:0;width:250px;height:100vh;background:#343a40;color:white;z-index:1040;transition:margin-left .3s ease-in-out}.main-wrapper{margin-left:250px;display:flex;flex-direction:column;min-height:100vh;width:calc(100% - 250px);transition:margin-left .3s ease-in-out,width .3s ease-in-out}.top-navbar{position:fixed;top:0;left:250px;right:0;z-index:1030;box-shadow:0 2px 4px rgba(0,0,0,.08);transition:left .3s ease-in-out}.content-area{flex-grow:1;padding:2rem;margin-top:56px}body.sidebar-collapsed .sidebar{margin-left:-250px}body.sidebar-collapsed .main-wrapper{margin-left:0;width:100%}body.sidebar-collapsed .top-navbar{left:0}.sidebar-backdrop{position:fixed;top:0;left:0;width:100%;height:100%;background-color:rgba(0,0,0,.5);z-index:1035;display:none}body.sidebar-mobile-open .sidebar-backdrop{display:block}@media (max-width:991.98px){.sidebar{margin-left:-250px}.main-wrapper,body.sidebar-collapsed .main-wrapper{margin-left:0;width:100%}.top-navbar,body.sidebar-collapsed .top-navbar{left:0}body.sidebar-collapsed .sidebar{margin-left:-250px}body.sidebar-mobile-open .sidebar{margin-left:0}}.sidebar .nav-link{color:#adb5bd}.sidebar .nav-link:hover,.sidebar .nav-link.active{color:#fff;background-color:#495057}.sidebar .nav-link .fa-solid{margin-right:10px}.footer{flex-shrink:0}.table-responsive table td,.table-responsive table th{white-space:nowrap;vertical-align:middle}.col-no{width:1%;text-align:center}.col-aksi{width:18%;text-align:center}.col-status{width:10%;text-align:center}.col-foto{width:10%;text-align:center}
    </style>
</head>
<body>
    <div class="sidebar p-3 d-flex flex-column">
        <a href="<?= BASE_URL; ?>admin/dashboard.php" class="text-white text-decoration-none">
            <h4 class="text-center mb-4"><img src="<?= BASE_URL; ?>assets/images/logo.png" alt="Logo" width="40" class="me-2"> SISFO</h4>
        </a>
        
        <div class="flex-grow-1">
            <ul class="nav flex-column">
                <?php
                $role_id = $_SESSION['role_id'];
                $sql_menu = "SELECT m.* FROM mst_menu m JOIN mst_hak_akses_menu ham ON m.id = ham.menu_id WHERE ham.role_id = '$role_id' AND m.parent_id = 0 ORDER BY m.urutan";
                $result_menu = mysqli_query($db, $sql_menu);
                if ($result_menu) { while ($menu = mysqli_fetch_assoc($result_menu)) {
                    $sql_submenu = "SELECT m.* FROM mst_menu m JOIN mst_hak_akses_menu ham ON m.id = ham.menu_id WHERE ham.role_id = '$role_id' AND m.parent_id = '{$menu['id']}' ORDER BY m.urutan";
                    $result_submenu = mysqli_query($db, $sql_submenu);
                    if (mysqli_num_rows($result_submenu) > 0) {
                        echo '<li class="nav-item"><a class="nav-link" data-bs-toggle="collapse" href="#submenu-'.$menu['id'].'" role="button"><i class="'.$menu['icon'].'"></i> '.$menu['nama_menu'].'</a><div class="collapse" id="submenu-'.$menu['id'].'"><ul class="nav flex-column ms-3">';
                        while($submenu = mysqli_fetch_assoc($result_submenu)){ echo '<li class="nav-item"><a class="nav-link" href="'.BASE_URL.$submenu['url'].'"><i class="'.$submenu['icon'].'"></i> '.$submenu['nama_menu'].'</a></li>'; }
                        echo '</ul></div></li>';
                    } else {
                        echo '<li class="nav-item"><a class="nav-link" href="'.BASE_URL.$menu['url'].'"><i class="'.$menu['icon'].'"></i> '.$menu['nama_menu'].'</a></li>';
                    }
                }}
                ?>
            </ul>
        </div>

        <div>
            <hr class="text-secondary-50">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL; ?>logout.php">
                        <i class="fa-solid fa-right-from-bracket fa-fw me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
        </div>

    ```

### **Penjelasan Perubahan**

Saya telah mengubah sedikit struktur di dalam sidebar.
1.  Menu dinamis yang diambil dari database kini dibungkus dalam `<div class="flex-grow-1">`. Class ini akan "mendorong" semua elemen di bawahnya ke bagian paling bawah.
2.  Di bawahnya, ditambahkan blok baru yang berisi garis pemisah (`<hr>`) dan item menu "Logout".

Dengan struktur ini, tombol Logout akan selalu berada di bagian bawah sidebar, baik di tampilan desktop maupun saat sidebar dibuka di tampilan mobile.