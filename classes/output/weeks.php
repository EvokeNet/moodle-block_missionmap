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

        if (has_capability('block/mission_map:managechapters', $context)) {
            $data->can_edit = true;
            $data->edit_url = new moodle_url('/blocks/mission_map/chapters.php') . "?courseid={$COURSE->id}&blockid={$this->chapters[0]->blockid}";
        }

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $chapter->img = $output->image_url("map_main_{$mapno}", 'block_mission_map');

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
