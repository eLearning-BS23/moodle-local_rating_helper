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
 * The externallib of the elediawebservicesuite.
 *
 * Here you'll find the methods you can directly access through
 * the webservice.
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2020 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/externallib.php');

class rating_helper_services extends external_api {

    public static function get_all_ratings_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' =>
                    new external_value(PARAM_INT, 'The Id of the course module to check for.'
                    )
            )
        );
    }
    public static function get_all_ratings($cmid)
    {
        global $DB, $OUTPUT;
        // Parameter validation.
        $params = self::validate_parameters(
            self::get_all_ratings_parameters(),
            array(
                'cmid' => $cmid
            )
        );

        if (! ($cm = $DB->get_record('course_modules', array('id' => $params['cmid'])))) {
            $output['result'] = 'Course module not found for id '.$params['cmid'];
            $output['success'] = false;
            return $output;
        }

        $allratingList = 'select lrh.*,lrc.*,u.firstname,u.lastname from {local_rating_helper} as lrh 
                LEFT JOIN {user} as u ON u.id = lrh.userid 
                LEFT JOIN {local_rating_comment} as lrc ON lrh.id = lrc.ratingid 
                where cmid = '.$cmid;
        $allratings = $DB->get_records_sql($allratingList);

        $newArr = [];
        foreach ($allratings as $data){
            $pfpic = '';
            $user = core_user::get_user($data->userid);
            $pfpic = $OUTPUT->user_picture($user, array('size' => 100));
            $outpuArr = [
                'id' => $data->id,
                'userid' => $data->userid,
                'cmid' => $data->cmid,
                'rating' => $data->rating,
                'ratingdate' => $data->ratingdate,
                'comment' => $data->comment,
                'firstname' => $data->firstname,
                'lastname' => $data->lastname,
                'profilepicture' => $pfpic
            ];
            array_push($newArr,$outpuArr);
        }

        $output['result'] = 'Data found';
        $output['success'] = true;
        $output['ratings'] = $newArr;
        return $output;

    }
    public static function get_all_ratings_returns()
    {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
                'ratings'    => new external_multiple_structure(self::ratings_list_structure()),
            )
        );
    }
    public static function get_indivisual_rating_parameters()
    {
        return new external_function_parameters(
            array(
                'cmid' =>
                    new external_value(PARAM_INT, 'The Id of the course module to check for.'
                    )
            )
        );
    }
    public static function get_indivisual_rating($cmid)
    {
        global $DB,$OUTPUT;
        // Parameter validation.
        $params = self::validate_parameters(
            self::get_indivisual_rating_parameters(),
            array(
                'cmid' => $cmid
            )
        );

        if (! ($cm = $DB->get_record('course_modules', array('id' => $params['cmid'])))) {
            $output['result'] = 'Course module not found for id '.$params['cmid'];
            $output['success'] = false;
            return $output;
        }

        $sql = 'SELECT
            (select COUNT(*) FROM mdl_local_rating_helper where rating = 1 ) as rating1,
            (select COUNT(*) FROM mdl_local_rating_helper where rating = 2) as rating2,
            (select COUNT(*) FROM mdl_local_rating_helper where rating = 3 ) as rating3,
            (select COUNT(*) FROM mdl_local_rating_helper where rating = 4 ) as rating4,
            (select COUNT(*) FROM mdl_local_rating_helper where rating = 5 ) as rating5
        FROM mdl_local_rating_helper r
        WHERE r.cmid = '.$cmid.' limit 1';
        $ratingList = $DB->get_record_sql($sql);

        $output['rated1'] = $ratingList->rating1 ?? 0;
        $output['rated2'] = $ratingList->rating2 ?? 0;
        $output['rated3'] = $ratingList->rating3 ?? 0;
        $output['rated4'] = $ratingList->rating4 ?? 0;
        $output['rated5'] = $ratingList->rating5 ?? 0;
        $output['result'] = 'Data found';
        $output['success'] = true;
        return $output;

    }
    public static function get_indivisual_rating_returns()
    {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
                'rated1'    => new external_value(PARAM_INT, 'Return value'),
                'rated2'    => new external_value(PARAM_INT, 'Return value'),
                'rated3'    => new external_value(PARAM_INT, 'Return value'),
                'rated4'    => new external_value(PARAM_INT, 'Return value'),
                'rated5'    => new external_value(PARAM_INT, 'Return value'),
            )
        );
    }

    /**
     * Parameterdefinition for method "user_has_rated"
     *
     * @return {object} external_function_parameters
     */
    public static function user_has_rated_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'Id of the user to check.'
                ),
                'cmid' =>
                    new external_value(PARAM_INT, 'The Id of the course module to check for.'
                )
            )
        );
    }

    /**
     * Check if the given user has already rated the given course module.
     *
     * @param int $userid
     * @param int $cmid
     * @return array
     * @throws {moodle_exception}
     */
    public static function user_has_rated($userid, $cmid) {
        global $DB, $CFG, $USER;

        // Parameter validation.
        $params = self::validate_parameters(
            self::user_has_rated_parameters(),
            array(
                'cmid' => $cmid,
                'userid' => $userid,
            )
        );

        // Data validation.
        try {
            if (! ($cm = $DB->get_record('course_modules', array('id' => $params['cmid'])))) {
                $output['result'] = 'Course module not found for id '.$params['cmid'];
                $output['success'] = false;
                return $output;
            }
            if (! ($user = $DB->get_record('user', array('id' => $params['userid'])))) {
                $output['result'] = 'User not found for id '.$params['userid'];
                $output['success'] = false;
                return $output;
            }
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return $output;
        }

        require_once($CFG->dirroot.'/local/rating_helper/lib.php');
        if (user_has_rated ($userid, $cmid)) {
            $output['result'] = 'User already rated.';
            $output['success'] = true;
            $output['rated'] = true;
            return $output;
        } else {
            $output['result'] = 'User has not rated.';
            $output['success'] = true;
            $output['rated'] = false;
            return $output;
        }
    }

    /**
     * Returndefinition for method "user_has_rated"
     *
     * @return external_description
     */
    public static function user_has_rated_returns() {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
                'rated'    => new external_value(PARAM_RAW, 'Return message', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Parameterdefinition for method "save_rating"
     *
     * @return {object} external_function_parameters
     */
    public static function save_rating_parameters() {
        return new external_function_parameters(
            array(
                'userid' =>
                    new external_value(PARAM_INT, 'Id of the user who rated.'
                ),
                'cmid' =>
                    new external_value(PARAM_INT, 'The Id of the course module to rate for.'
                ),
                'rating' =>
                    new external_value(PARAM_INT, 'The rate value.'
                ),
                'comment' =>
                    new external_value(PARAM_TEXT, 'The message value.'
                )
            )
        );
    }

    /**
     * Save a rating for a course module.
     *
     * @param string $userid Id of the user who rated.
     * @param string $cmid The Id of the course module to rate for.
     * @param string $rating The rate value.
     * @param string $comment The rate value.
     * @return array
     * @throws {moodle_exception}
     */
    public static function save_rating($userid, $cmid, $rating, $comment) {
        global $DB, $CFG, $USER;

        // Parameter validation.
        $params = self::validate_parameters(
            self::save_rating_parameters(),
            array(
                'cmid' => $cmid,
                'userid' => $userid,
                'rating' => $rating,
                'comment' => $comment,
            )
        );

        // Data validation.
        try {
            if (! ($cm = $DB->get_record('course_modules', array('id' => $params['cmid'])))) {
                $output['result'] = 'Course module not found for id '.$params['cmid'];
                $output['success'] = false;
                return $output;
            }
            if (! ($user = $DB->get_record('user', array('id' => $params['userid'])))) {
                $output['result'] = 'User not found for id '.$params['userid'];
                $output['success'] = false;
                return $output;
            }
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return $output;
        }

        require_once($CFG->dirroot.'/local/rating_helper/lib.php');

        $id = save_rating ($userid, $cmid, $rating,$comment);
        if ($id) {
            $output['success'] = true;
        } else {
            $output['success'] = false;
        }

        if ($output['success']) {
            $output['result'] = 'Rating saved.';
        } else {
            $output['result'] = 'user has already rated.';
        }
        return $output;
    }

    /**
     * Returndefinition for method "save_rating"
     *
     * @return external_description
     */
    public static function save_rating_returns() {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
            )
        );
    }

    /**
     * Parameterdefinition for method "get_cm_rating"
     *
     * @return {object} external_function_parameters
     */
    public static function get_cm_rating_parameters() {
        return new external_function_parameters(
            array(
                'cmid' =>
                    new external_value(PARAM_INT, 'The Id of the course module to rate for.'
                ),
            )
        );
    }

    /**
     * Get the rating for a single course module.
     *
     * @param int $cmid The Id of the course module to get the rate for.
     * @return array
     * @throws {moodle_exception}
     */
    public static function get_cm_rating($cmid) {
        global $DB, $CFG, $USER;

        // Parameter validation.
        $params = self::validate_parameters(
            self::get_cm_rating_parameters(),
            array(
                'cmid' => $cmid,
            )
        );

        // Data validation.
        try {
            if (! ($cm = $DB->get_record('course_modules', array('id' => $params['cmid'])))) {
                $output['result'] = 'Course module not found for id '.$params['cmid'];
                $output['success'] = false;
                return $output;
            }
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return $output;
        }

        require_once($CFG->dirroot.'/local/rating_helper/lib.php');

        $output['rating'] = get_cm_rating ($cmid);
        $output['result'] = 'Average rating '.$output['rating'];
        $output['success'] = true;
        return $output;
    }

    /**
     * Returndefinition for method "get_cm_rating"
     *
     * @return external_description
     */
    public static function get_cm_rating_returns() {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
                'rating'    => new external_value(PARAM_RAW, 'Course module rating'),
            )
        );
    }

    /**
     * Parameterdefinition for method "get_course_rating"
     *
     * @return {object} external_function_parameters
     */
    public static function get_course_rating_parameters() {
        return new external_function_parameters(
            array(
                'courseid' =>
                    new external_value(PARAM_INT, 'The Id of the course to get the rate for.'
                ),
            )
        );
    }

    /**
     * Get the rating for a course.
     *
     * @param string $courseid The Id of the course to get the rates for.
     * @return array
     * @throws {moodle_exception}
     */
    public static function get_course_rating($courseid) {
        global $DB, $CFG, $USER;

        // Parameter validation.
        $params = self::validate_parameters(
            self::get_course_rating_parameters(),
            array(
                'courseid' => $courseid,
            )
        );

        // Data validation.
        try {
            if (! ($course = $DB->get_record('course', array('id' => $params['courseid'])))) {
                $output['result'] = 'Course not found for id '.$params['courseid'];
                $output['success'] = false;
                return $output;
            }
        } catch (Exception $exc) {
            $output['result'] = $exc->getMessage();
            $output['success'] = false;
            return $output;
        }

        require_once($CFG->dirroot.'/local/rating_helper/lib.php');

        $finalrating = get_course_rating ($courseid);
        $finalreviewcount = 0;

        if($finalrating>0){
            $finalreviewcount = get_number_of_reviews ($courseid);
        }

        $output['rating'] = $finalrating;
        $output['result'] = 'Average rating '.$output['rating'];
        $output['success'] = true;
        $output['reviewcount'] = $finalreviewcount;
        return $output;
    }

    /**
     * Returndefinition for method "get_course_rating"
     *
     * @return external_description
     */
    public static function get_course_rating_returns() {
        return new external_single_structure(
            array(
                'success'   => new external_value(PARAM_BOOL, 'Return success of operation true or false'),
                'result'    => new external_value(PARAM_RAW, 'Return message'),
                'rating'    => new external_value(PARAM_RAW, 'Course rating'),
                'reviewcount'    => new external_value(PARAM, 'Course review count'),
            )
        );
    }

    /**
     * Returns a issued certificated structure
     *
     * @return external_single_structure External single structure
     */
    private static function ratings_list_structure()
    {
        return new external_single_structure(
            array(
                'id' => new external_value(PARAM_INT, 'Issue id'),
                'userid' => new external_value(PARAM_INT, 'User id'),
                'cmid' => new external_value(PARAM_INT, 'Course id'),
                'rating' => new external_value(PARAM_INT, 'Course Rating'),
                'ratingdate' => new external_value(PARAM_RAW, 'Course Rating Date'),
                'comment' => new external_value(PARAM_TEXT, 'Rating Comment'),
                'firstname' => new external_value(PARAM_TEXT, 'User first name'),
                'lastname' => new external_value(PARAM_TEXT, 'user Last Name'),
                'profilepicture' => new external_value(PARAM_RAW, 'profilepicture'),
            )
        );
    }

}

