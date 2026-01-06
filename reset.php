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
 * Reset handler for jikanwari block — clears user's stored slots and descriptions.
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_login();
require_sesskey();

global $USER, $DB, $CFG;

// Only allow resetting own data. Use sesskey for CSRF protection above.

$return = optional_param('return', '', PARAM_URL);

// Delete user's slots and descriptions.
if ($DB->get_manager()->table_exists(new xmldb_table('block_jikanwari_slots'))) {
    $DB->delete_records('block_jikanwari_slots', array('userid' => $USER->id));
}
if ($DB->get_manager()->table_exists(new xmldb_table('block_jikanwari_slots_descriptions'))) {
    $DB->delete_records('block_jikanwari_slots_descriptions', array('userid' => $USER->id));
}

// Redirect back to provided URL or to the site home if none.
if (!empty($return)) {
    redirect(new moodle_url($return));
} else {
    redirect($CFG->wwwroot);
}
