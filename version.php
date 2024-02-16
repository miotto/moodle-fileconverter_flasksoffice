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
 * Version details
 *
 * @package   fileconverter_flasksoffice
 * @copyright 2020 Mirko Otto
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'fileconverter_flasksoffice'; // Full name of the plugin.
$plugin->release = 2024021600;
$plugin->version = 2024021600; // The current plugin version (Date: YYYYMMDDXX).
$plugin->requires = 2019052000; // Requires this Moodle version.
$plugin->maturity = MATURITY_STABLE;
$plugin->supported = [37, 403]; // A range of branch numbers of supported moodle versions.
