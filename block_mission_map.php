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

        // Let's check if user is in a course section, where we don't 
        // want to display the map
        $sectionno = optional_param('section', 0, PARAM_INT);
        $returnto = optional_param('returnto', null, PARAM_TEXT);
        $chapterid = optional_param('chapterid', 0, PARAM_INT);
        $levelid = optional_param('levelid', 0, PARAM_INT);

        if ($this->content !== null) {
            return $this->content;
        }

        // @TODO: add voting results to a strip on the top of the course section
        if ($sectionno != 0) {
            $this->content = new stdClass;
            if ($returnto == "level") {
                $button = new \block_mission_map\output\button(new moodle_url('/blocks/mission_map/levels.php', ['chapterid' => $chapterid, 'levelid' => $levelid]));
            } else {
                $button = new \block_mission_map\output\button(new moodle_url('/course/view.php', ['id' => $this->page->course->id]));
            }
            $renderer = $this->page->get_renderer('block_mission_map');
            $this->content->text = $renderer->render($button);
            return $this->content;
        }

        $this->title = get_string('block_title', 'block_mission_map');

        // Fetches all chapters created
        $chapters = $DB->get_records('block_mission_map_chapters', ['blockid' => $this->instance->id]);

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
        // else {
        //     $map = new \block_mission_map\output\map($chapters);
        //     $renderer = $this->page->get_renderer('block_mission_map');
        //     $this->content = new stdClass;
        //     $this->content->text = $renderer->render($map);
        //     $this->content->footer = '';
        // }
        // $this->page->requires->js_call_amd('block_mission_map/colorizer', 'init', ['.block_mission_map']);

        return $this->content;
    }

    public function get_content_for_output($output)
    {
        global $COURSE;
        $bc = parent::get_content_for_output($output);

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

    public function applicable_formats()
    {
        return array(
            'site-index' => false,
            'course-view' => true,
            'mod' => false,
            'my' => false
        );
    }

    public function hide_header()
    {
        return true;
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function instance_delete()
    {
        global $DB;

        // We must retrieve a chapter to delete its levels and voting sessions
        $chapters = $DB->get_records('block_mission_map_chapters', ['blockid' => $this->instance->id]);

        foreach ($chapters as $chapter) {

            // Let's delete all options related to voting sessions
            $votings = $DB->get_records('block_mission_map_votings', ['chapterid' => $chapter->id]);
            foreach ($votings as $voting) {
                $options = $DB->get_records('block_mission_map_options', ['votingid' => $voting->id]);

                // Let's delete votes associated to options
                foreach ($options as $option) {
                    $DB->delete_records('block_mission_map_votes', ['optionid' => $option->id]);
                }

                // Then, let's delete the options
                $DB->delete_records('block_mission_map_options', ['votingid' => $voting->id]);
            }

            // Let's delete voting sessions and levels
            $DB->delete_records('block_mission_map_votings', ['chapterid' => $chapter->id]);
            $DB->delete_records('block_mission_map_levels', ['chapterid' => $chapter->id]);
        }
        // Finally, let's delete chapters
        $DB->delete_records('block_mission_map_chapters', ['blockid' => $this->instance->id]);
    }
}
