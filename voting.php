<?php

require_once('../../config.php');
require_once('lib.php');

global $DB, $OUTPUT, $PAGE, $COURSE;

$context = context_system::instance();

$chapterid = required_param('chapterid', PARAM_INT);
$levelid = required_param('levelid', PARAM_INT);

// Configurations to the page (display, context etc)
$PAGE->set_context($context);
$PAGE->set_url('/blocks/mission_map/edit_voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$PAGE->set_pagelayout('course');
$PAGE->set_heading(get_string('view_voting', 'block_mission_map'));

// Breadcrumbs navigation
$settingsnode = $PAGE->settingsnav->add(get_string('chapter_settings', 'block_mission_map'));
$editurl = new moodle_url('/blocks/mission_map/edit_voting.php', array('chapterid' => $chapterid, 'levelid' => $levelid));
$editnode = $settingsnode->add(get_string('view_voting', 'block_mission_map'), $editurl);
$editnode->make_active();

$groupsutil = new \block_mission_map\util\groups();
$usercoursegroup = $groupsutil->get_user_group(2);
$groupmembers = (!empty($usercoursegroup)) ? $groupsutil->get_group_members($usercoursegroup->id) : [$USER];

$voting_session = $DB->get_record('block_mission_map_votings', ['chapterid' => $chapterid, 'levelid' => $levelid]);
$voting_options = $DB->get_records('block_mission_map_options', ['votingid' => $voting_session->id]);

$option_ids = array();
foreach ($voting_options as $option) {
    $option_ids[] = $option->id;
}

list($insql, $params) = $DB->get_in_or_equal($option_ids);
$sql1 = "SELECT * FROM {block_mission_map_votes} WHERE optionid $insql";
$sql2 = "SELECT * FROM {block_mission_map_votes} WHERE userid = '$USER->id' AND optionid $insql";

$cast_votes = $DB->get_records_sql($sql1, $params);
$user_votes = $DB->get_records_sql($sql2, $params);

// var_dump(mktime(0, 0, 0, 9, 1, 2021));
// die();


echo $OUTPUT->header();

// User voted and session is closed, so let's show results based on voting algorithm
// @TODO: add checks if its a teacher or admin
if (!empty($user_votes)) {

    $groupsize = sizeof($groupmembers);
    $votesize = sizeof($cast_votes);

    // Let's count the votes
    $totalization = array();
    foreach ($cast_votes as $vote) {
        if (!isset($totalization[$vote->optionid])) {
            $totalization[$vote->optionid] = 1;
        } else {
            $totalization[$vote->optionid] += 1;
        }
    }

    // Based on the chosen algorithm, winner is defined in different ways
    switch ($voting_session->mechanic) {
        case BLOCK_MISSIONMAP_VOTINGAL_MAJORITY:
            // Simple majority means 50% + 1 defines a winner

            // Lets display "waiting" information if there are still pending votes to be cast
            if ($groupsize > 1 && $votesize < $groupsize && $voting_session->voting_deadline < time()) {
                // Prints header with "waiting" and countdown
                $voting_header = new \block_mission_map\output\voting_session(false, $USER, $groupmembers, $voting_session, $voting_options, true);
                $renderer = $PAGE->get_renderer('block_mission_map');
                echo html_writer::div($renderer->render($voting_header), 'block_mission_map');
                break;
            }

            $winner = isMajority($totalization);
            if (!$winner) {
                $voting_header = new \block_mission_map\output\voting_session(false, $USER, $groupmembers, $voting_session, $voting_options, false, true);
                $renderer = $PAGE->get_renderer('block_mission_map');
                echo html_writer::div($renderer->render($voting_header), 'block_mission_map');
            } else {
                $result = new \stdClass;
                $result->option = $voting_options[$winner->index]->name;
                $result->votes = $winner->score;
                $voting_header = new \block_mission_map\output\voting_session(false, $USER, $groupmembers, $voting_session, $voting_options, false, false, true, $result);
                $renderer = $PAGE->get_renderer('block_mission_map');
                echo html_writer::div($renderer->render($voting_header), 'block_mission_map');
            }

            break;
        case BLOCK_MISSIONMAP_VOTINGAL_THRESHOLD:
            // Voting threshold means N% must cast a vote, simple majority wins
            break;
        default:
            // No votes cast for this user, so print the form
            echo '<p>What now?</p>';
            break;
    }
}

// Session is not expired, but user already cast a vote
// else if ($voting_session->voting_deadline > time()) {
//     $voting_header = new \block_mission_map\output\voting_session(false, $USER, $groupmembers, $voting_session, $voting_options, true);
//     $renderer = $PAGE->get_renderer('block_mission_map');
//     echo html_writer::div($renderer->render($voting_header), 'block_mission_map');
// }

// User has not cast a vote, so it's time to do it!
else {
    $voting_header = new \block_mission_map\output\voting_session(true, $USER, $groupmembers, $voting_session, $voting_options);
    $renderer = $PAGE->get_renderer('block_mission_map');
    echo html_writer::div($renderer->render($voting_header), 'block_mission_map');

    echo html_writer::start_tag('div', ['class' => 'voting_session']);
    echo html_writer::start_tag('div', ['class' => 'options']);
    foreach ($voting_options as $option) {
        $option_form = new \block_mission_map\local\forms\vote_form([
            'chapterid' => $chapterid,
            'levelid' => $levelid,
            'votingid' => $voting_session->id,
            'deadline' => $voting_session->voting_deadline,
            'userid' => $USER->id,
            'optionid' => $option->id,
            'name' => $option->name
        ]);
        echo html_writer::div($option_form->display(), 'block_mission_map');
    }
    echo html_writer::end_tag('div');
    echo html_writer::end_tag('div');

    if ($data = $option_form->get_data()) {
        $vote = new \stdClass;
        $vote->optionid = $data->optionid;
        $vote->userid = $data->userid;
        $vote->timecreated = time();
        $vote->timemodified = time();
        $DB->insert_record('block_mission_map_votes', $vote);
        $returnurl = new moodle_url('voting.php', array('chapterid' => $data->chapterid, 'levelid' => $data->levelid));
        redirect($returnurl);
    }
}

echo $OUTPUT->footer();


function isMajority(array $arr)
{
    $max_score = max($arr);
    $winners = array_keys($arr, $max_score);

    // There is a tie!
    if (sizeof($winners) > 1) {
        return false;
    }

    $result = new \stdClass;
    $result->index = reset($winners);
    $result->score = $max_score;

    return $result;
}
