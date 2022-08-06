<?php
date_default_timezone_set('Asia/Bangkok');
if (!file_exists("env.php")) {
    die("env.php not found!");
}
require_once("env.php");
if (!isset($isLocalDev) || !isset($baseUrl) || !isset($url)) {
    die("env.php is not correct");
}

$userIP = "127.0.0.1";
$userCountry = "TH";

if (isset($CFEnabled) && $CFEnabled) {
    $userIP = $_SERVER['REMOTE_ADDR'];
    $userCountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
}

//sso
if (!isset($AnthroICUSecureKey) || !isset($AnthroICUURL)) {
    die("SSO is not correct");
}

//database connect
if (!isset($sqlHost) || !isset($sqlName) || !isset($sqlUser) || !isset($sqlPass)) {
    die("Database not correctly setup.");
}

try {
    $conn = new PDO("mysql:host=$sqlHost;dbname=$sqlName", $sqlUser, $sqlPass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //echo "Connection failed: " . $e->getMessage();
    die('Database Down!');
}

session_start();

//request URI
$uri = $_SERVER['REDIRECT_URL'];
$query = $_SERVER['QUERY_STRING'];

if ($isLocalDev && $uri != "" && $baseUrl != "") {
    $uriDev = explode($baseUrl, $uri);
    $uri = $uriDev[1];
}

//spilt URL
$uriLevel = explode("/", $uri);
//rebuild URL
$urlRewrite = array();
foreach ($uriLevel as $ul) {
    if ($ul != "") {
        $urlRewrite[] = $ul;
    }
}

//pre process
$isLogin = false;
$isHaveRecord = false;
if (isset($_SESSION['userLogin']) && $_SESSION['userLogin']) {
    //check User status
    $sql = "SELECT * FROM users WHERE user_externalid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute(array($_SESSION['userID']));
    $userDB = $stmt->fetchAll();
    if (count($userDB) == 1) {
        $isHaveRecord = true;
    }

    $isLogin = true;
}

//start url zone
if (count($urlRewrite) == 0) {
    //show home
    require_once("./view/home_page.php");
    die();
} elseif ($urlRewrite[0] == "login") {
    if ($isLogin) {
        header("location: {$baseUrl}user");
        die();
    } else {
        if (!empty($_GET) && isset($_GET['sso']) && isset($_GET['sig']) && isset($_SESSION['loginToken'])) {
            //return from AnthroICU
            $sso = $_GET['sso'];
            $sig = $_GET['sig'];

            if (hash_hmac('sha256', urldecode($sso), $AnthroICUSecureKey) !== $sig) {
                header("HTTP/1.1 404 Not Found");
                die('500');
            }

            $sso = urldecode($sso);
            $query = array();
            parse_str(base64_decode($sso), $query);
            if ($query['nonce'] != $_SESSION['loginToken']) {
                header("HTTP/1.1 404 Not Found");
                die('501');
            }

            //clear loginToken
            $_SESSION['loginToken'] = "";
            unset($_SESSION['loginToken']);
            if (isset($query['external_id']) && is_numeric($query['external_id'])) {
                /*
                 * array(8) { ["admin"]=> string(4) "true" ["moderator"]=> string(5) "false"
                 * ["email"]=> string(21) "" ["external_id"]=> string(1) "1"
                 * ["groups"]=> string(40) "trust_level_0,trust_level_1,admins,staff"
                 * ["nonce"]=> string(128) "" ["return_sso_url"]=> string(25) "http://localhost/at/login"
                 * ["username"]=> string(9) "" }
                 */

                //check external_id with DB if found go login -> if not go PDPA consent
                $isHaveRecord = false;
                $_SESSION['userLogin'] = true;
                $_SESSION['userID'] = $query['external_id'];
                $_SESSION['userAdmin'] = $query['admin'];
                $_SESSION['userMod'] = $query['moderator'];
                $_SESSION['userGroup'] = $query['groups'];
                $_SESSION['userName'] = $query['username'];

                $sql = "SELECT * FROM users WHERE user_externalid = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute(array($_SESSION['userID']));
                $userDB = $stmt->fetchAll();
                if (count($userDB) == 1) {
                    $isHaveRecord = true;
                }

                if ($isHaveRecord) {
                    header("location: {$baseUrl}user");
                } else {
                    header("location: {$baseUrl}user/start");
                }
                die();

            } else {
                die('502');
            }
        } else {
            $nonce = hash('sha512', mt_rand());
            $_SESSION['loginToken'] = $nonce;
            $payload = base64_encode(http_build_query(array(
                    'nonce' => $nonce,
                    'return_sso_url' => $url . "/login"
                )
            ));
            $request = array(
                'sso' => $payload,
                'sig' => hash_hmac('sha256', $payload, $AnthroICUSecureKey)
            );

            $query = http_build_query($request);
            $redir = "$AnthroICUURL/session/sso_provider?$query";

            header("location: {$redir}");
            echo "<a href='{$redir}'>click here</a>";

        }
    }
} elseif ($urlRewrite[0] == "u") {
    //is in USER SESSION ?
    if (!$isLogin) {
        require_once("./view/error_403.php");
        die();
    }else{
        if(isset($urlRewrite[1])){
            //search in DB

        }
        die('uwu');
    }
} elseif ($urlRewrite[0] == "user") {
    if ($isLogin) {
        if (!isset($urlRewrite[1])) {
            //user start

            require_once("./view/user_page.php");
        } elseif ($urlRewrite[1] == "userQRCode") {
            require_once ("./externalClass/qrcode.php");
            $qr = QRCode::getMinimumQRCode("{$url}/u/".$userDB[0]['user_pk'], QR_ERROR_CORRECT_LEVEL_L);
            $im = $qr->createImage(10, 8);
            header("Content-type: image/gif");
            imagegif($im);
            imagedestroy($im);
        } elseif ($urlRewrite[1] == "start") {
            if ($isHaveRecord) {
                header("location: {$baseUrl}user");
                die();
            } else {
                //PDPA consent here!
                if ($_POST) {
                    //accept
                    $sql = "INSERT INTO users(user_externalid,user_username,user_pdpaIP) VALUES(?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute(array($_SESSION['userID'], $_SESSION['userName'], $userIP));
                    header("location: {$baseUrl}user");
                    die();
                } else {
                    require_once("./view/pdpa_consent.php");
                }
            }
        }
    } else {
        require_once("./view/error_403.php");
    }
    die();
} elseif ($urlRewrite[0] == "logout") {
    unset($_SESSION);
    session_destroy();
    header("location: {$baseUrl}");
    die();
} elseif ($urlRewrite[0] == "debug") {
    //var_dump($_SERVER);
} elseif ($urlRewrite[0] == "contact.html") {
    $menu = "contact";
    require_once("./view/contact_page.php");
    die();
} elseif ($urlRewrite[0] == "about.html") {
    $menu = "about";
    require_once("./view/about_page.php");
    die();
} elseif ($urlRewrite[0] == "community.html") {
    $menu = "community";
    require_once("./view/community_page.php");
    die();
} elseif ($urlRewrite[0] == "meeting.html") {
    $menu = "meeting";
    require_once("./view/meeting_page.php");
    die();
} else {
    echo "404";
}