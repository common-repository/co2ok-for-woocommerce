<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    south-pole-climate-click
 * @subpackage south-pole-climate-click/includes
 * @author     ClimateClick
 */
class South_Pole_Climate_Click
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Plugin_Name_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('PLUGIN_NAME_VERSION')) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        
        $this->plugin_name = 'south-pole-climate-click';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
     * - Plugin_Name_Admin. Defines all hooks for the admin area.
     * - Plugin_Name_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-south-pole-climate-click-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-south-pole-climate-click-admin.php';

        /**
         * The class responsible for getting response of the API.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-south-pole-climate-click-api.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-south-pole-climate-click-public.php';

        $this->loader = new South_Pole_Climate_Click_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new South_Pole_Climate_Click_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_filter('woocommerce_settings_tabs_array', $plugin_admin, 'add_configuration_tab', 50);
        $this->loader->add_action('admin_init', $plugin_admin, 'activation_redirect');
        $this->loader->add_action('woocommerce_settings_tabs_climate_click_settings_tabs', $plugin_admin, 'climate_click_settings_tab');
        $this->loader->add_action('woocommerce_update_options_climate_click_settings_tabs', $plugin_admin, 'climate_click_update_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new South_Pole_Climate_Click_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');

        $this->loader->add_action('woocommerce_cart_totals_after_order_total', $plugin_public, 'climate_click_block_cart_page');

        $this->loader->add_action(
            'woocommerce_review_order_before_payment',
            $plugin_public,
            'climate_click_block_checkout_page',
            10
        );
       
        $this->loader->add_action('woocommerce_cart_calculate_fees', $plugin_public, 'add_surcharge_in_cart_total', 50);
       
        $this->loader->add_action(
            'wp_ajax_nopriv_store_surcharge_checkbox_value',
            $plugin_public,
            'store_surcharge_checkbox_value'
        );
       
        $this->loader->add_action(
            'wp_ajax_store_surcharge_checkbox_value',
            $plugin_public,
            'store_surcharge_checkbox_value'
        );
       
        $this->loader->add_action('wp_footer', $plugin_public, 'add_surcharge_block_script');
       
        $this->loader->add_action(
            'woocommerce_add_order_item_meta',
            $plugin_public,
            'add_surcharge_values_to_order_item_meta',
            1,
            2
        );
        
        $this->loader->add_filter(
            'woocommerce_order_item_get_formatted_meta_data',
            $plugin_public,
            'hide_surcharge_item_meta',
            10,
            2
        );
        
        $this->loader->add_action('woocommerce_new_order', $plugin_public, 'compensate_order_after_process', 1, 1);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }
}
