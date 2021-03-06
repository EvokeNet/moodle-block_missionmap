<?php
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
 * myprofile block rendrer
 *
 * @package    block_myprofile
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die;

use plugin_renderer_base;
use renderable;

/**
 * myprofile block renderer
 *
 * @package    block_myprofile
 * @copyright  2018 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base
{

    public function render_mission_map(map $mission_map)
    {
        return $this->render_from_template(
            'block_mission_map/map',
            $mission_map->export_for_template($this)
        );
    }

    public function render_blank(blank $blank)
    {
        return $this->render_from_template(
            'block_mission_map/blank',
            $blank->export_for_template($this)
        );
    }

    public function render_button(button $button)
    {
        return $this->render_from_template(
            'block_mission_map/button',
            $button->export_for_template($this)
        );
    }

    public function render_chapters(chapters $chapter)
    {
        return $this->render_from_template(
            'block_mission_map/chapters',
            $chapter->export_for_template($this)
        );
    }

    public function render_level(level $level)
    {
        return $this->render_from_template(
            'block_mission_map/level',
            $level->export_for_template($this)
        );
    }

    public function render_voting(voting $voting)
    {
        return $this->render_from_template(
            'block_mission_map/voting',
            $voting->export_for_template($this)
        );
    }

    public function render_voting_session(voting_session $voting_session)
    {
        return $this->render_from_template(
            'block_mission_map/voting_session',
            $voting_session->export_for_template($this)
        );
    }

    public function render_blockintohq(renderable $page) {
        return $this->render_from_template(
            'block_mission_map/blockintohq',
            $page->export_for_template($this)
        );
    }
}
