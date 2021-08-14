<?php

require_once('../../config.php');

global $DB, $OUTPUT, $PAGE;

$context = context_system::instance();

$chapterid = required_param('chapterid', PARAM_INT);
$levelid = required_param('levelid', PARAM_INT);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_url('/blocks/mission_map/edit_voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_pagelayout('course');
$PAGE->set_heading(get_string('edit_voting', 'block_mission_map'));

// Breadcrumbs navigation
$settingsnode = $PAGE->settingsnav->add(get_string('chapter_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/edit_voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$editnode = $settingsnode->add(get_string('view_voting', 'block_mission_map'), $editurl);
$editnode->make_active();

$voting_form = new \block_mission_map\local\forms\voting_form(['chapterid' => $chapterid, 'levelid' => $levelid]);

if ($voting_form->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($data = $voting_form->get_data()) {
    $voting_session = new stdClass;
    $voting_session->chapterid = $data->chapterid;
    $voting_session->levelid = $data->levelid;
    $voting_session->model = $data->voting_type;
    $voting_session->mechanic = $data->algorithm;
    $voting_session->tiebreaker = $data->tiebreak;
    $voting_session->threshold = isset($data->threshold) ? $data->threshold : null;
    $voting_session->deadline = $data->deadline;
    $voting_session->minerva_userid = isset($data->minerva_userid) ? $data->minerva_userid : null;
    $voting_session->tiebreaker_deadline = $data->tiebreaker_deadline;
    $voting_session->timecreated = time();
    $voting_session->timemodified = time();

    $voting_session_id = $DB->insert_record('block_mission_map_votings', $voting_session);

    $option_names = $data->option_name;
    $option_types = $data->option_type;
    $option_urls = $data->option_url;
    $option_courses = $data->option_course;
    $option_sections = $data->option_section;

    $voting_options = array();
    for ($i = 0; $i < sizeof($option_names); $i++) {
        $voting_options[$i]['votingid'] = $voting_session_id;
        $voting_options[$i]['name'] = $option_names[$i];
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