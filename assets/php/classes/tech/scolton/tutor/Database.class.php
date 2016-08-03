<?php
namespace tech\scolton\tutor;

use mysqli;

class Database {

    public static function getSQL() {
        $config = parse_ini_file(REL . "assets/php/config/database.ini");

        define("HOST", $config["db_host"]);
        define("USERNAME", $config["db_user"]);
        define("PASSWORD", $config["db_pass"]);
        define("DATABASE", $config["db_name"]);

        return new mysqli(HOST, USERNAME, PASSWORD, DATABASE);
    }

    public static function init() {
        // $sql = Database::getSQL();

        // TODO: Add database self-initialization features
    }
}
