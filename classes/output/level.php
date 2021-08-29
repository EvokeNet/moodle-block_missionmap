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

class level implements renderable, templatable
{

    private $level;
    private $context;

    public function __construct($level, $sublevels, $context, $isEditing = false)
    {
        $this->level = $level;
        $this->sublevels = $sublevels;
        $this->context = $context;
        $this->isEditing = $isEditing;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();

        $i = 0;
        foreach ($this->sublevels as &$sublevel) {
            $sublevel->no = ++$i;
            switch ($sublevel->type) {
                case TYPE_SUBLEVEL:
                    $sublevel->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$sublevel->chapterid}&levelid={$sublevel->id}";
                    break;
                case TYPE_VOTING:
                    $sublevel->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$sublevel->chapterid}&levelid={$sublevel->id}";
                    break;
                case TYPE_SECTION:
                    $sublevel->url = new moodle_url('/course/view.php') . "?id={$sublevel->courseid}&section={$sublevel->sectionid}&returnto=level&chapterid={$sublevel->chapterid}&levelid={$this->level->id}";
                    break;
                default:
                    break;
            }
        }

        $this->level->sublevels = $this->sublevels;

        $data->img = $output->image_url("map_zoom_1", 'block_mission_map');
        $data->level = $this->level;
        $data->contextid = $this->context->id;
        $data->isEditing = $this->isEditing;

        return $data;
    }
}
