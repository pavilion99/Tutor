<?php
namespace tech\scolton\tutor;

use DateTime;
use tech\scolton\tutor\exception\SQLException;

class TimeSlot {

    private $id;
    private $start;
    private $end;
    private $label;

    private function __construct($id, $start, $end) {
        $this->id = $id;
        $this->start = new DateTime($start);
        $this->end = new DateTime($end);

        $startLabel = $this->start->format('g:i A');
        $endLabel = $this->end->format('g:i A');

        $this->label = $startLabel . ' - ' . $endLabel;
    }

    public static function getAll() {
        $sql = Database::getSQL();

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

    public function getLabel() {
        return $this->label;
    }

    public function getId() {
        return $this->id;
    }

    public function renderStart() {
        return $this->start->format('g:i A');
    }

    public function renderEnd() {
        return $this->end->format('g:i A');
    }

}