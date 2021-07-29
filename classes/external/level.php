<?php

namespace block_mission_map\external;

use context;
use external_api;
use external_value;
use external_single_structure;
use external_function_parameters;
use block_mission_map\local\forms\level_form;
use context_system;

class level extends external_api
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
            ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]
        );

        $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        self::validate_context($context);

        $serialiseddata = json_decode($params['jsonformdata']);

        $data = [];
        parse_str($serialiseddata, $data);

        $mform = new level_form($data);
        $validateddata = $mform->get_data();

        if (!$validateddata) {
            throw new \moodle_exception('invalidformdata');
        }

        $data = new \stdClass();
        $data->name = $validateddata->name;
        $data->chapterid = $validateddata->chapterid;
        $data->parentlevelid = $validateddata->parentlevelid;
        $data->url = $validateddata->url;
        $data->has_sublevel = $validateddata->has_sublevel;
        $data->timecreated = time();
        $data->timemodified = time();

        $levelid = $DB->insert_record('block_mission_map_levels', $data);

        $data->id = $levelid;

        return [
            'status' => 'ok',
            'message' => get_string('create_level_success', 'block_mission_map'),
            'data' => json_encode($data)
        ];
    }

    /**
     * Create level return fields
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

    /**
     * Edit level parameters
     *
     * @return external_function_parameters
     */
    public static function edit_parameters()
    {
        // return new external_function_parameters([
        //     'contextid' => new external_value(PARAM_INT, 'The context id for the course module'),
        //     'jsonformdata' => new external_value(PARAM_RAW, 'The data from the level form, encoded as a json array')
        // ]);
        return new external_function_parameters([
            'level' => new external_single_structure([
                'id' => new external_value(PARAM_INT, 'id of level'),
                'name' => new external_value(PARAM_RAW, 'name of level'),
                'url' => new external_value(PARAM_RAW, 'URL of level'),
                'chapterid' => new external_value(PARAM_INT, 'chapterid of level'),
                'parentlevelid' => new external_value(PARAM_INT, 'parentlevelid of level'),
                'posx' => new external_value(PARAM_INT, 'x position of level'),
                'posy' => new external_value(PARAM_INT, 'y position of level')
            ])
        ]);
    }

    /**
     * Edit level method
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
    public static function edit($level)
    {
        global $DB;

        $params = self::validate_parameters(self::edit_parameters(), array('level' => $level));
        $context = context_system::instance();

        self::validate_context($context);

        $transaction = $DB->start_delegated_transaction();

        // Builds level object with ID
        $level = new \stdClass();
        $level->id = $params['level']->id;
        $level->chapterid = $params['level']->chapterid;
        !empty($params['level']->name) ? $level->name = $params['level']->name : '';
        !empty($params['level']->url) ? $level->url = $params['level']->url : '';
        $params['level']->coordinates = json_encode([$params['level']->posx, $params['level']->posy]);
        $level->timemodified = time();

        $level = $DB->update_record('block_mission_map_levels', $level);

        $transaction->allow_commit();

        return $level;

        // We always must pass webservice params through validate_parameters.
        // $params = self::validate_parameters(
        //     self::edit_parameters(),
        //     ['contextid' => $contextid, 'jsonformdata' => $jsonformdata]
        // );

        // $context = context::instance_by_id($params['contextid'], MUST_EXIST);

        // We always must call validate_context in a webservice.
        // self::validate_context($context);

        // $serialiseddata = json_decode($params['jsonformdata']);

        // $data = [];
        // parse_str($serialiseddata, $data);

        // $mform = new level_edit_form($data);
        // $validateddata = $mform->get_data();

        // if (!$validateddata) {
        //     throw new \moodle_exception('invalidformdata');
        // }

        // $data = new \stdClass();
        // $data->id = $validateddata->levelid;
        // $data->chapterid = $validateddata->chapterid;
        // $data->coordinates = json_encode([
        //     'x' => $validateddata->posx,
        //     'y' => $validateddata->posy
        // ]);
        // $data->timemodified = time();

        // $DB->update_record('block_mission_map_levels', $data);

        // return [
        //     'status' => 'ok',
        //     'message' => get_string('edit_level_success', 'block_mission_map'),
        //     'data' => json_encode($data)
        // ];
    }

    /**
     * Edit level return fields
     *
     * @return external_single_structure
     */
    public static function edit_returns()
    {
        // return new external_single_structure(
        //     array(
        //         'status' => new external_value(PARAM_TEXT, 'Operation status'),
        //         'message' => new external_value(PARAM_RAW, 'Return message'),
        //         'data' => new external_value(PARAM_RAW, 'Return data')
        //     )
        // );
        return new external_function_parameters([
            'level' => new external_single_structure([
                'id' => new external_value(PARAM_INT, VALUE_REQUIRED),
                'name' => new external_value(PARAM_RAW, VALUE_OPTIONAL),
                'url' => new external_value(PARAM_RAW, VALUE_OPTIONAL),
                'chapterid' => new external_value(PARAM_INT, VALUE_REQUIRED),
                'posx' => new external_value(PARAM_INT, VALUE_REQUIRED),
                'posy' => new external_value(PARAM_INT, VALUE_REQUIRED)
            ])
        ]);
    }
}
