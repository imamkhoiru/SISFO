<div class="sidebar p-3">
    <a href="<?= BASE_URL; ?>admin/dashboard.php" class="text-white text-decoration-none">
        <h4 class="text-center mb-4">
            <img src="<?= BASE_URL; ?>assets/images/logo.png" alt="Logo" width="40" class="me-2">
            SISFO
        </h4>
    </a>
    <ul class="nav flex-column">
        <?php
        $role_id = $_SESSION['role_id'];
        $sql_menu = "SELECT m.* FROM mst_menu m 
                     JOIN mst_hak_akses_menu ham ON m.id = ham.menu_id 
                     WHERE ham.role_id = '$role_id' AND m.parent_id = 0 
                     ORDER BY m.urutan";
        $result_menu = mysqli_query($db, $sql_menu);

        if ($result_menu) {
            while ($menu = mysqli_fetch_assoc($result_menu)) {
                $sql_submenu = "SELECT m.* FROM mst_menu m 
                                JOIN mst_hak_akses_menu ham ON m.id = ham.menu_id 
                                WHERE ham.role_id = '$role_id' AND m.parent_id = '{$menu['id']}' 
                                ORDER BY m.urutan";
                $result_submenu = mysqli_query($db, $sql_submenu);

                if (mysqli_num_rows($result_submenu) > 0) {
                    echo '<li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#submenu-'.$menu['id'].'" role="button">
                                <i class="'.$menu['icon'].'"></i> '.$menu['nama_menu'].'
                            </a>
                            <div class="collapse" id="submenu-'.$menu['id'].'">
                                <ul class="nav flex-column ms-3">';
                    while($submenu = mysqli_fetch_assoc($result_submenu)){
                        echo '<li class="nav-item">
                                <a class="nav-link" href="'.BASE_URL.$submenu['url'].'">
                                    <i class="'.$submenu['icon'].'"></i> '.$submenu['nama_menu'].'
                                </a>
                              </li>';
                    }
                    echo '      </ul>
                            </div>
                          </li>';
                } else {
                    echo '<li class="nav-item">
                            <a class="nav-link" href="'.BASE_URL.$menu['url'].'">
                                <i class="'.$menu['icon'].'"></i> '.$menu['nama_menu'].'
                            </a>
                          </li>';
                }
            }
        }
        ?>
    </ul>
</div>
<div class="content-area">