<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.e-goi.com
 * @since             1.0.0
 * @package           Smart_Marketing_Addon_Sms_Order
 *
 * @wordpress-plugin
 * Plugin Name:       E-goi SMS Orders Alert/Notifications
 * Plugin URI:        https://wordpress.org/plugins/sms-orders-alertnotifications-for-woocommerce/
 * Description:       Send SMS notifications to your buyers and admins for each change to the order status in your WooCommerce store. Increase your conversions and better communicate with your customers.
 * Version:           2.0.1
 * Author:            E-goi
 * Author URI:        https://www.e-goi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smart-marketing-addon-sms-order
 * Domain Path:       /languages
 * WC requires at least: 4.7
 * WC tested up to: 6.4.2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

$recipents = json_decode( get_option( 'egoi_sms_order_recipients' ) );
if ( isset( $recipents->egoi_reminders ) && 1 === $recipents->egoi_reminders && ! defined( 'ALTERNATE_WP_CRON' ) ) {
	define( 'ALTERNATE_WP_CRON', true );
}

add_action( 'admin_init', 'smsonw_child_plugin_has_parent_plugin' );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );


/**
 * Check if main plugin is activated
 */
function smsonw_child_plugin_has_parent_plugin() {
	$parant_plugin = plugin_dir_path( __DIR__ ) . 'smart-marketing-for-wp';

	if ( ! is_dir( $parant_plugin ) ) {

		add_action( 'admin_notices', 'smsonw_parent_plugin_notice' );

		deactivate_plugins( plugin_basename( __FILE__ ) );

	} elseif ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( 'smart-marketing-for-wp/egoi-for-wp.php' ) ) {
		add_action( 'admin_notices', 'smsonw_child_plugin_notice' );

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}
}

/**
 * Plugin notice error to install main plugin
 */
function smsonw_parent_plugin_notice() {
	?><div class="notice notice-error is-dismissible">
	<p>
		<?php esc_html_e( 'To use this plugin, you first need to install', 'smart-marketing-addon-sms-order' ); ?>
		<a href="https://wordpress.org/plugins/smart-marketing-for-wp/" target="_blank">Smart Marketing SMS and Newsletters Forms by E-goi</a>
	</p>
	</div>
	<?php
}

/**
 * Plugin notice when removing
 */
function smsonw_child_plugin_notice() {
	?>
	<div class="notice notice-error is-dismissible">
	<p><?php esc_html_e( 'By removing this plugin, you will no longer be able to use the SMS plugin', 'smart-marketing-addon-sms-order' ); ?></p>
	</div>
	<?php
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EGOI_SMART_MARKETING_SMS_WOOCOMMERCE', '2.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smart-marketing-addon-sms-order-activator.php
 */
function activate_smart_marketing_addon_sms_order() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-marketing-addon-sms-order-activator.php';
	Smart_Marketing_Addon_Sms_Order_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smart-marketing-addon-sms-order-deactivator.php
 */
function deactivate_smart_marketing_addon_sms_order() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-marketing-addon-sms-order-deactivator.php';
	Smart_Marketing_Addon_Sms_Order_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smart_marketing_addon_sms_order' );
register_deactivation_hook( __FILE__, 'deactivate_smart_marketing_addon_sms_order' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smart-marketing-addon-sms-order.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smart_marketing_addon_sms_order() {

	$plugin = new Smart_Marketing_Addon_Sms_Order();
	$plugin->run();

}

/**
 * Begins plugin action.
 *
 * @param function $action action function.
 */
function egoi_woo_run_smart_marketing_addon_sms_order_action( $action ) {
	$plugin = new Smart_Marketing_Addon_Sms_Order_Public();
	$plugin->$action();
}

/**
 * Add multiple produtcts to cart.
 *
 * @param false $url url.
 *
 * @return bool
 */
function egoi_add_multiple_products_to_cart( $url = false ) {

	if ( ! class_exists( 'WC_Form_Handler' ) || empty( $_REQUEST['create-cart'] ) || false === strpos( sanitize_text_field( wp_unslash( $_REQUEST['create-cart'] ) ), ',' ) ) {
		return false;
	}
	add_filter( 'wc_add_to_cart_message_html', '__return_false' );
	$product_ids = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST['create-cart'] ) ) );

	if ( ! empty( $_REQUEST['sid_eg'] ) ) {
		global $wpdb;
		$_SESSION['sid_eg'] = sanitize_text_field( wp_unslash( $_REQUEST['sid_eg'] ) );
		$wpdb->update( $wpdb->prefix . 'egoi_sms_abandoned_carts', array( 'status' => 'clicked' ), array( 'php_session_key' => esc_attr( $_SESSION['sid_eg'] ) ) );
	} else {
		return false;
	}

	WC()->cart->empty_cart();
	foreach ( $product_ids as $id_and_quantity ) {

		$id_and_quantity = explode( ':', $id_and_quantity );

		$product_id = $id_and_quantity[0];
		$quantity   = ! empty( $id_and_quantity[1] ) ? absint( $id_and_quantity[1] ) : 1;

		$adding_to_cart = wc_get_product( $product_id );
		if ( ! $adding_to_cart ) {
			continue;
		}

		WC()->cart->add_to_cart( $product_id, $quantity );

	}

	return true;
}

add_action( 'wp_loaded', 'egoi_add_multiple_products_to_cart', 15 );
add_action( 'wp_ajax_process_cellphone', 'egoi_woo_process_cellphone' );
add_action( 'wp_ajax_nopriv_process_cellphone', 'egoi_woo_process_cellphone' );

/**
 * Process cellphone
 */
function egoi_woo_process_cellphone() {
	egoi_woo_run_smart_marketing_addon_sms_order_action( __FUNCTION__ );
}

/**
 * Add new interval to WordPress cron schedules
 *
 * @param array $schedules schedules array.
 *
 * @return mixed
 */
function egoi_woo_smsonw_my_add_every_fifteen_minutes( $schedules ) {
	$schedules['every_fifteen_minutes'] = array(
		'interval' => 60 * 15,
		'display'  => __( 'Every Fifteen Minutes' ),
	);
	return $schedules;
}
add_filter( 'cron_schedules', 'egoi_woo_smsonw_my_add_every_fifteen_minutes' );
// Schedule an action if it's not already scheduled.
if ( ! wp_next_scheduled( 'egoi_woo_smsonw_my_add_every_fifteen_minutes' ) ) {
	wp_schedule_event( time(), 'every_fifteen_minutes', 'egoi_woo_smsonw_my_add_every_fifteen_minutes' );
}

add_filter( 'upgrader_pre_install', 'filter_upgrader_pre_install', 10, 2 );

/**
 * Pre intall filter upgrade
 *
 * @param string $response response.
 * @param array  $hook_extra hook array.
 */
function filter_upgrader_pre_install( $response, $hook_extra ) {

	$path = 'sms-orders-alertnotifications-for-woocommerce/smart-marketing-addon-sms-order.php';

	if ( $hook_extra['plugin'] == $path ) {
		if ( version_compare( EGOI_SMART_MARKETING_SMS_WOOCOMMERCE, '1.5.2', '<' ) ) {
			update_option( 'egoi_sms_counter', 0 );
		}
	}
}

add_action(
	'in_admin_header',
	function () {

		if ( strpos( get_current_screen()->id, 'smart-marketing-addon-sms-order' ) == false ) {
			return false;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );

	},
	1000
);



run_smart_marketing_addon_sms_order();
