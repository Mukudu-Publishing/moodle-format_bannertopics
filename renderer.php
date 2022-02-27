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
 * Plugin format Renderer
 *
 * @package    format_bannertopics
 * @copyright  2021 Mukudu Publishing
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Extending the class in format renderer.
require_once($CFG->dirroot . '/course/format/renderer.php');

class format_bannertopics_renderer extends format_section_renderer_base {

    private $defaultbanner = '';
    private $bannerfiles = array();

    public function __construct(moodle_page $page, $target) {
        global $COURSE;
        parent::__construct($page, $target);

        // Define our default image only once.
        $pixpath = '/course/format/bannertopics/pix/defaultbanner.png';
        $bimage = new moodle_url($pixpath);
        $this->defaultbanner = $bimage->out(false);
        $coursectx = context_course::instance($COURSE->id);
        $contextid = $coursectx->id;

        // Get all the uploaded banners in one step.
        $fs = get_file_storage();
        $bannerfiles = $fs->get_area_files(
            $contextid,
            'format_bannertopics',
            'banners',
            false,
            null,
            false
            );
        foreach ($bannerfiles as $bannerfile) {
            $bannerimg = moodle_url::make_pluginfile_url(
                $bannerfile->get_contextid(),
                $bannerfile->get_component(),
                $bannerfile->get_filearea(),
                $bannerfile->get_itemid(),
                $bannerfile->get_filepath(),
                $bannerfile->get_filename(),
                false
                );
            $this->bannerfiles[$bannerfile->get_itemid()] = $bannerimg;
        }
    }

    private function get_sectionbanner_html($section) {
        $html = '';

        if (empty($this->bannerfiles[$section->id])) {
            $bannerimage = $this->defaultbanner;
        }else{
            $bannerimage = $this->bannerfiles[$section->id];
        }
        $html = html_writer::img($bannerimage, 'banner');
        $bannerhtml = html_writer::div($html, 'text-center');

        return $bannerhtml;
    }

    public function section_title($section, $course) {
        $cf = course_get_format($course);
        $sthtml = $cf->inplace_editable_render_section_name($section);
        $sectiontitle = $this->get_sectionbanner_html($section);
        $sectiontitle .= $this->render($sthtml);
        return $sectiontitle;
    }

    public function section_title_without_link($section, $course) {
        $cf = course_get_format($course);
        $sthtml = $cf->inplace_editable_render_section_name($section, 0);
        $sectiontitle = $this->get_sectionbanner_html($section);
        $sectiontitle .= $this->render($sthtml);
        return $sectiontitle;
    }

    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
    }

    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    protected function page_title() {
        return get_string('topicoutline');
    }

}
