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
    private string $shortcode_tag;

    /**
     * Initialize the plugin and register hooks
     * 
     * @param string $shortcode_tag The shortcode tag to use in examples
     */
    public function __construct(string $shortcode_tag)
    {
        $this->shortcode_tag = $shortcode_tag;

        // Add notice to plugins page showing help link
        add_filter('plugin_row_meta', array($this, 'add_help_link'), 10, 2);

        // Add contextual help tab on plugins page
        add_action('admin_head', array($this, 'add_help_tab'));

        // Add JavaScript / styles for help tab functionality on plugins page
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Enqueue admin scripts for the plugins page
     *
     * @param string $hook The current admin page
     * @return void
     */
    public function enqueue_admin_scripts(string $hook): void
    {
        if ($hook !== 'plugins.php') {
            return;
        }

        // Enqueue admin styles
        wp_enqueue_style(
            'runthings-current-year-shortcode-admin',
            RUNTHINGS_CYS_URL . 'assets/css/admin-styles.css',
            [],
            RUNTHINGS_CYS_VERSION
        );

        // Enqueue admin script
        wp_enqueue_script(
            'runthings-current-year-shortcode-admin',
            RUNTHINGS_CYS_URL . 'assets/js/admin-scripts.js',
            ['jquery'],
            RUNTHINGS_CYS_VERSION,
            true
        );
    }

    /**
     * Add a help link to the plugin's row in the plugins list
     *
     * @param array  $plugin_meta An array of the plugin's metadata
     * @param string $plugin_file Path to the plugin file relative to the plugins directory
     * @return array Modified plugin meta array
     */
    public function add_help_link(array $plugin_meta, string $plugin_file): array
    {
        if (plugin_basename(RUNTHINGS_CYS_FILE) === $plugin_file) {
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
    public function add_help_tab(): void
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
    private function get_help_content(): string
    {
        $current_year = current_time('Y');
        ob_start();
?>
        <h3><?php esc_html_e('Current Year Shortcode Usage', 'runthings-current-year-shortcode'); ?></h3>
        <p><?php
            printf(
                '%s <code>[%s]</code>',
                esc_html__('Active shortcode:', 'runthings-current-year-shortcode'),
                esc_html($this->shortcode_tag)
            ); ?></p>

        <table class="widefat" style="max-width: 1000px;">
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
