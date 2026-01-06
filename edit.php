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
 * Slot edit page
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards Takahiro NAKAHARA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_login();
global $DB, $OUTPUT, $PAGE, $CFG, $USER;

$weekday = required_param('weekday', PARAM_INT);
$period = required_param('period', PARAM_INT);

$userid = $USER->id;

$returnurl = new moodle_url('/my/');

// basic page setup (do not output header yet; output after processing to avoid headers-sent before redirect)
$PAGE->set_url(new moodle_url('/blocks/jikanwari/edit.php', array('weekday' => $weekday, 'period' => $period)));
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'block_jikanwari'));

// Use namespaced moodleform class (autoloader will load from classes/)
// courses: try enrol_get_users_courses
$courses = array();
if (function_exists('enrol_get_users_courses')) {
    $courses = enrol_get_users_courses($userid, true);
}
if (empty($courses)) {
    // fallback: all visible courses
    $courses = $DB->get_records_select('course', 'visible = ?', array(1), 'fullname');
}

$formclass = '\\block_jikanwari\\output\\edit_form';
$mform = new $formclass(null, array('weekday' => $weekday, 'period' => $period, 'courses' => $courses));

if ($mform->is_cancelled()) {
    redirect($returnurl);
} else if ($data = $mform->get_data()) {
    // validate sesskey
    if (!confirm_sesskey()) {
        print_error('invalidsesskey', 'error');
    }

    $courseid = intval($data->courseid);
    $customname = isset($data->customname) ? trim($data->customname) : '';

    $existing = $DB->get_record('block_jikanwari_slots', array('userid' => $userid, 'weekday' => $weekday, 'period' => $period));
    if ($courseid) {
        if ($existing) {
            $existing->courseid = $courseid;
            $existing->customname = $customname;
            $existing->timemodified = time();
            $DB->update_record('block_jikanwari_slots', $existing);
        } else {
            $rec = new stdClass();
            $rec->userid = $userid;
            $rec->weekday = $weekday;
            $rec->period = $period;
            $rec->courseid = $courseid;
            $rec->customname = $customname;
            $rec->timemodified = time();
            $DB->insert_record('block_jikanwari_slots', $rec);
        }
    } else {
        if ($existing) {
            // remove the slot record, but do not automatically remove the description here;
            // description handling runs separately so users can save descriptions alone.
            $DB->delete_records('block_jikanwari_slots', array('id' => $existing->id));
        }
    }

    // handle description (allow saving description even if no slot exists)
    $desc = isset($data->description) ? trim($data->description) : '';
    $existingdesc = $DB->get_record('block_jikanwari_slots_descriptions', array('userid' => $userid, 'weekday' => $weekday, 'period' => $period));
    if ($desc !== '') {
        if ($existingdesc) {
            $existingdesc->description = $desc;
            $existingdesc->timemodified = time();
            $DB->update_record('block_jikanwari_slots_descriptions', $existingdesc);
        } else {
            $drec = new stdClass();
            $drec->userid = $userid;
            $drec->weekday = $weekday;
            $drec->period = $period;
            $drec->description = $desc;
            $drec->timemodified = time();
            $DB->insert_record('block_jikanwari_slots_descriptions', $drec);
        }
    } else {
        if ($existingdesc) {
            $DB->delete_records('block_jikanwari_slots_descriptions', array('id' => $existingdesc->id));
        }
    }

    redirect($returnurl);
} else {
    // set current values into form
    $existing = $DB->get_record('block_jikanwari_slots', array('userid' => $userid, 'weekday' => $weekday, 'period' => $period));
    $toform = new stdClass();
    $toform->weekday = $weekday;
    $toform->period = $period;
    $toform->courseid = $existing ? $existing->courseid : 0;
    $toform->customname = $existing ? (isset($existing->customname) ? $existing->customname : '') : '';
    // load existing description
    $existingdesc = $DB->get_record('block_jikanwari_slots_descriptions', array('userid' => $userid, 'weekday' => $weekday, 'period' => $period));
    $toform->description = $existingdesc ? $existingdesc->description : '';
    $mform->set_data($toform);

    // Now safe to output header and display the form
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('edit_slot', 'block_jikanwari'));
    $mform->display();

    // footer printed after form display
    echo $OUTPUT->footer();
}
