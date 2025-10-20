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
define("TYPE_ACTIVITY", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

class map implements renderable, templatable
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
        
        if (has_capability('block/mission_map:managechapters', $context)) {
            $data->can_add_mission = true;
        }

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $chapter->img = $output->image_url("map_main_{$mapno}", 'block_mission_map');

            // Ensure has_lock is properly formatted (convert to int if needed)
            $chapter->has_lock = (int)$chapter->has_lock;
            
            // Ensure unlocking_date is properly formatted
            $chapter->unlocking_date = (int)$chapter->unlocking_date;

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
                    case TYPE_URL:
                        // Use the URL directly from the database
                        $level->url = $level->url;
                        break;
                    case TYPE_ACTIVITY:
                        // Use the URL from the database (which contains the activity URL)
                        $level->url = $level->url;
                        break;
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    default:
                        // Fallback: if no URL is set, don't make it clickable
                        if (empty($level->url)) {
                            $level->url = '#';
                        }
                        break;
                }
            }
        }

        $data->chapters = $this->chapters;
        return $data;
    }
}
