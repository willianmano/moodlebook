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

namespace mod_book\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use plugin_renderer_base;

/**
 * Renderer class.
 *
 * @package    mod_book
 * @copyright  2019 Willian Mano {@link http://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Renders the book graphs report page.
     *
     * @param renderable $renderable
     *
     * @return string HTML
     *
     * @throws \moodle_exception
     */
    protected function render_report_index(renderable $renderable) {
        $data = $renderable->export_for_template($this);

        return parent::render_from_template('mod_book/report_index', $data);
    }

    /**
     * Renders the page withe the users lecture progress in a book.
     *
     * @param renderable $renderable
     *
     * @return string HTML
     *
     * @throws \moodle_exception
     */
    protected function render_report_read(renderable $renderable) {
        $data = $renderable->export_for_template($this);

        return parent::render_from_template('mod_book/report_read', $data);
    }
}