<?php

require_once("../../config.php");

global $DB, $OUTPUT, $CFG, $PAGE;
require_login();
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT);
$courseid = optional_param('course', '', PARAM_INT);
$userid = optional_param('user', '', PARAM_INT);
$ratingid = optional_param('rating', '', PARAM_INT);
$date = optional_param('date', '', PARAM_TEXT);

$min = $page * $perpage;
$max = $min + $perpage;
$arr = [];
if ($courseid) {
    array_push($arr, ['cmid' => $courseid]);
}
if ($userid) {
    array_push($arr, ['userid' => $userid]);
}
if ($ratingid) {
    array_push($arr, ['rating' => $ratingid]);
}
if ($date) {
    array_push($arr, ['ratingdate' => $date]);
}
$query = '';
$finalQuery ='';
if (count($arr) > 0) {
    $finalQuery = 'WHERE ';
    foreach ($arr as $key => $data) {
        foreach ($data as $index => $value) {
            if ($index == 'ratingdate') {
                $query = 'lrh.'.$index . ' >= ' . $value;
            } else {
                $query = $index . '=' . $value;
            }
            if ($finalQuery === 'WHERE ') {
                $finalQuery .= $query;
            } else {
                $finalQuery .= ' AND ';
                $finalQuery .= $query;
            }
        }
    }
}

$sql = 'select lrh.rating,lrh.cmid,lrh.userid,lrh.ratingdate,lrc.*,u.firstname,u.lastname from {local_rating_helper} as lrh
        LEFT JOIN {user} as u ON u.id = lrh.userid
        LEFT JOIN {local_rating_comment} as lrc ON lrh.id = lrc.ratingid 
        ' . $finalQuery ?? '' . ' LIMIT ' . $min . ',' . $max;

$ratingList = $DB->get_records_sql($sql);

$context = context_system::instance();

$PAGE->set_url('/rating_helper/all_ratings.php');

$PAGE->set_context(context_system::instance());

$PAGE->set_pagetype('my-index');

$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));

$PAGE->set_heading(get_string('pluginname', 'local_rating_helper'));

$PAGE->navbar->add(get_string('pluginname', 'local_rating_helper'));

echo $OUTPUT->header();

$PAGE->requires->js_call_amd('local_rating_helper/sortorder');


$courses = $DB->get_records('course', null);
$users = $DB->get_records('user', null);

?>

    <form autocomplete="off" method="GET" accept-charset="utf-8" class="mform">

        <div class="d-flex mb-4">
            <div class="form-inline align-items-start felement" data-fieldtype="select">
                <select class="custom-select" name="course">
                    <?php
                    echo '<option value="">' . get_string('chosecourse', 'local_rating_helper') . '</option>';
                    foreach ($courses as $course) {
                        $selected = (isset($courseid) && $courseid == $course->id) ? 'selected' : '';
                        echo '<option '.$selected.' value="' . $course->id . '">' . $course->fullname . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-inline align-items-start felement" data-fieldtype="select">
                <select class="custom-select" name="user">
                    <?php
                    echo '<option value="">' . get_string('choseuser', 'local_rating_helper') . '</option>';
                    foreach ($users as $user) {
                        $selected = (isset($userid) && $userid == $user->id) ? 'selected' : '';
                        echo '<option value="' . $user->id . '">' . $user->firstname . ' (' . $user->email . ')</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-inline align-items-start felement" data-fieldtype="select">
                <select class="custom-select" name="rating">
                    <?php

                    echo '<option value="">' . get_string('choserating', 'local_rating_helper') . '</option>';
                    for ($i = 1; $i <= 5; $i++) {
                        $selected = (isset($ratingid) && $ratingid == $i) ? 'selected' : '';
                        ?>
                        <option <?= $selected ?> value="<?= $i ?>">
                            <?php

                            for ($k = 1; $k <= $i; $k++) {
                                echo '✰';
                            }
                            ?>

                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <div class="form-inline align-items-start felement" data-fieldtype="date">
                <input value="<?=$date?>" class="p-1" type="date" name="date"/>
            </div>
            <input type="submit" name="submit" class="btn btn-primary" id="id_submit"
                   value="<?php
                   echo get_string('submit', 'local_rating_helper');
                   ?>"/>
            <a href="<?= $CFG->wwwroot . '/local/rating_helper/all_ratings.php'?>" class="btn btn-primary"><?php
                echo get_string('reset', 'local_rating_helper');
                ?></a>

        </div>
    </form>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>UserName</th>
            <th>CourseId</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody class="list-unstyled" id="page_list">
        <?php
        foreach ($ratingList as $rating) {
            ?>
            <tr>
                <td><?= $rating->firstname . ' ' . $rating->lastname; ?></td>
                <td><?= $rating->cmid; ?></td>
                <td><?php
                    for ($k = 1; $k <= $rating->rating; $k++) {
                        echo '✰';
                    }
                    ?></td>
                <td><?= $rating->comment; ?></td>
                <td><?= $rating->ratingdate; ?></td>
            </tr>

        <?php }
        ?>
        </tbody>
    </table>

<?php

$totalcount = count($courses);

$baseurl = new moodle_url('/local/rating_helper/all_ratings.php', array('course' => $courseid, 'user' => $userid, 'rating' => $ratingid, 'date' => $date, 'perpage' => $perpage));

echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);

echo $OUTPUT->footer();

