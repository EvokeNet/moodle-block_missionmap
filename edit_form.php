<?php

class block_mission_map_edit_form extends block_edit_form {
        
    protected function specific_definition($mform) {

        $formats = [
            0 => get_string('map_format_grid', 'block_mission_map'),
            1 => get_string('map_format_row', 'block_mission_map'),
        ];

        $mform->addElement('header', 'map_format_header', get_string('map_format_header', 'block_mission_map'));

        $mform->addElement('select', 'map_format', get_string('map_format', 'block_mission_map'), $formats);
        $mform->setType('map_format', PARAM_INT);
    }
}