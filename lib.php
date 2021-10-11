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
 * A small helper lib of the local_rating_helper.
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2020 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Check if the given user has already rated the given course module.
 *
 * @param string $userid Id of the user to check.
 * @param string $cmid The Id of the course module to check for.
 * @return bool
 * @throws dml_exception
 */
function user_has_rated ($userid, $cmid) {
    global $DB;
    if($DB->record_exists('local_rating_helper', array('userid' => $userid, 'cmid' => $cmid))){
        return $DB->record_exists('local_rating_helper', array('userid' => $userid, 'cmid' => $cmid));
    }else{
        return false;
    }
}

/**
 * Save a rating for a course module.
 *
 * @param string $userid Id of the user who rated.
 * @param string $cmid The Id of the course module to rate for.
 * @param string $rating The rate value.
 * @return bool
 * @throws dml_exception
 */
function save_rating ($userid, $cmid, $rating,$comment) {
    global $DB;

    // Validate input.
    if (!$DB->record_exists('user', array('id' => $userid))) {
        // User dont exists.
        return false;
    }
    if (!$DB->record_exists('course_modules', array('id' => $cmid))) {
        // Cm dont exists.
        return false;
    }

    if ($DB->record_exists('local_rating_helper', array('userid' => $userid, 'cmid' => $cmid))) {
        // Rating for this user already saved.
        return false;
    } else {
        $new_rating = new stdClass();
        $new_rating->userid = $userid;
        $new_rating->cmid = $cmid;
        $new_rating->rating = $rating;
        $new_rating->ratingdate = date("Y-m-d H:i:s");
        $data = $DB->insert_record('local_rating_helper', $new_rating);

        $rating_comment = new stdClass();
        $rating_comment->ratingid =$data;
        $rating_comment->comment =$comment;
        $rating_comment->ratingdate = date("Y-m-d H:i:s");
        return $DB->insert_record('local_rating_comment',$rating_comment );

    }
}

/**
 * Get the rating for a single course module.
 *
 * @param int $cmid The Id of the course module to rate for.
 * @return int/false
 * @throws dml_exception
 */
function get_cm_rating ($cmid) {
    global $DB;

    $ratings = $DB->get_records('local_rating_helper', array('cmid' => $cmid));

    if (empty($ratings)) {
        return false;
    }
    $ratings_array = array();
    foreach ($ratings as $rating) {
        $ratings_array[] = $rating->rating;
    }

    $avrg = (array_sum($ratings_array) / count($ratings_array));
    return $avrg;
}

/**
 * Get the rating for a course.
 *
 * @param string $courseid The Id of the course to get the rates for.
 * @return bool
 * @throws dml_exception
 */
function get_course_rating ($courseid) {
    global $DB;

    $mod_list = get_course_mods($courseid);
    $ratings = array();
    foreach ($mod_list as $mod) {
        $rating = get_cm_rating ($mod->id);
        if (!empty($rating)) {
            $ratings[] = $rating;
        }
    }

    if(count($ratings) > 0){
        $avrg = (array_sum($ratings) / count($ratings));
        return $avrg;
    }
    return "0";
}

/**
 * Get the review count for a single course module.
 *
 * @param int $cmid The Id of the course module to rate for.
 * @return int/false
 * @throws dml_exception
 */
function get_cm_review_count ($cmid) {
    global $DB;

    $sql = "SELECT DISTINCT userid FROM {local_rating_helper} WHERE cmid=:cmid";
    $reviews = $DB->get_records_sql($sql, array('cmid'=>$cmid));

    if(count($reviews)>0){
        return count($reviews);
    }
    else{
        return 0;
    }
}

/**
 * Get the number of reviewers for a course.
 *
 * @param string $courseid The Id of the course to get the rates for.
 * @return bool
 * @throws dml_exception
 */
function get_number_of_reviews ($courseid) {
    global $DB;

    $mod_list = get_course_mods($courseid);
    $reveiwers = array();
    foreach ($mod_list as $mod) {
        $reviewer = get_cm_review_count ($mod->id);
        if (!empty($reviewer)) {
            $reveiwers[] = $reviewer;
        }
    }

    if(count($reveiwers) > 0){
        $sum = array_sum($reveiwers);
        return $sum;
    }
    else{
        return "0";
    }
}



/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function local_rating_helper_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $OUTPUT;
    $courseid = array_shift($args);
    $filename = array_pop($args);

    $context = context_course::instance($courseid);
    $fs = get_file_storage();

    // Prepare file record object
    $fileinfo = array(
        'component' => 'course',     // usually = table name
        'filearea' => 'overviewfiles',     // usually = table name
        'itemid' => 0,               // usually = ID of row in table
        'contextid' => $context->id, // ID of context
        'filepath' => '/',           // any path beginning and ending in /
        'filename' => $filename); // any filename

    // Get file
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * @param $courseid
 * @return string
 */
function get_course_image_url($courseid){
    global $CFG,$OUTPUT;
    $data = new stdClass();
    $data->id = $courseid;

    $courseimage = core_course\external\course_summary_exporter::get_course_image($data);
    if (!$courseimage) {
        $courseimage = $OUTPUT->get_generated_image_for_id($data->id);
        return $courseimage;
    }
    $filename = basename($courseimage);
    return $CFG->wwwroot.'/pluginfile.php/1/local_rating_helper/overview/'.$courseid.'/'.$filename;
}

/**
 * @param $star
 * @return string
 */
function generate_star_dom($star){
    $htm = '<div class="">';
    for($i=0; $i<$star; $i++ ){
        $htm .= '<svg width="21" height="20" viewBox="0 0 21 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3.58266 18.5634C3.48541 19.1178 4.03237 19.5517 4.51542 19.3034L10.002 16.4835L15.4885 19.3034C15.9715 19.5517 16.5185 19.1178 16.4212 18.5634L15.3841 12.6516L19.787 8.45578C20.1985 8.06366 19.9855 7.34671 19.4342 7.2684L13.3111 6.39856L10.581 0.990381C10.3351 0.503206 9.66885 0.503206 9.42291 0.990381L6.69276 6.39856L0.569668 7.2684C0.0184315 7.34671 -0.194569 8.06366 0.216907 8.45578L4.61982 12.6516L3.58266 18.5634ZM9.71342 15.1033L5.10623 17.4712L5.97405 12.5246C6.01495 12.2915 5.93803 12.0527 5.77061 11.8931L2.13706 8.4305L7.20245 7.71092C7.41184 7.68117 7.59468 7.54743 7.69346 7.35176L10.002 2.77884L12.3104 7.35176C12.4092 7.54743 12.5921 7.68117 12.8015 7.71092L17.8668 8.4305L14.2333 11.8931C14.0659 12.0527 13.989 12.2915 14.0299 12.5246L14.8977 17.4712L10.2905 15.1033C10.1085 15.0097 9.89541 15.0097 9.71342 15.1033Z"
                                  fill="#9CA4B6"/>
                        </svg>';
    }
    $htm .= '</div>';

    return $htm;
}

/**
 * @param $str
 * @param int $wordCount
 * @return string
 */
function get_snippet( $str, $wordCount = 10 ) {
    return implode(
        '',
        array_slice(
            preg_split(
                '/([\s,\.;\?\!]+)/',
                $str,
                $wordCount*2+1,
                PREG_SPLIT_DELIM_CAPTURE
            ),
            0,
            $wordCount*2-1
        )
    );
}