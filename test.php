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
 * Test that flask rest server is configured correctly
 *
 * @package   fileconverter_flasksoffice
 * @copyright 2020 Mirko Otto
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

$sendpdf = optional_param('sendpdf', 0, PARAM_BOOL);

$PAGE->set_url(new moodle_url('/files/converter/flasksoffice/test.php'));
$PAGE->set_context(context_system::instance());

require_login();
require_capability('moodle/site:config', context_system::instance());

$strheading = get_string('test_conversion', 'fileconverter_flasksoffice');
$PAGE->navbar->add(get_string('administrationsite'));
$PAGE->navbar->add(get_string('plugins', 'admin'));
$PAGE->navbar->add(get_string('pluginname', 'fileconverter_flasksoffice'),
        new moodle_url('/admin/settings.php', array('section' => 'fileconverterflasksoffice')));
$PAGE->navbar->add($strheading);
$PAGE->set_heading($strheading);
$PAGE->set_title($strheading);

$converter = new \fileconverter_flasksoffice\converter();

if ($sendpdf) {
    require_sesskey();

    $converter->serve_test_document();
    die();
}

$result = $converter->are_requirements_met();
if ($result) {
    $msg = $OUTPUT->notification(get_string('test_conversionready', 'fileconverter_flasksoffice'), 'success');
    $pdflink = new moodle_url($PAGE->url, array('sendpdf' => 1, 'sesskey' => sesskey()));
    $msg .= html_writer::link($pdflink, get_string('test_conversion', 'fileconverter_flasksoffice'));
    $msg .= html_writer::empty_tag('br');
} else {

    // Diagnostics time.
    $msg = '';

    if (empty($msg)) {
        $msg = $OUTPUT->notification(get_string('test_conversionnotready', 'fileconverter_flasksoffice'), 'warning');
    }
}
$returl = new moodle_url('/admin/settings.php', array('section' => 'fileconverterflasksoffice'));
$msg .= $OUTPUT->continue_button($returl);

echo $OUTPUT->header();
echo $OUTPUT->box($msg, 'generalbox');
echo $OUTPUT->footer();
