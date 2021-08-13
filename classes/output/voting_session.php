<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class voting_session implements renderable, templatable
{

    private $user;
    private $colleagues;
    private $session;
    private $options;

    public function __construct(
        $isOpen = true,
        $user,
        $colleagues,
        $session,
        $options,
        $totalizing = false,
        $tie = false,
        $completed = false,
        $winner = null
    ) {
        $this->isOpen = $isOpen;
        $this->user = $user;
        $this->colleagues = $colleagues;
        $this->session = $session;
        $this->options = $options;
        $this->totalizing = $totalizing;
        $this->tie = $tie;
        $this->completed = $completed;
        $this->winner = $winner;
    }

    public function export_for_template(renderer_base $output)
    {
        $data = new \stdClass();
        $data->session = $this->session;
        $data->session->isOpen = $this->isOpen;
        $data->session->user = $this->user;
        $data->session->colleagues = $this->colleagues;
        $data->session->options = $this->options;
        $data->session->totalizing = $this->totalizing;
        $data->session->tie = $this->tie;
        $data->session->completed = $this->completed;
        $data->session->winner_name = (!empty($this->winner->option)) ? $this->winner->option : null;
        $data->session->winner_votes = (!empty($this->winner->votes)) ? $this->winner->votes : null;

        return $data;
    }
}
