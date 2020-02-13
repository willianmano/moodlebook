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
 * Book views report index page.
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

class report_index implements renderable, templatable {

    protected $bookid;

    /**
     * Constructor.
     *
     * @param $bookid
     */
    public function __construct($bookid) {
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

        $totaluserviews = \mod_book\data\userviews::get_book_total_userviews($this->bookid);
        $uniqueuserviews = \mod_book\data\userviews::get_book_total_unique_userviews($this->bookid);

        $chart = new \core\chart_line();
        $chart->set_smooth(true);

        $reportchart = false;
        if ($totaluserviews && $uniqueuserviews) {
            $mostvisitedserie = new \core\chart_series(get_string('totalviews', 'mod_book'), $totaluserviews['series']);
            $uniqueuserviewserie = new \core\chart_series(get_string('uniqueuserviews', 'mod_book'), $uniqueuserviews['series']);
            $chart->add_series($mostvisitedserie);
            $chart->add_series($uniqueuserviewserie);
            $chart->set_labels($totaluserviews['labels']);

            $reportchart = $output->render_chart($chart);
        }

        return [
            'reportchart' => $reportchart
        ];
    }
}