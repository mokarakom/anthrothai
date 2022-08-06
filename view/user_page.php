<?php
require_once("_header.php");

if (!isset($isHaveRecord) || !$isHaveRecord) {
    ?>
    <div class='col-12'>
        <div class="alert alert-warning">เนื่องจากคุณยังไม่ยอมรับใน <a href="<?php echo $url; ?>/user/start">เงื่อนไขและข้อตกลงของเรา</a>
            คุณจึงยังไม่สามารถใช้งานระบบนี้ได้ โปรดใช้งานในหน้าอื่น หรือ พิจารณาใน<a
                    href="<?php echo $url; ?>/user/start">เงื่อนไขและข้อตกลงของเรา</a>ของเราอีกครั้ง
        </div>
    </div>
    <?php
} else {

    if (!isset($userDB)) {
        die();
    }

    $isDOB = false;
    $currentAge = 0;
    if ($userDB[0]['user_dob'] !== NULL) {
        $isDOB = true;
        $date1 = new DateTime("today");
        try {
            $date2 = new DateTime($userDB[0]['user_dob']);
        } catch (Exception $e) {
            die('?');
        }

        $interval = $date1->diff($date2);
        if ($interval->y > 20) {
            $currentAge = "20";
        } elseif ($interval->y > 18) {
            $currentAge = "18";
        } elseif ($interval->y > 15) {
            $currentAge = "15";
        } elseif ($interval->y > 13) {
            $currentAge = "13";
        }
    }

    $isPhone = false;
    if ($userDB[0]['user_phoneNumber'] != "" && strlen($userDB[0]['user_phoneNumber']) == 10 && $userDB[0]['user_phoneVerify'] == "1") {
        $isPhone = true;
    }

    ?>
    <div class='col-12'>
        <div class="row">
            <div class="col-12">
                <h1>สวัสดี! <?php echo $_SESSION['userName']; ?> (<a href="<?php echo $url; ?>/logout"
                                                                     class="text-danger">ออกจากระบบ</a>)</h1>
                <hr/>
            </div>
            <div class="col-md-6 col-12">
                <h2>ข้อมูลส่วนตัว</h2>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <img class="img-fluid"
                             src="<?php echo $url; ?>/user/userQRCode?<?php echo sha1(time()."Mokarakom"); ?>"/>
                    </div>
                    <div class="col-12 col-md-6">
                        PK: <a href="<?php echo $url; ?>/u/<?php echo $userDB[0]['user_pk']; ?>"><?php echo $userDB[0]['user_pk']; ?></a> (<a href="<?php echo $url;?>/user/resetPK" class="text-danger" onclick="return confirm('แน่ใจจริง ๆ เหรอ?')">reset</a>)<br/>
                        <small>(ผู้มีข้อมูลนี้จะสามารถดูข้อมูลของคุณได้ในตารางแนบท้าย หรือลงแสกนด้วยตัวเองเพื่อดูว่าผู้อื่นจะเห็นอะไรบ้าง)</small>
                        <hr/>
                        COVID-19 ATK: <span class="badge badge-danger">ยัง</span> <span class="badge badge-info">เมื่อ: 30 วันที่แล้ว</span>

                    </div>
                    <div class="col-12">
                        <hr/>
                        <h3>การยืนยันตัว</h3>
                        <ul>
                            <li><a href="<?php echo $url; ?>/user/dob">ใส่ข้อมูลวันเดือนปีเกิด</a></li>
                            <li><a href="<?php echo $url; ?>/user/phone">ยินยันหรือเปลี่ยนหมายเลขโทรศัพท์</a></li>
                        </ul>
                        <hr/>
                        <h3>เฉพาะกิจ</h3>
                        <ul>
                            <li><a href="<?php echo $url; ?>/user/atk">ส่งผล ATK (COVID-19)</a></li>
                        </ul>
                    </div>

                </div>
            </div>
            <div class="col-md-6 col-12">
                สถานะการเป็นสมาชิก:
                <ul>
                    <li>PDPA: เมื่อ <?php echo $userDB[0]['user_pdpaDate']; ?></li>
                    <li>เบอร์โทรศัพท์:
                        <?php
                        if ($isPhone) {
                            echo '<span class="badge badge-success">เรียบร้อย ' . substr($userDB[0]['user_phoneNumber'], 0, 3) . 'xxxx' . substr($userDB[0]['user_phoneNumber'], 7) . '</span>';
                        } else {
                            echo '<span class="badge badge-danger">ยังไม่ยืนยัน</span>';
                        }
                        ?>
                    </li>
                    <li>อายุ: <?php
                        if ($isDOB) {
                            if ($currentAge >= 15) {
                                echo '<span class="badge badge-info">' . $currentAge . '+</span>';
                            } else {
                                echo '<span class="badge badge-warning"><=' . $currentAge . '</span>';
                            }
                        } else {
                            echo '<span class="badge badge-danger">ยังไม่ระบุ</span>';
                        }
                        ?></li>
                    <li>ร่วมมีตติ้ง: ยังไม่เคย</li>
                </ul>
                <p>
                    <small>คำอธิบายการเก็บข้อมูล:
                <ul>
                    <li>เบอร์โทรศัพท์: เพื่อใช้ในการยืนยันว่าผู้ใช้อยู่ในประเทศไทยจริง และใช้ในการยืนยันข้อมูลต่าง ๆ
                    </li>
                    <li>วันเดือนปีเกิด: ใช้เพื่อยืนยันว่าผู้ใช้อายุ ถึงข้อกำหนดตามที่ระบุ
                        ซึ่งจะถูกใช้ในการเข้าร่วมกิจกรรม อาจมีการขอตรวจเอกสารเพื่อตรวจสอบย้อนหลังในบางกรณี /
                        โดยจะแสดงเพียง <=13, 15+, 18+ และ 20+ ในหน้าจอของผู้จัดกิจกรรมเท่านั้น /
                        จะมีเพียงผู้ดูแลระบบที่เห็นข้อมูลเต็ม
                    </li>
                    <li>ร่วมมีตติง: เข้าร่วมมีตติงอย่างเป็นทางการของเราอย่างน้อยหนึ่งครั้ง
                        เนื่องจากในบางกิจกรรมเพื่อป้องกันการลงทะเบียนจากผู้ไม่มีตัวตน เราอาจตรวจสอบสิทธินี้
                    </li>
                </ul>

                <p>ข้อมูลที่ถูกแสดงกรณีแสดง QRCode/PK (Public Key) ให้ผู้อื่น</p>
                <ul>
                    <li>ชื่อผู้ใช้ (ข้อมูลจาก AnthroICU)</li>
                    <li>ภาพประจำตัว (ข้อมูลจาก AnthroICU)</li>
                    <li>อายุ: แสดงเพียง 15+,18+ และ 20+ เท่านั้น</li>
                    <li>การยืนยันตัวและกลุ่มสมาชิก</li>
                    <li>ข้อมูลการตรวจ ATK</li>
                    <li>ประวัติการเข้าร่วมมีตติง</li>
                </ul>

                <p>ข้อมูลที่จะแสดงเพิ่มเติมเมื่อแสดง QRCode ให้กับผู้จัดมีตติงอนุญาต</p>
                <ul>
                    <li>หมายเลขโทรศัพท์</li>
                    <li>จะได้รับอนุญาตให้เพิ่มข้อมูลการเข้าร่วมมีตติง</li>
                </ul>
                </small>
                </p>
            </div>
        </div>
    </div>
    <?php
}
require_once("_footer.php");
?>