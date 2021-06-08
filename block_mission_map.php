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
        global $COURSE, $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->title = get_string('block_title', 'block_mission_map');

        $url = new moodle_url('/blocks/mission_map/chapters.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));

        if (!empty($this->config->course)) {
            $sections = get_fast_modinfo($this->config->course)->get_section_info_all();

            // Removes first section as it is a summary one
            array_shift($sections);

            foreach ($sections as $section) {
                $missions[] = $section;
            }
        }

        if (!empty($this->config->chapters)) {
            print_r($this->config->chapters);
        }

        if (!empty($this->config->seed)) {
            $seed = $this->config->seed;
        } else {
            $seed = 1;
        }

        if (empty($chapters)) {
            $blank = new \block_mission_map\output\blank(html_writer::link($url, get_string('add_page', 'block_mission_map')));
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($blank);
        } else {
            $map = new \block_mission_map\output\map($missions, $seed);
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($map);
            $this->content->footer = '';
        }

        return $this->content;
    }

    function instance_config_save($data, $nolongerused = false)
    {
        parent::instance_config_save($data);
    }

    public function instance_allow_multiple()
    {
        return true;
    }
}
