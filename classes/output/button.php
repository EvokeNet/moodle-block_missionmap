<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class button implements renderable, templatable
{

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();
        $data->url = $this->url;
        return $data;
    }
}
