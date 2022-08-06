<?php

//for local dev
$baseUrl = "/at/";

//request URI
$uri = $_SERVER['REQUEST_URI'];
$query = $_SERVER['QUERY_STRING'];

if($uri != "" && $baseUrl != ""){
    $uriDev = explode($baseUrl,$uri);
    var_dump($uriDev);
}

var_dump($_SERVER);