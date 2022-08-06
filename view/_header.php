<!doctype html>
<html lang="th">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <meta charset="utf-8">
    <link href='/assets/thaianthro.ico' rel='icon' type='image/x-icon' />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="เว็บไซต์ชุมชนแอนโทรไทย">
    <meta name="author" content="AnthroThai">
    <meta name="theme-color" content="#000000">
    <title>หน้าแรก - แอนโทรไทย</title>
    <style>
        body {
            padding-top: 3rem;
            padding-bottom: 3rem;
            color: #5a5a5a;
        }
        .navbar-custom-color {
            background-color: #981E97;
        }
    </style>
</head>

<header>
    <nav class='navbar navbar-expand-md navbar-dark fixed-top navbar-custom-color'>
        <a class='navbar-brand' href='index.html'>ชุมชนแอนโทรไทย</a>
        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarCollapse'
                aria-controls='navbarCollapse' aria-expanded='false' aria-label='Toggle navigation'>
            <span class='navbar-toggler-icon'></span>
        </button>
        <?php
        if(!isset($menu)){
          $menu = "home";
        }
        ?>
        <div class='collapse navbar-collapse' id='navbarCollapse'>
            <ul class='navbar-nav mr-auto'>
                <li class='nav-item <?php if($menu == "home"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>'>หน้าแรก</a>
                </li>

                <li class='nav-item <?php if($menu == "about"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>/about.html'>เกี่ยวกับ</a>
                </li>

                <?php
                if(isset($_SESSION['userName'])){
                    $username = $_SESSION['userName'];
                }else{
                    $username = "บุคคลทั่วไป";
                }

                ?>
                <li class='nav-item <?php if($menu == "user"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>/user'>สมาชิก (<?php echo $username; ?>)</a>
                </li>


                <li class='nav-item <?php if($menu == "community"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>/community.html'>ชุมชน</a>
                </li>

                <li class='nav-item <?php if($menu == "meeting"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>/meeting.html'>มีตติง</a>
                </li>


                <li class='nav-item <?php if($menu == "contact"){ echo "active"; } ?>'>
                    <a class='nav-link' href='<?php echo $url; ?>/contact.html'>ติดต่อ</a>
                </li>

            </ul>
        </div>
    </nav>
</header>


<div class="container mt-3">
    <div class="row">