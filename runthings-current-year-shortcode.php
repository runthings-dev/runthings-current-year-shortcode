<?php
/*
Plugin Name: Current Year Shortcode
Plugin URI: https://runthings.dev
Description: Add a shortcode for displaying the current year as a range, usage: [year from="2023"]
Version: 1.2.0
Author: Matthew Harris, runthings.dev
Author URI: https://runthings.dev/
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright 2022-2023 Matthew Harris

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('WPINC')) {
    die;
}

/**
 * Shortcode to display current year in a copyright statement.
 *
 * Set "from" to apply a range, after "from" year has passed.
 *
 * @example
 * // assuming current year is 2023
 * [year] = 2023
 * @example
 * // assuming current year is 2023
 * [year from="2022"] = 2023
 * @example
 * // assuming current year is 2023
 * [year from="1983"] = 1983-2023
 *
 * @return {string} the current year or year range specified
 */
function rtp_copyright_year($atts)
{
    $atts = shortcode_atts(
        array(
            'from' => null,
        ),
        $atts,
        'year'
    );
    $year = date('Y');
    $from = $atts['from'];
    if ($from != null && $from < $year) {
        return "$from-$year";
    }
    return $year;
}
add_shortcode('year', 'rtp_copyright_year');
// END shortcode to add current year feature
