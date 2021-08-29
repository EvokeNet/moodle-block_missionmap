<?php
// This file is part of the Mission Map block for Moodle - http://moodle.org/
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
 * The mform for creating a Campaign Chapter
 *
 * @package    block_mission_map
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mission_map\local\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

/**
 * The mform class for creating a chapter
 *
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class level_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customdata = null)
    {
        parent::__construct(null, $customdata, 'level',  '', ['class' => 'block_mission_map_level_form'], true, $formdata);
        $this->set_display_vertical();
    }

    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition()
    {
        global $DB, $PAGE;

        $types = [
            TYPE_URL => get_string('level_option_url', 'block_mission_map'),
            TYPE_SECTION => get_string('level_option_section', 'block_mission_map'),
            TYPE_VOTING => get_string('level_option_voting', 'block_mission_map'),
            TYPE_SUBLEVEL => get_string('level_option_sublevel', 'block_mission_map')
        ];

        $mform = $this->_form;

        // Fetches courses to filter sections if voting result redirects to a course section
        $courses = $DB->get_records('course');

        $option_courses = [0 => get_string('level_select_course', 'block_mission_map')];
        $option_sections = [0 => get_string('level_select_section', 'block_mission_map')];

        foreach ($courses as $course) {
            $course_arr = array();
            $course_arr['id'] = $course->id;
            $course_arr['fullname'] = $course->fullname;
            $course_arr['sections'] = array();

            $sections = get_fast_modinfo($course->id)->get_section_info_all();
            foreach ($sections as $section) {
                $section_arr = array();
                $section_arr['id'] = $section->id;
                $section_arr['no'] = $section->section;
                $section_arr['name'] = ($section->name == null) ? $section->section : $section->name;
                $course_arr['sections'][] = $section_arr;

                $option_sections[$section->section] = ($section->name == null) ? $section->section : $section->name;
            }
            $option_courses[$course->id] = $course->fullname;
            $courses_arr[] = $course_arr;
        }

        $PAGE->requires->js_call_amd('block_mission_map/course_selector', 'init', array($courses_arr));

        $id = !(empty($this->_customdata['id'])) ? $this->_customdata['id'] : null;
        $chapterid = !(empty($this->_customdata['chapterid'])) ? $this->_customdata['chapterid'] : null;
        $name = !(empty($this->_customdata['name'])) ? $this->_customdata['name'] : null;
        $url = !(empty($this->_customdata['url'])) ? $this->_customdata['url'] : null;

        $mform->addElement('hidden', 'id', $id);
        $mform->addElement('hidden', 'chapterid', $chapterid);

        $mform->addElement('text', 'name', get_string('campaign_add_level_name', 'block_mission_map'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('select', 'type', get_string('level_type', 'block_mission_map'), $types);
        $mform->addRule('type', get_string('required'), 'required', null, 'client');
        $mform->setType('type', PARAM_INT);

        $mform->addElement('text', 'url', get_string('campaign_add_level_url', 'block_mission_map'));
        $mform->setType('url', PARAM_RAW);

        $mform->addElement('select', 'courseid', get_string('level_course', 'block_mission_map'), $option_courses, ['data-element' => 'course_select']);
        $mform->addElement('select', 'sectionid', get_string('level_section', 'block_mission_map'), $option_sections, ['data-element' => 'course_sections']);

        $mform->hideIf('url', 'type', 'neq', TYPE_URL);
        $mform->hideIf('courseid', 'type', 'neq', TYPE_SECTION);
        $mform->hideIf('sectionid', 'type', 'neq', TYPE_SECTION);
        $mform->disabledIf('section', 'course', 'eq', 0);

        if ($name) {
            $mform->setDefault('name', $name);
        }

        if ($url) {
            $mform->setDefault('url', $url);
        }
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
