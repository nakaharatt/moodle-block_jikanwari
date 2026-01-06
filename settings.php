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
 * Admin settings for jikanwari block
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards Takahiro NAKAHARA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    $defaultperiods = get_config('block_jikanwari', 'periods') ? get_config('block_jikanwari', 'periods') : 5;
    $settings->add(new admin_setting_configtext('block_jikanwari/periods',
        get_string('setting_periods', 'block_jikanwari'),
        get_string('setting_periods_desc', 'block_jikanwari'),
        $defaultperiods, PARAM_INT));

    $defaultweekdays = get_config('block_jikanwari', 'weekdays') ? get_config('block_jikanwari', 'weekdays') : 'mon,tue,wed,thu,fri';
    $settings->add(new admin_setting_configtext('block_jikanwari/weekdays',
        get_string('setting_weekdays', 'block_jikanwari'),
        get_string('setting_weekdays_desc', 'block_jikanwari'),
        $defaultweekdays, PARAM_TEXT));

}
