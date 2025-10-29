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
require_once('lib.php');

/**
 * The mform class for creating a chapter
 *
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class voting_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($customdata = null)
    {
        parent::__construct(null, $customdata, 'chapter',  '', ['class' => 'block_mission_map_voting_form'], true);
    }

    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition()
    {
        global $PAGE, $DB, $COURSE;

        $mform = $this->_form;
        $context = \context_course::instance($COURSE->id);

        $id = !empty($this->_customdata['id']) ? $this->_customdata['id'] : null;
        $chapterid = !empty($this->_customdata['chapterid']) ? $this->_customdata['chapterid'] : null;
        $levelid = !empty($this->_customdata['levelid']) ? $this->_customdata['levelid'] : null;
        $description = !empty($this->_customdata['description']) ? $this->_customdata['description'] : null;
        $options = !empty($this->_customdata['options']) ? $this->_customdata['options'] : null;

        $types = [
            // BLOCK_MISSIONMAP_VOTINGTYPE_ALL => get_string('voting_type_all', 'block_mission_map'),
            BLOCK_MISSIONMAP_VOTINGTYPE_GROUPS => get_string('voting_type_groups', 'block_mission_map')
        ];

        $algorithms = [
            BLOCK_MISSIONMAP_VOTINGAL_MAJORITY => get_string('voting_al_simplemajority', 'block_mission_map'),
            // BLOCK_MISSIONMAP_VOTINGAL_TIMEBOUND => get_string('voting_al_timebound', 'block_mission_map'),
            BLOCK_MISSIONMAP_VOTINGAL_THRESHOLD => get_string('voting_al_threshold', 'block_mission_map'),
            // BLOCK_MISSIONMAP_VOTINGAL_RANDOM => get_string('voting_al_random', 'block_mission_map')
        ];

        $tiebreakers = [
            BLOCK_MISSIONMAP_TIEBREAKER_RANDOM => get_string('voting_tiebreak_random', 'block_mission_map'),
            // BLOCK_MISSIONMAP_TIEBREAKER_SECONDROUND => get_string('voting_tiebreak_secondround', 'block_mission_map'),
            // BLOCK_MISSIONMAP_TIEBREAKER_MINERVA => get_string('voting_tiebreak_minerva', 'block_mission_map')
        ];

        $thresholds = [
            BLOCK_MISSIONMAP_THRESHOLD_25 => '25%',
            BLOCK_MISSIONMAP_THRESHOLD_50 => '50%',
            BLOCK_MISSIONMAP_THRESHOLD_75 => '75%',
            BLOCK_MISSIONMAP_THRESHOLD_90 => '90%'
        ];

        $option_types = [
            BLOCK_MISSIONMAP_OPTION_URL => get_string('voting_option_url', 'block_mission_map'),
            BLOCK_MISSIONMAP_OPTION_SECTION => get_string('voting_option_section', 'block_mission_map'),
            BLOCK_MISSIONMAP_OPTION_SUBLEVEL => get_string('voting_option_sublevel', 'block_mission_map')
        ];

        // Fetches courses to filter sections if voting result redirects to a course section
        $courses = $DB->get_records('course');

        $option_courses = [0 => get_string('voting_select_course', 'block_mission_map')];
        $option_sections = [0 => get_string('voting_select_section', 'block_mission_map')];

        foreach ($courses as $course) {
            $course_arr = array();
            $course_arr['id'] = $course->id;
            $course_arr['fullname'] = $course->fullname;
            $course_arr['sections'] = array();

            $courseformat = course_get_format($course);
            $sections = $courseformat->get_sections();
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

        $PAGE->requires->js_call_amd('block_mission_map/voting', 'init', array($courses_arr));

        $mform->addElement('hidden', 'id', $id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid', $chapterid);
        $mform->setType('chapterid', PARAM_INT);

        $mform->addElement('hidden', 'levelid', $levelid);
        $mform->setType('levelid', PARAM_INT);

        $mform->addElement('select', 'voting_type', get_string('voting_type', 'block_mission_map'), $types);
        $mform->addRule('voting_type', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('voting_type', 'voting_type', 'block_mission_map');
        $mform->setType('voting_type', PARAM_INT);

        $mform->addElement('select', 'algorithm', get_string('voting_algorithm', 'block_mission_map'), $algorithms);
        $mform->addRule('algorithm', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('algorithm', 'voting_algorithm', 'block_mission_map');
        $mform->setType('algorithm', PARAM_INT);

        $mform->addElement('date_time_selector', 'deadline', get_string('voting_deadline', 'block_mission_map'));
        $mform->hideIf('deadline', 'algorithm', 'neq', BLOCK_MISSIONMAP_VOTINGAL_TIMEBOUND);

        $mform->addElement('select', 'threshold', get_string('voting_threshold', 'block_mission_map'), $thresholds);
        $mform->hideIf('threshold', 'algorithm', 'neq', BLOCK_MISSIONMAP_VOTINGAL_THRESHOLD);

        $mform->addElement('select', 'tiebreak', get_string('voting_tiebreak', 'block_mission_map'), $tiebreakers);
        $mform->addRule('tiebreak', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('tiebreak', 'voting_tiebreak', 'block_mission_map');
        $mform->setType('tiebreak', PARAM_INT);

        // $mform->addElement('text', 'minerva', get_string('voting_minerva', 'block_mission_map'));
        // $mform->addElement('autocomplete', 'minverva', get_string('voting_minerva', 'block_mission_map'), $users);
        // $mform->addRule('minerva', get_string('required'), 'required', null, 'client');
        // $mform->hideIf('minerva', 'tiebreak', 'neq', BLOCK_MISSIONMAP_TIEBREAKER_MINERVA);
        // $mform->setType('minerva', PARAM_INT);

        $textfieldoptions = array('trusttext'=>true, 'subdirs'=>true, 'maxfiles'=> 1, 'maxbytes' => 5000000, 'context' => $context);

        $mform->addElement('date_time_selector', 'tiebreaker_deadline', get_string('voting_tiebreak_deadline', 'block_mission_map'));
        $mform->hideIf('tiebreaker_deadline', 'tiebreak', 'eq', BLOCK_MISSIONMAP_TIEBREAKER_RANDOM);

        $mform->addElement('text', 'description', get_string('voting_description', 'block_mission_map'), ['size' => 50]);
        $mform->setType('description', PARAM_TEXT);

        $repeatarray = array();
        $repeatarray[] = $mform->createElement('header', 'option_title', get_string('voting_option_title', 'block_mission_map'));
        $repeatarray[] = $mform->createElement('hidden', 'option_id', 0);
        $repeatarray[] = $mform->createElement('text', 'option_name', get_string('voting_option_name', 'block_mission_map'), ['size' => 50]);
        $repeatarray[] = $mform->createElement('editor', 'option_description', get_string('voting_option_description', 'block_mission_map'), null, $textfieldoptions);
        $repeatarray[] = $mform->createElement('select', 'option_type', get_string('voting_option_type', 'block_mission_map'), $option_types);
        $repeatarray[] = $mform->createElement('text', 'option_url', get_string('voting_option_url', 'block_mission_map'));
        $repeatarray[] = $mform->createElement('select', 'option_course', get_string('voting_option_course', 'block_mission_map'), $option_courses, ['data-element' => 'voting_course_select']);
        $repeatarray[] = $mform->createElement('select', 'option_section', get_string('voting_option_section', 'block_mission_map'), $option_sections, ['data-element' => 'voting_course_sections']);

        $repeateloptions = array();
        $repeateloptions['option_id']['type'] = PARAM_INT;
        $repeateloptions['option_name']['type'] = PARAM_TEXT;
        $repeateloptions['option_name']['rule'] = 'required';
        $repeateloptions['option_description']['type'] = PARAM_RAW;
        $repeateloptions['option_url']['type'] = PARAM_RAW;
        $repeateloptions['option_url']['hideif'] = array('option_type', 'neq', BLOCK_MISSIONMAP_OPTION_URL);
        $repeateloptions['option_course']['hideif'] = array('option_type', 'neq', BLOCK_MISSIONMAP_OPTION_SECTION);
        $repeateloptions['option_section']['hideif'] = array('option_type', 'neq', BLOCK_MISSIONMAP_OPTION_SECTION);
        $repeateloptions['option_section']['disabledif'] = array('option_course', 'eq', 0);

        $mform->setType('option', PARAM_CLEANHTML);

        $repeatno = count($options) > 0 ? count($options) : 1;

        $this->repeat_elements($repeatarray, $repeatno, $repeateloptions, 'option_repeats', 'option_add_fields', 1, null, false);

        $this->add_action_buttons(true, get_string('voting_save', 'block_mission_map'));

        if ($description) {
            $mform->setDefault('description', $description);
        }

        if ($options) {
            $i=0;
            foreach ($options as $option) {
                $mform->setDefault('option_id['.$i.']', $option['id']);
                $mform->setDefault('option_name['.$i.']', $option['name']);
                $mform->setDefault('option_description['.$i.']', array('text'=> $option['description']));
                $mform->setDefault('option_type['.$i.']', $option['type']);
                $mform->setDefault('option_url['.$i.']', $option['url']);
                $mform->setDefault('option_course['.$i.']', $option['course']);
                $mform->setDefault('option_section['.$i.']', $option['section']);
                $i++;
            }
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
