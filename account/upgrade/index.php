<?php
use tech\scolton\tutor\Category;
use tech\scolton\tutor\exception\NotFoundException;
use tech\scolton\tutor\exception\SQLException;
use tech\scolton\tutor\Member;
use tech\scolton\tutor\TimeSlot;
use tech\scolton\tutor\User;

define("PAGE_NAME", "ACCOUNT_UPGRADE");
define("REL", "../../");

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

session_start();
if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: ../../login");

$categories = Category::getAllObj();

array_unshift($categories, null, null);
array_push($categories, null);

if (isset($_POST["submit"])) {
    $schedule = $_POST["schedule"];
    $subjects = $_POST["subjects"];
    $phone = $_POST["phone"];

    $user = User::get($_SESSION["id"]);

    if ($user) {
        try {
            $tutor = $user->genTutor($phone, $schedule, $subjects);

            exit('{"success": true}');
        } catch (SQLException $e) {
            die('{"success": false, "error": "SQL Error: ' . $e->getMessage() . '"}');
        } catch (NotFoundException $e) {
            die('{"success": false, "error": "Tutor object not created."}');
        }
    } else {
        die ('{"success": false, "error": "Session error."}');
    }
}

if (isset($_POST["page"])):
    $page = intval($_POST["page"]);
    /** @var Category $cat */
    $cat = $categories[intval($_POST["page"])];
    $catted = [];
    $extras = [];

    if ($cat != null) {
        $catted["__NONE__"] = [];
        foreach ($cat->getSubcats() as $sub) {
            $catted[$sub] = [];
            /** @var Member $member */
            foreach ($cat->getMembers() as $member) {
                if ($member->getSubcat() == $sub)
                    $catted[$sub][] = $member;
                else if ($member->getSubcat() == null)
                    $catted["__NONE__"][] = $member;
            }
        }
        if ($cat->hasExtra()) {
            foreach ($cat->getMembers() as $member) {
                if ($member->isExtra())
                    $extras[] = $member;
            }
        }
    }

    ?>
    <form id="upgrade-form"
          action="javascript:void(0);"
          onsubmit="next();">
        <?php if ($page == 1): ?>
            <input type="tel"
                   pattern="\d{10}"
                   name="phone"
                   id="phone"
                   placeholder="Phone number (ten digits, no dashes or parentheses)"
                   class="form-control"/>
        <?php elseif ($page == 8): ?>
            <h3>
                Schedule
            </h3>
            <p>
                Check all of the boxes that work for your schedule. You will only be given
                appointments based on this schedule. If for an unexpected reason you
                are unable to fulfill a requested appointment, you will always have the option
                to deny the request.
            </p>
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Sunday</th>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $slots = TimeSlot::getAll();

                    /** @var TimeSlot $slot */
                    foreach ($slots as $slot):
                        ?>
                        <tr>
                            <th><?php echo $slot->getLabel(); ?></th>
                            <?php for ($day = 1; $day < 8; $day++): ?>
                                <td><input class="form-control" type="checkbox" data-day="<?php echo $day; ?>"
                                           data-slot="<?php echo $slot->getId(); ?>"
                                           id="<?php echo $day; ?>-<?php echo $slot->getId(); ?>"/></td>
                            <?php endfor; ?>
                        </tr>
                        <?php
                    endforeach;
                    ?>
                </tbody>
            </table>
        <?php else: ?>

            <h3>
                <?php echo $cat->getName(); ?>
            </h3>

            <?php if (!empty($cat->getSubcats()) && !empty($catted["__NONE__"])): ?>
                <select class="form-control" <?php echo $catted["__NONE__"][0]->isMultiple() ? "multiple" : ""; ?>>
                    <?php foreach ($catted["__NONE__"] as $member): ?>
                        <option value="<?php echo $member->getId(); ?>"
                                id="s-<?php echo $member->getId(); ?>"><?php echo $member->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if (!empty($cat->getSubcats())): ?>
                <?php foreach ($cat->getSubcats() as $sub): ?>
                    <h4>
                        <?php echo $sub; ?>
                    </h4>
                    <select class="form-control" <?php /** @var Member $tmp */
                    $tmp = $catted[$sub][0];
                    echo $tmp->isMultiple() ? "multiple" : ""; ?>>
                        <?php foreach ($catted[$sub] as $member): ?>
                            <option value="<?php echo $member->getId(); ?>"
                                    id="s-<?php echo $member->getId(); ?>"><?php echo $member->getName(); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endforeach; ?>
            <?php else: ?>
                <select class="form-control" <?php /** @var Member $tmp */
                $tmp = $cat->getMembers()[0];
                echo $tmp->isMultiple() ? "multiple" : ""; ?>>
                    <?php foreach ($cat->getMembers() as $member): ?>
                        <?php if ($member->isExtra())
                            continue; ?>
                        <option value="<?php echo $member->getId(); ?>"
                                id="s-<?php echo $member->getId(); ?>"><?php echo $member->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

            <?php if ($cat->hasExtra()): ?>
                <h3>
                    <?php echo $cat->getName(); ?> (Extra)
                </h3>
                <select class="form-control" multiple>
                    <?php foreach ($extras as $member): ?>
                        <option value="<?php echo $member->getId(); ?>"
                                id="s-<?php echo $member->getId(); ?>"><?php echo $member->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>

        <?php endif; ?>
        <br/>
        <?php if ($page != 1): ?>
            <button type="button"
                    class="btn btn-lg btn-default left"
                    id="back"
                    onclick="prev();">
                Go Back
            </button>
        <?php endif; ?>
        <input type="submit"
               class="btn btn-lg btn-primary right"
               value="Continue"
               id="continue"/>
    </form>
    <?php
    exit();
endif;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../../assets/parts/head.php"); ?>
        <script src="../../assets/js/main.js"></script>
        <link rel="stylesheet" href="../../assets/css/main.css"/>
        <script>window.currentPage = 1;</script>
    </head>
    <body>
        <?php include("../../assets/parts/nav.php"); ?>
        <div class="container-fluid"
             id="main-content">
            <div class="login-signup-form-wrapper">
                <h1>
                    Become a Tutor
                </h1>
                <div id="data">
                    <form id="upgrade-form"
                          action="javascript:void(0);"
                          onsubmit="next();">
                        <input type="tel"
                               pattern="\d{10}"
                               name="phone"
                               id="phone"
                               placeholder="Phone number (ten digits, no dashes or parentheses)"
                               class="form-control"/>
                        <br/>
                        <input type="submit"
                               class="btn btn-lg btn-primary right"
                               value="Continue"
                               id="continue"/>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>