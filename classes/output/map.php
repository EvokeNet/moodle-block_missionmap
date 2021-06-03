<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class map implements renderable, templatable
{

    protected $missions;

    public function __construct($missions, $seed)
    {
        $this->missions = $missions;
        $this->seed = $seed;
    }

    public function export_for_template(renderer_base $output)
    {
        global $CFG;
        $data = new \stdClass();

        // Seed the random generator to displace missions across the map
        srand($this->seed);

        if (!empty($this->missions)) {
            $left = 10;
            $top = 45;
            foreach ($this->missions as $mission) {
                $noise = rand(-15, 15);
                $data->missions[] = array(
                    'section' => $mission->section,
                    'url' => $CFG->wwwroot . "/course/view.php?id=" . $mission->course . '&section=' . $mission->section,
                    'name' => $mission->name,
                    'left' => $left,
                    'top' => $top + $noise
                );
                $left += 15;
            }
        } else {
            $data->missions = '';
        }
        return $data;
    }
}
