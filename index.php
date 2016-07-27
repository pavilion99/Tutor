<?php
define("PAGE_NAME", "MAIN");
define("REL", "");
session_start();
if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: login");

require_once("assets/php/var.php");

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
    <?php
    if (isset($_GET["tutor"])):
        ?>
        <div class="alert alert-success" id="tutor-message">
            Congratulations! You are now a tutor.
        </div>
    <?php endif; ?>
    <h1>
        Schedule an Appointment
    </h1>
    <?php if ($tutor): ?>

    <?php endif; ?>
</div>
</body>
</html>