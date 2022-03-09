<?php

/**
 * Upgrade file.
 *
 * @package    block_mission_map
 * @copyright   2021 World Bank Group <https://worldbank.org>
 * @author      Marcos Soledade <msoledade@quanti.ca>
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Upgrade code for the Mission Map block.
 *
 * @param int $oldversion - the version we are upgrading from.
 *
 * @return bool result
 *
 * @throws ddl_exception
 * @throws downgrade_exception
 * @throws upgrade_exception
 */
function xmldb_block_mission_map_upgrade($oldversion)
{
    global $DB;

    if ($oldversion < 202109020000) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('block_mission_map_votings');
        if ($dbman->table_exists($table)) {
            $description = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'levelid');

            $dbman->add_field($table, $description);
        }
        upgrade_plugin_savepoint(true, 202109020000, 'block', 'mission_map');
    }

    if ($oldversion < 202109081400) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('block_mission_map_options');
        if ($dbman->table_exists($table)) {
            $description = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'votingid');

            $dbman->add_field($table, $description);
        }
        upgrade_plugin_savepoint(true, 202109081400, 'block', 'mission_map');
    }

    if ($oldversion < 202203082300) {
        $dbman = $DB->get_manager();

        $table = new xmldb_table('block_mission_map_levels');
        if ($dbman->table_exists($table)) {
            $description = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');

            $dbman->add_field($table, $description);
        }
        upgrade_plugin_savepoint(true, 202203082300, 'block', 'mission_map');
    }

    return true;
}
