<?php
require_once ("_header.php");

$pdpa = false;

if(!$pdpa){
    ?>
    <div class='col-12'>
        <div class="alert alert-warning">เนื่องจากคุณยังไม่ยอมรับใน <a href="<?php echo $url; ?>/user/start">เงื่อนไขและข้อตกลงของเรา</a> คุณจึงยังไม่สามารถใช้งานระบบนี้ได้ โปรดใช้งานในหน้าอื่น หรือ พิจารณาใน<a href="<?php echo $url; ?>/user/start">เงื่อนไขและข้อตกลงของเรา</a>ของเราอีกครั้ง</div>
    </div>
    <?php
}else{
?>
    <div class='col-12'>
        <div class="col-12">

        </div>
    </div>
<?php
}
require_once ("_footer.php");
?>