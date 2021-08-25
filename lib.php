<?php

// Voting session types
define('BLOCK_MISSIONMAP_VOTINGTYPE_ALL', 1);
define('BLOCK_MISSIONMAP_VOTINGTYPE_GROUPS', 2);

// Voting algorithms
define('BLOCK_MISSIONMAP_VOTINGAL_MAJORITY', 1);
define('BLOCK_MISSIONMAP_VOTINGAL_TIMEBOUND', 2);
define('BLOCK_MISSIONMAP_VOTINGAL_THRESHOLD', 3);
define('BLOCK_MISSIONMAP_VOTINGAL_RANDOM', 4);

// Voting tiebreaker types
define('BLOCK_MISSIONMAP_TIEBREAKER_RANDOM', 1);
define('BLOCK_MISSIONMAP_TIEBREAKER_SECONDROUND', 2);
define('BLOCK_MISSIONMAP_TIEBREAKER_MINERVA', 3);

// Voting thresholds
define('BLOCK_MISSIONMAP_THRESHOLD_25', 1);
define('BLOCK_MISSIONMAP_THRESHOLD_50', 2);
define('BLOCK_MISSIONMAP_THRESHOLD_75', 3);
define('BLOCK_MISSIONMAP_THRESHOLD_90', 4);

// Voting result options
define('BLOCK_MISSIONMAP_OPTION_URL', 1);
define('BLOCK_MISSIONMAP_OPTION_SECTION', 2);
define('BLOCK_MISSIONMAP_OPTION_SUBLEVEL', 3);

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
            'name' => $serialiseddata->name,
            'blockid' => $serialiseddata->blockid,
            'courseid' => $serialiseddata->courseid
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

function block_mission_map_output_fragment_level_form($args)
{
    $args = (object) $args;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    $mform = new \block_mission_map\local\forms\level_form(
        $formdata,
        [
            'id' => $serialiseddata->id,
            'chapterid' => $serialiseddata->chapterid,
            'name' => $serialiseddata->name,
            'url' => $serialiseddata->url,
            'has_sublevel' => $serialiseddata->has_sublevel,
            'has_voting' => $serialiseddata->has_voting
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

function block_mission_map_output_fragment_sublevel_form($args)
{
    $args = (object) $args;
    $o = '';

    $formdata = [];
    if (!empty($args->jsonformdata)) {
        $serialiseddata = json_decode($args->jsonformdata);
        parse_str($serialiseddata, $formdata);
    }

    $mform = new \block_mission_map\local\forms\sublevel_form(
        $formdata,
        [
            'id' => $serialiseddata->id,
            'chapterid' => $serialiseddata->chapterid,
            'parentlevelid' => $serialiseddata->parentlevelid,
            'name' => $serialiseddata->name,
            'url' => $serialiseddata->url,
            'has_sublevel' => $serialiseddata->has_sublevel,
            'has_voting' => $serialiseddata->has_voting
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
