<?php
define("PAGE_NAME", "SIGNUP");
define("REL", "../");

use tech\scolton\tutor\Database;

spl_autoload_register(function ($class) {
    $tmp = str_replace("\\", DIRECTORY_SEPARATOR, $class);

    /** @noinspection PhpIncludeInspection */
    if (@require_once(REL . "assets/php/classes/$tmp.php")) {
        return;
    }

    $i = new RecursiveDirectoryIterator(REL . "assets/php/classes/", RecursiveDirectoryIterator::SKIP_DOTS);
    $j = new RecursiveIteratorIterator($i, RecursiveIteratorIterator::SELF_FIRST);

    foreach ($j as $item) {
        if (strtolower($item->getExtension()) != "php")
            continue;

        if (strtolower($item->getBasename(".php")) != $class)
            continue;

        /** @noinspection PhpIncludeInspection */
        require_once($item->getPath());
    }
});

if (session_status() != PHP_SESSION_ACTIVE)
    session_start();

if (isset($_POST["email"])) {
    $sql = Database::getSQL();

    $email = $sql->escape_string($_POST["email"]);
    $password = $sql->escape_string($_POST["password"]);
    $name = $sql->escape_string($_POST["name"]);
    $grade = $sql->escape_string($_POST["grade"]);

    $email .= "@ohschools.org";

    $emailCheck = $sql->query("SELECT `email` FROM `users` WHERE `email`='$email'");
    if ($row = $emailCheck->fetch_assoc()) {
        die('{"success":false, "error": "That email is already in use."}');
    }

    if (!$sql->query("INSERT INTO `users` (`email`,`name`,`grade`,`password`) VALUES ('$email','$name','$grade','$password')")) {
        die('{"success":false, "error": "Failed to register new user.  Server said: ' . $sql->error . '"}');
    } else {
        exit('{"success": true}');
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../assets/parts/head.php"); ?>
        <link rel="stylesheet" href="../assets/css/main.css"/>
        <script src="../assets/js/main.js"></script>
        <script src="../assets/js/md5.min.js"></script>
    </head>
    <body>
        <?php include("../assets/parts/nav.php"); ?>
        <div class="login-signup-form-wrapper">
            <h1>
                Sign Up
            </h1>
            <br/>
            <div class="alert alert-danger error" id="error">

            </div>
            <form action="javascript:void(0);"
                  onsubmit="signup()">
                <input type="text"
                       name="name"
                       placeholder="Your name"
                       id="name"
                       class="form-control"
                       required/>
                <br/>
                <div class="input-group">
                    <input type="text"
                           name="email"
                           id="email"
                           placeholder="Email address"
                           class="form-control"
                           required/>
                    <span class="input-group-addon">@ohschools.org</span>
                </div>
                <br/>
                <select class="form-control"
                        id="grade"
                        name="grade">
                    <option value="-1">Grade Level</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="11">11</option>
                    <option value="12">12</option>
                </select>
                <br/>
                <input class="form-control"
                       type="password"
                       name="password"
                       placeholder="Password"
                       id="password"
                       required/>
                <br/>
                <input class="form-control"
                       type="password"
                       name="password-confirmation"
                       placeholder="Password confirmation"
                       id="password-confirmation"
                       required/>
                <br/>
                <input type="submit"
                       class="btn btn-lg btn-primary right"
                       value="Sign Up"/>
            </form>
        </div>
    </body>
</html>