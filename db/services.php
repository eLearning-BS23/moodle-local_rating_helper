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
 * Function definition for the webservicesuite functions.
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Brain Station 23
 * @copyright  2021 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'user_has_rated' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'user_has_rated',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Check if the user has rated already.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
    'save_rating' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'save_rating',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Save a rating for a user.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
    'get_cm_rating' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'get_cm_rating',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Get the rating for one course module.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
    'get_course_rating' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'get_course_rating',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Get the rating for one course.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
    'get_indivisual_rating' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'get_indivisual_rating',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Get the rating for one course module.',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
    'get_all_ratings' => array(
        'classname' => 'rating_helper_services',
        'methodname' => 'get_all_ratings',
        'classpath' => 'local/rating_helper/externallib.php',
        'description' => 'Get all ratings and comments for one course module.',
        'loginrequired' => false,
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/rating_helper:access',
    ),
);
