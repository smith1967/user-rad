<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
$title = "อัพโหลดไฟล์และตรวจสอบข้อมูล";
$active = 'admin';
$subactive = 'upload-std';
is_admin('home/index');

/* -- upload process -- */
if (isset($_POST['submit'])):
    $err = do_upload();
endif;
?>
<?php require_once INC_PATH . 'header.php'; ?>

<div class='container'>
    <?php include_once INC_PATH . 'submenu-admin.php'; ?>
    <?php
    show_message();
    if (isset($_GET['action'])) {
        if ($_GET['action'] == 'del') {
            $filename = UPLOAD_DIR . $_GET['filename'];
            if (is_file($filename))
                unlink($filename);
            else
                $_SESSION['err'][] = 'ไม่สามารถลบไฟล์ ' . $filename;
        }
    }
    ?> 
    <div class="page-header">
        <h3>จัดการไฟล์</h3>
    </div>
    <div class="table-responsive col-md-6">
        <table class="table" >
            <thead><th>ชื่อไฟล์</th><th>ตรวจสอบไฟล์</th><th>ลบไฟล์</th></thead>
            <?php
            //get file list in upload folder
            if ($handle = opendir(UPLOAD_DIR)) :
                while (false !== ($entry = readdir($handle))) :
                    if ($entry != "." && $entry != "..") :
                        ?>
                        <tr>
                            <td> <?php echo "$entry\n"; ?></td>
                            <?php
                            $checklink = site_url('admin/check-data') . '&action=check&filename=' . $entry;
                            $unlink = site_url('admin/file-manager') . '&action=del&filename=' . $entry;
                            ?>
                            <td class="text-center"><a href="<?php echo $checklink ?>"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td class="text-center"><a href="<?php echo $unlink ?>"><span class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>
                        <?php
                    endif;
                endwhile;
                closedir($handle);
            endif;
            ?>
        </table>
    </div>  
    <span class="clearfix"></span>
    <form class="form-horizontal" id="upload_form" method="post" action="" enctype="multipart/form-data">
        <fieldset>
            <div class="form-group">
                <label class="control-label col-md-2" for="uploadfile">เลือกไฟล์ .csv</label>
                <div class="col-md-3">
                    <input type="file" class="btn btn-primary btn-file" id="uploadfile" name="uploadfile" />
                </div>
            </div>

            <div class="form-group">
                <div class="col-md-offset-2 col-md-10">
                    <button type="submit" class="btn btn-primary" name='submit'>อัพโหลดไฟล์</button>
                </div>
            </div>
        </fieldset>
    </form>

</div>
<?php require_once INC_PATH . 'footer.php'; ?>
<?php

function do_upload() {
    $err = array();
    $filename = $_FILES['uploadfile']['tmp_name'];
    $stdfile = UPLOAD_DIR . date('Y-m-d') . '_' . basename($_FILES['uploadfile']['name']);
    $ext = pathinfo($stdfile, PATHINFO_EXTENSION); // die();
    if (strtolower($ext) != 'csv') {
        $err[] = "ชนิดของไฟล์ไม่ถูกต้อง กรุณาตรวจสอบอีกครั้งครับ";
        //$_SESSION['err'] = $err;
        //redirect('admin/upload-std');
    }

    if ($_FILES["uploadfile"]["error"] > 0) {
        //echo "Error: " . $_FILES["uploadfile"]["error"] . "<br>";
        $err[] = "<p>Error: " . $_FILES["uploadfile"]["error"] . "<p/>";
    }

    if (file_exists($stdfile)) {
        unlink($stdfile);
    }
    if (!move_uploaded_file($filename, $stdfile)) {
        $err[] = "อัพโหลดไฟล์ข้อมูลผิดพลาด :" . $stdfile;
    }
    if (count($err) > 0) {
        $_SESSION['err'] = $err;
        //redirect('admin/upload-std');
    } else {
        $_SESSION['info'][] = "upload ข้อมูลเรียบร้อย";
        //do_transfer($stdfile);
        //redirect('admin/upload-std');
    }
    redirect('admin/file-manager');
}
