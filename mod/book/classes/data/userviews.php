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

namespace mod_book\data;

defined('MOODLE_INTERNAL') || die();

class userviews {

    /**
     * Returns the last book visited page ID.
     *
     * @param $book
     * @param $userid
     *
     * @return bool
     *
     * @throws \dml_exception
     */
    public static function get_last_book_userview($book, $userid) {
        global $DB;

        $sql = "SELECT uv.chapterid
                FROM {book_chapters_userviews} uv
                INNER JOIN {book_chapters} bc ON bc.id = uv.chapterid
                INNER JOIN {book} b ON b.id = bc.bookid
                WHERE bc.bookid = :bookid AND uv.userid = :userid AND bc.hidden = 0
                ORDER BY uv.timecreated DESC";

        $parameters = [
            'bookid' => $book->id,
            'userid' => $userid
        ];

        $record = $DB->get_record_sql($sql, $parameters, IGNORE_MULTIPLE);

        if ($record) {
            return $record->chapterid;
        }

        return false;
    }

    /**
     * Returns all chapters views of a user.
     *
     * @param $bookid
     * @param $userid
     *
     * @return array|bool
     *
     * @throws \dml_exception
     */
    public static function get_book_userviews($bookid, $userid) {
        global $DB;

        $userviewedchapterssql = "SELECT DISTINCT uv.chapterid
                                  FROM {book_chapters_userviews} uv
                                  INNER JOIN {book_chapters} bc ON bc.id = uv.chapterid
                                  INNER JOIN {book} b ON b.id = bc.bookid
                                  WHERE bc.bookid = :bookid AND uv.userid = :userid AND bc.hidden = 0";
        $parameters = [
            'bookid' => $bookid,
            'userid' => $userid
        ];

        $userviewedchapters = $DB->get_records_sql($userviewedchapterssql, $parameters);

        if ($userviewedchapters) {
            return $userviewedchapters;
        }

        return false;
    }

    /**
     * Returns the user progress in a book based on their userviews
     *
     * @param $bookid
     * @param $userid
     *
     * @return int
     *
     * @throws \dml_exception
     */
    public static function get_book_userview_progress($bookid, $userid) {
        global $DB;

        $chapters = $DB->get_records('book_chapters', ['bookid' => $bookid, 'hidden' => 0], 'id', 'id');

        $userviewedchapters = self::get_book_userviews($bookid, $userid);

        if (!$chapters || !$userviewedchapters) {
            return 0;
        }

        return (int)((count($userviewedchapters) / count($chapters)) * 100);
    }

    /**
     * Create a record of chapter user view.
     *
     * @param $chapterid
     * @param $userid
     *
     * @return \stdClass
     *
     * @throws \dml_exception
     */
    public static function create_userview($chapterid, $userid) {
        global $DB;

        $record = new \stdClass();
        $record->chapterid = $chapterid;
        $record->userid = $userid;
        $record->timecreated = time();

        $isertedid = $DB->insert_record('book_chapters_userviews', $record);

        $record->id = $isertedid;

        return $record;
    }

    /**
     * Returns all book userviews grouped and counted by page
     *
     * @param $bookid
     *
     * @return bool|array
     *
     * @throws \dml_exception
     */
    public static function get_book_total_userviews($bookid) {
        global $DB;

        $sql = "SELECT c.id, c.title, count(u.chapterid) as qtd
                FROM {book_chapters_userviews} u
                RIGHT JOIN {book_chapters} c ON c.id = u.chapterid
                WHERE c.bookid = :bookid AND c.hidden = 0
                GROUP BY c.id
                ORDER BY c.id";

        $records = $DB->get_records_sql($sql, ['bookid' => $bookid]);

        if (!$records) {
            return false;
        }

        $data['series'] = [];
        $data['labels'] = [];
        if ($records) {
            foreach ($records as $record) {
                $data['series'][] = $record->qtd;
                $data['labels'][] = $record->title;
            }
        }

        return $data;
    }

    /**
     * Returns all book unique userviews grouped by chapter and user
     *
     * @param $bookid
     *
     * @return array
     *
     * @throws \dml_exception
     */
    public static function get_book_total_unique_userviews($bookid) {
        global $DB;

        $sql = "SELECT c.id, count(book_chapters.userid) as qtd, c.title
                FROM {book_chapters} c
                LEFT JOIN (SELECT userid, chapterid
                FROM {book_chapters_userviews} u
                GROUP BY userid, chapterid) book_chapters ON book_chapters.chapterid = c.id
                WHERE c.bookid = :bookid
                GROUP BY c.id
                ORDER BY c.id";

        $records = $DB->get_records_sql($sql, ['bookid' => $bookid]);

        if (!$records) {
            return false;
        }

        $data['series'] = [];
        $data['labels'] = [];
        if ($records) {
            foreach ($records as $record) {
                $data['series'][] = $record->qtd;
                $data['labels'][] = $record->title;
            }
        }

        return $data;
    }

    /**
     * Return a general course students list.
     *
     * @param int $courseid The course id
     *
     * @return array
     *
     * @throws \dml_exception
     */
    public static function get_course_students($courseid) {
        global $DB;

        $userfields = \user_picture::fields('u', array('username'));

        $sql = "SELECT
                    DISTINCT $userfields
                FROM {user} u
                INNER JOIN {role_assignments} ra ON ra.userid = u.id
                INNER JOIN {context} c ON c.id = ra.contextid
                WHERE
                    ra.contextid = :contextid
                    AND ra.userid = u.id
                    AND ra.roleid = :roleid
                    AND c.instanceid = :courseinstanceid
                ORDER BY u.firstname ASC";

        $params['contextid'] = \context_course::instance($courseid)->id;
        $params['roleid'] = 5;
        $params['courseinstanceid'] = $courseid;

        return array_values($DB->get_records_sql($sql, $params));
    }
}