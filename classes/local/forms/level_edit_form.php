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
class level_edit_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($formdata, $customdata = null)
    {
        parent::__construct(null, $customdata, 'level',  '', ['class' => 'block_mission_map_level_edit_form'], true, $formdata);
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
        $posx = !(empty($this->_customdata['posx'])) ? $this->_customdata['posx'] : null;
        $posy = !(empty($this->_customdata['posy'])) ? $this->_customdata['posy'] : null;

        $mform->addElement('hidden', 'id', $id, ['id' => 'levelid']);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid', $chapterid, ['id' => 'chapterid']);
        $mform->setType('chapterid', PARAM_INT);

        $mform->addElement('hidden', 'posx', $posx, ['id' => 'posx']);
        $mform->setType('posx', PARAM_INT);

        $mform->addElement('hidden', 'posy', $posy, ['id' => 'posy']);
        $mform->setType('posy', PARAM_INT);
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
