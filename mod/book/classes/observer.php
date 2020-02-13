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
 * The mod_book event observer.
 *
 * @package    mod_book
 * @copyright  2019 Willian Mano {@link http://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_book;

defined('MOODLE_INTERNAL') || die();

use completion_info;
use mod_book\data\userviews;
use mod_book\event\course_module_viewed;
use mod_book\event\chapter_viewed;

/**
 * The mod_book event observer class.
 *
 * @package    mod_book
 * @since      Moodle 3.6
 * @copyright  2019 Willian Mano {@link http://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Listener of mod_book course module viewed event.
     *
     * @param event\course_module_viewed $event
     *
     * @throws \dml_exception
     */
    public static function module_viewed(course_module_viewed $event) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $event->courseid], '*', MUST_EXIST);
        $cm = $DB->get_record('course_modules', ['id' => $event->contextinstanceid], '*', MUST_EXIST);

        $completion = new completion_info($course);

        if ($completion->is_enabled($cm)) {
            $completion->set_module_viewed($cm);
        }
    }

    /**
     * Listener of mod_book chapter viewed event.
     *
     * @param event\chapter_viewed $event
     *
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public static function chapter_viewed(chapter_viewed $event) {
        global $DB;

        $course = $DB->get_record('course', ['id' => $event->courseid], '*', MUST_EXIST);
        $cm = $DB->get_record('course_modules', ['id' => $event->contextinstanceid], '*', MUST_EXIST);
        $book = $DB->get_record('book', ['id' => $cm->instance], '*', MUST_EXIST);

        $completion = new completion_info($course);

        if ($completion->is_enabled($cm) && $book->completionview != 0) {
            userviews::create_userview($event->objectid, $event->userid);

            $status = book_get_completion_state($course, $cm, $event->userid, COMPLETION_AND);

            $complete = COMPLETION_INCOMPLETE;
            if ($status === true) {
                $complete = COMPLETION_COMPLETE;
            }

            $completion->update_state($cm, $complete, $event->userid);
        }
    }
}