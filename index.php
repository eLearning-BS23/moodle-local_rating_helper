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
 * Index Page
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Brain Station 23
 * @copyright  2021 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
global $DB, $USER, $OUTPUT, $CFG, $PAGE;
require_once($CFG->dirroot . '/local/rating_helper/lib.php');
$courseid = required_param('course_id', PARAM_INT);

$course = $DB->get_record("course", array("id" => $courseid), "*", MUST_EXIST);

require_login($course, false);

$sql = "select * from {local_rating_helper} where cmid = :cmid AND userid= :userid";
$israting = $DB->get_record_sql($sql, ['cmid' => $courseid, 'userid' => $USER->id]);
$courseurl = $CFG->wwwroot . '/course/view.php?id=' . $courseid;
$context = context_system::instance();
$PAGE->set_url($CFG->wwwroot . '/local/rating_helper/index.php');
$PAGE->set_pagetype('my-index');
$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
$PAGE->set_heading(get_string('pluginname', 'local_rating_helper'));

$PAGE->navbar->add($course->fullname, $courseurl);
$PAGE->navbar->add(get_string('ratings', 'local_rating_helper'));
$imageurl = get_course_image_url($courseid);


$data['ratings'] = get_string('ratings', 'local_rating_helper');
$data['previousratings'] = get_string('previousratings', 'local_rating_helper');
$data['whatyourfeedback'] = get_string('whatyourfeedback', 'local_rating_helper');
$data['rateyourexperience'] = get_string('rateyourexperience', 'local_rating_helper');
$data['leaveareview'] = get_string('leaveareview', 'local_rating_helper');
$data['sendrate'] = get_string('sendrate', 'local_rating_helper');
$data['commentplaceholder'] = get_string('commentplaceholder', 'local_rating_helper');
$data['imageurl'] = $imageurl;
$data['courseurl'] = $courseurl;
$data['coursesummary'] = get_snippet(html_entity_decode($course->summary), 15);
$data['coursefullname'] = $course->fullname;

$data['star5'] = generate_star_dom(5);
$data['star4'] = generate_star_dom(4);
$data['star3'] = generate_star_dom(3);
$data['star2'] = generate_star_dom(2);
$data['star1'] = generate_star_dom(1);

$data['israting'] = $israting;
$data['courseid'] = $course->id;
$data['uid'] = $USER->id;
$paramsforratinguse = [
    'course_id' => $courseid,
    'user_id' => $USER->id,
];
$PAGE->requires->js_call_amd('local_rating_helper/submitrating', 'init', $paramsforratinguse);
echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_rating_helper/index', $data);
echo $OUTPUT->footer();
