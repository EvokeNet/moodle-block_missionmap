<?php

require_once("{$CFG->libdir}/formslib.php");

class block_mission_map_level_edit_form extends moodleform
{

    function __construct()
    {
        parent::__construct();
    }

    function definition()
    {
        $mform = &$this->_form;

        $mform->addElement('hidden', 'id', null, ['id' => 'levelid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid', null, ['id' => 'chapterid']);
        $mform->setType('chapterid', PARAM_INT);

        $mform->addElement('hidden', 'posx', null, ['id' => 'posx']);
        $mform->setType('posx', PARAM_INT);

        $mform->addElement('hidden', 'posy', null, ['id' => 'posy']);
        $mform->setType('posy', PARAM_INT);
    }
}
