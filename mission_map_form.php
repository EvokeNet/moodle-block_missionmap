<?php

require_once("{$CFG->libdir}/formslib.php");

class block_mission_map_edit_form extends moodleform
{

    function __construct($is_editing, $chaptersno, $selected_course = null)
    {
        $this->is_editing = $is_editing;
        $this->chaptersno = $chaptersno;
        $this->selected_course = $selected_course;
        parent::__construct();
    }

    function definition()
    {
        global $PAGE, $DB;

        $mform = &$this->_form;
        $courses = $DB->get_records('course');

        $courses_arr = array();
        $section_options = array();
        $course_options = array(
            0 => get_string('form_course_blank', 'block_mission_map')
        );

        foreach ($courses as $course) {
            $course_arr = array();
            $course_arr['id'] = $course->id;
            $course_arr['fullname'] = $course->fullname;
            $course_arr['sections'] = array();

            $courseformat = course_get_format($course);
            $sections = $courseformat->get_sections();
            foreach ($sections as $section) {
                $section_arr = array();
                $section_options[$section->section] = $section->name ? $section->name : $section->section;
                $section_arr['id'] = $section->id;
                $section_arr['no'] = $section->section;
                $section_arr['name'] = $section->name;
                $section_arr['is_sidequest'] = $courseformat->get_format_options($section);
                $course_arr['sections'][] = $section_arr;
            }
            $course_options[$course_arr['id']] = $course_arr['fullname'];
            $courses_arr[] = $course_arr;
        }

        $PAGE->requires->js_call_amd('block_mission_map/mission_map', 'init', array($courses_arr, $this->is_editing, $this->selected_course));

        // Form header
        $mform->addElement('header', 'config_header', get_string('form_settings', 'block_mission_map'));

        // Select field to choose course to pull sections and plot in the map
        $mform->addElement('select', 'config_course', get_string('form_course', 'block_mission_map'), $course_options, array('id' => 'campaign_select'));

        // For correct redirects
        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_INT);

        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        // Add new chapters
        $repeat_chapters = array();
        $repeat_chapters[] = &$mform->createElement('header', 'config_header', get_string('form_chapters_header', 'block_mission_map'));
        $repeat_chapters[] = &$mform->createElement('text', 'chapters', get_string('form_chapter', 'block_mission_map'));
        $repeat_chapters[] = &$mform->createElement('select', 'sections', get_string('form_missions', 'block_mission_map'), $section_options, ['data-type' => 'sections', 'multiple' => true]);
        $repeat_chapters[] = &$mform->createElement('text', 'seeds', get_string('form_seed', 'block_mission_map'));

        $repeat_chapters_options = array();
        $repeat_chapters_options['chapters']['type'] = PARAM_RAW;
        $repeat_chapters_options['seeds']['type'] = PARAM_INT;
        $repeat_chapters_options['seeds']['default'] = 95878957349875;

        if ($this->is_editing) {
            $repeatno = $this->chaptersno;
        } else {
            $repeatno = 1;
        }

        $this->repeat_elements($repeat_chapters, $repeatno, $repeat_chapters_options, 'chapter_repeats', get_string('form_add_chapter', 'block_mission_map'), 1, null, true);

        $this->add_action_buttons();
    }
}
