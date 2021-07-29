<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

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
            if ($sublevel->has_sublevel) {
                $sublevel->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$sublevel->chapterid}&levelid={$sublevel->id}";
            }
        }

        $this->level->sublevels = $this->sublevels;

        $data->level = $this->level;
        $data->contextid = $this->context->id;
        $data->isEditing = $this->isEditing;

        return $data;
    }
}
