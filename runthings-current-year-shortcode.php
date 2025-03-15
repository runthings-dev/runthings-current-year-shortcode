<?php
/*
Plugin Name: Current Year Shortcode
Plugin URI: https://runthings.dev/wordpress-plugins/current-year-shortcode/
Description: Add a shortcode for displaying the current year as a range, usage: [year from="2025"] or [runthings_year from="2025"] if there's a conflict.
Version: 1.3.0
Author: runthingsdev
Author URI: https://runthings.dev/
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

class CurrentYearShortcode
{
    /**
     * The default shortcode tag
     * 
     * @var string
     * @readonly
     */
    private $default_tag = 'year';

    /**
     * The actual shortcode tag that is registered
     * 
     * @var string
     */
    private $shortcode_tag;

    /**
     * Initialize the plugin and register hooks
     */
    public function __construct()
    {
        $this->shortcode_tag = $this->default_tag;

        // Register shortcode on init with low priority (20)
        // This ensures we register after most other plugins, allowing us to check for conflicts
        add_action('init', array($this, 'register_shortcode'), 20);

        // Add notice to plugins page showing help link / active shortcode
        add_filter('plugin_row_meta', array($this, 'add_help_link'), 10, 2);
        add_filter('plugin_row_meta', array($this, 'add_shortcode_notice'), 20, 2);

        // Add contextual help tab on plugins page
        add_action('admin_head', array($this, 'add_help_tab'));
    }

    /**
     * Register our shortcode on init
     * 
     * If 'year' shortcode already exists, we'll use 'runthings_year' instead
     * The shortcode tag can also be filtered using 'runthings_current_year_shortcode_tag'
     */
    public function register_shortcode()
    {
        $tag = shortcode_exists($this->default_tag) ? 'runthings_year' : $this->default_tag;

        $this->shortcode_tag = apply_filters('runthings_current_year_shortcode_tag', $tag);

        add_shortcode($this->shortcode_tag, array($this, 'render'));
    }

    public function add_help_link($plugin_meta, $plugin_file)
    {
        if (plugin_basename(__FILE__) === $plugin_file) {
            // Add help link that opens help tab via JavaScript
            $plugin_meta[] = sprintf(
                '<a href="#" onclick="jQuery(\'#contextual-help-link\').trigger(\'click\'); return false;">%s</a>',
                __('Usage Examples', 'runthings-current-year-shortcode')
            );
        }

        return $plugin_meta;
    }

    /**
     * Add a notice to the plugin's row in the plugins list showing the active shortcode
     *
     * @param array  $plugin_meta An array of the plugin's metadata
     * @param string $plugin_file Path to the plugin file relative to the plugins directory
     * @return array
     */
    public function add_shortcode_notice($plugin_meta, $plugin_file)
    {
        if (plugin_basename(__FILE__) === $plugin_file) {
            $is_custom = $this->shortcode_tag !== $this->default_tag;
            $style = $is_custom ? 'color: #dba617; font-weight: bold;' : '';

            $notice = sprintf(
                '<span style="%s">%s <code>[%s]</code></span>',
                $style,
                __('Active shortcode:', 'runthings-current-year-shortcode'),
                $this->shortcode_tag
            );
            $plugin_meta[] = $notice;
        }

        return $plugin_meta;
    }

    public function add_help_tab()
    {
        $screen = get_current_screen();

        // Only add to plugins page
        if ($screen->id !== 'plugins') {
            return;
        }

        $screen->add_help_tab(array(
            'id'      => 'runthings-year-shortcode-help',
            'title'   => __('Year Shortcode Usage', 'runthings-current-year-shortcode'),
            'content' => $this->get_help_content(),
        ));
    }

    private function get_help_content()
    {
        $current_year = date('Y');
        ob_start();
?>
        <style>
            .runthings-nowrap {
                white-space: nowrap;
            }
        </style>
        <h3><?php _e('Current Year Shortcode Usage', 'runthings-current-year-shortcode'); ?></h3>
        <p><?php printf(__('Active shortcode: <code>[%s]</code>', 'runthings-current-year-shortcode'), $this->shortcode_tag); ?></p>

        <table class="widefat" style="max-width: 600px;">
            <thead>
                <tr>
                    <th><?php _e('Example', 'runthings-current-year-shortcode'); ?></th>
                    <th><?php _e('Output', 'runthings-current-year-shortcode'); ?></th>
                    <th><?php _e('Description', 'runthings-current-year-shortcode'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[<?php echo $this->shortcode_tag; ?>]</code></td>
                    <td class='runthings-nowrap'><?php echo $current_year; ?></td>
                    <td><?php _e('Displays the current year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
                <tr>
                    <td><code>[<?php echo $this->shortcode_tag; ?> from="2020"]</code></td>
                    <td class='runthings-nowrap'>2020-<?php echo $current_year; ?></td>
                    <td><?php _e('Year range from 2020 to current year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
                <tr>
                    <td><code>[<?php echo $this->shortcode_tag; ?> from="2020" mode="short"]</code></td>
                    <td class='runthings-nowrap'>2020-<?php echo substr($current_year, 2); ?></td>
                    <td><?php _e('Year range with shortened end year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
            </tbody>
        </table>
<?php
        return ob_get_clean();
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
     * @param array $atts Shortcode attributes
     * @return string The current year or year range specified
     */
    public function render($atts)
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

        $year = current_time('Y');
        $from = $atts['from'];
        $mode = strtolower($atts['mode']);

        $output = $year;

        if ($from !== null && $from < $year) {
            $formatted_year = $year;

            if ($mode === 'short' && substr($from, 0, 2) === substr($year, 0, 2)) {
                $formatted_year = substr($year, 2);
            }

            $output = "$from-$formatted_year";
        }

        return $output;
    }
}

// Initialize the plugin
new CurrentYearShortcode();
