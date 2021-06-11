<?php

use core_question\bank\view;

require_once('../../config.php');
require_once('mission_map_form.php');

global $DB, $OUTPUT, $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$blockid = required_param('blockid', PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourse', 'block_simplehtml', $courseid);
}

require_login($course);

$PAGE->set_url('/blocks/mission_map/chapters.php', array('id' => $courseid));
$PAGE->set_pagelayout('standard');
$PAGE->set_heading(get_string('view_chapters', 'block_mission_map'));

$settingsnode = $PAGE->settingsnav->add(get_string('mission_map_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/chapters.php', array('courseid' => $courseid, 'blockid' => $blockid));
$editnode = $settingsnode->add(get_string('view_chapters', 'block_mission_map'), $editurl);
$editnode->make_active();

$chapters = $DB->get_records('block_mission_map', array('blockid' => $blockid));
$chapters = array_values($chapters);

$toform['blockid'] = $blockid;
$toform['courseid'] = $courseid;

if (!empty($chapters)) {
    $toform['config_course'] = $chapters[0]->courseid;
    for ($i = 0; $i < sizeof($chapters); $i++) {
        $toform['chapters[' . $i . ']'] = $chapters[$i]->name;
        $toform['sections[' . $i . ']'] = json_decode($chapters[$i]->missions);
        $toform['seeds[' . $i . ']'] = $chapters[$i]->seed;
    }
    // receives __construct($is_editing, $chaptersno, $selected_course = null) {}
    $mission_map = new block_mission_map_edit_form(true, sizeof($chapters), $chapters[0]->courseid);
    $mission_map->set_data($toform);
} else {
    // receives __construct($is_editing, $chaptersno, $selected_course = null) {}
    $mission_map = new block_mission_map_edit_form(false, sizeof($chapters));
    $mission_map->set_data($toform);
}

if ($mission_map->is_cancelled()) {
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else if ($data = $mission_map->get_data()) {
    $quadrants = $data->chapters;
    $sections = $data->sections;
    $seeds = $data->seeds;
    $chapters = array();
    for ($i = 0; $i < sizeof($quadrants); $i++) {
        $chapters[$i]['blockid'] = $data->blockid;
        $chapters[$i]['courseid'] = $data->config_course;
        $chapters[$i]['name'] = $quadrants[$i];
        $chapters[$i]['seed'] = $seeds[$i];
        $chapters[$i]['missions'] = json_encode($sections[$i]);
    }
    for ($i = 0; $i < sizeof($chapters); $i++) {
        $DB->insert_record('block_mission_map', $chapters[$i]);
    }
    $courseurl = new moodle_url('/course/view.php', array('id' => $courseid));
    redirect($courseurl);
} else {
    echo $OUTPUT->header();
    $mission_map->display();
    echo $OUTPUT->footer();
}
