<?php

require_once('../../config.php');

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

$chapterid = required_param('chapterid', PARAM_INT);
$levelid = required_param('levelid', PARAM_INT);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_url('/blocks/mission_map/level.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_pagelayout('course');
$PAGE->set_heading(get_string('view_level', 'block_mission_map'));

// Breadcrumbs navigation
$settingsnode = $PAGE->settingsnav->add(get_string('mission_map_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/level.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$editnode = $settingsnode->add(get_string('view_level', 'block_mission_map'), $editurl);
$editnode->make_active();

echo $OUTPUT->header();
echo html_writer::div('hi');
echo $OUTPUT->footer();
