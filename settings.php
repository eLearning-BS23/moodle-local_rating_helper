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
 *
 * @package    local
 * @subpackage rating_helper
 * @author     Benjamin Wolf <support@eledia.de>
 * @copyright  2020 eLeDia GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;

if ($hassiteconfig) {
//    $settings = new admin_settingpage('local_rating_helper', get_string('pluginname', 'local_rating_helper'));
//    $ADMIN->add('localplugins', $settings);
    $ADMIN->add('localplugins', new admin_externalpage('local_rating_helper',get_string('pluginname','local_rating_helper'), $CFG->wwwroot.'/local/rating_helper/all_ratings.php'));

    $configs = array();

//TODO

    foreach ($configs as $config) {
        $config->plugin = 'local_rating_helper';
        $settings->add($config);
    }
}