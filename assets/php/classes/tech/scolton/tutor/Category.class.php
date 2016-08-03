<?php
namespace tech\scolton\tutor;

use tech\scolton\tutor\exception\NotFoundException;
use tech\scolton\tutor\exception\SQLException;

class Category {

    private $id;
    private $name;
    private $members;
    private $idBlock;
    private $subcats;
    private $hasExtra;

    private function __construct($id, $name, $idBlock) {
        $this->id = $id;
        $this->name = $name;
        $this->idBlock = $idBlock;
        $this->members = Member::getAll($this->id);

        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT DISTINCT `subcat` FROM `classes` WHERE `cat`='" . $this->id . "'")) {
            if ($row = $res->fetch_assoc()) {
                $subcats = array();
                do {
                    if ($row["subcat"] == "")
                        continue;
                    $subcats[] = $row["subcat"];
                } while ($row = $res->fetch_assoc());
                $this->subcats = $subcats;
            } else {
                $this->subcats = array();
            }
        } else {
            throw new SQLException($sql->error);
        }

        if ($res = $sql->query("SELECT * FROM `classes` WHERE `cat`='" . $this->id . "' AND `extra`=1")) {
            if ($row = $res->fetch_assoc()) {
                $this->hasExtra = true;
            } else {
                $this->hasExtra = false;
            }
        } else {
            throw new SQLException($sql->error);
        }

        $sql->close();
    }

    public static function get($name) {
        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT * FROM `categories` WHERE `id`='$name'")) {
            if ($row = $res->fetch_assoc()) {
                $id = $row["id"];
                $name = $row["name"];
                $idBlock = $row["id_block"];
                $sql->close();
                return new Category($id, $name, $idBlock);
            } else {
                $sql->close();
                throw new NotFoundException();
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public static function getAll() {
        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT DISTINCT `name` FROM `categories`")) {
            if ($row = $res->fetch_assoc()) {
                $cats = [];
                do {
                    $cats[] = $row["id"];
                } while ($row = $res->fetch_assoc());
                $sql->close();
                return $cats;
            } else {
                $sql->close();
                throw new NotFoundException("Categories table is empty.");
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public static function getAllObj() {
        $sql = Database::getSQL();

        if ($res = $sql->query("SELECT DISTINCT `id` FROM `categories`")) {
            if ($row = $res->fetch_assoc()) {
                $cats = [];
                do {
                    $cats[] = Category::get($row["id"]);
                } while ($row = $res->fetch_assoc());
                $sql->close();
                return $cats;
            } else {
                $sql->close();
                throw new NotFoundException("Categories table is empty.");
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public function getSubcats() {
        return $this->subcats;
    }

    public function hasExtra() {
        return $this->hasExtra;
    }

    public function getName() {
        return $this->name;
    }

    public function getMembers() {
        return $this->members;
    }

}