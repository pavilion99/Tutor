<?php
use tech\scolton\tutor\User;

session_start();
define("PAGE_NAME", "ACCOUNT");
define("REL", "../");

/** @noinspection PhpIncludeInspection */
include_once(REL . "assets/php/var.php");

if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: " . REL . "/login");

$user = User::get($_SESSION["id"]);
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../assets/parts/head.php"); ?>
        <script src="../assets/js/main.js"></script>
        <script src="../assets/js/md5.min.js"></script>
        <link rel="stylesheet" href="../assets/css/main.css"/>
    </head>
    <body onload="$('[data-toggle=\'tooltip\']').tooltip();">
        <?php include("../assets/parts/nav.php"); ?>
        <div class="container-fluid">
            <?php echo $user->render(); ?>
        </div>
    </body>
</html>