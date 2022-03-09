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
define('BLOCK_MISSIONMAP_THRESHOLD_25', 25);
define('BLOCK_MISSIONMAP_THRESHOLD_50', 50);
define('BLOCK_MISSIONMAP_THRESHOLD_75', 75);
define('BLOCK_MISSIONMAP_THRESHOLD_90', 90);

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
            'map_image' => $serialiseddata->map_image,
            'has_lock' => $serialiseddata->has_lock,
            'unlocking_date' => $serialiseddata->unlocking_date,
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
            'description' => $serialiseddata->description,
            'type' => $serialiseddata->url,
            'courseid' => $serialiseddata->courseid,
            'sectionid' => $serialiseddata->sectionid
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
            'type' => $serialiseddata->url,
            'courseid' => $serialiseddata->courseid,
            'sectionid' => $serialiseddata->sectionid
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

/**
 * Form for editing Mission Map block instances.
 */
function block_mission_map_pluginfile($course, $birecord_or_cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG, $USER;

    if ($context->contextlevel != CONTEXT_BLOCK) {
        send_file_not_found();
    }

    // If block is in course context, then check if user has capability to access course.
    if ($context->get_course_context(false)) {
        require_course_login($course);
    } else if ($CFG->forcelogin) {
        require_login();
    } else {
        // Get parent context and see if user have proper permission.
        $parentcontext = $context->get_parent_context();
        if ($parentcontext->contextlevel === CONTEXT_COURSECAT) {
            // Check if category is visible and user can view this category.
            if (!core_course_category::get($parentcontext->instanceid, IGNORE_MISSING)) {
                send_file_not_found();
            }
        } else if ($parentcontext->contextlevel === CONTEXT_USER && $parentcontext->instanceid != $USER->id) {
            // The block is in the context of a user, it is only visible to the user who it belongs to.
            send_file_not_found();
        }
        // At this point there is no way to check SYSTEM context, so ignoring it.
    }

    if ($filearea !== 'content') {
        send_file_not_found();
    }

    $fs = get_file_storage();

    $filename = array_pop($args);
    $filepath = $args ? '/'.implode('/', $args).'/' : '/';

    if (!$file = $fs->get_file($context->id, 'block_mission_map', 'content', 0, $filepath, $filename) or $file->is_directory()) {
        send_file_not_found();
    }

    if ($parentcontext = context::instance_by_id($birecord_or_cm->parentcontextid, IGNORE_MISSING)) {
        if ($parentcontext->contextlevel == CONTEXT_USER) {
            // force download on all personal pages including /my/
            //because we do not have reliable way to find out from where this is used
            $forcedownload = true;
        }
    } else {
        // weird, there should be parent context, better force dowload then
        $forcedownload = true;
    }

    // NOTE: it woudl be nice to have file revisions here, for now rely on standard file lifetime,
    //       do not lower it because the files are dispalyed very often.
    \core\session\manager::write_close();
    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param  string $filearea The filearea.
 * @param  array  $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function block_mission_map_get_path_from_pluginfile(string $filearea, array $args) : array {
    // This block never has an itemid (the number represents the revision but it's not stored in database).
    array_shift($args);

    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}