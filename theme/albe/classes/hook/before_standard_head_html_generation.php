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

namespace theme_albe\hook;

/**
 * Theme Boost Union - Hook: Allows plugins to add any elements to the page <head> html tag.
 *
 * @package    theme_boost_union
 * @copyright  2024 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class before_standard_head_html_generation {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function callback(\core\hook\output\before_standard_head_html_generation $hook): void {
        $hook->add_html(<<< EOT
<script type="module" src="https://unpkg.com/@zanichelli/albe-web-components/dist/web-components-library/web-components-library.esm.js"></script>
<script type="module" src="https://unpkg.com/@zanichelli/idp-login-topbar"></script>
<script type="module" src="https://unpkg.com/@zanichelli/zanichelli-footer"></script>
<link type="text/css" rel="stylesheet" href="https://unpkg.com/@zanichelli/albe-web-components/www/build/web-components-library.css" />
EOT);
    }
}