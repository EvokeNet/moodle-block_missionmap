<?php
global $DB, $COURSE, $OUTPUT, $PAGE, $CFG;

require_once('../../config.php');
require_once( $CFG->libdir.'/blocklib.php' );

$context = context_course::instance($COURSE->id);

// Only users that can manage chapters (managers) can access this page
require_capability('block/mission_map:managechapters', $context);

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->force_settings_menu();
$PAGE->set_url('/blocks/mission_map/chapters.php', array('courseid' => $courseid, 'blockid' => $blockid));
$PAGE->set_heading(get_string('chapter_settings', 'block_mission_map'));

// Breadcrumbs navigation
$settingsnode = $PAGE->settingsnav->add(get_string('chapter_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/chapters.php', array('blockid' => $blockid));
$editnode = $settingsnode->add(get_string('chapter_settings', 'block_mission_map'), $editurl);
$editnode->make_active();

// Retrieves all chapters from this course
$instance = $DB->get_record('block_instances', array('id' => $blockid), '*', MUST_EXIST);
$block_map = block_instance('mission_map', $instance);
$chapters = $DB->get_records('block_mission_map_chapters', ['courseid' => $courseid, 'blockid' => $blockid]);

// Fetch all levels associated with each chapter
foreach ($chapters as &$chapter) {
    $levels = $DB->get_records('block_mission_map_levels', ['chapterid' => $chapter->id, 'parentlevelid' => null]);
    $levels = array_values($levels);
    if (!empty($levels)) $chapter->levels = $levels;
}
$chapters = array_values($chapters);

// Pass data to editable map
$campaign = new \block_mission_map\output\chapters($chapters, $context, $blockid, $courseid);
$levelform = new \block_mission_map\local\forms\level_edit_form(null);
$renderer = $PAGE->get_renderer('block_mission_map');

// Prints editable mission map
echo $OUTPUT->header();
echo html_writer::div($renderer->render($campaign), 'block_mission_map');
$levelform->display();
echo $OUTPUT->footer();
