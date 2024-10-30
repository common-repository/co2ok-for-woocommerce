<?php
/**
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @wordpress-plugin
 * Plugin Name:       Climate Click by South Pole
 * Plugin URI:
 * Description:       Calculate the CO2 footprint for the product when it is added to cart and the calculated CO2 surcharge will be displayed on the cart and checkout page.
 * Version:           2.0.9
 * Author:            ClimateClick
 * Author URI:        https://climateclick.com
 * License:           proprietary
 * License URI:
 * Text Domain:
 * Domain Path:
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('PLUGIN_NAME_VERSION', '2.0.9');
define('API_URL', 'https://api.climateclick.com/');
define('API_KEY_URL', 'https://climate-click.shpwr.nl/api/wp/');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-south-pole-climate-click-activator.php
 */
function activate_south_pole_climate_click()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-south-pole-climate-click-activator.php';
    South_Pole_Climate_Click_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-south-pole-climate-click-deactivator.php
 */
function deactivate_south_pole_climate_click()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-south-pole-climate-click-deactivator.php';
    South_Pole_Climate_Click_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_south_pole_climate_click');
register_deactivation_hook(__FILE__, 'deactivate_south_pole_climate_click');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-south-pole-climate-click.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_south_pole_climate_click()
{
    $plugin = new South_Pole_Climate_Click();
    $plugin->run();
}

run_south_pole_climate_click();
