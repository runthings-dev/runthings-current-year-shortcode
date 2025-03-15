<?php

namespace RunthingsCurrentYearShortcode;

if (!defined('WPINC')) {
    die;
}

class HelpTab
{
    /**
     * The actual shortcode tag that is registered
     * 
     * @var string
     */
    private $shortcode_tag;

    /**
     * Initialize the plugin and register hooks
     */
    public function __construct($shortcode_tag)
    {
        $this->shortcode_tag = $shortcode_tag;

        // Add notice to plugins page showing help link
        add_filter('plugin_row_meta', array($this, 'add_help_link'), 10, 2);

        // Add contextual help tab on plugins page
        add_action('admin_head', array($this, 'add_help_tab'));

        // Add JavaScript for help tab functionality on plugins page
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Enqueue admin scripts for the plugins page
     *
     * @param string $hook The current admin page
     */
    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'plugins.php') {
            return;
        }

        // Add inline script for help tab functionality
        $script = '
            function runthingsCYSOpenHelpTab() {
                // Scroll to top
                jQuery("html, body").animate({
                    scrollTop: 0
                }, 200);

                // Open help panel if not already open
                if (!jQuery("#contextual-help-wrap").is(":visible")) {
                    jQuery("#contextual-help-link").trigger("click");
                }

                // Small delay to ensure panel is open before selecting tab
                setTimeout(function() {
                    jQuery("#tab-link-runthings-year-shortcode-help a").trigger("click");
                }, 100);

                return false;
            }
        ';

        wp_add_inline_script('jquery', $script);
    }

    /**
     * Add a help link to the plugin's row in the plugins list
     *
     * @param array  $plugin_meta An array of the plugin's metadata
     * @param string $plugin_file Path to the plugin file relative to the plugins directory
     * @return array Modified plugin meta array
     */
    public function add_help_link($plugin_meta, $plugin_file)
    {
        if (plugin_basename(__FILE__) === $plugin_file) {
            $plugin_meta[] = sprintf(
                '<a href="#" onclick="return runthingsCYSOpenHelpTab();">%s</a>',
                esc_html__('Usage Examples', 'runthings-current-year-shortcode')
            );
        }

        return $plugin_meta;
    }

    /**
     * Add a contextual help tab on the plugins page
     *
     * @return void
     */
    public function add_help_tab()
    {
        $screen = get_current_screen();

        // Only add to plugins page
        if ($screen->id !== 'plugins') {
            return;
        }

        $screen->add_help_tab(array(
            'id'      => 'runthings-year-shortcode-help',
            'title'   => esc_html__('Year Shortcode Usage', 'runthings-current-year-shortcode'),
            'content' => $this->get_help_content(),
        ));
    }

    /**
     * Generate the HTML content for the help tab
     *
     * @return string HTML content for the help tab
     */
    private function get_help_content()
    {
        $current_year = current_time('Y');
        ob_start();
?>
        <style>
            .runthings-nowrap {
                white-space: nowrap;
            }
        </style>
        <h3><?php esc_html_e('Current Year Shortcode Usage', 'runthings-current-year-shortcode'); ?></h3>
        <p><?php
            printf(
                '%s <code>[%s]</code>',
                esc_html__('Active shortcode:', 'runthings-current-year-shortcode'),
                esc_html($this->shortcode_tag)
            ); ?></p>

        <table class="widefat" style="max-width: 600px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Example', 'runthings-current-year-shortcode'); ?></th>
                    <th><?php esc_html_e('Output', 'runthings-current-year-shortcode'); ?></th>
                    <th><?php esc_html_e('Description', 'runthings-current-year-shortcode'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[<?php echo esc_html($this->shortcode_tag); ?>]</code></td>
                    <td class='runthings-nowrap'><?php echo esc_html($current_year); ?></td>
                    <td><?php esc_html_e('Displays the current year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
                <tr>
                    <td><code>[<?php echo esc_html($this->shortcode_tag); ?> from="2020"]</code></td>
                    <td class='runthings-nowrap'><?php echo esc_html("2020-{$current_year}"); ?></td>
                    <td><?php esc_html_e('Year range from 2020 to current year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
                <tr>
                    <td><code>[<?php echo esc_html($this->shortcode_tag); ?> from="2020" mode="short"]</code></td>
                    <td class='runthings-nowrap'><?php echo esc_html('2020-' . substr($current_year, 2)); ?></td>
                    <td><?php esc_html_e('Year range with shortened end year', 'runthings-current-year-shortcode'); ?></td>
                </tr>
            </tbody>
        </table>
<?php
        return ob_get_clean();
    }
}
