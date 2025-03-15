<?php
/*
Plugin Name: Current Year Shortcode
Plugin URI: https://runthings.dev/wordpress-plugins/current-year-shortcode/
Description: Add a shortcode for displaying the current year as a range, usage: [year from="2025"] or [runthings_year from="2025"] if there's a conflict.
Version: 2.0.0
Author: runthingsdev
Author URI: https://runthings.dev/
Requires PHP: 7.4
Requires at least: 6.0
Tested up to: 6.8
Text Domain: runthings-current-year-shortcode
Domain Path: /languages
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Copyright 2022-2025 Matthew Harris

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

namespace RunthingsCurrentYearShortcode;

if (!defined('WPINC')) {
    die;
}

define('RUNTHINGS_CYS_FILE', __FILE__);
define('RUNTHINGS_CYS_DIR', plugin_dir_path(__FILE__));

require_once RUNTHINGS_CYS_DIR . 'lib/help-tab.php';

class CurrentYearShortcode
{
    /**
     * The default shortcode tag
     * 
     * @var string
     */
    private const DEFAULT_TAG = 'year';

    /**
     * The actual shortcode tag that is registered
     * 
     * @var string
     */
    private string $shortcode_tag;

    /**
     * Initialize the plugin and register hooks
     */
    public function __construct()
    {
        $this->shortcode_tag = self::DEFAULT_TAG;

        // Register shortcode on init with low priority (20)
        // This ensures we register after most other plugins, allowing us to check for conflicts
        add_action('init', array($this, 'register_shortcode'), 20);

        add_filter('plugin_row_meta', array($this, 'add_active_shortcode_notice'), 20, 2);

        new HelpTab($this->shortcode_tag);
    }

    /**
     * Register our shortcode on init
     * 
     * If 'year' shortcode already exists, we'll use 'runthings_year' instead
     * The shortcode tag can also be filtered using 'runthings_current_year_shortcode_tag'
     * 
     * @return void
     */
    public function register_shortcode(): void
    {
        $tag = shortcode_exists(self::DEFAULT_TAG) ? 'runthings_year' : self::DEFAULT_TAG;

        $this->shortcode_tag = apply_filters('runthings_current_year_shortcode_tag', $tag);

        add_shortcode($this->shortcode_tag, array($this, 'render'));
    }

    /**
     * Add a notice to the plugin's row in the plugins list showing the active shortcode
     *
     * @param array  $plugin_meta An array of the plugin's metadata
     * @param string $plugin_file Path to the plugin file relative to the plugins directory
     * @return array Modified plugin meta array
     */
    public function add_active_shortcode_notice(array $plugin_meta, string $plugin_file): array
    {
        if (plugin_basename(__FILE__) === $plugin_file) {
            $is_custom = $this->shortcode_tag !== self::DEFAULT_TAG;
            $style = $is_custom ? 'color: #dba617; font-weight: bold;' : '';

            $notice = sprintf(
                '<span style="%s">%s <code>[%s]</code></span>',
                esc_attr($style),
                esc_html__('Active shortcode:', 'runthings-current-year-shortcode'),
                esc_html($this->shortcode_tag)
            );
            $plugin_meta[] = $notice;
        }

        return $plugin_meta;
    }

    /**
     * Shortcode to display current year in a copyright statement.
     *
     * Set "from" to apply a range when "from" year has passed.
     * Set "mode" to "short" to abbreviate the end year when the century matches.
     *
     * @example
     * // assuming current year is 2025
     * [year] = 2025 (or [runthings_year] if 'year' is taken)
     * @example
     * // assuming current year is 2025
     * [year from="2025"] = 2025
     * @example
     * // assuming current year is 2025
     * [year from="1983"] = 1983-2025
     * @example
     * // assuming current year is 2025
     * [year from="2020" mode="short"] = 2020-25
     * @example
     * // assuming current year is 2025
     * [year from="1995" mode="short"] = 1995-2025
     *
     * @param array|string $atts Shortcode attributes
     * @return string The current year or year range specified
     */
    public function render($atts): string
    {
        $output = '';

        $atts = shortcode_atts(
            array(
                'from' => null,
                'mode' => 'long',
            ),
            $atts,
            'year'
        );

        $year = esc_html(current_time('Y'));
        $from = $atts['from'] !== null ? esc_html($atts['from']) : null;
        $mode = esc_html(strtolower($atts['mode']));

        $output = $year;

        if ($from !== null && $from < $year) {
            $formatted_year = $year;

            if ($mode === 'short' && substr($from, 0, 2) === substr($year, 0, 2)) {
                $formatted_year = substr($year, 2);
            }

            $output = esc_html($from . '-' . $formatted_year);
        }

        return $output;
    }
}

// Initialize the plugin
new CurrentYearShortcode();
