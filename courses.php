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
 * Courses List
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
$context = context_system::instance();
$PAGE->set_url($CFG->wwwroot . '/local/rating_helper/courses.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_rating_helper'));
$PAGE->set_heading(get_string('courses', 'local_rating_helper'));
$PAGE->navbar->add(get_string('pluginname', 'local_rating_helper'));

$min = $page * $perpage;

$sql = "SELECT {course}.id,{course}.fullname FROM {course} WHERE category > :category";

$coursedata = $DB->get_records_sql($sql, ['category' => 0], $min, $perpage);

$courses = [];
$sn = 1;
foreach ($coursedata as $course) {
    $courseobj = new stdClass();
    $courseobj->sn = $sn++;
    $courseobj->name = $course->fullname;
    $courseobj->submitrating = $CFG->wwwroot . '/local/rating_helper/index.php?course_id=' . $course->id;
    $courseobj->ratings = $CFG->wwwroot . '/local/rating_helper/ratings.php?course=' . $course->id;
    $courses[] = $courseobj;
}

$data['data'] = $courses;
$data['ratings'] = $CFG->wwwroot . '/local/rating_helper/ratings.php';

$sql = "SELECT COUNT(id) FROM {course} WHERE category > :category";

$totalcount = $DB->count_records_sql($sql, ['category' => 0]);

$baseurl = new moodle_url('/local/rating_helper/courses.php', array('perpage' => $perpage, 'page' => $page));

$PAGE->requires->js_call_amd('local_rating_helper/submitrating', 'copy');

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_rating_helper/courses', $data);
echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
echo $OUTPUT->footer();


