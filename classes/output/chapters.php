<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

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
        $data = new \stdClass();

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            $chapter->mapno = ++$mapno;
            if (!isset($chapter->levels)) continue;
            foreach ($chapter->levels as &$level) {
                $level->no = ++$i;
                if ($level->has_sublevel) {
                    $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                } else if ($level->has_voting) {
                    $level->url = new moodle_url('/blocks/mission_map/edit_voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
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
