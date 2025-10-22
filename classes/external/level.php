<?php
namespace block_mission_map\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External API for block_mission_map level/mission operations.
 */
class level extends \external_api {

    /**
     * Create or update a mission/level.
     *
     * @param int $blockid Block instance ID
     * @param int $courseid Course ID
     * @param int $chapterid Chapter ID
     * @param string $name Mission name
     * @param string $description Mission description
     * @param int $type Mission type
     * @param string $color Mission color
     * @param string $url Mission URL (optional)
     * @param int $cmid Course module ID (optional)
     * @param int $sectionid Section ID (optional)
     * @param int $levelid Level ID for update (optional)
     * @return array
     */
    public static function create($blockid, $courseid, $chapterid, $name, $description, $type, $color, $url = null, $cmid = null, $sectionid = null, $levelid = null) {
        global $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::create_parameters(), [
            'blockid' => $blockid,
            'courseid' => $courseid,
            'chapterid' => $chapterid,
            'name' => $name,
            'description' => $description,
            'type' => $type,
            'color' => $color,
            'url' => $url,
            'cmid' => $cmid,
            'sectionid' => $sectionid,
            'levelid' => $levelid
        ]);

        // Check permissions
        $context = \context_course::instance($params['courseid']);
        require_capability('block/mission_map:managechapters', $context);

        // Validate block belongs to course
        $block = $DB->get_record('block_instances', [
            'id' => $params['blockid'],
            'blockname' => 'mission_map'
        ], '*', MUST_EXIST);

        $blockcontext = \context::instance_by_id($block->parentcontextid);
        if ($blockcontext->instanceid != $params['courseid']) {
            throw new \invalid_parameter_exception('Block does not belong to this course');
        }

        // Validate chapter exists and belongs to this block
        $chapter = $DB->get_record('block_mission_map_chapters', [
            'id' => $params['chapterid'],
            'blockid' => $params['blockid']
        ], '*', MUST_EXIST);

        // Prepare level data
        $leveldata = [
            'chapterid' => $params['chapterid'],
            'parentlevelid' => null, // Top-level mission
            'name' => $params['name'],
            'description' => $params['description'],
            'color' => $params['color'],
            'type' => $params['type'],
            'url' => $params['url'],
            'courseid' => $params['courseid'],
            'sectionid' => $params['sectionid'],
            'cmid' => $params['cmid'],
            'coordinates' => null, // Will be set by frontend
            'timemodified' => time()
        ];

        $isUpdate = !empty($params['levelid']);
        
        if ($isUpdate) {
            // Update existing level
            $leveldata['id'] = $params['levelid'];
            $success = $DB->update_record('block_mission_map_levels', $leveldata);
            
            if (!$success) {
                return [
                    'success' => false,
                    'message' => 'Failed to update mission'
                ];
            }
            
            return [
                'success' => true,
                'levelid' => $params['levelid'],
                'message' => 'Mission updated successfully'
            ];
        } else {
            // Create new level
            $leveldata['timecreated'] = time();
            $levelid = $DB->insert_record('block_mission_map_levels', $leveldata);

            if (!$levelid) {
                return [
                    'success' => false,
                    'message' => 'Failed to create mission'
                ];
            }

            return [
                'success' => true,
                'levelid' => $levelid,
                'message' => 'Mission created successfully'
            ];
        }
    }

    /**
     * Parameters for create.
     *
     * @return external_function_parameters
     */
    public static function create_parameters() {
        return new \external_function_parameters([
            'blockid' => new \external_value(PARAM_INT, 'Block instance ID'),
            'courseid' => new \external_value(PARAM_INT, 'Course ID'),
            'chapterid' => new \external_value(PARAM_INT, 'Chapter ID'),
            'name' => new \external_value(PARAM_TEXT, 'Mission name'),
            'description' => new \external_value(PARAM_TEXT, 'Mission description'),
            'type' => new \external_value(PARAM_INT, 'Mission type'),
            'color' => new \external_value(PARAM_TEXT, 'Mission color'),
            'url' => new \external_value(PARAM_TEXT, 'Mission URL', VALUE_DEFAULT, null),
            'cmid' => new \external_value(PARAM_INT, 'Course module ID', VALUE_DEFAULT, null),
            'sectionid' => new \external_value(PARAM_INT, 'Section ID', VALUE_DEFAULT, null),
            'levelid' => new \external_value(PARAM_INT, 'Level ID for update', VALUE_DEFAULT, null)
        ]);
    }

    /**
     * Return values for create.
     *
     * @return external_single_structure
     */
    public static function create_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'levelid' => new \external_value(PARAM_INT, 'Created level ID', VALUE_OPTIONAL),
            'message' => new \external_value(PARAM_TEXT, 'Response message')
        ]);
    }
}