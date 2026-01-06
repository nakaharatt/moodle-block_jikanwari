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
 * Minimal timetable block for Japanese schedules: "jikanwari".
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards Takahiro NAKAHARA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_jikanwari extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_jikanwari');
    }

    public function applicable_formats() {
        return array('my' => true); // Dashboard only
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    public function get_content() {
        global $USER, $DB, $OUTPUT, $CFG;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();

        $periods = intval(get_config('block_jikanwari', 'periods')) ?: 5;
        $weekdaysconf = get_config('block_jikanwari', 'weekdays') ?: 'mon,tue,wed,thu,fri';
        $weekdays = explode(',', $weekdaysconf);

        // Load user's slots
        $slots = array();
        $descriptions = array();
        $manager = $DB->get_manager();
        if ($manager->table_exists(new xmldb_table('block_jikanwari_slots'))) {
            $records = $DB->get_records('block_jikanwari_slots', array('userid' => $USER->id));
            foreach ($records as $r) {
                $slots[$r->weekday][$r->period] = $r;
            }
        }
        // load descriptions if table exists
        if ($manager->table_exists(new xmldb_table('block_jikanwari_slots_descriptions'))) {
            $drecords = $DB->get_records('block_jikanwari_slots_descriptions', array('userid' => $USER->id));
            foreach ($drecords as $d) {
                $descriptions[$d->weekday][$d->period] = $d->description;
            }
        }

        // Build table using Moodle table API
        // Enqueue plugin CSS for better table styling
        if (!empty($this->page)) {
            $this->page->requires->css(new moodle_url('/blocks/jikanwari/styles.css'));
        }

        $table = new html_table();
        $table->attributes['class'] = 'generaltable block_jikanwari';

        // Header
        $head = array('');
        foreach ($weekdays as $wd) {
            $head[] = get_string('day_' . $wd, 'block_jikanwari');
        }
        $table->head = $head;

        // Rows
        for ($p = 1; $p <= $periods; $p++) {
            $row = array();
            $row[] = html_writer::tag('strong', $p, array('class' => 'jikanwari-period'));
            foreach ($weekdays as $widx => $wd) {
                $weekdayindex = $widx + 1; // 1..n
                $cell = '';
                if (!empty($slots[$weekdayindex][$p])) {
                    $slot = $slots[$weekdayindex][$p];
                    if ($course = $DB->get_record('course', array('id' => $slot->courseid))) {
                        // prefer user-provided custom name when present
                        $displayname = !empty($slot->customname) ? $slot->customname : $course->fullname;
                        $courselink = html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)), format_string($displayname), array('title' => format_string($course->fullname)));
                        $cell .= html_writer::tag('span', $courselink, array('class' => 'jikanwari-course'));
                    } else {
                        $cell .= html_writer::tag('span', get_string('nocourse', 'block_jikanwari'), array('class' => 'jikanwari-course'));
                    }
                } else {
                    $cell .= html_writer::tag('span', '&nbsp;', array('class' => 'jikanwari-course'));
                }

                // show description under course name, if any
                if (!empty($descriptions[$weekdayindex][$p])) {
                    $desc = $descriptions[$weekdayindex][$p];
                    $cell .= html_writer::tag('div', nl2br(s($desc)), array('class' => 'jikanwari-description'));
                }

                // Edit icon only in editing mode
                if (!empty($this->page) && !empty($this->page->user_is_editing()) && $this->page->user_is_editing()) {
                    $params = array('weekday' => $weekdayindex, 'period' => $p);
                    $url = new moodle_url('/blocks/jikanwari/edit.php', $params);
                    $icon = new pix_icon('t/edit', get_string('edit', 'block_jikanwari'), '', array('class' => 'iconsmall jikanwari-editicon'));
                    $cell .= ' ' . $OUTPUT->action_icon($url, $icon);
                }

                $row[] = $cell;
            }
            $table->data[] = $row;
        }

        // Render just the table as the block content. Reset action is available in the block edit menu.
        $this->content->text = html_writer::table($table);
        $this->content->footer = '';

        return $this->content;
    }

    /**
     * Extend parent output so we can add our custom reset action into the block's edit menu.
     *
     * @param core_renderer $output
     * @return block_contents|null
     */
    public function get_content_for_output($output) {
        $bc = parent::get_content_for_output($output);

        // Only add our reset action when the block is editable and the user is editing the page.
        if ($bc && !empty($this->page) && $this->page->user_is_editing() && $this->instance_can_be_edited()) {
            $returnurl = '';
            if (!empty($this->page->url)) {
                $returnurl = $this->page->url->out(false);
            }
            $reseturl = new moodle_url('/blocks/jikanwari/reset.php', array('sesskey' => sesskey(), 'return' => $returnurl));
            $str = new lang_string('reset_timetable', 'block_jikanwari');

            $bc->controls[] = new action_menu_link_secondary(
                $reseturl,
                new pix_icon('i/reload', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                $str,
                [
                    'class' => 'editing_reset',
                    'data-modal' => 'confirmation',
                    'data-modal-title-str' => json_encode(['reset_timetable','block_jikanwari']),
                    'data-modal-content-str' => json_encode(['reset_confirm','block_jikanwari']),
                    'data-modal-yes-button-str' => json_encode(['yes','core']),
                    'data-modal-destination' => $reseturl->out(false),
                ]
            );
        }

        return $bc;
    }
}
