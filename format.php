<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Course format
 *
 * @package    format_bannertopics
 * @copyright  2021 Mukudu Publishing
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Retrieve course format option fields ...
// ... and add them to the $course object.
$course = course_get_format($course)->get_course();

// HACK – add coursedisplay to the course options.
$course->coursedisplay = COURSE_DISPLAY_SINGLEPAGE;

$renderer = $PAGE->get_renderer('format_bannertopics');

$renderer->print_multiple_section_page($course, null, null, null, null);

