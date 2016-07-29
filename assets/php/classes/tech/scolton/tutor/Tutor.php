<?php
namespace tech\scolton\tutor;

use tech\scolton\tutor\exception\NotFoundException;

class Tutor {

    private $id;
    private $phone;
    private $subjects;
    private $availability;

    public function __construct(User $user) {
        $this->id = $user->getId();

        $sql = Database::getSQL();

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

    public function getId() {
        return $this->id;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function getSubjects() {
        return $this->subjects;
    }

    public function getAvailability() {
        return $this->availability;
    }

}