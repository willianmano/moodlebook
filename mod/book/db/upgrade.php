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
 * Book module upgrade code
 *
 * @package    mod_book
 * @copyright  2009-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Book module upgrade task
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool always true
 */
function xmldb_book_upgrade($oldversion) {
    global $DB;

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

<<<<<<< HEAD
    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    $dbman = $DB->get_manager();

    if ($oldversion < 2019062000) {
        // Adds the new field to the user completion criteria.
        $table = new xmldb_table('book');
        $field = new xmldb_field('readpercent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'revision');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('backtolastpage', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1', 'customtitles');

        // Conditionally launch add field.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table book_chapters_userviews to be created.
        $table = new xmldb_table('book_chapters_userviews');

        // Adding fields to table book_chapters_userviews.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('chapterid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table book_chapters_userviews.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('chapterid', XMLDB_KEY_FOREIGN, array('chapterid'), 'book_chapters', array('id'));

        // Conditionally launch create table for book_chapters_userviews.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Book savepoint reached.
        upgrade_mod_savepoint(true, 2019062000, 'book');
    }

=======
>>>>>>> c988b19c5730921b788c596aab4ff7c4ec1e981e
    return true;
}
