<?php

namespace block_mission_map\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderable;
use renderer_base;
use templatable;

class voting_session implements renderable, templatable
{

    private $isOpen;
    private $user;
    private $colleagues;
    private $session;
    private $options;
    private $votes;
    private $totalizing;
    private $tie;
    private $completed;

    public function __construct(
        $isOpen = true,
        $user,
        $colleagues,
        $session,
        $options,
        $votes = null,
        $totalizing = false,
        $tie = false,
        $completed = false
    ) {
        $this->isOpen = $isOpen;
        $this->user = $user;
        $this->colleagues = $colleagues;
        $this->session = $session;
        $this->options = array_values($options);
        $this->votes = $votes;
        $this->totalizing = $totalizing;
        $this->tie = $tie;
        $this->completed = $completed;
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

        return $data;
    }
}
