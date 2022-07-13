<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Ratings List
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Brain Station 23
 * @copyright  2021 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/rating_helper/lib.php');

global $DB, $OUTPUT, $CFG, $PAGE;
require_login();
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 30, PARAM_INT);
$courseid = optional_param('course', '', PARAM_INT);
$userid = optional_param('user', '', PARAM_INT);
$ratingid = optional_param('rating', '', PARAM_INT);
$date = optional_param('date', '', PARAM_TEXT);

$context = context_system::instance();
$PAGE->set_url($CFG->wwwroot . '/local/rating_helper/ratings.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
$PAGE->set_heading(get_string('ratings', 'local_rating_helper'));
$PAGE->navbar->add(get_string('pluginname', 'local_rating_helper'));

$min = $page * $perpage;

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
$finalquery = '';
if (count($arr) > 0) {
    $finalquery = 'WHERE ';
    foreach ($arr as $key => $data) {
        foreach ($data as $index => $value) {
            if ($index == 'ratingdate') {
                $query = 'lrh.' . $index . ' >= ' . $value;
            } else {
                $query = $index . '=' . $value;
            }
            if ($finalquery === 'WHERE ') {
                $finalquery .= $query;
            } else {
                $finalquery .= ' AND ';
                $finalquery .= $query;
            }
        }
    }
}

$sql = 'select lrh.rating,
       lrh.cmid,
       lrh.userid,
       lrh.ratingdate,
       lrc.*,
       u.firstname,
       u.lastname
       FROM {local_rating_helper} lrh
       LEFT JOIN {user} u ON u.id = lrh.userid
       LEFT JOIN {local_rating_comment} lrc
           ON lrh.id = lrc.ratingid
        ' . $finalquery ?? '';

$ratingdata = $DB->get_records_sql($sql, null, $min, $perpage);

foreach ($ratingdata as $rate) {
    $rate->rating = rating_count($rate->rating);
    $data['userratings'][] = $rate;
}

$ratings = array_fill(1, 5, 1);

$data['ratings'][] = [
    'value' => null,
    'text' => 'All',
];

foreach ($ratings as $ratingcid => $rating) {
    $data['ratings'][] = ['value' => $ratingcid, 'text' => rating_count($ratingcid), 'selected' => ($ratingcid == $ratingid)];
}

$sql = "SELECT {course}.id,{course}.fullname FROM {course} WHERE category > :category;";

$courses = $DB->get_records_sql($sql, ['category' => 0]);

$data['courses'][] = [
    'value' => null,
    'text' => 'All',
];

foreach ($courses as $coursename) {
    $data['courses'][] = [
        'value' => $coursename->id,
        'text' => $coursename->fullname,
        'selected' => ($coursename->id == $courseid)];
}

$users = $DB->get_records_menu('user', null, null, 'id,username');

$data['users'][] = [
    'value' => null,
    'text' => 'All',
];

foreach ($users as $uservalue => $username) {
    $data['users'][] = ['value' => $uservalue, 'text' => $username, 'selected' => ($uservalue == $userid)];
}

$sql = 'select COUNT(lrh.id)
        FROM {local_rating_helper} lrh
        ' . $finalquery ?? '';

$totalcount = $DB->count_records_sql($sql, null);

$baseurl = new moodle_url('/local/rating_helper/ratings.php',
    array('course' => $courseid,
        'user' => $userid,
        'rating' => $ratingid,
        'date' => $date,
        'perpage' => $perpage));

$data['submit'] = get_string('submit', 'local_rating_helper');
$data['reset'] = get_string('reset', 'local_rating_helper');
$data['allratingsurl'] = $CFG->wwwroot . '/local/rating_helper/ratings.php';
$data['coursesurl'] = $CFG->wwwroot . '/local/rating_helper/courses.php';

echo $OUTPUT->header();

echo $OUTPUT->render_from_template('local_rating_helper/searchbar', $data);
echo $OUTPUT->render_from_template('local_rating_helper/ratings', $data);
echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
echo $OUTPUT->footer();

