<?php
if(!file_exists("env.php")){
    die("env.php not found!");
}
require_once ("env.php");
if(!isset($isLocalDev) || !isset($baseUrl)){
    die("env.php is not correct");
}

//request URI
$uri = $_SERVER['REQUEST_URI'];
$query = $_SERVER['QUERY_STRING'];

if($isLocalDev && $uri != "" && $baseUrl != ""){
    $uriDev = explode($baseUrl,$uri);
    $uri = $uriDev[1];
}

//spilt URL
$uriLevel = explode("/",$uri);
var_dump($uriLevel);