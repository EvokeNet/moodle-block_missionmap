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

    public function __construct($level, $context)
    {
        $this->level = $level;
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();

        // foreach ($this->chapters as &$chapter) {
        //     if (!isset($chapter->levels)) continue;
        //     foreach ($chapter->levels as &$level) {
        //         if ($level->has_sublevel) {
        //             $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
        //         }
        //     }
        // }

        $data->level = $this->level;
        $data->contextid = $this->context->id;

        return $data;
    }
}
