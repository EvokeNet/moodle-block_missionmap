<?php

require_once("{$CFG->libdir}/formslib.php");

class block_mission_map_edit_form extends moodleform
{

    function definition()
    {
        global $PAGE;

        $mform = &$this->_form;

        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $topcategory = core_course_category::top();
        $categories = $topcategory->get_children();
        $category = array_shift($categories);
        $category_courses = $category->get_courses();

        $courses = array();
        $options = array(
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
                $section['id'] = $course_section->id;
                $section['no'] = $course_section->section;
                $section['name'] = $course_section->name;
                $course['sections'][] = $section;
            }
            $options[$course['id']] = $course['fullname'];
            $courses[] = $course;
        }

        $PAGE->requires->js_call_amd('block_mission_map/mission_map', 'init', array($courses));

        // Form header
        $mform->addElement('header', 'config_header', get_string('form_settings', 'block_mission_map'));

        // Select field to choose course to pull sections and plot in the map
        $mform->addElement('select', 'config_course', get_string('form_course', 'block_mission_map'), $options, ['id' => 'campaign_select']);

        // Text field to add seed
        $mform->addElement('text', 'config_seed', get_string('form_seed', 'block_mission_map'));
        $mform->setType('config_seed', PARAM_INT);
        $mform->setDefault('config_seed', 95878957349875);

        // Add new chapters
        $repeat_chapters = array();
        $repeat_chapters[] = &$mform->createElement('header', 'config_header', get_string('form_chapters_header', 'block_mission_map'));
        $repeat_chapters[] = &$mform->createElement('text', 'chapter', get_string('form_chapter', 'block_mission_map'));
        $repeat_chapters[] = &$mform->createElement('select', 'missions', get_string('form_missions', 'block_mission_map'), null, ['data-type' => 'sections', 'multiple' => true]);

        $repeat_chapters_options = array();
        $repeat_chapters_options['chapter']['type'] = PARAM_RAW;
        $repeat_chapters_options['missions']['multiple'] = true;

        $this->repeat_elements($repeat_chapters, 1, $repeat_chapters_options, 'chapter_repeats', 'form_add_chapter', 1, null, true);

        $this->add_action_buttons();
    }
}
