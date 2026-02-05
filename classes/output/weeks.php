<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use completion_info;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

require_once("{$CFG->libdir}/completionlib.php");

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

class weeks implements renderable, templatable
{

    private $chapters;

    public function __construct($chapters)
    {
        $this->chapters = $chapters;
    }

    public function export_for_template(renderer_base $output)
    {
        global $DB, $COURSE, $USER;

        $data = new \stdClass();
        $context = \context_course::instance($COURSE->id);
        $fs = get_file_storage();
        $backgrounds = [];
        $blockid = !empty($this->chapters) ? ($this->chapters[0]->blockid ?? null) : null;
        $blockcontext = $blockid ? \context_block::instance($blockid) : null;
        $markerstyle = 'dot';
        if ($blockid) {
            $blockrecord = $DB->get_record('block_instances', ['id' => $blockid], 'configdata', IGNORE_MISSING);
            if ($blockrecord && !empty($blockrecord->configdata)) {
                $config = @unserialize(base64_decode($blockrecord->configdata));
                if (is_object($config) && !empty($config->marker_style)) {
                    $markerstyle = $config->marker_style;
                }
            }
        }
        $markerclass = ($markerstyle === 'speech') ? 'mission-speech' : 'mission-dot';

        if (has_capability('block/mission_map:managechapters', $context)) {
            $data->can_edit = true;
            $data->edit_url = new moodle_url('/blocks/mission_map/chapters.php') . "?courseid={$COURSE->id}&blockid={$this->chapters[0]->blockid}";
        }

        for ($i = 1; $i <= 4; $i++) {
            $backgrounds[$i] = $output->image_url("map_main_{$i}", 'block_mission_map');
            if ($blockcontext) {
                $filearea = "map_bg_{$i}";
                $files = $fs->get_area_files($blockcontext->id, 'block_mission_map', $filearea, $blockid, 'itemid, filepath, filename', false);
                if (empty($files)) {
                    $files = $fs->get_area_files($blockcontext->id, 'block_mission_map', $filearea, 0, 'itemid, filepath, filename', false);
                }
                if (!empty($files)) {
                    $file = array_values($files)[0];
                    $backgrounds[$i] = \moodle_url::make_pluginfile_url(
                        $blockcontext->id,
                        'block_mission_map',
                        $filearea,
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename()
                    );
                }
            }
        }

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $imgindex = (($mapno - 1) % 4) + 1;
            $chapter->img = $backgrounds[$imgindex];

            if ($chapter->has_lock) {
                if ($chapter->unlocking_date > time()) {
                    $chapter->isLocked = true;
                } else {
                    $chapter->isLocked = false;
                }
            }

            if (!isset($chapter->levels)) continue;
            foreach ($chapter->levels as &$level) {
                $level->no = ++$i;
                $level->marker_class = $markerclass;

                if (!empty($level->cmid)) {
                    $cm = get_coursemodule_from_id('', $level->cmid);

                    $completion_info = new \completion_info($COURSE);
                    $completion = $completion_info->get_data($cm, false, $USER->id);
    
                    // $level->completion['view'] = $completion->viewed;
                    // $level->completion['submit'] = $completion->customcompletion["completionrequiresubmit"];
                    $level->isCompleted = !empty($completion->completionstate) ? true : false;
                }

                switch ($level->type) {
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_SECTION:
                        $section = $DB->get_record('course_sections', ['id' => $level->sectionid]);
                        $level->isLocked = (!$section->visible) ? true : false;
                        $level->url = new moodle_url('/course/view.php') . "?id={$level->courseid}&section={$section->section}&returnto=map";
                        break;
                    default:
                        break;
                }
            }
        }

        $data->chapters = $this->chapters;
        return $data;
    }
}
