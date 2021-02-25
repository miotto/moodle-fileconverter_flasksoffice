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
 * Link to Flask Webserver.
 *
 * @package   fileconverter_flasksoffice
 * @copyright 2020 Mirko Otto
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings->add(new admin_setting_configtext('fileconverter_flasksoffice/flasksofficeurl',
        get_string('settings:flasksofficeurl', 'fileconverter_flasksoffice'),
        get_string('settings:flasksofficeurl_help', 'fileconverter_flasksoffice'),
        ''));

    $url = new moodle_url('/files/converter/flasksoffice/test.php');
    $link = html_writer::link($url, get_string('test_converter', 'fileconverter_flasksoffice'));
    $settings->add(new admin_setting_heading('test_converter', '', $link));
}
