<?php
namespace block_mission_map\external;

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External API for block_mission_map.
 *
 * @package    block_mission_map
 * @copyright  2025
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External API for block_mission_map chapter operations.
 */
class chapter extends \external_api {

    /**
     * Create or update a chapter.
     *
     * @param int $blockid Block instance ID
     * @param int $courseid Course ID
     * @param string $name Chapter name
     * @param int $haslock Whether chapter has lock
     * @param string $unlockingdate Unlocking date (optional)
     * @param int $chapterid Chapter ID for update (optional)
     * @return array
     */
    public static function create($blockid, $courseid, $name, $haslock, $unlockingdate = null, $chapterid = null) {
        global $DB, $USER;

        // Validate parameters
        $params = self::validate_parameters(self::create_parameters(), [
            'blockid' => $blockid,
            'courseid' => $courseid,
            'name' => $name,
            'has_lock' => $haslock,
            'unlocking_date' => $unlockingdate,
            'chapterid' => $chapterid
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

        // Prepare chapter data
        $chapterdata = [
            'blockid' => $params['blockid'],
            'courseid' => $params['courseid'],
            'name' => $params['name'],
            'has_lock' => $params['has_lock'],
            'unlocking_date' => $params['unlocking_date'] ? strtotime($params['unlocking_date']) : 0,
            'timemodified' => time()
        ];

        $isUpdate = !empty($params['chapterid']);
        
        if ($isUpdate) {
            // Update existing chapter
            $chapterdata['id'] = $params['chapterid'];
            $success = $DB->update_record('block_mission_map_chapters', $chapterdata);
            
            if (!$success) {
                return [
                    'success' => false,
                    'message' => 'Failed to update chapter'
                ];
            }
            
            return [
                'success' => true,
                'chapterid' => $params['chapterid'],
                'message' => 'Chapter updated successfully'
            ];
        } else {
            // Create new chapter
            $chapterdata['timecreated'] = time();
            $chapterid = $DB->insert_record('block_mission_map_chapters', $chapterdata);

            if (!$chapterid) {
                return [
                    'success' => false,
                    'message' => 'Failed to create chapter'
                ];
            }

            return [
                'success' => true,
                'chapterid' => $chapterid,
                'message' => 'Chapter created successfully'
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
            'name' => new \external_value(PARAM_TEXT, 'Chapter name'),
            'has_lock' => new \external_value(PARAM_INT, 'Whether chapter has lock'),
            'unlocking_date' => new \external_value(PARAM_TEXT, 'Unlocking date', VALUE_DEFAULT, null),
            'chapterid' => new \external_value(PARAM_INT, 'Chapter ID for update', VALUE_DEFAULT, null)
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
            'chapterid' => new \external_value(PARAM_INT, 'Created chapter ID', VALUE_OPTIONAL),
            'message' => new \external_value(PARAM_TEXT, 'Response message')
        ]);
    }

    /**
     * Delete a chapter and all its missions.
     *
     * @param int $blockid Block instance ID
     * @param int $courseid Course ID
     * @param int $chapterid Chapter ID to delete
     * @return array
     */
    public static function delete($blockid, $courseid, $chapterid) {
        global $DB;

        $params = self::validate_parameters(self::delete_parameters(), [
            'blockid' => $blockid,
            'courseid' => $courseid,
            'chapterid' => $chapterid
        ]);

        // Permission check.
        $context = \context_course::instance($params['courseid']);
        require_capability('block/mission_map:managechapters', $context);

        // Validate block belongs to course.
        $block = $DB->get_record('block_instances', [
            'id' => $params['blockid'],
            'blockname' => 'mission_map'
        ], '*', MUST_EXIST);
        $blockcontext = \context::instance_by_id($block->parentcontextid);
        if ($blockcontext->instanceid != $params['courseid']) {
            throw new \invalid_parameter_exception('Block does not belong to this course');
        }

        // Validate chapter.
        $chapter = $DB->get_record('block_mission_map_chapters', [
            'id' => $params['chapterid'],
            'blockid' => $params['blockid']
        ], '*', MUST_EXIST);

        // Cascade delete missions in this chapter.
        $DB->delete_records('block_mission_map_levels', ['chapterid' => $params['chapterid']]);

        // Delete the chapter itself.
        $success = $DB->delete_records('block_mission_map_chapters', ['id' => $params['chapterid']]);

        return [
            'success' => (bool)$success,
            'message' => $success ? 'Chapter deleted successfully' : 'Failed to delete chapter'
        ];
    }

    /**
     * Parameters for delete.
     * @return external_function_parameters
     */
    public static function delete_parameters() {
        return new \external_function_parameters([
            'blockid' => new \external_value(PARAM_INT, 'Block instance ID'),
            'courseid' => new \external_value(PARAM_INT, 'Course ID'),
            'chapterid' => new \external_value(PARAM_INT, 'Chapter ID to delete')
        ]);
    }

    /**
     * Returns structure for delete.
     * @return external_single_structure
     */
    public static function delete_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'message' => new \external_value(PARAM_TEXT, 'Response message')
        ]);
    }
}