<?php
if (!defined('BASE_PATH'))
    exit('No direct script access allowed');
?>
</div> <!-- End wrap -->

<div id="footer">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-md-6">
                <p class='text-muted'><?php echo $site_subtitle; ?></p>
  <!--              <p><?php echo $project; ?></p> -->
            </div>
            <div class="col-xs-6 col-md-6">
                <p class='text-muted'>ผู้พัฒนา : <?php echo $auhtor; ?></p>
<!--                <p class="text-muted">อีเมล์ : <?php echo $author_email; ?></p> -->
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo BOOTSTRAP_URL ?>js/bootstrap.min.js"></script>
<script src="<?php echo SITE_URL ?>js/common.js"></script>
</body>
</html>
<?php
mysqli_close($db);
?>

