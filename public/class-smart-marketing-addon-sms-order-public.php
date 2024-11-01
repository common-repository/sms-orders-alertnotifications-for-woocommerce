<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.e-goi.com
 * @since      1.0.0
 *
 * @package    Smart_Marketing_Addon_Sms_Order
 * @subpackage Smart_Marketing_Addon_Sms_Order/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smart_Marketing_Addon_Sms_Order
 * @subpackage Smart_Marketing_Addon_Sms_Order/public
 * @author     E-goi <egoi@egoi.com>
 */
class Smart_Marketing_Addon_Sms_Order_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string $plugin_name       The name of the plugin.
	 * @param string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function smsonw_enqueue_styles() {

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function smsonw_enqueue_scripts() {

		wp_enqueue_script( 'jquery' );
		wp_localize_script(
			'jquery',
			'egoi_public_object',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'egoi_public_object' ),
			)
		);

	}

	/**
	 * Add field to order checkout form
	 *
	 * @param array $checkout    notification checkout field.
	 */
	public function smsonw_notification_checkout_field( $checkout ) {
		$recipients = json_decode( get_option( 'egoi_sms_order_recipients' ), true );
		if ( isset( $recipients['notification_option'] ) && $recipients['notification_option'] ) {
			$checked = $checkout->get_value( 'egoi_notification_option' ) ? $checkout->get_value( 'egoi_notification_option' ) : 1;

			woocommerce_form_field(
				'egoi_notification_option',
				array(
					'type'  => 'checkbox',
					'class' => array( 'my-field-class form-row-wide' ),
					'label' => __( 'I want to be notified by SMS (Order Status)', 'smart-marketing-addon-sms-order' ),
				),
				$checked
			);

		}
	}

	/**
	 * Save notification field from order checkout
	 *
	 * @param string $order_id notification order id.
	 */
	public function smsonw_notification_checkout_field_update_order_meta( $order_id ) {
		if ( isset( $_POST['egoi_notification_option'] ) && ! empty( $_POST['egoi_notification_option'] ) ) {
			$option = sanitize_text_field( wp_unslash( $_POST['egoi_notification_option'] ) );
		}

		$order         = wc_get_order( $order_id );

		if ( isset( $option ) && filter_var( $option, FILTER_VALIDATE_BOOLEAN ) ) {
			$order->update_meta_data( 'egoi_notification_option', 1 );
			$order->save();
		} else {
			$order->update_meta_data( 'egoi_notification_option', 0 );
			$order->save();
		}
	}


	/**
	 * FOLLOW PRICE
	 */
	public function smsonw_follow_price_add_button() {
		$follow_price = json_decode( get_option( 'egoi_sms_follow_price' ), true );
		$button_text  = ( isset( $follow_price['follow_price_button_name'] ) && '' !== $follow_price['follow_price_button_name'] ) ? $follow_price['follow_price_button_name'] : 'Follow price!';

		echo'<a class="button" id="triggerFollowPrice">' . $button_text . '</a>';

		$this->print_follow_price_form( $follow_price );
	}

	/**
	 * Process request
	 *
	 * @param array $post form post.
	 */
	private function process_request( $post ) {

		if ( isset( $post['egoi_action'] ) && 'saveFollowPrice' === $post['egoi_action'] ) {
			// Validate ProductId.
			if ( ! isset( $post['productId'] ) || $post['productId'] <= 0 ) {
				wp_send_json_error( 'Product not found!' );
			}

			// Validate mphone.
			if ( ! isset( $post['mphone'] ) || '' === $post['mphone'] ) {
				wp_send_json_error( 'Must enter your mobile phone' );
			}

			// Validate prefix mphone.
			if ( ! isset( $post['prefixMphone'] ) || '' === $post['prefixMphone'] ) {
				wp_send_json_error( 'Must enter your mobile phone country code' );
			}

			$this->save_follow_price( $post['productId'], $post['prefixMphone'] . '-' . $post['mphone'] );
			wp_send_json_success( 'Save with success!' );
		}
	}

	/**
	 * Print follow price form
	 *
	 * @param array $follow_price follow price array.
	 */
	private function print_follow_price_form( $follow_price = array() ) {

		$this->get_customer_mobile_phone();
		if ( ! is_product() ) {
			return; }
		?>
		<style>
			#printFollowPriceForm {
				position: absolute;
				margin: 0 auto;
				right: 0px;
				z-index: 999999;
				background: <?php echo ! empty( $follow_price['follow_background_color'] ) ? esc_attr( $follow_price['follow_background_color'] ) : ''; ?>;
				padding: 2em;
				border: 2px solid <?php echo ! empty( $follow_price['follow_background_color'] ) ? esc_attr( $follow_price['follow_background_color'] ) : ''; ?>;
				border-radius: 5px;
			}
			#printFollowPriceForm input[type=text] {
				width: 190px;
				height: 30px;
				border: none;
				background-color: #fff;
				-moz-border-radius: 4px;
				border-radius: 4px;
				padding-left: 10px;
				padding-right: 10px;
				border: 1px solid #ccc;
			}

			#printFollowPriceForm input[type=submit],
			#printFollowPriceForm input[type=button]{
				font-size: 100%;
				margin: 0;
				line-height: 1;
				cursor: pointer;
				position: relative;
				text-decoration: none;
				overflow: visible;
				padding: .618em 1em;
				font-weight: 700;
				border-radius: 3px;
				left: auto;
				color: <?php echo ! empty( $follow_price['follow_button_text_color'] ) ? esc_attr( $follow_price['follow_button_text_color'] ) : ''; ?>;
				background-color: <?php echo ! empty( $follow_price['follow_button_color'] ) ? esc_attr( $follow_price['follow_button_color'] ) : ''; ?>;
				border: 0;
				display: inline-block;
				background-image: none;
				box-shadow: none;
				text-shadow: none;
			}
		</style>

		<div id="printFollowPriceForm" style="display: none;">
			<form method="POST" action="#" id="saveFollowPriceEgoi" >
				<input type="hidden" name="egoi_action" value="saveFollowPrice" />
				<input type="hidden" name="action" value="egoi_cellphone_actions" />
				<input type="hidden" name="productId" value="<?php echo esc_attr( wc_get_product()->get_id() ); ?>" />
				<p style="color: <?php echo esc_html( $follow_price['follow_text_color'] ); ?>;"><?php echo esc_html( $follow_price['follow_title_pop'] ); ?> </p>
				<p>  + <input name="prefixMphone" placeholder="351" style="width: 35px;" value="" /> <input name="mphone" placeholder="917789988" value="<?php echo esc_attr( $this->get_customer_mobile_phone()[1] ); ?>" /> </p>
				<p> <input type="submit" value="OK" /> </p>
			</form>
			<div id="followPriceMessage" style="display: none; color: <?php echo esc_html( $follow_price['follow_text_color'] ); ?>;">
				<span><?php esc_html_e( 'An error has occurred! Please try later.', 'smart-marketing-addon-sms-order' ); ?></span>
			</div>
		</div>
		<script>
			(function( $ ) {

				$( document ).ready(function() {

					const anim = 200;
					var messageRef = $('#followPriceMessage');

					const tooglePopFollowPrice = () => {
						if( jQuery('#printFollowPriceForm').css('display') == 'none' ) { jQuery('#printFollowPriceForm').show(anim); } else {jQuery('#printFollowPriceForm').hide(anim);} return true;
					}

					$('#triggerFollowPrice').on('click', () => {
						tooglePopFollowPrice();
					});

					$('#saveFollowPriceEgoi').submit(function(e){
						e.preventDefault();
						var data = {
							security: egoi_public_object.ajax_nonce
						};

						$(this).serializeArray().forEach((obj) => {
							data[obj.name] = obj.value
						})

						$.ajax({
							url: egoi_public_object.ajax_url,
							type: "POST",
							data:data,
							success: function(data){
								tooglePopFollowPrice();
							},
							error: function() {
								var messageHolder = $(messageRef.find("span")[0]);
								messageHolder.text(response.data)
								messageRef.show(anim)
								return;
							}
						});
					});
				});

			})( jQuery );
		</script>
		<?php
	}
	/**
	 * Get billing mobile phone
	 */
	private function get_customer_mobile_phone() {
		$customer_id = get_current_user_id();
		$phone       = get_user_meta( $customer_id, 'billing_phone', true );
		// TODO:set default if not found.
		return explode( '-', $phone );
	}

	/**
	 * Save the follow price
	 *
	 * @param string $product_id product identification.
	 * @param string $mobile mobile number.
	 */
	private function save_follow_price( $product_id, $mobile ) {
		if ( $this->get_follow_price( $product_id, $mobile ) > 0 ) {
			return true;
		}

		global $wpdb;
		$wpdb->insert(
			"{$wpdb->prefix}egoi_sms_follow_price",
			array(
				'product_id' => $product_id,
				'mobile'     => $mobile,
				'time'       => current_time( 'mysql' ),
			)
		);
		return true;
	}

	/**
	 * Get the follow price
	 *
	 * @param string $product_id product identification.
	 * @param string $mobile mobile number.
	 */
	private function get_follow_price( $product_id, $mobile ) {
		global $wpdb;
		$mobile     = sanitize_text_field( $mobile );
		$product_id = sanitize_text_field( $product_id );
		$result     = $wpdb->get_results( $wpdb->prepare( "SELECT count(1) AS exist FROM {$wpdb->prefix}egoi_sms_follow_price where mobile = %s and product_id = %s", array( $mobile, $product_id ) ), ARRAY_A );
		return $result[0]['exist'];
	}

	/**
	 * Abandoned cart trigger
	 */
	public function smsonw_notification_abandoned_cart_trigger() {

		if ( is_admin() ) {
			return false;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-marketing-addon-sms-order-abandonned-cart.php';
		$abandoned_service = new Smart_Marketing_Addon_Sms_Order_Abandoned_Cart();
		$abandoned_service->start();
	}

	/**
	 * Abandoned cart clear
	 *
	 * @param string $order_id order identification.
	 */
	public function smsonw_notification_abandoned_cart_clear( $order_id ) {
		
		if ( is_admin() ) {
			return false;
		}

		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return false;
		}
		
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smart-marketing-addon-sms-order-abandonned-cart.php';
		$abandoned_service = new Smart_Marketing_Addon_Sms_Order_Abandoned_Cart();
		$abandoned_service->convertCart( $order_id );
	}

	/**
	 * Cellphone actions
	 */
	public function egoi_cellphone_actions() {
		check_ajax_referer( 'egoi_public_object', 'security' );
		$result = $this->process_request( $_POST );
	}

}
