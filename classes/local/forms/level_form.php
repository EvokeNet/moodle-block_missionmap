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
 * The mform for creating a Campaign Chapter
 *
 * @package    block_mission_map
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mission_map\local\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');

define("TYPE_URL", 1);
define("TYPE_SECTION", 2);
define("TYPE_VOTING", 3);
define("TYPE_SUBLEVEL", 4);

/**
 * The mform class for creating a chapter
 *
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class level_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customdata = null)
    {
        parent::__construct(null, $customdata, 'level',  '', ['class' => 'block_mission_map_level_form'], true, $formdata);
        $this->set_display_vertical();
    }

    /**
     * The form definition.
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function definition()
    {
        $types = [
            TYPE_URL => get_string('level_option_url', 'block_mission_map'),
            TYPE_SECTION => get_string('level_option_section', 'block_mission_map'),
            TYPE_VOTING => get_string('level_option_voting', 'block_mission_map'),
            TYPE_SUBLEVEL => get_string('level_option_sublevel', 'block_mission_map')
        ];

        $colors = [
            'blue' => get_string('level_color_blue', 'block_mission_map'),
            'green' => get_string('level_color_green', 'block_mission_map'),
            'orange' => get_string('level_color_orange', 'block_mission_map'),
            'red' => get_string('level_color_red', 'block_mission_map'),
            'yellow' => get_string('level_color_yellow', 'block_mission_map'),
            'white' => get_string('level_color_white', 'block_mission_map'),
            'black' => get_string('level_color_black', 'block_mission_map'),
            'purple' => get_string('level_color_purple', 'block_mission_map'),
            'pink' => get_string('level_color_pink', 'block_mission_map'),
            'brown' => get_string('level_color_brown', 'block_mission_map'),
            'gray' => get_string('level_color_gray', 'block_mission_map')
        ];

        $mform = $this->_form;

        $id = !(empty($this->_customdata['id'])) ? $this->_customdata['id'] : null;
        $chapterid = !(empty($this->_customdata['chapterid'])) ? $this->_customdata['chapterid'] : null;
        $courseid = !(empty($this->_customdata['courseid'])) ? $this->_customdata['courseid'] : null;
        $sectionid = !(empty($this->_customdata['sectionid'])) ? $this->_customdata['sectionid'] : null;
        $name = !(empty($this->_customdata['name'])) ? $this->_customdata['name'] : null;
        $description = !(empty($this->_customdata['description'])) ? $this->_customdata['description'] : null;
        $url = !(empty($this->_customdata['url'])) ? $this->_customdata['url'] : null;
        $option_sections = !(empty($this->_customdata['sections'])) ? $this->_customdata['sections'] : null;

        $option_sections = (array) $option_sections;

        $mform->addElement('hidden', 'id', $id);
        $mform->addElement('hidden', 'chapterid', $chapterid);
        $mform->addElement('hidden', 'courseid', $courseid);

        $mform->addElement('text', 'name', get_string('campaign_add_level_name', 'block_mission_map'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('text', 'description', get_string('campaign_add_level_description', 'block_mission_map'));
        $mform->addRule('description', get_string('required'), 'required', null, 'client');
        $mform->setType('description', PARAM_TEXT);

        $mform->addElement('select', 'color', get_string('campaign_add_level_color', 'block_mission_map'), $colors);
        $mform->addRule('color', get_string('required'), 'required', null, 'client');
        $mform->setType('color', PARAM_TEXT);

        $mform->addElement('select', 'sectionid', get_string('level_section', 'block_mission_map'), $option_sections);
        $mform->addRule('sectionid', get_string('required'), 'required', null, 'client');

        $mform->addElement('select', 'type', get_string('level_type', 'block_mission_map'), $types);
        $mform->setType('type', PARAM_INT);

        $mform->addElement('text', 'url', get_string('campaign_add_level_url', 'block_mission_map'));
        $mform->setType('url', PARAM_RAW);

        $mform->hideIf('url', 'type', 'neq', TYPE_URL);

        if ($name) {
            $mform->setDefault('name', $name);
        }

        if ($description) {
            $mform->setDefault('name', $name);
        }

        if ($sectionid) {
            $mform->setDefault('sectionid', $sectionid);
        }

        if ($url) {
            $mform->setDefault('url', $url);
        }
    }

    /**
     * A bit of custom validation for this form
     *
     * @param array $data An assoc array of field=>value
     * @param array $files An array of files
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
