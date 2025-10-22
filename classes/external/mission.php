<?php
namespace block_mission_map\external;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External API for block_mission_map mission operations.
 */
class mission extends \external_api {

    /**
     * Get course activities grouped by section.
     *
     * @param int $courseid Course ID
     * @return array
     */
    public static function get_course_activities($courseid) {
        global $DB, $COURSE;

        // Validate parameters
        $params = self::validate_parameters(self::get_course_activities_parameters(), [
            'courseid' => $courseid
        ]);

        // Check permissions
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('block/mission_map:managechapters', $context);

        // Get course sections
        $sections = $DB->get_records('course_sections', ['course' => $params['courseid']], 'section ASC');
        
        $activities = [];
        foreach ($sections as $section) {
            if ($section->section == 0) continue; // Skip general section
            
            $sectionActivities = [];
            
            // Get course modules for this section
            $cms = $DB->get_records('course_modules', ['section' => $section->id, 'deletioninprogress' => 0]);
            
            foreach ($cms as $cm) {
                $module = $DB->get_record('modules', ['id' => $cm->module]);
                if (!$module) continue;
                
                // Get module instance
                $instance = $DB->get_record($module->name, ['id' => $cm->instance]);
                if (!$instance) continue;
                
                $sectionActivities[] = [
                    'id' => $cm->id,
                    'name' => $instance->name,
                    'type' => $module->name,
                    'url' => (new \moodle_url('/mod/' . $module->name . '/view.php', ['id' => $cm->id]))->out()
                ];
            }
            
            if (!empty($sectionActivities)) {
                $activities[] = [
                    'section_id' => $section->id,
                    'section_name' => get_section_name($params['courseid'], $section->section),
                    'activities' => $sectionActivities
                ];
            }
        }

        return [
            'success' => true,
            'activities' => $activities
        ];
    }

    /**
     * Get course sections.
     *
     * @param int $courseid Course ID
     * @return array
     */
    public static function get_course_sections($courseid) {
        global $DB;

        // Validate parameters
        $params = self::validate_parameters(self::get_course_sections_parameters(), [
            'courseid' => $courseid
        ]);

        // Check permissions
        $context = \context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('block/mission_map:managechapters', $context);

        // Get course sections
        $sections = $DB->get_records('course_sections', ['course' => $params['courseid']], 'section ASC');
        
        $sectionList = [];
        foreach ($sections as $section) {
            if ($section->section == 0) continue; // Skip general section
            
            // Generate section name
            $sectionName = 'Section ' . $section->section;
            
            $sectionList[] = [
                'id' => $section->id,
                'section' => $section->section,
                'name' => $sectionName,
                'url' => (new \moodle_url('/course/view.php', ['id' => $params['courseid'], 'section' => $section->section]))->out()
            ];
        }

        return [
            'success' => true,
            'sections' => $sectionList
        ];
    }

    /**
     * Parameters for get_course_activities.
     *
     * @return external_function_parameters
     */
    public static function get_course_activities_parameters() {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'Course ID')
        ]);
    }

    /**
     * Return values for get_course_activities.
     *
     * @return external_single_structure
     */
    public static function get_course_activities_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'activities' => new \external_multiple_structure(
                new \external_single_structure([
                    'section_id' => new \external_value(PARAM_INT, 'Section ID'),
                    'section_name' => new \external_value(PARAM_TEXT, 'Section name'),
                    'activities' => new \external_multiple_structure(
                        new \external_single_structure([
                            'id' => new \external_value(PARAM_INT, 'Course module ID'),
                            'name' => new \external_value(PARAM_TEXT, 'Activity name'),
                            'type' => new \external_value(PARAM_TEXT, 'Activity type'),
                            'url' => new \external_value(PARAM_URL, 'Activity URL')
                        ])
                    )
                ])
            )
        ]);
    }

    /**
     * Parameters for get_course_sections.
     *
     * @return external_function_parameters
     */
    public static function get_course_sections_parameters() {
        return new \external_function_parameters([
            'courseid' => new \external_value(PARAM_INT, 'Course ID')
        ]);
    }

    /**
     * Return values for get_course_sections.
     *
     * @return external_single_structure
     */
    public static function get_course_sections_returns() {
        return new \external_single_structure([
            'success' => new \external_value(PARAM_BOOL, 'Whether the operation was successful'),
            'sections' => new \external_multiple_structure(
                new \external_single_structure([
                    'id' => new \external_value(PARAM_INT, 'Section ID'),
                    'section' => new \external_value(PARAM_INT, 'Section number'),
                    'name' => new \external_value(PARAM_TEXT, 'Section name'),
                    'url' => new \external_value(PARAM_URL, 'Section URL')
                ])
            )
        ]);
    }
}
