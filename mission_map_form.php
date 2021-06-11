<?php

require_once("{$CFG->libdir}/formslib.php");

class block_mission_map_edit_form extends moodleform
{

    function __construct($is_editing, $chaptersno)
    {
        $this->is_editing = $is_editing;
        $this->chaptersno = $chaptersno;
        parent::__construct();
    }

    function definition()
    {
        global $PAGE, $DB;

        $mform = &$this->_form;

        $topcategory = core_course_category::top();
        $categories = $topcategory->get_children();
        $category = array_shift($categories);
        $category_courses = $category->get_courses();

        $courses = array();
        $course_options = array(
            0 => get_string('form_course_blank', 'block_mission_map')
        );
        foreach ($category_courses as $category_course) {
            $course = array();
            $course['id'] = $category_course->id;
            $course['fullname'] = $category_course->fullname;
            $course['sections'] = array();

            $course_sections = get_fast_modinfo($course['id'])->get_section_info_all();
            foreach ($course_sections as $course_section) {
                $section = array();
                $section_options[$course_section->id] = $course_section->name ? $course_section->name : $course_section->id;
                $section['id'] = $course_section->id;
                $section['no'] = $course_section->section;
                $section['name'] = $course_section->name;
                $course['sections'][] = $section;
            }
            $course_options[$course['id']] = $course['fullname'];
            $courses[] = $course;
        }

        $PAGE->requires->js_call_amd('block_mission_map/mission_map', 'init', array($courses, $this->is_editing));

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
        // $repeat_chapters[] = &$mform->createElement('hidden', 'blockid');
        // $repeat_chapters[] = &$mform->createElement('hidden', 'courseid');

        $repeat_chapters_options = array();
        $repeat_chapters_options['chapters']['type'] = PARAM_RAW;
        $repeat_chapters_options['seeds']['type'] = PARAM_INT;
        // $repeat_chapters_options['blockid']['type'] = PARAM_INT;
        // $repeat_chapters_options['courseid']['type'] = PARAM_INT;
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
