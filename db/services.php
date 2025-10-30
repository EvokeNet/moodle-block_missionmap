<?php
// This file is part of the Mission Map block for Moodle - http://moodle.org/
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
 * Mission Map block services
 *
 * @package    block_mission_map
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'block_mission_map_create_chapter' => [
        'classname' => 'block_mission_map\external\chapter',
        'methodname' => 'create',
        'classpath' => 'blocks/mission_map/classes/external/chapter.php',
        'description' => 'Creates a new Chapter for a Campaign',
        'type' => 'write',
        'ajax' => true
    ],
    'block_mission_map_delete_chapter' => [
        'classname' => 'block_mission_map\external\chapter',
        'methodname' => 'delete',
        'classpath' => 'blocks/mission_map/classes/external/chapter.php',
        'description' => 'Deletes a chapter and all its missions',
        'type' => 'write',
        'ajax' => true
    ],
    'block_mission_map_get_course_activities' => [
        'classname' => 'block_mission_map\external\mission',
        'methodname' => 'get_course_activities',
        'classpath' => 'blocks/mission_map/classes/external/mission.php',
        'description' => 'Gets course activities grouped by section',
        'type' => 'read',
        'ajax' => true
    ],
    'block_mission_map_get_course_sections' => [
        'classname' => 'block_mission_map\external\mission',
        'methodname' => 'get_course_sections',
        'classpath' => 'blocks/mission_map/classes/external/mission.php',
        'description' => 'Gets course sections',
        'type' => 'read',
        'ajax' => true
    ],
    'block_mission_map_create_level' => [
        'classname' => 'block_mission_map\external\level',
        'methodname' => 'create',
        'classpath' => 'blocks/mission_map/classes/external/level.php',
        'description' => 'Creates a new Level for a Chapter',
        'type' => 'write',
        'ajax' => true
    ],
    'block_mission_map_delete_level' => [
        'classname' => 'block_mission_map\external\level',
        'methodname' => 'delete',
        'classpath' => 'blocks/mission_map/classes/external/level.php',
        'description' => 'Deletes a Level/Mission',
        'type' => 'write',
        'ajax' => true
    ],
    'block_mission_map_create_sublevel' => [
        'classname' => 'block_mission_map\external\sublevel',
        'methodname' => 'create',
        'classpath' => 'mission_map/classes/external/sublevel.php',
        'description' => 'Creates a new SubLevel for a Chapter',
        'type' => 'write',
        'ajax' => true
    ]
];

$services = array(
    'block_missionmap_edit_level_service' => array(
        'functions' => array('block_mission_map_edit_level'),
        'requiredcapability' => '',
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' =>  '',
        'downloadfiles' => 0,
        'uploadfiles'  => 0
    )
);
