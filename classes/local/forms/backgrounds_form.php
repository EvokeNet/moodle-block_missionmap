<?php

namespace block_mission_map\local\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

class backgrounds_form extends \moodleform {
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'backgrounds_header', get_string('map_backgrounds_header', 'block_mission_map'));

        $mform->addElement('hidden', 'blockid');
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'courseid');
        $mform->setType('courseid', PARAM_INT);

        $fileoptions = [
            'maxfiles' => 1,
            'subdirs' => 0,
            'accepted_types' => ['image']
        ];

        $mform->addElement('filemanager', 'map_bg_1', get_string('map_background', 'block_mission_map', 1), null, $fileoptions);
        $mform->addElement('filemanager', 'map_bg_2', get_string('map_background', 'block_mission_map', 2), null, $fileoptions);
        $mform->addElement('filemanager', 'map_bg_3', get_string('map_background', 'block_mission_map', 3), null, $fileoptions);
        $mform->addElement('filemanager', 'map_bg_4', get_string('map_background', 'block_mission_map', 4), null, $fileoptions);

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}
