<?php
define("PAGE_NAME", "LOGIN");
define("REL", "../");

use tech\scolton\tutor\Database;

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

if (isset($_POST["email"])) {
    $sql = Database::getSQL();

    $email = $sql->escape_string($_POST["email"]);
    $password = $sql->escape_string($_POST["password"]);

    $userPDO = $sql->query("SELECT `id`,`name`,`email`,`grade` FROM `users` WHERE `email`='$email' AND `password`='$password'");

    if ($userPDO && $user = $userPDO->fetch_assoc()) {
        $idTemp = $user["id"];
        $tutorPDO = $sql->query("SELECT `phone`,`subjects`,`availability` FROM `tutors` WHERE `id`=$idTemp");

        if ($tutorPDO && $tutor = $tutorPDO->fetch_assoc()) {
            $_SESSION["tutor"] = $tutor;
        } else {
            $_SESSION["tutor"] = false;
        }

        $_SESSION["id"] = $user["id"];

        exit('{"success": true}');
    } else {
        die('{"success": false, "error": "Unknown email address or bad password.", "sqlerror": "' . $sql->error . '"}');
    }
}
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
        <?php include("../assets/parts/nav.php"); ?>
        <div class="login-signup-form-wrapper">
            <h1>
                Welcome
            </h1>
            <br/>
            <div class="alert alert-danger error" id="error">

            </div>
            <?php if (isset($_GET["signup"])): ?>
                <div class="alert alert-success" id="signup">
                    You have successfully registered for an account. You may now log in.
                </div>
            <?php endif; ?>
            <form id="login-form"
                  action="javascript:void(0);"
                  onsubmit="login()">
                <input type="email"
                       name="email"
                       id="email"
                       placeholder="Email Address"
                       class="form-control"/>
                <br/>
                <input type="password"
                       name="password"
                       id="password"
                       placeholder="Password"
                       class="form-control"/>
                <br/>
                <div>
                    <a class="btn btn-lg btn-default"
                       id="create-account"
                       href="../signup">
                        Create an Account
                    </a>
                    <input type="submit"
                           id="login-submit"
                           class="btn btn-lg btn-primary right"
                           value="Login"/>
                </div>
            </form>
        </div>
    </body>
</html>