<?php
namespace tech\scolton\tutor;

use tech\scolton\tutor\exception\AlreadyExistsException;
use tech\scolton\tutor\exception\MethodNotImplementedException;
use tech\scolton\tutor\exception\NotFoundException;
use tech\scolton\tutor\exception\SQLException;
use tech\scolton\tutors\helpers\UserHelper;

class User {

    private $id;
    private $name;
    private $email;
    private $grade;

    private function __construct($id, $name, $email, $grade) {
        $this->id = $id;
        $this->email = $email;
        $this->name = $name;
        $this->grade = $grade;
    }

    public function __call($name, $arguments) {
        $helper = new UserHelper($this);

        if (method_exists($helper, $name))
            call_user_func_array(array($helper, $name), $arguments);
        else
            throw new MethodNotImplementedException();
    }

    public static function get($id) {
        $sql = Database::getSQL();

        $res = $sql->query("SELECT * FROM `users` WHERE `id`=$id");

        if ($res && $row = $res->fetch_assoc()) {
            $sql->close();
            return new User($row["id"], $row["name"], $row["email"], $row["grade"]);
        } else {
            $sql->close();
            throw new NotFoundException();
        }
    }

    public function genTutor($phone, $schedule, $subjects) {
        try {
            $this->getTutor();
            throw new AlreadyExistsException();
        } catch (NotFoundException $e) {
            $sql = Database::getSQL();
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

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getName() {
        return $this->name;
    }

    public function getGrade() {
        return $this->grade;
    }

    public function getTutor() {
        return new Tutor($this);
    }

    public function setEmail($email) {
        $sql = Database::getSQL();

        if ($sql->query("UPDATE `users` SET `email`='" . $email . "' WHERE `id`=" . $this->id)) {
            $this->email = $email;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

    public function setName($name) {
        $sql = Database::getSQL();

        if ($sql->query("UPDATE `users` SET `name`='" . $name . "' WHERE `id`=" . $this->id)) {
            $this->name = $name;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

    public function setGrade($grade) {
        $sql = Database::getSQL();

        if ($sql->query("UPDATE `users` SET `grade`='" . $grade . "' WHERE `id`=" . $this->id)) {
            $this->grade = $grade;
            $sql->close();
            return true;
        } else {
            $sql->close();
            return false;
        }
    }

    public function render() {
        $helper = new UserHelper($this);
        return $helper->render();
    }

}