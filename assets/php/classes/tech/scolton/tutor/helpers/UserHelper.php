<?php
namespace tech\scolton\tutors\helpers;

use tech\scolton\tutor\Member;
use tech\scolton\tutor\TimeSlot;
use tech\scolton\tutor\Tutor;
use tech\scolton\tutor\User;
use tech\scolton\tutor\exception\NotFoundException;

class UserHelper {

    private $user;

    /** @var Tutor */
    private $tutor;

    public function __construct(User $user) {
        $this->user = $user;

        try {
            $this->tutor = $user->getTutor();
        } catch (NotFoundException $e) {
            $this->tutor = null;
        }
    }

    public function render() {
        $arguments = $this->preRender();
        echo $this->doRender($arguments);
    }

    private function preRender() {
        $slots = TimeSlot::getAll();
        array_unshift($slots, null);

        $days = [
            "Sunday",
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday"
        ];
        array_unshift($days, null);

        $actual = null;

        if ($this->tutor) {
            $subjects = $this->tutor->getSubjects();
            $possible = Member::getAllClasses();

            $actual = [];

            /** @var Member $sub */
            foreach ($possible as $sub) {
                if (!in_array($sub->getId(), $subjects))
                    continue;
                if ($sub->getId() % 100 == 0)
                    continue;

                $cName = $sub->getCat()->getName();

                if (!$actual[$cName])
                    $actual[$cName] = [];

                $actual[$cName][] = $sub;
            }
        }

        return array (
            "days" => $days,
            "slots" => $slots,
            "actual" => $actual,
        );
    }

    private function doRender($arguments) {
        $days   = $arguments["days"];
        $slots  = $arguments["slots"];
        $actual = $arguments["actual"];

        ob_start();
?>
        <div class="well account-info">
            <div class="row">
                <div class="col-md-6 col-sm-6">
                    <img data-toggle="tooltip" data-placement="bottom" title="Profile pictures coming soon!"
                         class="profile-picture" alt="Profile pictures coming soon!"
                         src="http://s3.amazonaws.com/37assets/svn/765-default-avatar.png">
                </div>
                <div class="col-md-6 col-sm-6">
                    <h1 id="details-name">
                        <?php /** @noinspection PhpUndefinedVariableInspection */
                        echo $user->getName(); ?>
                        <?php if ($this->tutor): ?>
                            <span title="Verified Tutor" data-toggle="tooltip" data-placement="right"
                                  class="glyphicon glyphicon-apple verified-tutor"></span>
                        <?php endif; ?>
                    </h1>
                    <h4>
                        <?php
                        $grade = $user->getGrade();

                        $addendum = "";
                        if ($grade >= 9 && $grade <= 12) {
                            $addendum .= " - ";
                            switch ($grade) {
                                case 9: {
                                    $addendum .= "Freshman";
                                    break;
                                }
                                case 10: {
                                    $addendum .= "Sophomore";
                                    break;
                                }
                                case 11: {
                                    $addendum .= "Junior";
                                    break;
                                }
                                case 12: {
                                    $addendum .= "Senior";
                                    break;
                                }
                            }
                        }
                        echo 'Grade ' . $grade . $addendum;
                        ?>
                    </h4>
                    <h4>
                        <a href="mailto:<?php echo $user->getEmail(); ?>">
                            <?php echo $user->getEmail(); ?>
                        </a>
                    </h4>
                </div>
            </div>
        </div>
        <div class="well account-info x-padding">
            <?php if ($this->tutor): ?>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <h1>
                            Schedule
                        </h1>
                        <table class="thin">
                            <tbody>
                                <?php
                                for ($day = 1; $day < sizeof($days); $day++):
                                    ?>
                                    <tr>
                                        <th><?php echo $days[$day]; ?></th>
                                        <td>
                                            <?php
                                            $finalString = "";
                                            $schedule = $this->tutor->getAvailability();

                                            $slot = 1;
                                            while ($slot < sizeof($slots)) {
                                                if ($schedule[$day][$slot]) {
                                                    $start = $slot;
                                                    $end = $slot + 1;
                                                    while ($schedule[$day][$end] && $end < sizeof($slots)) {
                                                        $end++;
                                                    }
                                                    /** @var TimeSlot $tSlotStart */
                                                    $tSlotStart = $slots[$start];

                                                    /** @var TimeSlot $tSlotEnd */
                                                    $tSlotEnd = $slots[$end - 1];

                                                    $finalString .= $tSlotStart->renderStart() . " - " . $tSlotEnd->renderEnd() . ", ";

                                                    $slot = $end;
                                                } else {
                                                    $slot++;
                                                }
                                            }

                                            if ($finalString == "")
                                                $finalString = "No availability.";
                                            else
                                                $finalString = substr($finalString, 0, -2);

                                            echo $finalString;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                endfor;
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 col-sm-6">
                        <h1>
                            Subjects
                        </h1>
                        <?php
                        foreach ($actual as $cat => $subs):
                            ?>
                            <h3>
                                <?php echo $cat; ?>
                            </h3>
                            <?php
                            $str = "";
                            /** @var Member $sub */
                            foreach ($subs as $sub) {
                                $str .= $sub->getName() . ", ";
                            }
                            $str = substr($str, 0, -2);
                            echo $str;
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <h4>
                    <?php if (PAGE_NAME == "ACCOUNT"): ?>
                        You aren't registered as a tutor yet.  <!--suppress HtmlUnknownTarget -->
                        <a href="upgrade">Upgrade account now.</a>
                    <?php else: ?>
                        This person isn't registered as a tutor yet.
                    <?php endif; ?>
                </h4>
            <?php endif; ?>
        </div>
<?
        return ob_get_clean();
    }
}