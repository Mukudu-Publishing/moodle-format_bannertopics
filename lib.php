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
 * Lib file
 *
 * @package   format_bannertopics
 * @copyright 2019 - 2021 Mukudu Ltd - Bham UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('classes/bannertopics.class.php');

/**
 * Serve the files from the MYPLUGIN file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function format_bannertopics_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {

    // Leave this line out if itemid is null in make_pluginfile_url().
    $itemid = array_shift($args); // 1st item $args.

    // Extract the filepath from the $args array.
    $filename = array_pop($args); // Last item.
    if (!$args) {
        $filepath = '/'; // The $args is empty - default '/'.
    } else {
        // The $args array now has elements of the filepath.
        $filepath = '/'.implode('/', $args).'/';
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file(
            $context->id,
            'format_bannertopics',
            $filearea,
            $itemid,
            $filepath,
            $filename
        );
    if (!$file) {
        return false; // The file does not exist.
    }

    // Send the file back with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

