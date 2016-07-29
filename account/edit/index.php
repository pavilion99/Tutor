<?php
use tech\scolton\tutor\Category;
use tech\scolton\tutor\Database;
use tech\scolton\tutor\exception\NotFoundException;
use tech\scolton\tutor\Member;
use tech\scolton\tutor\User;

session_start();
define("PAGE_NAME", "ACCOUNT_EDIT");
define("REL", "../../");
if (!isset($_SESSION["id"]) || $_SESSION["id"] == -1)
    header("Location: " . REL . "/login");

if (isset($_POST["name"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $grade = intval($_POST["grade"]);
}

$sql = Database::getSQL();

$user = User::get($_SESSION["id"]);

$tutor = null;
try {
    $tutor = $user->getTutor();
} catch (NotFoundException $e) {
    $tutor = false;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include("../../assets/parts/head.php"); ?>
        <script>window.currentPage = 1;</script>
        <script src="../../assets/js/main.js"></script>
        <script src="../../assets/js/md5.min.js"></script>
        <link rel="stylesheet" href="../../assets/css/main.css"/>
        <script>
            window.tutor = <?php echo $tutor ? "true" : "false"; ?>;
        </script>
    </head>
    <body>
        <?php include("../../assets/parts/nav.php"); ?>
        <div class="container-fluid">
            <h1 class="edit-heading">
                Basic Information
            </h1>
            <div class="account-info">
                <input id="name"
                       class="form-control"
                       name="name"
                       placeholder="Your name"
                       value="<?php echo $user->getName(); ?>">
                <br/>
                <div class="input-group">
                    <input type="text"
                           name="email"
                           id="email"
                           placeholder="Email address"
                           class="form-control"
                           value="<?php echo str_replace("@ohschools.org", "", $user->getEmail()); ?>"
                           required/>
                    <span class="input-group-addon">@ohschools.org</span>
                </div>
                <br/>
                <select class="form-control"
                        id="grade"
                        name="grade">
                    <option value="-1">Grade Level</option>
                    <option <?php echo $user->getGrade() == 7 ? "selected" : ""; ?> value="7">7</option>
                    <option <?php echo $user->getGrade() == 8 ? "selected" : ""; ?> value="8">8</option>
                    <option <?php echo $user->getGrade() == 9 ? "selected" : ""; ?> value="9">9</option>
                    <option <?php echo $user->getGrade() == 10 ? "selected" : ""; ?> value="10">10</option>
                    <option <?php echo $user->getGrade() == 11 ? "selected" : ""; ?> value="11">11</option>
                    <option <?php echo $user->getGrade() == 12 ? "selected" : ""; ?> value="12">12</option>
                </select>
                <?php if ($tutor): ?>
                    <br/>
                    <input id="phone"
                           class="form-control"
                           type="tel"
                           pattern="\d{10}"
                           name="phone"
                           placeholder="Phone number (ten digits, no dashes or parentheses)"
                           value="<?php echo $tutor->getPhone(); ?>">
                <?php endif; ?>
            </div>
            <br/>
            <h1 class="edit-heading">
                Account Security
            </h1>
            <div class="account-info">
                <input id="password"
                       class="form-control"
                       placeholder="Password (leave blank to keep unchanged)"
                       name="password">
                <br/>
                <input id="password-confirmation"
                       class="form-control"
                       placeholder="Password confirmation"
                       name="password-confirmation">
            </div>
            <br/>
            <?php if ($tutor): ?>
                <h1 class="edit-heading">
                    Subjects
                </h1>
                <div class="account-info">
                    <?php
                    $categories = Category::getAllObj();
                    $subjects = $tutor->getSubjects();

                    /** @var Category $cat */
                    foreach ($categories as $cat):
                        $catted = [];
                        $extras = [];

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
                        ?>
                        <h3>
                            <?php echo $cat->getName(); ?>
                        </h3>
                        <?php if (!empty($cat->getSubcats()) && !empty($catted["__NONE__"])): ?>
                        <select
                            class="form-control" <?php echo $catted["__NONE__"][0]->isMultiple() ? "multiple" : ""; ?>>
                            <?php foreach ($catted["__NONE__"] as $member): ?>
                                <option value="<?php echo $member->getId(); ?>"
                                        id="s-<?php echo $member->getId(); ?>" <?php if (in_array($member->getId(), $subjects))
                                    echo "selected"; ?>><?php echo $member->getName(); ?></option>
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
                                            id="s-<?php echo $member->getId(); ?>" <?php if (in_array($member->getId(), $subjects))
                                        echo "selected"; ?>><?php echo $member->getName(); ?></option>
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
                                        id="s-<?php echo $member->getId(); ?>" <?php if (in_array($member->getId(), $subjects))
                                    echo "selected"; ?>><?php echo $member->getName(); ?></option>
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
                                        id="s-<?php echo $member->getId(); ?>" <?php if (in_array($member->getId(), $subjects))
                                    echo "selected"; ?>><?php echo $member->getName(); ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>

                    <?php endforeach; ?>
                </div>
                <h1 class="edit-heading">
                    Schedule
                </h1>
                <div class="account-info">

                </div>
            <?php endif; ?>
        </div>
    </body>
</html>