<?php
if (!file_exists("env.php")) {
    die("env.php not found!");
}
require_once("env.php");
if (!isset($isLocalDev) || !isset($baseUrl)) {
    die("env.php is not correct");
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
if(isset($_SESSION['userLogin']) && $_SESSION['userLogin']){
    $isLogin = true;
}

//start url zone
if (count($urlRewrite) == 0) {
    //show home
    echo "home";
} elseif ($urlRewrite[0] == "login") {
    //TODO: check is user login ?
    if ($isLogin) {
        echo "logined";
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
                $found = false;
                $_SESSION['userLogin'] = true;
                $_SESSION['userID'] = $query['external_id'];
                $_SESSION['userAdmin'] = $query['admin'];
                $_SESSION['userMod'] = $query['moderator'];
                $_SESSION['userGroup'] = $query['groups'];
                $_SESSION['userName'] = $query['username'];

                if ($found) {
                    header("location: {$baseUrl}user");
                    die();
                } else {
                    header("location: {$baseUrl}user/start");
                    die();
                }

                header("Access-Control-Allow-Origin: *");
                die('logined');
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


            print " 
<a href='$AnthroICUURL/session/sso_provider?$query'>sign in with discourse</a><pre>
";

        }
    }
} elseif ($isLogin && $urlRewrite[0] == "user") {
    if (!isset($urlRewrite[1])) {
        //user start
    } elseif ($urlRewrite[1] == "start") {
        //PDPA consent here!
        if($_POST){
            //accept
        }else {
            require_once("./view/pdpa_consent.php");
        }
        die();
    }
} elseif ($urlRewrite[0] == "debug") {
    var_dump($_SERVER);
} elseif ($urlRewrite[0] == "contact") {
    echo "contact";
} else {
    echo "404";

    var_dump($urlRewrite);
    var_dump($_SERVER);
}