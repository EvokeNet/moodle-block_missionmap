<?php

namespace block_mission_map\external;

use context;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use block_mission_map\local\forms\chapter_form;

class chapter extends external_api
{
    /**
     * Create chapter parameters
     *
     * @return external_function_parameters
     */
    public static function create_parameters()
    {
        return new external_function_parameters([
            'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
            'jsonformdata' => new external_value(PARAM_RAW, 'The data from the chapter form, encoded as a json array')
        ]);
    }

    /**
     * Create chapter method
     *
     * @param int $contextid
     * @param string $jsonformdata
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     * @throws \moodle_exception
     */
    public static function create($contextid, $jsonformdata)
    {
        global $DB;

        // We always must pass webservice params through validate_parameters.
        $params = self::validate_parameters(
            self::create_parameters(),
            [
                'contextid' => $contextid,
                'jsonformdata' => $jsonformdata
            ]
        );

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        $mform = new chapter_form($data);

        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $data = new \stdClass();
        $data->name = $validateddata->name;
        $data->blockid = $validateddata->blockid;
        $data->courseid = $validateddata->courseid;
        $data->timecreated = time();
        $data->timemodified = time();

        $chapterid = $DB->insert_record('block_mission_map_chapters', $data);

        $data->id = $chapterid;

        return [
            'status' => 'ok',
            'message' => get_string('create_chapter_success', 'block_mission_map'),
            'data' => json_encode($data)
        ];
    }

    /**
     * Create chapter return fields
     *
     * @return external_single_structure
     */
    public static function create_returns()
    {
        return new external_single_structure(
            array(
                'status' => new external_value(PARAM_TEXT, 'Operation status'),
                'message' => new external_value(PARAM_RAW, 'Return message'),
                'data' => new external_value(PARAM_RAW, 'Return data')
            )
        );
    }
}
