<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
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
        $data = new \stdClass();

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
                switch ($level->type) {
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_SECTION:
                        $level->url = new moodle_url('/course/view.php') . "?id={$level->courseid}&section={$level->sectionid}&returnto=map";
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
