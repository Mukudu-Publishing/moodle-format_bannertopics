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
 * @package    format_bannertopics
 * @copyright  2021 Mukudu Publishing
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/format/lib.php');

// Class Definition PHP Docs here //
class format_bannertopics extends format_base {

    public function uses_sections() {
        return true;
    }

    public function supports_news() {
        return true;
    }

    public function section_format_options($foreditform = false) {

        // Are we editing the form?
        if ($foreditform && !isset($sectionformatoptions['sectionbanner']['label'])) {
            $label = new lang_string('sectionnbannerprompt', 'format_bannertopics');
            $sectionformatoptions = array(
                'sectionbanner' => array(
                    'default' => '',
                    'type' => PARAM_FILE,
                    'label' => $label,
                    'element_type' => 'filemanager',
                    'element_attributes' => array(
                        'maxfiles' => 1,
                        'accepted_types' => array('image'),
                        'return_types'=> FILE_INTERNAL,
                    ),
                ),
            );
        }else{
            // Display default banner when rendering.
            $sectionformatoptions = array();
        }

        return $sectionformatoptions;
    }

    public function update_section_format_options($data) {
        $courseid = $this->courseid;
        $ctxtid = context_course::instance($courseid)->id;
        file_save_draft_area_files(
            $data['sectionbanner'],
            $ctxtid,
            'format_bannertopics',
            'banners',
            $data['id'],
            array('subdirs' => 0)
            );
        return parent::update_section_format_options($data);
    }
    
    public function get_format_options($section = null) {
        global $PAGE;
        $savedoptions = parent::get_format_options($section);
        
        // Bug somewhere that PAGE->context is not set.
        If (empty($PAGE->context)) {
            $PAGE->set_context(context_course::instance($this->courseid));
        }
        
        if ($PAGE->user_is_editing() && $section !== null) {
            $draftitemid = 0;
            $ctxid = context_course::instance($this->courseid)->id;
            $itemid = required_param('id', PARAM_INT);
            
            file_prepare_draft_area(
                $draftitemid,
                $ctxid,
                'format_bannertopics',
                'banners',
                $itemid,
                array('subdirs' => 0)
                );
            
            $savedoptions['sectionbanner'] = $draftitemid;
        }
        
        return $savedoptions;
    }

}

function format_bannertopics_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    
    // Leave this line out if itemid is null ...
    // ... in make_pluginfile_url().
    $itemid = array_shift($args); // 1st item $args.
    
    // Extract the filepath from the $args array.
    $filename = array_pop($args); // Last item.
    if (!$args) {
        $filepath = '/'; // $args empty default '/'.
    } else {
        // $args now has elements of the filepath.
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
    
    // Send the file back with a cache lifetime ...
    // ... of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}
