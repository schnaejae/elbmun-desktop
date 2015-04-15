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

if (isset($_GET['p']) AND $_GET['p'] == "rollcall") {
    include(PAGES_CLASSPATH . "Rollcall.php");
    $page = new Rollcall();
} elseif (isset($_GET['p']) AND $_GET['p'] == "setup") {
    include(PAGES_CLASSPATH . "Setup.php");
    $page = new Setup();
} elseif (isset($_GET['p']) AND $_GET['p'] == "vote") {
    include(PAGES_CLASSPATH . "Vote.php");
    $page = new Vote();
} else {
    include(PAGES_CLASSPATH . "Debate.php");
    $page = new Debate();
}
echo $page->produceOutput();

?>