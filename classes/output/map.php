<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class map implements renderable, templatable
{

    private $chapters;

    public function __construct($chapters)
    {
        $this->chapters = $chapters;
    }

    public function export_for_template(renderer_base $output)
    {
        // global $DB;

        // foreach ($this->chapters as &$chapter) {
        //     $levels = $DB->get_records('block_mission_map_levels', ['chapterid' => $chapter->id]);
        //     $levels = array_values($levels);
        //     if (!empty($levels)) $chapter->levels = $levels;
        // }
        // $this->chapters = array_values($this->chapters);

        $data = new \stdClass();
        $data->chapters = $this->chapters;
        return $data;
    }
}
