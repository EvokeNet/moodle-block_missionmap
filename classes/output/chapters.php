<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class chapters implements renderable, templatable
{

    private $chapters;
    private $context;
    private $blockid;
    private $courseid;

    public function __construct($chapters, $context, $blockid, $courseid)
    {
        $this->chapters = $chapters;
        $this->context = $context;
        $this->blockid = $blockid;
        $this->courseid = $courseid;
    }

    public function export_for_template(renderer_base $output)
    {
        global $OUTPUT;

        $data = new \stdClass();
        $fs = get_file_storage();
        $backgrounds = [];
        $blockcontext = $this->blockid ? \context_block::instance($this->blockid) : null;
        $markerstyle = 'dot';
        if ($this->blockid) {
            $blockrecord = $DB->get_record('block_instances', ['id' => $this->blockid], 'configdata', IGNORE_MISSING);
            if ($blockrecord && !empty($blockrecord->configdata)) {
                $config = @unserialize(base64_decode($blockrecord->configdata));
                if (is_object($config) && !empty($config->marker_style)) {
                    $markerstyle = $config->marker_style;
                }
            }
        }
        $markerclass = ($markerstyle === 'speech') ? 'mission-speech' : 'mission-dot';

        for ($i = 1; $i <= 4; $i++) {
            $backgrounds[$i] = $OUTPUT->image_url("map_main_{$i}", 'block_mission_map');
            if ($blockcontext) {
                $filearea = "map_bg_{$i}";
                $files = $fs->get_area_files($blockcontext->id, 'block_mission_map', $filearea, $this->blockid, 'itemid, filepath, filename', false);
                if (empty($files)) {
                    $files = $fs->get_area_files($blockcontext->id, 'block_mission_map', $filearea, 0, 'itemid, filepath, filename', false);
                }
                if (!empty($files)) {
                    $file = array_values($files)[0];
                    $backgrounds[$i] = \moodle_url::make_pluginfile_url(
                        $blockcontext->id,
                        'block_mission_map',
                        $filearea,
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename()
                    );
                }
            }
        }

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $imgindex = (($mapno - 1) % 4) + 1;
            $chapter->img = $backgrounds[$imgindex];

            if ($chapter->has_lock) {
                if ($chapter->unlocking_date > time()) {
                    $chapter->isLocked = true;
                } else {
                    $chapter->isLocked = false;
                }
            }

            if (!isset($chapter->levels)) continue;
            foreach ($chapter->levels as &$level) {
                $level->no = ++$i;
                $level->marker_class = $markerclass;
                switch ($level->type) {
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        $level->editurl = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        $level->editurl = new moodle_url('/blocks/mission_map/edit_voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_SECTION:
                        $level->url = new moodle_url('/course/view.php') . "?id={$level->courseid}&section={$level->sectionid}";
                        $level->editurl = new moodle_url('/course/edit_voting.php') . "?id={$level->courseid}&section={$level->sectionid}";
                        break;
                    default:
                        break;
                }
            }
        }

        // $option_sections = [0 => get_string('level_select_section', 'block_mission_map')];
        $option_sections = [];
        $sections = get_fast_modinfo($this->courseid)->get_section_info_all();
        foreach ($sections as $section) {
            // $option_sections[$section->id] = !empty($section->name) ? $section->name : $section->section;
            $option_sections[] = [
                'id' => $section->id,
                'name' => !empty($section->name) ? $section->name : $section->section,
            ];
        }

        // $str_sections = strval(implode(',', $option_sections));
        // $str_sections = '';
        // foreach($option_sections as $key=>$item) {
        //     $str_sections .= $key.':'.$item.',';
        // }
        // rtrim($str_sections, ',');

        $data->chapters = $this->chapters;
        $data->contextid = $this->context->id;
        $data->blockid = $this->blockid;
        $data->courseid = $this->courseid;
        $data->sections = $option_sections;

        return $data;
    }
}
