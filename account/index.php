<?php
use tech\scolton\tutor\User;

spl_autoload_register(function ($class) {
    $i = new RecursiveDirectoryIterator(REL, RecursiveDirectoryIterator::SKIP_DOTS);
    $j = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($j as $item) {
        if (strtolower($item->getExtension()) != "php")
            continue;

        if (strtolower($item->getBasename(".php")) != $class)
            continue;

        /** @noinspection PhpIncludeInspection */
        include ($item->getPath());
    }
});

session_start();
define("PAGE_NAME", "ACCOUNT");
define("REL", "../");
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
            <?php $user->render(); ?>
        </div>
    </body>
</html>