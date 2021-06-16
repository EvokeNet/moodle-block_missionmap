<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class map implements renderable, templatable
{

    private $chapters;

    public function __construct($chapters, $url)
    {
        $this->chapters = $chapters;
        $this->url = $url;
    }

    public function export_for_template(renderer_base $output)
    {
        global $CFG;
        $data = new \stdClass();

        // $left = 10;
        // $top = 45;
        // $size = 50;

        foreach ($this->chapters as &$chapter) {
            $chapter->missions = json_decode($chapter->missions);

            // Seed the random generator to displace missions across the chapter
            srand($chapter->seed);

            // Builds every mission object
            foreach ($chapter->missions as &$mission) {
                // $noise = rand(-15, 15);
                $id = $mission;

                $mission = new \stdClass();
                $mission->id = $id;
                $mission->url = $CFG->wwwroot . "/course/view.php?id=" . $chapter->courseid . "&section=" . $mission->id;
                // $mission->left = $left;
                // $mission->top = $top;

                // $left += 15;
            }
            // $left = 10;
            $data->chapters[] = $chapter;
        }
        $data->url = $this->url;

        return $data;
    }
}
