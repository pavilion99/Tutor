<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]);

include_once("sql.php");

class NotFoundException extends Exception
{
}

class AlreadyExistsException extends Exception
{
}

class SQLException extends Exception
{
}

class User
{

    private $id;
    private $name;
    private $email;
    private $grade;

    private function __construct($id, $name, $email, $grade)
    {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->grade = $grade;
    }

    public static function get($id)
    {
        $sql = getSQL();

        $res = $sql->query("SELECT * FROM `users` WHERE `id`=$id");

        if ($res && $row = $res->fetch_assoc()) {
            $sql->close();
            return new User($row["id"], $row["name"], $row["email"], $row["grade"]);
        } else {
            $sql->close();
            throw new NotFoundException();
        }
    }

    public function genTutor($phone, $schedule, $subjects)
    {
        try {
            $this->getTutor();
            throw new AlreadyExistsException();
        } catch (NotFoundException $e) {
            $sql = getSQL();
            $phone = $sql->escape_string($phone);
            $schedule = $sql->escape_string($schedule);
            $subjects = $sql->escape_string($subjects);

            if ($sql->query("INSERT INTO `tutors` (`id`,`availability`,`phone`,`subjects`) VALUES (" . $this->getId() . ", '$schedule', '$phone', '$subjects')")) {
                $sql->close();
                return $this->getTutor();
            } else {
                throw new SQLException($sql->error);
            }
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getGrade()
    {
        return $this->grade;
    }

    public function getTutor()
    {
        return new Tutor($this);
    }

    public function setEmail($email)
    {
        $sql = getSQL();

        if ($sql->query("UPDATE `users` SET `email`='" . $email . "' WHERE `id`=" . $this->id)) {
            $this->email = $email;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

    public function setName($name)
    {
        $sql = getSQL();

        if ($sql->query("UPDATE `users` SET `name`='" . $name . "' WHERE `id`=" . $this->id)) {
            $this->name = $name;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

    public function setGrade($grade)
    {
        $sql = getSQL();

        if ($sql->query("UPDATE `users` SET `grade`='" . $grade . "' WHERE `id`=" . $this->id)) {
            $this->grade = $grade;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

}

class Tutor
{

    private $id;
    private $phone;
    private $subjects;
    private $availability;

    public function __construct($user)
    {
        if (!($user instanceof User))
            return;

        $this->id = $user->getId();

        $sql = getSQL();

        $tutorPDO = $sql->query("SELECT * FROM `tutors` WHERE `id`=" . $this->id);

        if ($tutorPDO && $tutorRow = $tutorPDO->fetch_assoc()) {
            $this->phone = $tutorRow["phone"];
            $this->subjects = json_decode($tutorRow["subjects"]);
            $this->availability = json_decode($tutorRow["availability"]);
            $sql->close();
        } else {
            $sql->close();
            throw new NotFoundException();
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getSubjects()
    {
        return $this->subjects;
    }

    public function getAvailability()
    {
        return $this->availability;
    }

}

class Category
{

    private $id;
    private $name;
    private $members;
    private $idBlock;
    private $subcats;
    private $hasExtra;

    private function __construct($id, $name, $idBlock)
    {
        $this->id = $id;
        $this->name = $name;
        $this->idBlock = $idBlock;
        $this->members = Member::getAll($this->id);

        $sql = getSQL();

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

    public static function get($name)
    {
        $sql = getSQL();

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

    public static function getAll()
    {
        $sql = getSQL();

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

    public static function getAllObj()
    {
        $sql = getSQL();

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

    public function getSubcats()
    {
        return $this->subcats;
    }

    public function hasExtra()
    {
        return $this->hasExtra;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMembers()
    {
        return $this->members;
    }

}

class Member
{

    private $id;
    private $name;
    private $cat;
    private $subcat;
    private $extra;
    private $multiple;

    private function __construct($id, $name, $cat, $subcat, $extra, $multiple)
    {
        $this->id = $id;
        $this->name = $name;
        $this->subcat = $subcat;
        $this->cat = $cat;
        $this->extra = $extra;
        $this->multiple = $multiple || $extra;
    }

    public static function getAllClasses()
    {
        $sql = getSQL();

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

    public static function getAll($cat)
    {
        $sql = getSQL();

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

    public function getSubcat()
    {
        return $this->subcat;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isMultiple()
    {
        return $this->multiple;
    }

    public function isExtra()
    {
        return $this->extra;
    }

    public function getCat()
    {
        return Category::get($this->cat);
    }

}

class TimeSlot
{

    private $id;
    private $start;
    private $end;
    private $label;

    private function __construct($id, $start, $end)
    {
        $this->id = $id;
        $this->start = new DateTime($start);
        $this->end = new DateTime($end);

        $startLabel = $this->start->format('g:i A');
        $endLabel = $this->end->format('g:i A');

        $this->label = $startLabel . ' - ' . $endLabel;
    }

    public static function getAll()
    {
        $sql = getSQL();

        if ($res = $sql->query("SELECT * FROM `schedule`")) {
            if ($row = $res->fetch_assoc()) {
                $slots = array();
                do {
                    $id = $row["id"];
                    $start = $row["start"];
                    $end = $row["end"];

                    $slots[] = new TimeSlot($id, $start, $end);
                } while ($row = $res->fetch_assoc());
                $sql->close();
                return $slots;
            } else {
                $sql->close();
                throw new SQLException("Schedule table is empty.");
            }
        } else {
            throw new SQLException($sql->error);
        }
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getId()
    {
        return $this->id;
    }

    public function renderStart()
    {
        return $this->start->format('g:i A');
    }

    public function renderEnd()
    {
        return $this->end->format('g:i A');
    }

}
