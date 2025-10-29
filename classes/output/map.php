<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use completion_info;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

require_once("{$CFG->libdir}/completionlib.php");

define("TYPE_URL", 1);
define("TYPE_ACTIVITY", 2);
define("TYPE_SECTION", 3);
define("TYPE_VOTING", 4);
define("TYPE_SUBLEVEL", 5);

class map implements renderable, templatable
{

    private $chapters;

    public function __construct($chapters)
    {
        $this->chapters = $chapters;
    }

    public function export_for_template(renderer_base $output)
    {
        global $DB, $COURSE, $USER;

        $data = new \stdClass();
        $context = \context_course::instance($COURSE->id);

        if (has_capability('block/mission_map:managechapters', $context)) {
            $data->can_edit = true;
            $data->edit_url = new moodle_url('/blocks/mission_map/chapters.php') . "?courseid={$COURSE->id}&blockid={$this->chapters[0]->blockid}";
        }
        
        if (has_capability('block/mission_map:managechapters', $context)) {
            $data->can_add_mission = true;
        }

        $i = 0;
        $mapno = 0;
        foreach ($this->chapters as &$chapter) {
            ++$mapno;
            $chapter->img = $output->image_url("map_main_{$mapno}", 'block_mission_map');

            // Ensure has_lock is properly formatted (convert to int if needed)
            $chapter->has_lock = (int)$chapter->has_lock;
            
            // Ensure unlocking_date is properly formatted
            $chapter->unlocking_date = (int)$chapter->unlocking_date;

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

                // Check completion based on mission type
                if ($level->type == TYPE_SECTION && !empty($level->sectionid)) {
                    // For section-based missions, check if all activities in the section are completed
                    $level->isCompleted = self::check_section_completion($level->sectionid, $COURSE->id, $USER->id);
                } elseif (!empty($level->cmid)) {
                    // For activity-based missions, check individual activity completion
                    $cm = get_coursemodule_from_id('', $level->cmid);

                    $completion_info = new \completion_info($COURSE);
                    $completion = $completion_info->get_data($cm, false, $USER->id);
    
                    // $level->completion['view'] = $completion->viewed;
                    // $level->completion['submit'] = $completion->customcompletion["completionrequiresubmit"];
                    $level->isCompleted = !empty($completion->completionstate) ? true : false;
                } else {
                    // For URL-based missions, assume not completed (manual check needed)
                    $level->isCompleted = false;
                }

                switch ($level->type) {
                    case TYPE_URL:
                        // Use the URL directly from the database
                        $level->url = $level->url;
                        break;
                    case TYPE_ACTIVITY:
                        // Use the URL from the database (which contains the activity URL)
                        $level->url = $level->url;
                        break;
                    case TYPE_SECTION:
                        // Use the URL from the database (which contains the section URL)
                        $level->url = $level->url;
                        break;
                    case TYPE_SUBLEVEL:
                        $level->url = new moodle_url('/blocks/mission_map/levels.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    case TYPE_VOTING:
                        $level->url = new moodle_url('/blocks/mission_map/voting.php') . "?chapterid={$level->chapterid}&levelid={$level->id}";
                        break;
                    default:
                        // Fallback: if no URL is set, don't make it clickable
                        if (empty($level->url)) {
                            $level->url = '#';
                        }
                        break;
                }

                // Process color for inline styles
                if (!empty($level->color)) {
                    // Convert hex color to RGB for rgba background
                    $hex = ltrim($level->color, '#');
                    if (strlen($hex) == 3) {
                        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
                    }
                    $r = hexdec(substr($hex, 0, 2));
                    $g = hexdec(substr($hex, 2, 2));
                    $b = hexdec(substr($hex, 4, 2));
                    $level->color_rgb = "{$r}, {$g}, {$b}";
                } else {
                    // Default color if none set
                    $level->color = '#007bff';
                    $level->color_rgb = '0, 123, 255';
                }
            }
        }

        $data->chapters = $this->chapters;
        return $data;
    }

    /**
     * Check if all activities with completion enabled in a section are completed by a user
     *
     * @param int $sectionid The section ID
     * @param int $courseid The course ID
     * @param int $userid The user ID
     * @return bool True if all activities are completed, false otherwise
     */
    private static function check_section_completion($sectionid, $courseid, $userid) {
        global $DB;

        // Get all course modules in the section that have completion enabled
        $sql = "SELECT cm.id, cm.completion, cm.module
                FROM {course_modules} cm
                JOIN {course_sections} cs ON cs.id = cm.section
                WHERE cs.id = :sectionid 
                AND cm.completion > 0
                AND cm.deletioninprogress = 0
                AND cm.visible = 1";
        
        $cms = $DB->get_records_sql($sql, ['sectionid' => $sectionid]);
        
        if (empty($cms)) {
            // No activities with completion enabled in this section
            debugging("No activities with completion enabled found in section {$sectionid}", DEBUG_DEVELOPER);
            return true;
        }

        debugging("Found " . count($cms) . " activities with completion enabled in section {$sectionid}", DEBUG_DEVELOPER);

        // Check completion for each activity
        $completion_info = new \completion_info(get_course($courseid));
        
        foreach ($cms as $cm) {
            $completion = $completion_info->get_data($cm, false, $userid);
            
            debugging("Activity {$cm->id} completion state: " . ($completion->completionstate ?? 'null'), DEBUG_DEVELOPER);
            
            // If any activity is not completed, the section is not completed
            if (empty($completion->completionstate)) {
                debugging("Section {$sectionid} not completed due to activity {$cm->id}", DEBUG_DEVELOPER);
                return false;
            }
        }

        debugging("All activities in section {$sectionid} are completed", DEBUG_DEVELOPER);
        return true;
    }
}
