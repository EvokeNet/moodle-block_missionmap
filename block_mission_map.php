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
        global $DB;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->title = get_string('block_title', 'block_mission_map');

        // @TODO: pass blockid to bring only chapters associated with each block instance

        // Fetches all chapters created
        $chapters = $DB->get_records('block_mission_map_chapters');

        // Fetches all levels associated with each chapter
        foreach ($chapters as &$chapter) {
            $levels = $DB->get_records('block_mission_map_levels', ['chapterid' => $chapter->id, 'parentlevelid' => null]);
            $levels = array_values($levels);
            if (!empty($levels)) $chapter->levels = $levels;
        }
        $chapters = array_values($chapters);

        // If no chapters, render blank
        if (empty($chapters)) {
            $this->content = new stdClass;
            $this->content->text = null;
        }

        // If chapters, render mission map
        else {
            $map = new \block_mission_map\output\map($chapters);
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content = new stdClass;
            $this->content->text = $renderer->render($map);
            $this->content->footer = '';
        }
        $this->page->requires->js_call_amd('block_mission_map/colorizer', 'init', ['.block_mission_map']);

        return $this->content;
    }

    public function get_content_for_output($output)
    {
        global $COURSE;
        $bc = parent::get_content_for_output($output);
        // $courses = enrol_get_all_users_courses($USER->id);

        if (isset($bc)) {
            $context = context_system::instance();
            if (
                $this->page->user_can_edit_blocks() && has_capability('block/mission_map:managechapters', $context)
            ) {
                $str = new lang_string('add_page', 'block_mission_map');
                $controls = new action_menu_link_secondary(
                    new moodle_url('/blocks/mission_map/chapters.php', array('courseid' => $COURSE->id, 'blockid' => $bc->blockinstanceid)),
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

    public function instance_delete()
    {
        global $DB;
        $DB->delete_records('block_mission_map_chapters');
        $DB->delete_records('block_mission_map_levels');
        $DB->delete_records('block_mission_map_votings');
        $DB->delete_records('block_mission_map_options');
    }
}
