<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/mission_map/classes/local/forms/backgrounds_form.php');

$blockid = required_param('blockid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login($courseid);

$blockinstance = $DB->get_record('block_instances', ['id' => $blockid], '*', MUST_EXIST);
$blockcontext = context_block::instance($blockinstance->id);
$coursecontext = context_course::instance($courseid);
if ($blockcontext->get_parent_context()->id !== $coursecontext->id) {
    throw new moodle_exception('invalidcontext');
}

require_capability('block/mission_map:managechapters', $coursecontext);

$url = new moodle_url('/blocks/mission_map/backgrounds.php', ['blockid' => $blockid, 'courseid' => $courseid]);
$PAGE->set_url($url);
$PAGE->set_context($coursecontext);
$PAGE->set_title(get_string('map_backgrounds_header', 'block_mission_map'));
$PAGE->set_heading(get_string('map_backgrounds_header', 'block_mission_map'));

$mform = new \block_mission_map\local\forms\backgrounds_form($url);

// Prepare draft areas.
$data = new stdClass();
$data->blockid = $blockid;
$data->courseid = $courseid;
foreach (['map_bg_1', 'map_bg_2', 'map_bg_3', 'map_bg_4'] as $filearea) {
    $draftid = file_get_submitted_draft_itemid($filearea);
    file_prepare_draft_area($draftid, $blockcontext->id, 'block_mission_map', $filearea, $blockid, ['subdirs' => 0]);
    $data->$filearea = $draftid;
}
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
} else if ($formdata = $mform->get_data()) {
    require_sesskey();
    foreach (['map_bg_1', 'map_bg_2', 'map_bg_3', 'map_bg_4'] as $filearea) {
        if (isset($formdata->$filearea)) {
            // Remove legacy itemid=0 files to avoid duplicates.
            $fs = get_file_storage();
            $fs->delete_area_files($blockcontext->id, 'block_mission_map', $filearea, 0);
            file_save_draft_area_files($formdata->$filearea, $blockcontext->id, 'block_mission_map', $filearea, $blockid, [
                'subdirs' => 0,
                'maxfiles' => 1
            ]);
        }
    }
    redirect($url, get_string('changesaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('map_backgrounds_header', 'block_mission_map'));
$mform->display();
echo $OUTPUT->footer();
