<?php

require_once('../../config.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE;

$chapterid = required_param('chapterid', PARAM_INT);
$levelid = required_param('levelid', PARAM_INT);

$returnto = optional_param('returnto', null, PARAM_TEXT);
$returnchapterid = optional_param('returnchapterid', 0, PARAM_INT);
$returnlevelid = optional_param('returnlevelid', 0, PARAM_INT);

$chapter = $DB->get_record('block_mission_map_chapters', ['id' => $chapterid]);
$course = $DB->get_record('course', ['id' => $chapter->courseid]);

// If user not logged in, require login
require_login($course);

$context = context_course::instance($course->id);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/mission_map/voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_pagelayout('course');
$PAGE->set_heading(get_string('view_voting', 'block_mission_map'));

// Breadcrumbs navigation
$coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
if (!empty($coursenode)) {
    $chapterurl = new moodle_url('/course/view.php', array('id' => $chapter->courseid));
    $chapternode = $coursenode->add(get_string('chapter_view', 'block_mission_map', $chapter->name), $chapterurl);
    $votingnode = $chapternode->add(get_string('view_voting', 'block_mission_map'));
    $votingnode->make_active();
}

$voting = $DB->get_record('block_mission_map_votings', ['chapterid' => $chapterid, 'levelid' => $levelid]);
$voting->options = array_values($DB->get_records('block_mission_map_options', ['votingid' => $voting->id]));

// Let's prepare the voting_options array with needed information
$iterator = 0;
foreach ($voting->options as &$option) {
    // Let's prepare the URL for redirection
    // If it's a course section, let's build the URL
    if ($option->type == BLOCK_MISSIONMAP_OPTION_SECTION) {
        $option->url = new moodle_url('/course/view.php') . "?id={$option->courseid}&section={$option->sectionid}";
    }

    // Purely cosmetic for interchangeable images
    $option->index = ++$iterator;
}

echo $OUTPUT->header();

// Adds correct button URL (to level or chapter)
if ($returnto == "level") {
    $button = new \block_mission_map\output\button(new moodle_url('/blocks/mission_map/levels.php', ['chapterid' => $returnchapterid, 'levelid' => $returnlevelid]));
} else {
    $button = new \block_mission_map\output\button(new moodle_url('/course/view.php', ['id' => $course->id]));
}
$renderer = $PAGE->get_renderer('block_mission_map');
echo $renderer->render($button);

// Voting session is not yet configured!
if (empty($voting)) {
    echo html_writer::start_tag('div', ['class' => 'voting_session']);
    echo html_writer::start_tag('div', ['class' => 'banner']);
    echo html_writer::tag('h1', get_string('voting_notready', 'block_mission_map'));

    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');
}

$voting_options = new \block_mission_map\output\voting($voting);
$renderer = $PAGE->get_renderer('block_mission_map');
echo $renderer->render($voting_options);

$PAGE->requires->js_call_amd('block_mission_map/colorizer', 'init', ['.voting_session']);

echo $OUTPUT->footer();
