<?php

class block_mission_map_edit_form extends block_edit_form {
        
    protected function specific_definition($mform) {
        global $COURSE;

        $formats = [
            0 => get_string('map_format_grid', 'block_mission_map'),
            1 => get_string('map_format_row', 'block_mission_map'),
        ];

        $mform->addElement('header', 'config_map_format_header', get_string('map_format_header', 'block_mission_map'));

        $mform->addElement('select', 'config_map_format', get_string('map_format', 'block_mission_map'), $formats);
        $mform->setType('config_map_format', PARAM_INT);

        $mform->addElement('selectyesno', 'config_map_format_label', get_string('map_format_label', 'block_mission_map'));
        $mform->setType('config_map_format_label', PARAM_BOOL);

        $mform->addElement('header', 'config_map_backgrounds_header', get_string('map_backgrounds_header', 'block_mission_map'));
        $manageurl = new moodle_url('/blocks/mission_map/backgrounds.php', [
            'blockid' => $this->block->instance->id,
            'courseid' => $COURSE->id
        ]);
        $link = html_writer::link($manageurl, get_string('map_backgrounds_manage', 'block_mission_map'));
        $mform->addElement('static', 'config_map_backgrounds_link', '', $link);

        $mform->addElement('header', 'config_mission_style_header', get_string('mission_style_header', 'block_mission_map'));
        $styles = [
            'dot' => get_string('mission_style_dot', 'block_mission_map'),
            'speech' => get_string('mission_style_speech', 'block_mission_map'),
        ];
        $mform->addElement('select', 'config_marker_style', get_string('mission_style', 'block_mission_map'), $styles);
        $mform->setType('config_marker_style', PARAM_TEXT);
    }
}
