<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://webytude.com
 * @since      1.0.0
 *
 * @package    One_Click_Checkout
 * @subpackage One_Click_Checkout/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    One_Click_Checkout
 * @subpackage One_Click_Checkout/public
 * @author     WebyTude <ravi@gmail.com>
 */
class One_Click_Checkout_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in One_Click_Checkout_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The One_Click_Checkout_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/one-click-checkout-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in One_Click_Checkout_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The One_Click_Checkout_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/one-click-checkout-public.js', array( 'jquery' ), $this->version, false );

	}
	/**
	 * Prepare One click order, check with URL perma
	 *
	 * @since    1.0.0
	 */
	public function prepare_1ckick_order() {
		if( is_admin() ){
			return;
		}
		if( isset($_REQUEST['add-to-cart']) && isset($_REQUEST['email'] )){
			session_start();
      		$_SESSION['one_ckick_url_perma'] = $_REQUEST;

			$args = array(
				'post_type' => 'shop_order',
				'post_status' => array("wc-pending","wc-processing","wc-on-hold","wc-on-hold","wc-completed","wc-cancelled","wc-refunded","wc-failed"),
				'meta_query' => array(
			        array(
			            'key' => '_billing_email',
			            'value' => $_REQUEST['email'],
			            'compare' => 'LIKE'
			        )
				)
			);
			
			$ord_query = new WP_Query( $args );
			$order_id = '';
			if ( $ord_query->have_posts() ) :
				while ( $ord_query->have_posts() ) : $ord_query->the_post();
					$order_id = get_the_id();
				endwhile;
			endif;
			
			wp_reset_query();
			if( !empty($order_id) ){

				$order = new WC_Order($order_id); 
				if( !empty($order) ){
					$_SESSION['one_ckick_address'] = $order->get_address('billing');
				}
			}

      		if( isset($_REQUEST['redirect'])){
      			wp_safe_redirect( $_REQUEST['redirect'] );
      			exit;
      		}
		}
	}
	/**
	 * Prepare One click order, check with URL perma
	 *
	 * @since    1.0.0
	 */
	public function woocommerce_checkout_fields( $fields ) {

		session_start();
		// echo "<pre>"; print_r($checkout_fields); echo "</pre>";
	 	if( isset($_SESSION['one_ckick_address']) ){
			$add = $_SESSION['one_ckick_address'];
			$fields["billing"]["billing_first_name"]['default'] = $add["first_name"];
			$fields["billing"]["billing_last_name"]['default'] = $add["last_name"];
			$fields["billing"]["billing_company"]['default'] = $add["company"];
			$fields["billing"]["billing_country"]['default'] = $add["country"];
			$fields["billing"]["billing_address_1"]['default'] = $add["address_1"];
			$fields["billing"]["billing_address_2"]['default'] = $add["address_2"];
			$fields["billing"]["billing_postcode"]['default'] = $add["postcode"];
			$fields["billing"]["billing_city"]['default'] = $add["city"];
			$fields["billing"]["billing_state"]['default'] = $add["state"];
			$fields["billing"]["billing_phone"]['default'] = $add["phone"];
			$fields["billing"]["billing_email"]['default'] = $add["email"];
		}
		return $fields;
	}
}
