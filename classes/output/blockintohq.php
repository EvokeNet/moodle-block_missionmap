<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

/**
 * Block into HQ renderable class.
 *
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class blockintohq implements renderable, templatable {

    protected $page;
    protected $blockinstanceid;
    protected $sectionno = null;
    protected $returnto = null;
    protected $chapterid = null;
    protected $levelid = null;

    public function __construct($blockinstanceid) {
        global $PAGE;

        // Let's check if user is in a course section, where we don't
        // want to display the map
        $this->page = $PAGE;
        $this->blockinstanceid = $blockinstanceid;
        $this->sectionno = optional_param('section', 0, PARAM_INT);
        $this->returnto = optional_param('returnto', null, PARAM_TEXT);
        $this->chapterid = optional_param('chapterid', 0, PARAM_INT);
        $this->levelid = optional_param('levelid', 0, PARAM_INT);
    }

    public function export_for_template(renderer_base $output) {
        global $DB;


        // @TODO: add voting results to a strip on the top of the course section
        if ($this->sectionno != 0) {
            $this->content = new \stdClass;
            if ($this->returnto == "level") {
                $url = new \moodle_url(
                    '/blocks/mission_map/levels.php',
                    ['chapterid' => $this->chapterid, 'levelid' => $this->levelid]
                );
                $button = new \block_mission_map\output\button($url);
            } else {
                $url = new \moodle_url(
                    '/course/view.php',
                    ['id' => $this->page->course->id]
                );
                $button = new \block_mission_map\output\button($url);
            }

            return [
                'blockcontent' => $output->render($button)
            ];
        }

        // Fetches all chapters created
        $chapters = $DB->get_records('block_mission_map_chapters', ['blockid' => $this->blockinstanceid]);

        // Fetches all levels associated with each chapter
        foreach ($chapters as &$chapter) {
            $levels = $DB->get_records('block_mission_map_levels', ['chapterid' => $chapter->id, 'parentlevelid' => null]);
            $levels = array_values($levels);

            if (!empty($levels)) {
                $chapter->levels = $levels;
            }
        }

        $chapters = array_values($chapters);

        $blockcontent = '';

        if (!empty($chapters)) {
            $map = new \block_mission_map\output\map($chapters);

            $blockcontent = $output->render($map);
        }

        return [
            'blockcontent' => $blockcontent
        ];
    }
}
