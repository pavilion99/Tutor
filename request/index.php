<?php
define("PAGE_NAME", "REQUEST");
define("REL", "../");

/** @noinspection PhpIncludeInspection */
include_once(REL . "assets/php/var.php");

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: " . REL . "/login");
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../assets/parts/head.php"); ?>
        <script src="../assets/js/main.js"></script>
        <script src="../assets/js/md5.min.js"></script>
        <link rel="stylesheet" href="../assets/css/main.css"/>
    </head>
    <body>

    </body>
</html>