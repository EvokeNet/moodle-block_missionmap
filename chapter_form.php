<?php

require_once("{$CFG->libdir}/formslib.php");

class block_mission_map_chapter_form extends moodleform
{

    function __construct($formdata, $customdata = null)
    {
        parent::__construct(null, $customdata, 'chapter',  '', ['class' => 'block_mission_map_chapter_form'], true, $formdata);
        $this->set_display_vertical();
    }

    function definition()
    {
        $mform = &$this->_form;
        $mform->addElement('text', 'chapter_name', get_string('form_chapter', 'block_mission_map'));
        $mform->addRule('chapter_name', get_string('required'), 'required', null, 'client');
        $mform->setType('chapter_name', PARAM_TEXT);
    }
}
