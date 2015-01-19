<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
?>
<div>
    <div>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url() ?>">หน้าหลัก</a></li>
            <li class='active'>ดูแลระบบ</li>
        </ol>
    </div>
    <div class='submenu'>
        <?php
        $submenu = array(
            'home' => array(
                'title' => 'หน้าหลัก',
                'url' => 'admin/index',
                'cond' => true,
            ),
            'list-alluser' => array(
                'title' => 'จัดการผู้ใช้',
                'url' => 'admin/list-alluser',
                'cond' => true,
            ),
            'upload-std' => array(
                'title' => 'จัดการไฟล์',
                'url' => 'admin/file-management',
                'cond' => true,
            ),
            'check-data' => array(
                'title' => 'ตรวจสอบข้อมูล',
                'url' => 'admin/check-data',
                'cond' => true,
            ),
            'import-std' => array(
                'title' => 'นำเข้าข้อมูล',
                'url' => 'admin/import-std',
                'cond' => true,
            ),            
            'rep-alluser' => array(
                'title' => 'รายงานการใช้',
                'url' => 'user/rep-alluser',
                'cond' => true,
            ),
        );
        $menu_class = "nav nav-pills";
        echo gen_menu($menu_class, $submenu, $subactive);
        ?>
    </div>
</div>

