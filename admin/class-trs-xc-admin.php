<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.theritesites.com
 * @since      1.0.0
 *
 * @package    TRS_XC
 * @subpackage TRS_XC/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    TRS_XC
 * @subpackage TRS_XC/admin
 * @author     TheRiteSites <contact@theritesites.com>
 */
class TRS_XC_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

		add_action( 'wc_xero_send_payment', array( $this, 'possible_admin_alert' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		global $woocommerce;
		wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/trs-xc-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/trs-xc-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Alerts admin of wrong xero keys
	 */
	public function possible_admin_alert() {
		// $public_key = get_option('wc_xero_')
	}

	/**
	 * Add menu item
	 *
	 * @return void
	 */
	public function add_menu_item() {
		$sub_menu_page = add_submenu_page(
			'woocommerce',
			__( 'Xero Compatibility',
			'trs-xero-compatibility' ),
			__( 'Xero Compatibility', 'trs-xero-compatibility' ),
			'manage_woocommerce',
			'woocommerce_xero',
			array(
				$this,
				'compatibility_page'
			)
	 	);

		add_action( 'load-' . $sub_menu_page, array( $this, 'enqueue_styles' ) );
	}

	/**
	 * The options page
	 */
	public function compatibility_page() {


		if( !class_exists('WC_Admin_Report') ) {
			include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

		}

		$admin_report = new WC_Admin_Report();

		$all_order_ids = (array) $admin_report->get_order_report_data( array(
			'data'	=> array(
				'ID' => array(
					'type'	=> 'post_data',
					'function'	=> '',
					'name'		=> 'order_id',
				),

			),
			'group_by'	=> 'order_id',
			'filter_range'	=> false,
			'query_type'	=> 'get_results',
			'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
			'order_status'        => array( 'completed' ),
			'parent_order_status' => array( 'completed' ),
		) );
		
		$orders_w_xero = (array) $admin_report->get_order_report_data( array(
			'data'	=> array(
				'ID' => array(
					'type'	=> 'post_data',
					'function'	=> '',
					'name'		=> 'order_id',
				),

			),
			'where_meta'	=> array(
				'relation'	=> 'AND',
				array(
					'type'		 => 'post_meta',
					'meta_key'	 => '_xero_invoice_id',
					'meta_value' => '',
					'operator'	 => '>',
				),
			),
			'group_by'	=> 'order_id',
			'filter_range'	=> false,
			'query_type'	=> 'get_results',
			'order_types'         => array_merge( array( 'shop_order_refund' ), wc_get_order_types( 'sales-reports' ) ),
			'order_status'        => array( 'completed' ),
			'parent_order_status' => array( 'completed' ),
		) );

		$all_order_ids = json_decode(json_encode($all_order_ids), true);
		$orders_w_xero = json_decode(json_encode($orders_w_xero), true);
		$non_xero_orders = array_diff_assoc($all_order_ids, $orders_w_xero);

		// print_r( "Non xero order count: " . count($non_xero_orders) . " All orders: " . count($all_order_ids) . " Xero orders: " . count($orders_w_xero) );


		?>
		<div class="wrap woocommerce">
			<!-- <form method="post" id="mainform" enctype="multipart/form-data" action="options.php"> -->
			<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br/></div>
			<h2><?php _e( 'Xero Compatibility for WooCommerce', 'trs-xero-compatibility' ); ?></h2>
			<table>
				<?php foreach( $non_xero_orders as $order ): ?>
					<tr>
						<td><a href="/wp-admin/post.php?post=<?php echo $order['order_id'];?>&action=edit"><?php echo $order['order_id']; ?></a></td>
					</tr>
				<?php endforeach; ?> 
				<!-- <p class="submit"><input type="submit" class="button-primary" value="Save"/></p> -->
			</table>
			<!-- </form> -->
		</div>
	<?php
	}

}
