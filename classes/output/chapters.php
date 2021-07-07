<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class chapters implements renderable, templatable
{

    private $chapters;
    private $context;

    public function __construct($chapters, $context)
    {
        $this->chapters = $chapters;
        $this->context = $context;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();
        $data->chapters = $this->chapters;
        $data->contextid = $this->context->id;

        return $data;
    }
}
