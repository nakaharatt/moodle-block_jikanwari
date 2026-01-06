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
 * Slot edit form
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards Takahiro NAKAHARA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_jikanwari\output;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/formslib.php');

class edit_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $custom = (array)$this->_customdata;
        $weekday = isset($custom['weekday']) ? $custom['weekday'] : 0;
        $period = isset($custom['period']) ? $custom['period'] : 0;
        $courses = isset($custom['courses']) ? $custom['courses'] : array();

        $options = array(0 => get_string('none', 'block_jikanwari'));
        foreach ($courses as $c) {
            $options[$c->id] = format_string($c->fullname);
        }

        $mform->addElement('hidden', 'weekday', $weekday);
        $mform->setType('weekday', PARAM_INT);
        $mform->addElement('hidden', 'period', $period);
        $mform->setType('period', PARAM_INT);

        $mform->addElement('select', 'courseid', get_string('select_course', 'block_jikanwari'), $options);
        $mform->setType('courseid', PARAM_INT);

        // custom display name for the course in this slot (plain text)
        $mform->addElement('text', 'customname', get_string('customname', 'block_jikanwari'), array('size' => 50));
        $mform->setType('customname', PARAM_TEXT);

        // description textarea (plain text only)
        $mform->addElement('textarea', 'description', get_string('slot_description', 'block_jikanwari'), 'wrap="virtual" rows="3" cols="50"');
        $mform->setType('description', PARAM_TEXT);

        // sesskey hidden field
        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_ALPHANUMEXT);

        $this->add_action_buttons(true, get_string('savechanges', 'block_jikanwari'));
    }
}
