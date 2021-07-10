<?php

require_once('../../config.php');

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

// Only users that can manage chapters (managers) can access this page
require_capability('block/mission_map:managechapters', $context);

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_url('/blocks/mission_map/chapters.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->set_pagelayout('course');
$PAGE->set_heading(get_string('view_chapters', 'block_mission_map'));

// Breadcrumbs navigation
$settingsnode = $PAGE->settingsnav->add(get_string('mission_map_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/chapters.php', array('blockid' => $blockid));
$editnode = $settingsnode->add(get_string('view_chapters', 'block_mission_map'), $editurl);
$editnode->make_active();

// Retrieves all chapters from this course
$chapters = $DB->get_records('block_mission_map_chapters');

// Fetch all levels associated with each chapter
foreach ($chapters as &$chapter) {
    $levels = $DB->get_records('block_mission_map_levels', ['chapterid' => $chapter->id]);
    $levels = array_values($levels);
    if (!empty($levels)) $chapter->levels = $levels;
}
$chapters = array_values($chapters);

// Pass data to editable map
$campaign = new \block_mission_map\output\chapters($chapters, $context);
$renderer = $PAGE->get_renderer('block_mission_map');

// Prints editable mission map
echo $OUTPUT->header();
echo html_writer::div($renderer->render($campaign), 'block_mission_map');
echo $OUTPUT->footer();
