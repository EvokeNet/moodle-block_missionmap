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

require_capability('block/mission_map:managechapters', $context);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_course($course);
$PAGE->set_url('/blocks/mission_map/edit_voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_heading(get_string('edit_voting', 'block_mission_map'));

// Breadcrumbs navigation
$coursenode = $PAGE->navigation->find($course->id, navigation_node::TYPE_COURSE);
if (!empty($coursenode)) {
    $chapterurl = new moodle_url('/blocks/mission_map/chapters.php', array('courseid' => $chapter->courseid, 'blockid' => $chapter->blockid));
    $chapternode = $coursenode->add(get_string('chapter_view', 'block_mission_map', $chapter->name), $chapterurl);
    $votingnode = $chapternode->add(get_string('edit_voting', 'block_mission_map'));
    $votingnode->make_active();
}

$voting_session = $DB->get_record('block_mission_map_votings', ['chapterid' => $chapterid, 'levelid' => $levelid]);

if (!empty($voting_session)) {
    $toform = array();
    $toform['id'] = $voting_session->id;
    $toform['chapterid'] = $voting_session->chapterid;
    $toform['levelid'] = $voting_session->levelid;
    $toform['voting_type'] = $voting_session->model;
    $toform['algorithm'] = $voting_session->mechanic;
    $toform['deadline'] = $voting_session->voting_deadline;
    $toform['threshold'] = $voting_session->threshold;
    $toform['tiebreak'] = $voting_session->tiebreaker;
    $toform['tiebreaker_deadline'] = $voting_session->tiebreaker_deadline;
    $toform['description'] = $voting_session->description;
    $voting_form = new \block_mission_map\local\forms\voting_form($toform);
} else {
    $voting_form = new \block_mission_map\local\forms\voting_form(['chapterid' => $chapterid, 'levelid' => $levelid]);
}

if ($voting_form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($data = $voting_form->get_data()) {
    $voting_session = new stdClass;
    $voting_session->chapterid = $data->chapterid;
    $voting_session->levelid = $data->levelid;
    $voting_session->description = $data->description;
    $voting_session->model = $data->voting_type;
    $voting_session->mechanic = $data->algorithm;
    $voting_session->tiebreaker = $data->tiebreak;
    $voting_session->threshold = isset($data->threshold) ? $data->threshold : null;
    $voting_session->deadline = $data->deadline;
    $voting_session->minerva_userid = isset($data->minerva_userid) ? $data->minerva_userid : null;
    $voting_session->tiebreaker_deadline = $data->tiebreaker_deadline;
    $voting_session->timemodified = time();

    if (!empty($voting_session->id)) {
        $voting_session->id = $data->id;
        $DB->update_record('block_mission_map_votings', $voting_session);
    } else {
        $voting_session->timecreated = time();
        $voting_session_id = $DB->insert_record('block_mission_map_votings', $voting_session);
    }

    $option_names = $data->option_name;
    $option_descriptions = $data->option_description;
    $option_types = $data->option_type;
    $option_urls = isset($data->option_url) ? $data->option_url : null;
    $option_courses = isset($data->option_course) ? $data->option_course : null;
    $option_sections = isset($data->option_section) ? $data->option_section : null;

    $voting_options = array();
    for ($i = 0; $i < sizeof($option_names); $i++) {
        $voting_options[$i]['votingid'] = $voting_session_id;
        $voting_options[$i]['name'] = $option_names[$i];
        $voting_options[$i]['description'] = isset($option_descriptions[$i]) ? $option_descriptions[$i] : null;
        $voting_options[$i]['type'] = $option_types[$i];
        $voting_options[$i]['url'] = $option_urls[$i];
        $voting_options[$i]['courseid'] = isset($option_courses[$i]) ? $option_courses[$i] : null;
        $voting_options[$i]['sectionid'] = isset($option_sections[$i]) ? $option_sections[$i] : null;
        $voting_options[$i]['timecreated'] = time();
        $voting_options[$i]['timemodified'] = time();
    }

    for ($i = 0; $i < sizeof($voting_options); $i++) {
        $DB->insert_record('block_mission_map_options', $voting_options[$i]);
    }
    $returnurl = new moodle_url('voting.php', array('chapterid' => $data->chapterid, 'levelid' => $data->levelid));
    redirect($returnurl);
} else {
    echo $OUTPUT->header();
    $voting_form->display();
    echo $OUTPUT->footer();
}
