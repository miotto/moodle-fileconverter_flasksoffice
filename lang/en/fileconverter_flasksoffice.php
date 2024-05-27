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
 * Strings for plugin 'fileconverter_flasksoffice'
 *
 * @package   fileconverter_flasksoffice
 * @copyright 2020 Mirko Otto
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['disabled'] = 'Disabled';
$string['event:document_conversion'] = 'Document conversion';
$string['pluginname'] = 'Flask soffice';

$string['preparesubmissionsforannotation'] = 'Prepare submissions for annotation';
$string['privacy:metadata:fileconverter_flasksoffice:externalpurpose'] = 'This information is sent to Flask rest server in order the file to be converted to an alternative format. The file is temporarily kept on Flask rest server and gets deleted after the conversion is done.';
$string['privacy:metadata:fileconverter_flasksoffice:filecontent'] = 'The content of the file.';
$string['privacy:metadata:fileconverter_flasksoffice:filemimetype'] = 'The MIME type of the file.';
$string['privacy:metadata:fileconverter_flasksoffice:params'] = 'The query parameters passed to Flask rest server.';
$string['settings:flasksofficeurl'] = 'Document Server URL';
$string['settings:flasksofficeurl_help'] = 'Specify the URL at which document server can be reached *by Moodle*. The URL is never resolved in the browser, only in CURL requests by Moodle, so it will be resolved only in the local network.';
$string['test_conversion'] = 'Test document conversion';
$string['test_conversionnotready'] = 'This document converter is not configured properly.';
$string['test_conversionready'] = 'This document converter is configured properly.';
$string['test_converter'] = 'Test this converter is working properly.';
