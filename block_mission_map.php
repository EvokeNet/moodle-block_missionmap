<?php
require_once("{$CFG->libdir}/completionlib.php");

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

        $chapters = $DB->get_records('block_mission_map', ['blockid' => $this->instance->id]);

        $url = new moodle_url('/blocks/mission_map/chapters.php', array('blockid' => $this->instance->id, 'courseid' => $COURSE->id));
        if (empty($chapters)) {
            $blank = new \block_mission_map\output\blank(html_writer::link($url, get_string('add_page', 'block_mission_map')));
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($blank);
        } else {
            $map = new \block_mission_map\output\map($chapters, $url);
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($map);
            $this->content->footer = '';
        }

        return $this->content;
    }

    public function instance_allow_multiple()
    {
        return true;
    }
}
