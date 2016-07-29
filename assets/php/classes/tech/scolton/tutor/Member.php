<?php
namespace tech\scolton\tutor;

use tech\scolton\tutor\exception\SQLException;

class Member {

    private $id;
    private $name;
    private $cat;
    private $subcat;
    private $extra;
    private $multiple;

    private function __construct($id, $name, $cat, $subcat, $extra, $multiple) {
        $this->id = $id;
        $this->name = $name;
        $this->subcat = $subcat;
        $this->cat = $cat;
        $this->extra = $extra;
        $this->multiple = $multiple || $extra;
    }

    public static function getAllClasses() {
        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT * FROM `classes`")) {
            if ($row = $res->fetch_assoc()) {
                $members = array();
                do {
                    $id = $row["id"];
                    $name = $row["name"];
                    $cat = $row["cat"];
                    $subcat = $row["subcat"] == "" ? null : $row["subcat"];
                    $extra = $row["extra"] == 1;
                    $multiple = $row["multiple"] == 1;

                    $members[] = new Member($id, $name, $cat, $subcat, $extra, $multiple);
                } while ($row = $res->fetch_assoc());
                $sql->close();
                return $members;
            } else {
                $sql->close();
                return array();
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public static function getAll($cat) {
        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT * FROM `classes` WHERE `cat`='$cat'")) {
            if ($row = $res->fetch_assoc()) {
                $members = array();
                do {
                    $id = $row["id"];
                    $name = $row["name"];
                    $cat = $row["cat"];
                    $subcat = $row["subcat"] == "" ? null : $row["subcat"];
                    $extra = $row["extra"] == 1;
                    $multiple = $row["multiple"] == 1;

                    $members[] = new Member($id, $name, $cat, $subcat, $extra, $multiple);
                } while ($row = $res->fetch_assoc());
                $sql->close();
                return $members;
            } else {
                $sql->close();
                return array();
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public function getSubcat() {
        return $this->subcat;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function isMultiple() {
        return $this->multiple;
    }

    public function isExtra() {
        return $this->extra;
    }

    public function getCat() {
        return Category::get($this->cat);
    }

}