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
 * Book users reading report page.
 *
 * @package    mod_book
 * @copyright  2019 Willian Mano {@link http://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_book\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

class report_read implements renderable, templatable {

    protected $courseid;
    protected $bookid;

    /**
     * Constructor.
     *
     * @param $courseid
     * @param $bookid
     */
    public function __construct($courseid, $bookid) {
        $this->courseid = $courseid;
        $this->bookid = $bookid;
    }

    /**
     * Exports the data.
     *
     * @param renderer_base $output
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function export_for_template(renderer_base $output) {

        $coursestudents = \mod_book\data\userviews::get_course_students($this->courseid);

        $students = [];
        foreach ($coursestudents as $student) {
            $students[] = [
                'id' => $student->id,
                'fullname' => fullname($student),
                'userpicture' => $output->user_picture($student, array('size' => 24, 'alttext' => false)),
                'progress' => \mod_book\data\userviews::get_book_userview_progress($this->bookid, $student->id)
            ];
        }

        return [
            'students' => $students
        ];
    }
}