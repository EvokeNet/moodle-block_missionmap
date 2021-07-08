<?php

function block_mission_map_output_fragment_chapter_form($args)
{
    $args = (object) $args;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    $mform = new \block_mission_map\local\forms\chapter_form(
        $formdata,
        [
            'id' => $serialiseddata->id,
            'name' => $serialiseddata->name
        ]
    );

    if (!empty($args->jsonformdata)) {
        // If we were passed non-empty form data we want the mform to call validation functions and show errors.
        $mform->is_validated();
    }

    ob_start();
    $mform->display();
    $o .= ob_get_contents();
    ob_end_clean();

    return $o;
}
