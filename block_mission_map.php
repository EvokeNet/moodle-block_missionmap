<?php

defined('MOODLE_INTERNAL') || die();

class block_mission_map extends block_base
{
    public function init()
    {
        $this->title = get_string('mission_map', 'block_mission_map');
    }

    public function get_content()
    {
        if ($this->content !== null) {
            return $this->content;
        }

        $this->title = get_string('block_title', 'block_mission_map');

        if (!empty($this->config->course)) {
            $sections = get_fast_modinfo($this->config->course)->get_section_info_all();

            // Removes first section as it is a summary one
            array_shift($sections);

            foreach ($sections as $section) {
                $missions[] = $section;
            }
        }

        if (!empty($this->config->seed)) {
            $seed = $this->config->seed;
        } else {
            $seed = 1;
        }

        $map = new \block_mission_map\output\map($missions, $seed);
        $renderer = $this->page->get_renderer('block_mission_map');

        $this->content         = new stdClass;
        $this->content->text   = $renderer->render($map);
        $this->content->footer = '';

        return $this->content;
    }
}
