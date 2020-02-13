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
 * Book read report page
 *
 * @package    mod_book
 * @copyright  2019 Willian Mano {@link http://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT); // Course Module ID.

$cm = get_coursemodule_from_id('book', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id'=>$cm->course], '*', MUST_EXIST);
$book = $DB->get_record('book', ['id'=>$cm->instance], '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/book:viewreports', $context);

$PAGE->set_url('/mod/book/reportread.php', ['id'=>$id]);
$PAGE->set_pagelayout('report');
$PAGE->set_title(get_string('reportreadtitle', 'mod_book'));
$PAGE->set_heading(get_string('reportreadtitle', 'mod_book'));

// =====================================================
// Book display HTML code
// =====================================================

$renderable = new \mod_book\output\report_read($course->id, $book->id);

$output = $PAGE->get_renderer('mod_book');

echo $output->header();

echo $output->render($renderable);

echo $output->footer();
