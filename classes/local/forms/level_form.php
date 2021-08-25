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
        $mform = $this->_form;

        $id = !(empty($this->_customdata['id'])) ? $this->_customdata['id'] : null;
        $chapterid = !(empty($this->_customdata['chapterid'])) ? $this->_customdata['chapterid'] : null;
        $name = !(empty($this->_customdata['name'])) ? $this->_customdata['name'] : null;
        $url = !(empty($this->_customdata['url'])) ? $this->_customdata['url'] : null;
        $has_sublevel = !(empty($this->_customdata['has_sublevel'])) ? $this->_customdata['has_sublevel'] : null;
        $has_voting = !(empty($this->_customdata['has_voting'])) ? $this->_customdata['has_voting'] : null;

        $mform->addElement('hidden', 'id', $id);
        $mform->addElement('hidden', 'chapterid', $chapterid);

        $mform->addElement('text', 'name', get_string('campaign_add_level_name', 'block_mission_map'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('selectyesno', 'has_sublevel', get_string('campaign_add_level_hassublevel', 'block_mission_map'));
        $mform->addRule('has_sublevel', get_string('required'), 'required', null, 'client');
        $mform->setType('has_sublevel', PARAM_BOOL);

        $mform->addElement('text', 'url', get_string('campaign_add_level_url', 'block_mission_map'));
        $mform->setType('url', PARAM_RAW);

        $mform->addElement('selectyesno', 'has_voting', get_string('campaign_add_level_hasvoting', 'block_mission_map'));
        $mform->setType('has_voting', PARAM_BOOL);

        $mform->disabledIf('has_voting', 'has_sublevel', 'eq', true);
        $mform->hideIf('url', 'has_sublevel', 'eq', false);

        if ($name) {
            $mform->setDefault('name', $name);
        }

        if ($url) {
            $mform->setDefault('url', $url);
        }

        if ($has_sublevel) {
            $mform->setDefault('has_sublevel', $has_sublevel);
        }

        if ($has_voting) {
            $mform->setDefault('has_voting', $has_voting);
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
