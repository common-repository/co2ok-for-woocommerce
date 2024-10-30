<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and API class.
 */
class South_Pole_Climate_Click_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * The API class of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $api_curl The API class of this plugin.
     */
    private $api_curl;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->api_curl = new South_Pole_Climate_Click_API();
    }

    /**
     * Register the css for the admin side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/south-pole-climate-click-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Redirect to setting page after activation
     *
     * @since    1.0.0
     */
    public function add_configuration_tab($settings_tabs)
    {
        $settings_tabs['climate_click_settings_tabs'] = __('Climate Click by South Pole', 'south-pole-climate-click');
        return $settings_tabs;
    }

    public function activation_redirect()
    {
        if (get_option('redirect_after_activation_option', false)) {
            delete_option('redirect_after_activation_option');
            exit(wp_redirect(admin_url('admin.php?page=wc-settings&tab=climate_click_settings_tabs')));
        }
    }

    /**
     * Add custom tab in woocommerce setting tab
     *
     * @since    1.0.0
     */
    public function climate_click_settings_tab()
    {
        woocommerce_admin_fields($this->climate_click_get_settings());
    }

    /**
     * Add configuration fields
     *
     * @since    1.0.0
     */
    public function climate_click_get_settings()
    {
        $climateClickApiKey = get_option('climate_click_api_key');
        /* Get Dashboard API URL */
        if (!empty($climateClickApiKey)) {
            $url = API_URL . "GetDashboardUrl";
            $headers = array('X-Api-Key: ' . $climateClickApiKey, 'accept: application/json');

            $response = $this->api_curl->api_response($url, $headers);
            if ($response) {
                $result_array = json_decode($response, true);
                if (!empty($result_array['dashboardUrl'])) {
                    echo '<div class="dashboard-url-block"><a target="_blank" href="' . $result_array['dashboardUrl'] . '" class="dashboard-url">' . __(
                            'Climate Dashboard',
                            'south-pole-climate-click'
                        ) . '</a></div>';
                }
            }
        }
        $settings = $this->plugin_settings();
        /* Add API key in form */
        if (!empty($climateClickApiKey)) {
            $settings['api_key'] = array(
                'name' => __('API Key', 'south-pole-climate-click'),
                'type' => 'text',
                'id' => 'wc_climate_click_settings_tabs_api',
                'value' => $climateClickApiKey,
                'custom_attributes' => array('disabled' => 'disabled')
            );
        }
        return apply_filters('wc_climate_click_settings_tabs_settings', $settings);
    }

    private function plugin_settings()
    {
        $settings = array(
            'section_title' => array(
                'name' => __('Climate Click by South Pole', 'south-pole-climate-click'),
                'type' => 'title',
                'desc' => '',
                'id' => 'wc_climate_click_settings_tabs_section_title'
            ),
            'title' => array(
                'name' => __('Display button', 'south-pole-climate-click'),
                'type' => 'checkbox',
                'desc' => __('Enable Climate Click button', 'south-pole-climate-click'),
                'id' => 'wc_climate_click_settings_tabs_checkbox'
            ),
            'description' => array(
                'name' => __('Location', 'south-pole-climate-click'),
                'type' => 'select',
                'options' => array(
                    '' => ('Select location'),
                    'cartPage' => __('Cart overview page', 'south-pole-climate-click'),
                    'checkoutPage' => __('Checkout confirmation page', 'south-pole-climate-click'),
                    'checkoutCartPage' => __('Both pages', 'south-pole-climate-click')
                ),
                'id' => 'wc_climate_click_settings_tabs_page_selection'
            ),
            'api_key' => array(),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_climate_click_settings_tabs_section_end'
            )
        );
        return $settings;
    }

    public function climate_click_update_settings()
    {
        woocommerce_update_options($this->climate_click_get_settings());
    }
}
