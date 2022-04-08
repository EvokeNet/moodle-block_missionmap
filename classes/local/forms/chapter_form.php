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
class chapter_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customdata = null)
    {
        parent::__construct(null, $customdata, 'chapter',  '', ['class' => 'block_mission_map_chapter_form'], true, $formdata);
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
        $blockid = !(empty($this->_customdata['blockid'])) ? $this->_customdata['blockid'] : null;
        $courseid = !(empty($this->_customdata['courseid'])) ? $this->_customdata['courseid'] : null;
        $name = !(empty($this->_customdata['name'])) ? $this->_customdata['name'] : null;
        $map_image = !(empty($this->_customdata['map_image'])) ? $this->_customdata['map_image'] : null;
        $has_lock = !(empty($this->_customdata['has_lock'])) ? $this->_customdata['has_lock'] : null;
        $unlocking_date = !(empty($this->_customdata['unlocking_date'])) ? $this->_customdata['unlocking_date'] : null;

        $mform->addElement('hidden', 'id', $id);
        $mform->addElement('hidden', 'blockid', $blockid);
        $mform->addElement('hidden', 'courseid', $courseid);

        $mform->addElement('text', 'name', get_string('campaign_add_chapter', 'block_mission_map'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('filepicker', 'map_image', get_string('file'), null, array('maxbytes' => 5000, 'accepted_types' => ['jpg', 'png', 'gif', 'bmp', 'jpeg']));

        $mform->addElement('selectyesno', 'has_lock', get_string('campaign_locked_chapter', 'block_mission_map'));
        $mform->addRule('has_lock', get_string('required'), 'required', null, 'client');
        $mform->setType('has_lock', PARAM_TEXT);

        $mform->addElement('date_time_selector', 'unlocking_date', get_string('campaign_unlock_chapter', 'block_mission_map'));

        $mform->hideIf('unlocking_date', 'has_lock', 'eq', false);

        if ($name) {
            $mform->setDefault('name', $name);
        }

        if ($map_image) {
            $mform->setDefault('map_image', $map_image);
        }

        if ($has_lock) {
            $mform->getElement('has_lock')->setSelected($has_lock);
        }

        if ($unlocking_date) {
            $mform->setDefault('unlocking_date', $unlocking_date);
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
