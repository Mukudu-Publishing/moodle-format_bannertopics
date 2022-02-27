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
 * The format_bannertopics class file.
 *
 * @package   format_bannertopics
 * @copyright 2019 - 2021 Mukudu Ltd - Bham UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot. '/course/format/lib.php');

/**
 * The class file.
 *
 * @package   format_bannertopics
 * @copyright 2019 - 2021 Mukudu Ltd - Bham UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_bannertopics extends format_base {

    /**
     * Returns true if this course format uses sections
     *
     * @return bool
     */
    public function uses_sections() {
        return true;
    }

    /**
     * Indicates whether the course format supports the creation of the Announcements forum.
     *
     * @return bool
     */
    public function supports_news() {
        return true;
    }

    /**
     * Definitions of the additional options that this course format uses for section
     *
     * @param bool $foreditform
     * @return array
     */
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
                        'accepted_types' =>
                        array('image'),
                        'return_types' => FILE_INTERNAL,
                    ),
                ),
            );
        } else {
            // Display default banner when rendering.
            $sectionformatoptions = array();
        }

        return $sectionformatoptions;
    }

    /**
     * Updates format options for a section
     *
     * @param stdClass|array $data return value array with data
     * @return bool whether there were any changes to the options values
     */
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

    /**
     * Returns the format options stored for this course or course section
     *
     * @param null|int|stdClass|section_info $section if null the course format options will be returned
     *     otherwise options for specified section will be returned. This can be either
     *     section object or relative section number (field course_sections.section)
     * @return array
     */
    public function get_format_options($section = null) {
        global $PAGE;
        $savedoptions = parent::get_format_options($section);

        // Bug somewhere that PAGE->context is not set.
        if (!$PAGE->context) {
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
