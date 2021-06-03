<?php

class block_mission_map_edit_form extends block_edit_form
{

    protected function specific_definition($mform)
    {
        $topcategory = core_course_category::top();
        $categories = $topcategory->get_children();
        $category = array_shift($categories);
        $category_courses = $category->get_courses();

        foreach ($category_courses as $course) {
            $courses[$course->id] = $course->fullname;
        }

        $mform->addElement('header', 'config_header', get_string('blocksettings', 'block'));

        // Select field to choose course to pull sections and plot in the map
        $mform->addElement('select', 'config_course', get_string('form_course', 'block_mission_map'), $courses);

        // Text field to add seed
        $mform->addElement('text', 'config_seed', get_string('form_seed', 'block_mission_map'));
        $mform->setType('config_seed', PARAM_INT);
        $mform->setDefault('config_seed', 95878957349875);
    }
}
