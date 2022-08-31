<?php

require_once('../../config.php');

global $DB, $OUTPUT, $PAGE;

$chapterid = required_param('chapterid', PARAM_INT);
$levelid = required_param('levelid', PARAM_INT);

$chapter = $DB->get_record('block_mission_map_chapters', ['id' => $chapterid]);
$course = $DB->get_record('course', ['id' => $chapter->courseid]);

// If user not logged in, require login
require_login($course);

$context = context_course::instance($course->id);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/mission_map/level.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_heading(get_string('view_level', 'block_mission_map'));


// Breadcrumbs navigation
$coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
if (!empty($coursenode)) {
    $chapterurl = new moodle_url('/course/view.php', array('id' => $chapter->courseid));
    $chapternode = $coursenode->add(get_string('chapter_view', 'block_mission_map', $chapter->name), $chapterurl);
    $votingnode = $chapternode->add(get_string('view_voting', 'block_mission_map'));
    $votingnode->make_active();
}

$level = $DB->get_record('block_mission_map_levels', ['id' => $levelid]);
$sublevels = $DB->get_records('block_mission_map_levels', ['parentlevelid' => $levelid]);
$sublevels = array_values($sublevels);

if (
    has_capability('block/mission_map:managechapters', $context)
) {

    $level = new \block_mission_map\output\level($level, $sublevels, $context, true);
    $renderer = $PAGE->get_renderer('block_mission_map');
} else {
    $level = new \block_mission_map\output\level($level, $sublevels, $context, false);
    $renderer = $PAGE->get_renderer('block_mission_map');
}

$PAGE->requires->js_call_amd('block_mission_map/colorizer', 'init', ['.block_mission_map']);

echo $OUTPUT->header();
$button = new \block_mission_map\output\button(new moodle_url('/course/view.php', ['id' => $course->id]));
$renderer = $PAGE->get_renderer('block_mission_map');
echo $renderer->render($button);
echo html_writer::div($renderer->render($level), 'block_mission_map');
echo $OUTPUT->footer();
