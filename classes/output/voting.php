<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class voting implements renderable, templatable
{
    public function __construct($session)
    {
        $this->session = $session;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();

        $data->session = $this->session;

        return $data;
    }
}
