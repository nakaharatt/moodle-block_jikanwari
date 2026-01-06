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
 * Install script for block_jikanwari.
 *
 * @package    block_jikanwari
 * @copyright  2025 onwards Takahiro NAKAHARA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Runs after the plugin is installed to set default config values.
 */
function xmldb_block_jikanwari_install() {
    // Only set defaults if not already configured.
    if (get_config('block_jikanwari', 'periods') === false) {
        set_config('periods', 5, 'block_jikanwari');
    }
    if (get_config('block_jikanwari', 'weekdays') === false) {
        set_config('weekdays', 'mon,tue,wed,thu,fri', 'block_jikanwari');
    }
}
