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

use html_writer;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/formslib.php');
require_once('lib.php');

/**
 * The mform class for creating a chapter
 *
 * @copyright  2021 onwards Marcos Soledade {@link https://msoledade.com.br}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vote_form extends \moodleform
{

    /**
     * Class constructor.
     *
     * @param array $formdata
     * @param array $customodata
     */
    public function __construct($customdata = null)
    {
        $this->chapterid = $customdata['chapterid'];
        $this->levelid = $customdata['levelid'];
        $this->votingid = $customdata['votingid'];
        $this->userid = $customdata['userid'];
        $this->optionid = $customdata['optionid'];
        $this->name = $customdata['name'];
        $this->description = isset($customdata['description']) ? $customdata['description'] : null;
        $this->deadline = $customdata['deadline'];
        $this->iteration = $customdata['iteration'];
        parent::__construct(null, $customdata, 'chapter',  '', ['class' => 'option'], true);
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

        $mform->addElement('hidden', 'chapterid', $this->chapterid);
        $mform->setType('chapterid', PARAM_INT);

        $mform->addElement('hidden', 'levelid', $this->levelid);
        $mform->setType('levelid', PARAM_INT);

        $mform->addElement('hidden', 'votingid', $this->votingid);
        $mform->setType('votingid', PARAM_INT);

        $mform->addElement('hidden', 'userid', $this->userid);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'optionid', $this->optionid);
        $mform->setType('optionid', PARAM_INT);

        $mform->addElement('html', '<div class="title">');
        $mform->addElement('html', $this->name);
        $mform->addElement('html', '</div>');

        $mform->addElement('html', "<div class='vote_image image_{$this->iteration}'></div>");

        if (isset($this->description)) {
            $mform->addElement('html', '<div class="option_description">');
            $mform->addElement('html', $this->description);
            $mform->addElement('html', '</div>');
        }

        $mform->addElement('submit', 'votesubmit', get_string('vote_save', 'block_mission_map'), ['class' => 'vote_btn']);
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
