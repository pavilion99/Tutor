<?php
use tech\scolton\tutor\exception\NotFoundException;
use tech\scolton\tutor\User;

define("PAGE_NAME", "MAIN");
define("REL", ".");

phpinfo();

spl_autoload_register(function ($class) {
    echo "CLASS REQUESTED: ".$class;

    $i = new RecursiveDirectoryIterator(REL, RecursiveDirectoryIterator::SKIP_DOTS);
    $j = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($j as $item) {
        if (strtolower($item->getExtension()) != "php")
            continue;

        if (strtolower($item->getBasename(".php")) != $class)
            continue;

        echo "Included: ".$item->getPath();

        /** @noinspection PhpIncludeInspection */
        include ($item->getPath());
    }
});

session_start();
if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: login");

$user = User::get($_SESSION["id"]);

$tutor = null;
try {
    $tutor = $user->getTutor();
} catch (NotFoundException $e) {
    $tutor = false;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("assets/parts/head.php"); ?>
        <script src="assets/js/main.js"></script>
        <link rel="stylesheet" href="assets/css/main.css"/>
    </head>
    <body>
        <?php include("assets/parts/nav.php"); ?>
        <div class="container-fluid" id="main-content">
            <?php if (isset($_GET["tutor"])): ?>
                <div class="alert alert-success" id="tutor-message">
                    Congratulations! You are now a tutor.
                </div>
            <?php endif; ?>
            <h1>
                Schedule an Appointment
            </h1>
            <?php if ($tutor): ?>
                <h1>
                    Requests
                </h1>
                <table class="table table-striped">
                    <tr>
                        <td>Name</td>
                        <td>When</td>
                        <td>Where</td>
                        <td>Subject</td>
                        <td></td>
                    </tr>
                    <!-- TODO: add PHP to load unaccepted appointments here -->
                </table>
                <h1>
                    Upcoming Appointments
                </h1>
                <table class="table table-striped">
                    <tr>
                        <th>Name</th>
                        <th>When</th>
                        <th>Where</th>
                        <th>Subject</th>
                    </tr>
                    <!-- TODO: add PHP to load appointments here -->
                </table>
            <?php endif; ?>
        </div>
    </body>
</html>