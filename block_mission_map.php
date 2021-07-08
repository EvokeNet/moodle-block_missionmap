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
        global $DB, $CFG, $USER;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->title = get_string('block_title', 'block_mission_map');

        $courses = enrol_get_all_users_courses($USER->id);

        // Ugly hack: for now, only shows first course
        $courses = reset($courses);

        $chapters = $DB->get_records('block_mission_map_chapters');

        if (empty($chapters)) {
            $this->content = new stdClass;
            $this->content->text = null;
        } else {
            $url = new moodle_url('/blocks/mission_map/chapters.php', array('blockid' => $this->instance->id));
            $map = new \block_mission_map\output\map($chapters, $url);
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($map);
            $this->content->footer = '';
        }

        $config = ['paths' => ['leaderline' => $CFG->wwwroot . '/blocks/mission_map/amd/src/leaderline']];
        $requirejs = 'require.config(' . json_encode($config) . ')';
        $this->page->requires->js_amd_inline($requirejs);

        $this->page->requires->js_call_amd('block_mission_map/connectors', 'init');

        return $this->content;
    }

    public function get_content_for_output($output)
    {
        global $USER;
        $bc = parent::get_content_for_output($output);
        $courses = enrol_get_all_users_courses($USER->id);

        // Ugly hack: for now, only shows first course
        $course = reset($courses);

        if (isset($bc)) {
            $context = context_system::instance();
            if (
                $this->page->user_can_edit_blocks() && has_capability('block/mission_map:managechapters', $context)
            ) {
                $str = new lang_string('add_page', 'block_mission_map');
                $controls = new action_menu_link_secondary(
                    new moodle_url('/blocks/mission_map/chapters.php', array('courseid' => $course->id, 'blockid' => $bc->blockinstanceid)),
                    new pix_icon('a/view_list_active', $str, 'moodle', array('class' => 'iconsmall', 'title' => '')),
                    $str,
                    array('class' => 'editing_manage')
                );

                array_unshift($bc->controls, $controls);
            }
        }

        return $bc;
    }

    public function instance_allow_multiple()
    {
        return true;
    }

    public function hide_header()
    {
        $context = context_system::instance();
        if (
            $this->page->user_can_edit_blocks() &&
            has_capability('block/mission_map:managechapters', $context)
        ) {
            return false;
        }
        return true;
    }
}
