<?php
/*
 * elbmun-desktop, Web Based MUN Software
 * (c) by Jannes Riffert, Elbe Model United Nations e.V. Dresden
 * https://github.com/schnaejae/elbmun-desktop/, http://elbmun.org
 * MIT License
 */
error_reporting(E_ERROR);

header("Content-Type: text/html; charset=UTF-8");

session_start();

include "php/config.php";
include CLASSPATH . "Output.php";

if (isset($_GET['a']) OR isset($_POST['a'])) {
    $a = isset($_GET['a']) ? $_GET['a'] : $_POST['a'];
    if (isset($scripts[$a]) && file_exists(AJAX_CLASSPATH . $scripts[$a] . ".php")) {
        include(AJAX_CLASSPATH . $scripts[$a] . ".php");
        $page = new $scripts[$a]();
        echo json_encode($page->produceOutput());
    } else {
        echo json_encode(false);
    }
} else {
    echo json_encode(false);
}




?>