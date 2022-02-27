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
 * Renderer file.
 *
 * @package   format_bannertopics
 * @copyright 2019 - 2021 Mukudu Ltd - Bham UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/format/renderer.php');

/**
 * Renderer class.
 *
 * @package   format_bannertopics
 * @copyright 2019 - 2021 Mukudu Ltd - Bham UK
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_bannertopics_renderer extends format_section_renderer_base {

    /** @var string $defaultbanner - the default banner HTML snippet */
    private $defaultbanner = '';

    /** @var array $bannerfiles - array of the banner image files */
    private $bannerfiles = array();

    /**
     * Generate the starting container html for a list of sections
     * @return string HTML to output.
     */
    protected function start_section_list() {
        return html_writer::start_tag('ul', array('class' => 'topics'));
    }

    /**
     * Generate the closing container html for a list of sections
     * @return string HTML to output.
     */
    protected function end_section_list() {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     * @return string the page title
     */
    protected function page_title() {
        return get_string('topicoutline');
    }

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
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

    /**
     * Get the HTML snippet fir the section banner.
     *
     * @param stdClass $section section object.
     * @return string HTML snippet.
     */
    private function get_sectionbanner_html($section) {
        $html = '';

        if (empty($this->bannerfiles[$section->id])) {
            $bannerimage = $this->defaultbanner;
        } else {

            $bannerimage = $this->bannerfiles[$section->id];
        }
        $html = html_writer::img($bannerimage, 'banner');
        $bannerhtml = html_writer::div($html, 'text-center');

        return $bannerhtml;
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        $cf = course_get_format($course);
        $sthtml = $cf->inplace_editable_render_section_name($section);
        $sectiontitle = $this->get_sectionbanner_html($section);
        $sectiontitle .= $this->render($sthtml);
        return $sectiontitle;
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        $cf = course_get_format($course);
        $sthtml = $cf->inplace_editable_render_section_name($section, 0);
        $sectiontitle = $this->get_sectionbanner_html($section);
        $sectiontitle .= $this->render($sthtml);
        return $sectiontitle;
    }
}
