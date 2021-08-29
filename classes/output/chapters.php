<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class chapters implements renderable, templatable
{

    private $chapters;
    private $context;
    private $blockid;
    private $courseid;

    public function __construct($chapters, $context, $blockid, $courseid)
    {
        $this->chapters = $chapters;
        $this->context = $context;
        $this->blockid = $blockid;
        $this->courseid = $courseid;
    }

    public function export_for_template(renderer_base $output)
    {
        global $OUTPUT;

        $data = new \stdClass();

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $chapter->img = $OUTPUT->image_url("map_main_{$mapno}", 'block_mission_map');
            if (!isset($chapter->levels)) continue;
            foreach ($chapter->levels as &$level) {
                $level->no = ++$i;
                switch ($level->type) {
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_SECTION:
                        $level->url = new moodle_url('/course/view.php') . "?id={$level->courseid}&section={$level->sectionid}";
                        break;
                    default:
                        break;
                }
            }
        }

        $data->chapters = $this->chapters;
        $data->contextid = $this->context->id;
        $data->blockid = $this->blockid;
        $data->courseid = $this->courseid;

        return $data;
    }
}
