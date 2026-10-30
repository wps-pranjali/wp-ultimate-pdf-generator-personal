<?php
/**
 * Plugin Name:      Wp Ultimate Pdf Generator
 * Plugin URI:       https://codecanyon.net/item/wp-ultimate-pdf-generator/20218974
 * Description:      <code><strong>Wp Ultimate Pdf Generator</code></strong> plugin is a pdf generator for wordpress default and custom posts to pdf. Blog readers can export post into pdf and can print or send it to registered email address. <a href="https://wpswings.com/woocommerce-plugins/?utm_source=wpswings-pdf-shop&utm_medium=pdf-cc-backend&utm_campaign=shop-page" target="_blank">Elevate your e-commerce store by exploring more on <strong> WP Swings </strong></a>.
 * Version:          1.3.4
 * Text Domain:      wp-ultimate-pdf
 * Author:           WP Swings
 * Author URI:       https://wpswings.com/?utm_source=wpswings-pdf-official&utm_medium=pdf-cc-backend&utm_campaign=official
 * Requires at least:      6.7.0
 * Tested up to:           7.1
 * WC requires at least:   6.5.0
 * WC tested up to:        11.2.1
 * Domain Path:      /languages
 * License:          GNU General Public License v3.0
 * License URI:      http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Utilities\OrderUtil;
use Automattic\WooCommerce\Utilities\FeaturesUtil;


// Compatibility with WooCommerce Product Block Editor.
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'product_block_editor', plugin_basename( __FILE__ ), true );
		}
	}
);

define( 'MWBPDF_GEN_PREFIX', 'mwbpdf_gen' );
global $blog_id;

if ( isset( $_SERVER['REQUEST_SCHEME'] ) && $_SERVER['REQUEST_SCHEME'] == 'https' ) {
	define( 'MWBPDF_GEN_URL', str_replace( 'http://', 'https://', WP_PLUGIN_URL . '/wp-ultimate-pdf-generator' ) );
} else {
	define( 'MWBPDF_GEN_URL', WP_PLUGIN_URL . '/wp-ultimate-pdf-generator' );
}
define( 'MWB_CACHE_DIR', WP_CONTENT_DIR . '/uploads/wp-ultimate-pdf/' . $blog_id );
define( 'MWBPDF_GEN_PATH', WP_PLUGIN_DIR . '/wp-ultimate-pdf-generator' );

add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

include_once MWBPDF_GEN_PATH . '/Content/mbw-pdf-gen-log.php';
include_once MWBPDF_GEN_PATH . '/libs/class-mwbpdf-pdf-security.php';
include_once MWBPDF_GEN_PATH . '/libs/class-mwbpdf-custom-fonts.php';

if ( ! class_exists( 'mwbpdf' ) ) {
	/**
	 * Class mwbpdf .
	 */
	class mwbpdf {


		static $instance;
		public $log;
		public static function set_screen( $status, $option, $value ) {
			return $value;
		}
		private $options = array();
		/**
		 * Enqueues all the functionality of plugin.
		 *
		 * @name mwbpdf
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function __construct() {
			if ( ! session_id() ) {
				session_start(
					array(
						'read_and_close' => true,
					)
				);
			}
			global $pagenow;
			$this->options = get_option( MWBPDF_GEN_PREFIX );
			add_action( 'wp_initialize_site', array( $this, 'uwfw_standard_plugin_on_create_new_blog' ) );
			add_action( 'wp_ajax_mwb_pdf_register_license', array( $this, 'mwb_pdf_register_license' ) );
			add_action( 'admin_init', array( $this, 'deactivate_plugin_conditional' ) );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_filter( 'plugin_row_meta', array( $this, 'ptp_custom_plugin_row_meta' ), 10, 2 );
			if ( is_admin() ) {

				add_action( 'admin_menu', array( $this, 'ptp_main_menu' ) );
				add_action( 'admin_menu', array( $this, 'ptp_option_log' ) );
				add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
				add_action( 'admin_init', array( $this, 'ptp_on_admin_Init' ) );
				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'ptp_action_links_handler' ) );
				register_activation_hook( plugin_basename( __FILE__ ), array( $this, 'ptp_set_default_on_activate' ) );
				$mwb_pdf_license_hash = get_option( 'mwb_pdf_license_hash' );
				$mwb_pdf_license_key = get_option( 'mwb_pdf_license_key' );
				$mwb_pdf_license_plugin = get_option( 'mwb_pdf_plugin_name' );
				$mwb_pdf_hash = md5( $_SERVER['HTTP_HOST'] . $mwb_pdf_license_plugin . $mwb_pdf_license_key );
				if ( $mwb_pdf_license_hash == $mwb_pdf_hash ) {
					add_filter( 'page_row_actions', array( $this, 'ptp_add_export_to_pdf_option_hier' ), 10, 2 );
					add_filter( 'post_row_actions', array( $this, 'ptp_add_export_to_pdf_option_nhier' ), 10, 2 );
					add_action( 'load-edit.php', array( $this, 'ptp_custom_bulk_action' ) );
					add_action( 'admin_notices', array( $this, 'ptp_custom_bulk_admin_notices' ) );
				}
			} else {
				$mwb_pdf_license_hash = get_option( 'mwb_pdf_license_hash' );
				$mwb_pdf_license_key = get_option( 'mwb_pdf_license_key' );
				$mwb_pdf_license_plugin = get_option( 'mwb_pdf_plugin_name' );
				$mwb_pdf_hash = md5( $_SERVER['HTTP_HOST'] . $mwb_pdf_license_plugin . $mwb_pdf_license_key );
				if ( $mwb_pdf_license_hash == $mwb_pdf_hash ) {
					add_action( 'template_redirect', array( $this, 'ptp_generate_post_to_pdf' ) );
					add_filter( 'the_content', array( $this, 'ptp_add_pdf_link' ) );
				}
			}
			$mwb_pdf_license_hash = get_option( 'mwb_pdf_license_hash' );
			$mwb_pdf_license_key = get_option( 'mwb_pdf_license_key' );
			$mwb_pdf_license_plugin = get_option( 'mwb_pdf_plugin_name' );
			$mwb_pdf_hash = md5( $_SERVER['HTTP_HOST'] . $mwb_pdf_license_plugin . $mwb_pdf_license_key );
			register_activation_hook( __FILE__, array( $this, 'ptp_export_install' ) );
			if ( $mwb_pdf_license_hash == $mwb_pdf_hash ) {
				add_filter( 'cron_schedules', array( $this, 'ptp_pdfscheduleduration' ) );
				add_action( 'wp', array( $this, 'ptp_cronstarter_activation' ) );
				register_deactivation_hook( __FILE__, array( $this, 'ptp_cronstarter_deactivate' ) );
				add_action( 'schedulecacheupdate', array( $this, 'ptp_update_cache' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'ptp_mwbpdf_gen_theme_scripts' ) );
				add_action( 'wp_ajax_nopriv_postajax_exportandmail', array( $this, 'postajax_exportandmail_handle' ) );
				add_action( 'wp_ajax_postajax_exportandmail', array( $this, 'postajax_exportandmail_handle' ) );

				add_action( 'wp_head', array( $this, 'ptp_hook_div_for_guest' ) );
				add_action( 'init', array( $this, 'ptp_init_theme_method' ) );
				add_action( 'init', array( $this, 'updf_check_other_plugins' ) );
				add_action( 'wp_ajax_mwb_pdf_popupclose', array( $this, 'mwb_pdf_popupclose' ) );
				add_action( 'wp_ajax_savemeta', array( $this, 'savemeta_function' ) );
				add_action( 'wp_ajax_nopriv_savemeta', array( $this, 'savemeta_function' ) );
				if ( isset( $this->options['short'] ) ) {
					remove_filter( 'the_content', array( $this, 'ptp_add_pdf_link' ) );
					add_shortcode( 'WPS_PDF_GEN', array( $this, 'ptp_pdf_icon' ) );
					add_action( 'edit_form_after_title', array( $this, 'custom_html_post' ), 10, 1 );
				} else {
					add_shortcode( 'WPS_PDF_GEN', array( $this, 'ptp_pdf_icon_no_shortcode' ) );
				}

				/**
				 * ADD FOLLOWING ACTION OR FITER INTO YOU MAIN CLASS FILE.
				 */

				add_action( 'wp_ajax_remove', array( $this, 'remove' ) );
				add_action( 'wp_ajax_nopriv_remove', array( $this, 'remove' ) );
				add_action( 'wp_ajax_savemetafunction', array( $this, 'savemetafunction' ) );
				add_action( 'wp_ajax_nopriv_savemetafunction', array( $this, 'savemetafunction' ) );
				add_action( 'wp_ajax_unsavemeta', array( $this, 'unsave_meta' ) );
				add_action( 'wp_ajax_nopriv_unsavemeta', array( $this, 'unsave_meta' ) );
				add_action( 'admin_init', array( $this, 'mwbpdf_gen_preview' ) );
				add_action( 'wp_ajax_removelog', array( $this, 'mwbpdf_gen_remove_log' ) );
				add_action( 'wp_ajax_nopriv_removelog', array( $this, 'mwbpdf_gen_remove_log' ) );
				add_action( 'init', array( $this, 'wps_register_google_embed_blocks_cc' ) );
				add_action( 'wp_ajax_wps_pgfw_save_embed_source', array( $this, 'wps_pgfw_save_embed_source_callback_cc' ) );
			}
		}


		/**
		 * This is a ajax callback function for migration.
		 */
		public function wps_pgfw_save_embed_source_callback_cc() {
			$source = ! empty( $_POST['souce_name'] ) ? sanitize_text_field( wp_unslash( $_POST['souce_name'] ) ) : '';
			$value = ! empty( $_POST['is_enable'] ) ? sanitize_text_field( wp_unslash( $_POST['is_enable'] ) ) : '';
			$wps_nonce = ! empty( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

			if ( ! wp_verify_nonce( $wps_nonce, 'mwb-upf-verify-nonce' ) ) {
				wp_send_json_error( 'Invalid nonce' );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( 'Permission denied' );
			}

			update_option( "wps_embed_source_{$source}", $value );
			wp_send_json_success( "Saved $source as $value" );

			wp_die();
		}

		/**
		 * Register google embed block.
		 *
		 * @return void
		 */
		public function wps_register_google_embed_blocks_cc() {
			wp_register_script(
				'google-embed-block-cc',
				MWBPDF_GEN_URL . '/asset/js/pdf-google-embed-block.js',
				array( 'wp-blocks', 'wp-editor', 'wp-element', 'wp-components' ),
				time()
			);

			register_block_type(
				'wpswings/google-embed',
				array(
					'editor_script' => 'google-embed-block-cc',
				)
			);

			register_block_type(
				'custom/calendly-embed',
				array(
					'editor_script' => 'google-embed-block-cc',
					'render_callback' => array( $this, 'wps_render_calendly_block_cc' ),
					'attributes' => array(
						'url' => array(
							'type' => 'string',
							'default' => 'https://calendly.com/princekumaryadav-wpswings/new-meeting-2',
						),
					),
				)
			);

			wp_localize_script(
				'google-embed-block-cc',
				'embed_block_param',
				array(
					'ajaxurl'             => admin_url( 'admin-ajax.php' ),
					'reloadurl'           => MWBPDF_GEN_URL,
					'is_linkedln_active' => get_option( 'wps_embed_source_linkedin', '' ),
					'is_loom_active' => get_option( 'wps_embed_source_loom', '' ),
					'is_twitch_active' => get_option( 'wps_embed_source_twitch', '' ),
					'is_ai_chatbot_active' => get_option( 'wps_embed_source_ai_chatbot', '' ),
					'is_canva_active' => get_option( 'wps_embed_source_canva', '' ),
					'is_reddit_active' => get_option( 'wps_embed_source_reddit', '' ),
					'is_google_active' => get_option( 'wps_embed_source_google_elements', '' ),
					'is_calendly_active' => get_option( 'wps_embed_source_calendly', '' ),
					'is_strava_active' => get_option( 'wps_embed_source_strava', '' ),
					'is_rss_feed_active' => get_option( 'wps_embed_source_rss_feed', '' ),
					'is_x_active' => get_option( 'wps_embed_source_x', '' ),
					'is_view_pdf_active' => get_option( 'wps_embed_source_pdf_embed', '' ),
				)
			);
		}


		/**
		 * Callback of calendly Block.
		 *
		 * @param array $attributes string of HTML.
		 * @return string
		 */
		public function wps_render_calendly_block_cc( $attributes ) {
			$url = esc_url( $attributes['url'] );
			return '<iframe src="' . $url . '" width="100%" height="600px" style="border: none;color : red;" ></iframe>';
		}

		/**
		 * Activate .
		 *
		 * @param string $plugin_slug $plugin_slug.
		 */

		public function mwb_wfw_is_plugin_active( $plugin_slug = '' ) {

			if ( empty( $plugin_slug ) ) {

				return false;
			}

			$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {

				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}

			return in_array( $plugin_slug, $active_plugins ) || array_key_exists( $plugin_slug, $active_plugins );
		}
		/**
		 * Checking if VC is active and mapping shortcodes.
		 *
		 * @name mwb_pdf_popupclose
		 * @author wpswings
		 */
		public function updf_check_other_plugins() {
			$activated = true;
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				if ( ! is_plugin_active( 'js_composer/js_composer.php' ) ) {

					$activated = false;
				}
			} else {
				if ( ! in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
					$activated = false;
				}
			}
			/**
			 * Checking if VC is active.
			 */
			if ( $activated ) {
				WPBMap::addAllMappedShortcodes();
			}
		}
		/**
		 * Closing popup.
		 *
		 * @name mwb_pdf_popupclose
		 * @author wpswings
		 */
		public function mwb_pdf_popupclose() {
			update_option( 'mwb_updf_pop_up_status', true );
			wp_die();
		}
		/**
		 * Checking plugin licensing.
		 *
		 * @name mwb_pdf_register_license
		 * @author wpswings
		 */
		public function mwb_pdf_register_license() {
			check_ajax_referer( 'mwb-upf-verify-nonce', 'mwb_nonce' );
			$mwb_license_key = sanitize_text_field( $_POST['license_key'] );
			$mwb_admin_name = '';
			$mwb_admin_email = get_option( 'admin_email', null );
			$mwb_admin_details = get_user_by( 'email', $admin_email );
			if ( isset( $mwb_admin_details->data ) ) {
				if ( isset( $mwb_admin_details->data->display_name ) ) {
					$mwb_admin_name = $mwb_admin_details->data->display_name;
				}
			}

			$mwb_license_arr = array(
				'license_key' => $mwb_license_key,
				'domain_name' => $_SERVER['HTTP_HOST'],
				'admin_name' => $mwb_admin_name,
				'admin_email' => $mwb_admin_email,
				'plugin_name' => 'Wp Ultimate Pdf Generator',
			);
			$curl = curl_init();

			curl_setopt_array(
				$curl,
				array(
					CURLOPT_URL => 'https://wpswings.com/codecanyon/validate_license.php',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_SSL_VERIFYHOST => false,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_TIMEOUT => 30,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS => http_build_query( $mwb_license_arr ),
					CURLOPT_HTTPHEADER => array(
						'cache-control: no-cache',
						'content-type: application/x-www-form-urlencoded',
						'postman-token: d9d9a28b-94cc-7a0e-47b2-5bd10aa7aa89',
					),
				)
			);
			$mwb_res = curl_exec( $curl );
			$err = curl_error( $curl );

			curl_close( $curl );

			if ( $err ) {
				echo 'cURL Error #:' . $err;
			}
			$mwb_res = json_decode( $mwb_res, true );
			if ( $mwb_res['status'] == true ) {
				global $wpdb;
				if ( is_multisite() ) {

					$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

					foreach ( $blogids as $blog_id ) {

						switch_to_blog( $blog_id );
						update_option( 'mwb_pdf_license_hash', $mwb_res['hash'] );
						update_option( 'mwb_pdf_plugin_name', 'Wp Ultimate Pdf Generator' );
						update_option( 'mwb_pdf_license_key', $mwb_res['mwb_key'] );

						restore_current_blog();
					}
				} else {

					update_option( 'mwb_pdf_license_hash', $mwb_res['hash'] );
					update_option( 'mwb_pdf_plugin_name', 'Wp Ultimate Pdf Generator' );
					update_option( 'mwb_pdf_license_key', $mwb_res['mwb_key'] );
				}
				echo json_encode(
					array(
						'status' => true,
						'msg' => __(
							'Successfully Verified',
							'wp-ultimate-pdf'
						),
					)
				);
			} else if ( $mwb_res['status'] == false ) {
				echo json_encode(
					array(
						'status' => false,
						'msg' => $mwb_res['msg'],
					)
				);
			}
			wp_die();
		}
		/**
		 * Checking if plugin  installed.
		 *
		 * @name deactivate_plugin_conditional
		 * @author wpswings
		 */
		function deactivate_plugin_conditional() {
			if ( is_plugin_active( 'wp-ultimate-pdf/wp-ultimate-pdf.php' ) ) {
				deactivate_plugins( 'wp-ultimate-pdf/wp-ultimate-pdf.php' );
			}
		}

		/**
		 * Generates main menu of settings.
		 *
		 * @name ptp_main_menu
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_main_menu() {
			add_menu_page( __( 'Wp Ultimate Pdf Generator', 'wp-ultimate-pdf' ), __( 'Wp Ultimate Pdf Generator', 'wp-ultimate-pdf' ), 'manage_options', 'wp-ultimate-pdf-generator', array( $this, 'ptp_option_page_handler' ), plugins_url( 'wp-ultimate-pdf-generator/asset/images/pdf3.png' ) );
		}

		/**
		 * Generates a menu for pdf logs.
		 *
		 * @name ptp_option_log.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com.
		 */
		function ptp_option_log() {
			 $hook = add_submenu_page( 'wp-ultimate-pdf-generator', 'Wp Ultimate Pdf Generator log', __( 'Wp Ultimate Pdf Generator Log', 'wp-ultimate-pdf' ), 'manage_options', 'advancedpdflog', array( $this, 'ptp_option_log_handler' ) );
			add_action( "load-$hook", array( $this, 'screen_option' ) );
		}

		/**
		 * Handles the submenu.
		 *
		 * @name ptp_option_log_handler
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_option_log_handler() {                                                 ?>
			<div class="wrap">
				<h2><?php esc_html_e( 'Log Of PDF downloads', 'wp-ultimate-pdf' ); ?></h2>
				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-3">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									<?php
									$this->log->prepare_items();
									$this->log->display();
									?>
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
			</div>
			<?php
		}

		/**
		 * Adds screen option for Log.
		 *
		 * @name screen_option
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function screen_option() {
			$option = 'per_page';
			$args   = array(
				'label'   => 'Records',
				'default' => 5,
				'option'  => 'records_per_page',
			);
			add_screen_option( $option, $args );
			$this->log = new mbw_log_list();
		}

		/**
		 * Removes log.
		 *
		 * @name mwbpdf_gen_remove_log
		 * @author wpswings
		 * @link https://wpswings.com
		 */
		public function mwbpdf_gen_remove_log() {
			$id = sanitize_text_field( $_POST['id'] );
			global $wpdb;
			$export = $wpdb->get_results( 'Delete from ' . $wpdb->prefix . "ExporttoPDFRecord where id='$id'", OBJECT );
			esc_html_e( 'Record deleted successfully', 'wp-ultimate-pdf' );
			wp_die();
		}
		/**
		 * Get_post_preview_body.
		 *
		 * @param string $html html.
		 */
		public function get_post_preview_body( $html ) {
			$post_content = '<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostroris nisi ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod  et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'wp-ultimate-pdf' ) . '</p><br><br>' . __( 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?  Nam libero tempore, cum soluta nobis est eligendi opti Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? ', 'wp-ultimate-pdf' ) . '<br><br><br>' . __( 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.', 'wp-ultimate-pdf' ) . '
								<h2>' . __( 'This Is An H2 Tag', 'wp-ultimate-pdf' ) . '</h2>
								<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wp-ultimate-pdf' ) . ' </p>
								<ul style="padding-left: 1em; text-indent: -1em;">
								<li>' . __( 'First Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Second Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Third Item', 'wp-ultimate-pdf' ) . '</li>
								</ul>
								<ol style="padding-left: 1em; text-indent: -1em;">
								<li>' . __( 'First Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Second Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Third Item', 'wp-ultimate-pdf' ) . '</li>
								</ol>';

			$html .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );

			return $html;
		}
		/**
		 * Get_product_preview_body.
		 *
		 * @param string $html html .
		 * @param string $productpreview prodprview.
		 * @param string $pdf_title pdftitile.
		 */
		public function get_product_preview_body( $html, $productprevieew, $pdf_title ) {

			$html1 = '<img  style="height:250px; width=250px;" alt="" src="' . MWBPDF_GEN_URL . '/asset/images/product-image.jpg">';

			$html2 = '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';
			$html4 = '<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostroris nisi ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod  et dolore magna aliqua. ', 'wp-ultimate-pdf' ) . '</p>';

			$post_content = '<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostroris nisi ut aliquip ex ea commodo consequat. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod  et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'wp-ultimate-pdf' ) . '</p><br><br>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labo magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. ', 'wp-ultimate-pdf' ) . '<br><br>' . __( 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat. ', 'wp-ultimate-pdf' );
			$html3 = '';
			if ( $productprevieew == 'tmplt1' ) {
				$html3 .= $html2 . $html1;
			} elseif ( $productprevieew == 'tmplt2' ) {
				$html3 .= $html1 . '</td><td>' . $html2 . $html4;
			} elseif ( $productprevieew == 'tmplt3' ) {
				$html3 .= $html2 . $html4 . '</td><td>' . $html1;
			}
			$html .= '<table cellpadding="2" cellspacing="2"><tr><td>';

			$html .= $html3;
			$html .= '</td></tr></table>';
			$html .= '<h2>' . __( 'Description', 'wp-ultimate-pdf' ) . '</h2>';
			$html .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );

			return $html;
		}
		/**
		 * Shows preview of settings.
		 *
		 * @name mwbpdf_gen_preview.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function mwbpdf_gen_preview() {
			$post_product_preview = isset( $_GET['mwbpdf_preview'] ) ? sanitize_text_field( wp_unslash( $_GET['mwbpdf_preview'] ) ) : 'no';
			if ( $post_product_preview == 'post' || $post_product_preview == 'product' ) {
				$setting = isset( $_GET['mwbpdf_gensetting'] ) ? sanitize_text_field( wp_unslash( $_GET['mwbpdf_gensetting'] ) ) : '';
				$postprevieew = isset( $_GET['mwbpdf_posttmplt'] ) ? sanitize_text_field( wp_unslash( $_GET['mwbpdf_posttmplt'] ) ) : 'tmplt1';
				$productprevieew = isset( $_GET['mwbpdf_producttmplt'] ) ? sanitize_text_field( wp_unslash( $_GET['mwbpdf_producttmplt'] ) ) : 'tmplt1';

				$setname = get_option( 'ptp_settings', array() );
				if ( ! class_exists( 'TCPDF' ) ) {
					require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
				}
				if ( ! class_exists( 'pdfheader' ) ) {

					require_once MWBPDF_GEN_PATH . '/Content/pdf_headercontent.php';
				}
				if ( ! class_exists( 'simple_html_dom_node' ) ) {
					require_once MWBPDF_GEN_PATH . '/Htmlstructure/Htmlstructure.php';
				}
				if ( isset( $setname[ $setting ]['0']['mwb_file_name'] ) && ! empty( $setname[ $setting ]['0']['mwb_file_name'] ) && ( $setname[ $setting ]['0']['mwb_file_name'] == 'post_name' ) ) {
					$filePath = MWB_CACHE_DIR . '/' . 'demo' . '.pdf';
				} else {
					$filePath = MWB_CACHE_DIR . '/' . 99999 . '.pdf';
				}
				// new PDF document.
				if ( isset( $setname[ $setting ]['1']['page_size'] ) ) {
					$pagesize = ( $setname[ $setting ]['1']['page_size'] );
				} else {
					$pagesize = PDF_PAGE_FORMAT;
				}

				if ( isset( $setname[ $setting ]['1']['unitmeasure'] ) ) {
					$unit = ( $setname[ $setting ]['1']['unitmeasure'] );
				} else {
					$unit = PDF_UNIT;
				}
				if ( isset( $setname[ $setting ]['1']['page_orientation'] ) ) {
					$orientation = ( $setname[ $setting ]['1']['page_orientation'] );
				} else {
					$orientation = PDF_PAGE_ORIENTATION;
				}

				$pdf = new CUSTOMPDF( $orientation, $unit, $pagesize, true, 'UTF-8', false );
				Mwbpdf_Pdf_Security::apply( $pdf, $setname[ $setting ] );

				// information about doc.
				$pdf->SetCreator( 'Post to PDF plugin by makewebbetter with ' . PDF_CREATOR );
				$pdf->SetAuthor( get_bloginfo( 'name' ) );

				if ( ! empty( $setname[ $setting ]['1']['custom_title'] ) ) {
					$pdf_title = $setname[ $setting ]['1']['custom_title'];
				} else {
					$pdf_title = __( 'Default Post', 'wp-ultimate-pdf' );
				}

				// logo width calculation.

				if ( isset( $setname[ $setting ]['2']['page_header'] ) and ( $setname[ $setting ]['2']['page_header'] ) != 'None' and ! empty( $setname[ $setting ]['2']['logo_img_url'] ) ) {

					if ( $setname[ $setting ]['2']['page_header'] == 'upload-image' ) {
						$logo_image_url = $setname[ $setting ]['2']['logo_img_url'];
					}
					$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

					$get_url_path = ABSPATH . '/' . $get_url_path;
					$infologo = getimagesize( $get_url_path );
					if ( isset( $setname[ $setting ]['2']['image_factor'] ) ) {
						$logo_width = (int) ( ( $setname[ $setting ]['2']['image_factor'] * $infologo[0] ) / $infologo[1] );
					} else {
						$logo_width = (int) ( ( 12 * $infologo[0] ) / $infologo[1] );
					}
					// for PHP 5.4 or below set default header data.
					if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
						$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
					} else {
						$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
					}
				}
				if ( isset( $setname[ $setting ]['2']['page_header'] ) and ( $setname[ $setting ]['2']['page_header'] ) == 'None' ) {
					$pdf->setPrintHeader( false );
				}

				// set header and footer fonts.

				if ( isset( $setname[ $setting ]['2']['header_font_size'] ) && ! empty( $setname[ $setting ]['2']['header_font_size'] ) && ( ( $setname[ $setting ]['2']['header_font_size'] ) > 0 ) ) {
					$header_font_size = $setname[ $setting ]['2']['header_font_size'];
				} else {
					$header_font_size = 10;
				}
				if ( isset( $setname[ $setting ]['7']['footer_font_size'] ) && ! empty( $setname[ $setting ]['7']['footer_font_size'] ) && ( ( $setname[ $setting ]['7']['footer_font_size'] ) > 0 ) ) {
					$footer_font_size = $setname[ $setting ]['7']['footer_font_size'];
				} else {
					$footer_font_size = 10;
				}
				if ( isset( $setname[ $setting ]['2']['header_font_pdf'] ) ) {
					$pdf->setHeaderFont(
						array(
							$setname[ $setting ]['2']['header_font_pdf'],
							'',
							$header_font_size,
						)
					);
				}
				if ( isset( $setname[ $setting ]['2']['header_font_pdf'] ) ) {
					$pdf->setHeaderFont(
						array(
							$setname[ $setting ]['2']['header_font_pdf'],
							'',
							$header_font_size,
						)
					);
				}

				$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
				if ( isset( $setname[ $setting ]['1']['margin_left'] ) && ! empty( $setname[ $setting ]['1']['margin_left'] ) ) {
					$pdf->SetLeftMargin( $setname[ $setting ]['1']['margin_left'] );
				} else {
					$pdf->SetLeftMargin( PDF_MARGIN_LEFT );
				}

				if ( isset( $setname[ $setting ]['1']['margin_right'] ) ) {
					$pdf->SetRightMargin( $setname[ $setting ]['1']['margin_right'] );
				} else {
					$pdf->SetRightMargin( PDF_MARGIN_RIGHT );
				}

				if ( isset( $setname[ $setting ]['1']['margin_top'] ) ) {
					$pdf->SetTopMargin( $setname[ $setting ]['1']['margin_top'] );
				} else {
					$pdf->SetTopMargin( PDF_MARGIN_TOP );
				}
				if ( ( isset( $setname[ $setting ]['2']['logomTop'] ) ) ) {
					$pdf->SetHeaderMargin( $setname[ $setting ]['2']['logomTop'] );
				} else {
					$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
				}

				if ( isset( $setname[ $setting ]['7']['footer_font_margin'] ) ) {
					$pdf->SetFooterMargin( $setname[ $setting ]['7']['footer_font_margin'] );
					// set auto page breaks.
					$pdf->SetAutoPageBreak( true, $setname[ $setting ]['7']['footer_font_margin'] );
				} else {
					$pdf->SetFooterMargin( '10' );
					// set auto page breaks.
					$pdf->SetAutoPageBreak( true, '10' );
				}

				// set image scale factor.

				if ( isset( $setname[ $setting ]['1']['image_scale'] ) && ! empty( $setname[ $setting ]['1']['image_scale'] ) && $setname[ $setting ]['1']['image_scale'] > 0 ) {
					$pdf->setImageScale( $setname[ $setting ]['1']['image_scale'] );
				} else {
					$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
				}

				// set default font subsetting mode.
				$pdf->setFontSubsetting( true );
				if ( isset( $setname[ $setting ]['1']['content_font_pdf'] ) ) {
					$pdf->SetFont( $setname[ $setting ]['1']['content_font_pdf'], '', $setname[ $setting ]['1']['content_font_size'], '', true );
				}

				if ( ! empty( $setname[ $setting ]['5']['bullet_img_url'] ) ) {

					$temp = $setname[ $setting ]['5']['bullet_img_url'];
					$listsymbol = 'img|' . $temp . '|' . $setname[ $setting ]['5']['custom_image_width'] . '|' . $setname[ $setting ]['5']['custom_image_height'] . '|' . $setname[ $setting ]['5']['bullet_img_url'];
					$pdf->setLIsymbol( $listsymbol );
				}
				// Add a page.
				// This method has several options, check the source code documentation for more information.

				if ( isset( $setname[ $setting ]['4']['fontStretching'] ) && ! empty( $setname[ $setting ]['4']['fontStretching'] ) ) {
					$pdf->setFontStretching( $setname[ $setting ]['4']['fontStretching'] );
				}
				if ( isset( $setname[ $setting ]['4']['fontSpacig'] ) && ! empty( $setname[ $setting ]['4']['fontSpacig'] ) ) {
					$pdf->setFontSpacing( $setname[ $setting ]['4']['fontSpacig'] );
				} else {
					$pdf->setFontSpacing( '0' );
				}
				$page_format = array();
				if ( isset( $setname[ $setting ]['4']['set_rotation'] ) && ! empty( $setname[ $setting ]['4']['set_rotation'] ) ) {
					$page_format['Rotate'] = $setname[ $setting ]['4']['set_rotation'];
				} else {
					$page_format['Rotate'] = 0;
				}
				$pdf->AddPage( $setname[ $setting ]['1']['page_orientation'], $page_format, false, false );
				$html = '';
				if ( isset( $setname[ $setting ]['1']['CustomCSS_option'] ) ) {
					$html = '<style>' . $setname[ $setting ]['1']['Customcss'] . '</style>';
				}
				$html .= '<body>';

				if ( isset( $setname[ $setting ]['0']['author_name'] ) and ! $setname[ $setting ]['0']['author_name'] == '' ) {
					$html .= '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ': </strong>' . __( 'Author', 'wp-ultimate-pdf' ) . '</p>';
				}

				if ( isset( $setname[ $setting ]['0']['post_categ'] ) ) {
					$html .= '<p><strong>' . __( 'Category', 'wp-ultimate-pdf' ) . ': </strong>' . __( 'Category', 'wp-ultimate-pdf' ) . '</p>';
				}
				if ( $post_product_preview == 'post' ) {
					$html .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';
					$html .= '<img width="700px" height="250px" alt="" src="' . MWBPDF_GEN_URL . '/asset/images/wp-ultimate-pdf-banner-blog-768x256.jpg">';
					if ( $postprevieew == 'tmplt1' ) {
						$pdf->writeHTML( $html, true, 0, true, 0 );
						$pdf->setEqualColumns( 1, 84 );
						$html = $this->get_post_preview_body( $html );
					} elseif ( $postprevieew == 'tmplt2' ) {
						$pdf->writeHTML( $html, true, 0, true, 0 );
						$pdf->setEqualColumns( 2, 84 );
						$html = $this->get_post_preview_body( '' );
					} elseif ( $postprevieew == 'tmplt3' ) {
						$pdf->writeHTML( $html, true, 0, true, 0 );
						$pdf->setEqualColumns( 3, 57 );
						$html = $this->get_post_preview_body( '' );
					}
				} elseif ( $post_product_preview == 'product' ) {

					$html = $this->get_product_preview_body( $html, $productprevieew, $pdf_title );
					$html = $pdf->writeHTML( $html );
				}
				$html .= '</body>';

				$dom = new simple_html_dom();
				$dom->load( $html );

				foreach ( $dom->find( 'img' ) as $e ) {
					// Start Image check by om.
					$exurl = ''; // external streams.
					$imsize = false;
					$file = $e->src;
					// check if we are passing an image as file or string.
					if ( $file[0] === '@' ) {
						// image from string.
						$imgdata = substr( $file, 1 );
					} else { // image file.
						if ( $file[0] === '*' ) {
							// image as external stream.
							$file = substr( $file, 1 );
							$exurl = $file;
						}
						// check if is local file.
						if ( ! @file_exists( $file ) ) {
							// encode spaces on filename (file is probably an URL).
							$file = str_replace( ' ', '%20', $file );
						}
						if ( @file_exists( $file ) ) {
							// get image dimensions.
							$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

							$get_url_path = ABSPATH . '/' . $get_url_path;
							$imsize = @getimagesize( $get_url_path );
						}
						if ( $imsize === false ) {
							$imgdata = TCPDF_STATIC::fileGetContents( $file );
						}
					}
					if ( isset( $imgdata ) and ( $imgdata !== false ) and ( strpos( $file, '__tcpdf_img' ) === false ) ) {
						// check Image size.
						$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

						$get_url_path = ABSPATH . '/' . $get_url_path;
						$imsize = @getimagesize( $get_url_path );
					}
					if ( $imsize === false ) {
						$e->outertext = '';
					} else {
						// End Image Check.
						if ( preg_match( '/alignleft/i', $e->class ) ) {
							$imgalign = 'left';
						} elseif ( preg_match( '/alignright/i', $e->class ) ) {
							$imgalign = 'right';
						} elseif ( preg_match( '/aligncenter/i', $e->class ) ) {
							$imgalign = 'center';
							$htmlimgalign = 'middle';
						} else {
							$imgalign = 'none';
						}

						$e->class = null;
						$e->align = $imgalign;
						if ( isset( $htmlimgalign ) ) {
							$e->style = 'float:' . $htmlimgalign;
						} else {
							$e->style = 'float:' . $imgalign;
						}

						if ( strtolower( substr( $e->src, -4 ) ) == '.svg' ) {
							$e->src = null;
							$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
						} else {
							$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
						}
					}
				}
				$html = $dom->save();

				$dom->clear();

				$pdf->setFormDefaultProp(
					array(
						'lineWidth' => 1,
						'borderStyle' => 'solid',
						'fillColor' => array(
							255,
							255,
							200,
						),
						'strokeColor' => array(
							255,
							128,
							128,
						),
					)
				);

				// Print text using writeHTML.
				$pdf->writeHTML( $html, true, 0, true, 0 );

				if ( isset( $setname[ $setting ]['3']['add_watermark'] ) ) {
					$no_of_pages = $pdf->getNumPages();
					for ( $i = 1; $i <= $no_of_pages; $i++ ) {
						$pdf->setPage( $i );
						// Get the page width/height.
						$my_page_width = $pdf->getPageWidth();
						$my_page_height = $pdf->getPageHeight();

						// Find the middle of the page and adjust.
						$myX = ( $my_page_width / 2 ) - 75;
						$myY = ( $my_page_height / 2 ) + 25;

						// Set the transparency of the text to really light.
						$pdf->SetAlpha( 0.09 );

						// Rotate 45 degrees and write the watermarking text.
						$pdf->StartTransform();
						$rotate_degr = isset( $setname[ $setting ]['3']['rotate_water'] ) ? $setname[ $setting ]['3']['rotate_water'] : '45';
						$pdf->Rotate( $rotate_degr, $myX, $myY );
						$water_font = isset( $setname[ $setting ]['3']['water_font'] ) ? $setname[ $setting ]['3']['water_font'] : 'courier';
						$pdf->SetFont( $water_font, '', 30 );
						$watermark_text = isset( $setname[ $setting ]['3']['watermark_text'] ) ? $setname[ $setting ]['3']['watermark_text'] : '';
						$pdf->Text( $myX, $myY, $watermark_text );
						$pdf->StopTransform();
						$pdf->SetAlpha( 1 );
					}
				}
				if ( isset( $setname[ $setting ]['3']['add_watermark_image'] ) ) {
					if ( ! empty( $setname[ $setting ]['3']['background_img_url'] ) ) {
						$no_of_pages = $pdf->getNumPages();
						for ( $i = 1; $i <= $no_of_pages; $i++ ) {
							$pdf->setPage( $i );
							$my_page_width = $pdf->getPageWidth();
							$my_page_height = $pdf->getPageHeight();
							$myX = ( $my_page_width / 2 ) - 50; // WaterMark Positioning.
							$myY = ( $my_page_height / 2 ) - 40;
							$ImageT = isset( $setname[ $setting ]['3']['water_img_t'] ) ? $setname[ $setting ]['3']['water_img_t'] : '';
							// Set the transparency of the text to really light.
							$pdf->SetAlpha( $ImageT );

							// Rotate 45 degrees and write the watermarking text.
							$pdf->StartTransform();
							$ImageW = isset( $setname[ $setting ]['3']['water_img_h'] ) ? $setname[ $setting ]['3']['water_img_h'] : '';
							$ImageH = isset( $setname[ $setting ]['3']['water_img_w'] ) ? $setname[ $setting ]['3']['water_img_w'] : '';

							$watermark_img = isset( $setname[ $setting ]['3']['background_img_url'] ) ? $setname[ $setting ]['3']['background_img_url'] : '';
							$pdf->Image( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
							$pdf->StopTransform();
							$pdf->SetAlpha( 1 );
						}
					}
				}

				$pdf->Output( $filePath, 'I' );
				exit;
			}

			$setting = isset( $_GET['mwbpdf_gensetting'] ) ? sanitize_text_field( $_GET['mwbpdf_gensetting'] ) : '';
			$setname = get_option( 'ptp_settings', array() );

			if ( array_key_exists( $setting, $setname ) ) {

				if ( ! class_exists( 'TCPDF' ) ) {
					require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
				}
				if ( ! class_exists( 'pdfheader' ) ) {

					require_once MWBPDF_GEN_PATH . '/Content/pdf_headercontent.php';
				}
				if ( ! class_exists( 'simple_html_dom_node' ) ) {
					require_once MWBPDF_GEN_PATH . '/Htmlstructure/Htmlstructure.php';
				}
				if ( isset( $setname[ $setting ]['0']['mwb_file_name'] ) && ! empty( $setname[ $setting ]['0']['mwb_file_name'] ) && ( $setname[ $setting ]['0']['mwb_file_name'] == 'post_name' ) ) {
					$filePath = MWB_CACHE_DIR . '/' . 'demo' . '.pdf';
				} else {
					$filePath = MWB_CACHE_DIR . '/' . 99999 . '.pdf';
				}
				// new PDF document.
				if ( isset( $setname[ $setting ]['1']['page_size'] ) ) {
					$pagesize = ( $setname[ $setting ]['1']['page_size'] );
				} else {
					$pagesize = PDF_PAGE_FORMAT;
				}

				if ( isset( $setname[ $setting ]['1']['unitmeasure'] ) ) {
					$unit = ( $setname[ $setting ]['1']['unitmeasure'] );
				} else {
					$unit = PDF_UNIT;
				}
				if ( isset( $setname[ $setting ]['1']['page_orientation'] ) ) {
					$orientation = ( $setname[ $setting ]['1']['page_orientation'] );
				} else {
					$orientation = PDF_PAGE_ORIENTATION;
				}

				$pdf = new CUSTOMPDF( $orientation, $unit, $pagesize, true, 'UTF-8', false );
				Mwbpdf_Pdf_Security::apply( $pdf, $setname[ $setting ] );

				// information about doc.
				$pdf->SetCreator( 'Post to PDF plugin by makewebbetter with ' . PDF_CREATOR );
				$pdf->SetAuthor( get_bloginfo( 'name' ) );

				if ( ! empty( $setname[ $setting ]['1']['custom_title'] ) ) {
					$pdf_title = $setname[ $setting ]['1']['custom_title'];
				} else {
					$pdf_title = __( 'Default Post', 'wp-ultimate-pdf' );
				}

				// logo width calculation.

				if ( isset( $setname[ $setting ]['2']['page_header'] ) and ( $setname[ $setting ]['2']['page_header'] ) != 'None' and ! empty( $setname[ $setting ]['2']['logo_img_url'] ) ) {

					if ( $setname[ $setting ]['2']['page_header'] == 'upload-image' ) {
						$logo_image_url = $setname[ $setting ]['2']['logo_img_url'];
					}
					$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

					$get_url_path = ABSPATH . '/' . $get_url_path;
					$infologo = getimagesize( $get_url_path );
					if ( isset( $setname[ $setting ]['2']['image_factor'] ) ) {
						$logo_width = (int) ( ( $setname[ $setting ]['2']['image_factor'] * $infologo[0] ) / $infologo[1] );
					} else {
						$logo_width = (int) ( ( 12 * $infologo[0] ) / $infologo[1] );
					}
					// for PHP 5.4 or below set default header data.
					if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
						$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
					} else {
						$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
					}
				}
				if ( isset( $setname[ $setting ]['2']['page_header'] ) and ( $setname[ $setting ]['2']['page_header'] ) == 'None' ) {
					$pdf->setPrintHeader( false );
				}

				// set header and footer fonts.

				if ( isset( $setname[ $setting ]['2']['header_font_size'] ) && ! empty( $setname[ $setting ]['2']['header_font_size'] ) && ( ( $setname[ $setting ]['2']['header_font_size'] ) > 0 ) ) {
					$header_font_size = $setname[ $setting ]['2']['header_font_size'];
				} else {
					$header_font_size = 10;
				}
				if ( isset( $setname[ $setting ]['7']['footer_font_size'] ) && ! empty( $setname[ $setting ]['footer_font_size'] ) && ( ( $setname[ $setting ]['7']['footer_font_size'] ) > 0 ) ) {
					$footer_font_size = $setname[ $setting ]['7']['footer_font_size'];
				} else {
					$footer_font_size = 10;
				}
				if ( isset( $setname[ $setting ]['2']['header_font_pdf'] ) ) {
					$pdf->setHeaderFont(
						array(
							$setname[ $setting ]['2']['header_font_pdf'],
							'',
							$header_font_size,
						)
					);
				}
				if ( isset( $setname[ $setting ]['2']['header_font_pdf'] ) ) {
					$pdf->setHeaderFont(
						array(
							$setname[ $setting ]['2']['header_font_pdf'],
							'',
							$header_font_size,
						)
					);
				}

				$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );
				if ( isset( $setname[ $setting ]['1']['margin_left'] ) && ! empty( $setname[ $setting ]['1']['margin_left'] ) ) {
					$pdf->SetLeftMargin( $setname[ $setting ]['1']['margin_left'] );
				} else {
					$pdf->SetLeftMargin( PDF_MARGIN_LEFT );
				}

				if ( isset( $setname[ $setting ]['1']['margin_right'] ) ) {
					$pdf->SetRightMargin( $setname[ $setting ]['1']['margin_right'] );
				} else {
					$pdf->SetRightMargin( PDF_MARGIN_RIGHT );
				}

				if ( isset( $setname[ $setting ]['1']['margin_top'] ) ) {
					$pdf->SetTopMargin( $setname[ $setting ]['1']['margin_top'] );
				} else {
					$pdf->SetTopMargin( PDF_MARGIN_TOP );
				}
				if ( ( isset( $setname[ $setting ]['2']['logomTop'] ) ) ) {
					$pdf->SetHeaderMargin( $setname[ $setting ]['2']['logomTop'] );
				} else {
					$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
				}

				if ( isset( $setname[ $setting ]['7']['footer_font_margin'] ) ) {
					$pdf->SetFooterMargin( $setname[ $setting ]['7']['footer_font_margin'] );
					// set auto page breaks.
					$pdf->SetAutoPageBreak( true, $setname[ $setting ]['7']['footer_font_margin'] );
				} else {
					$pdf->SetFooterMargin( '10' );
					// set auto page breaks.
					$pdf->SetAutoPageBreak( true, '10' );
				}

				// set image scale factor.

				if ( isset( $setname[ $setting ]['1']['image_scale'] ) && ! empty( $setname[ $setting ]['1']['image_scale'] ) && $setname[ $setting ]['1']['image_scale'] > 0 ) {
					$pdf->setImageScale( $setname[ $setting ]['1']['image_scale'] );
				} else {
					$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
				}

				// set default font subsetting mode.
				$pdf->setFontSubsetting( true );
				if ( isset( $setname[ $setting ]['1']['content_font_pdf'] ) ) {
					$pdf->SetFont( $setname[ $setting ]['1']['content_font_pdf'], '', $setname[ $setting ]['1']['content_font_size'], '', true );
				}

				if ( ! empty( $setname[ $setting ]['5']['bullet_img_url'] ) ) {

					$temp = $setname[ $setting ]['5']['bullet_img_url'];
					$temp = ( explode( '/', $temp ) );
					$temp = end( $temp );
					$temp = ( explode( '.', $temp ) );
					$temp = end( $temp );
					$listsymbol = 'img|' . $temp . '|' . $setname[ $setting ]['5']['custom_image_width'] . '|' . $setname[ $setting ]['5']['custom_image_height'] . '|' . $setname[ $setting ]['5']['bullet_img_url'];
					$pdf->setLIsymbol( $listsymbol );
				}
				// Add a page.
				// This method has several options, check the source code documentation for more information.

				if ( isset( $setname[ $setting ]['4']['fontStretching'] ) && ! empty( $setname[ $setting ]['4']['fontStretching'] ) ) {
					$pdf->setFontStretching( $setname[ $setting ]['4']['fontStretching'] );
				}
				if ( isset( $setname[ $setting ]['4']['fontSpacig'] ) && ! empty( $setname[ $setting ]['4']['fontSpacig'] ) ) {
					$pdf->setFontSpacing( $setname[ $setting ]['4']['fontSpacig'] );
				} else {
					$pdf->setFontSpacing( '0' );
				}
				$page_format = array();
				if ( isset( $setname[ $setting ]['4']['set_rotation'] ) && ! empty( $setname[ $setting ]['4']['set_rotation'] ) ) {
					$page_format['Rotate'] = $setname[ $setting ]['4']['set_rotation'];
				} else {
					$page_format['Rotate'] = 0;
				}

				if ( isset( $setname[ $setting ]['1']['page_orientation'] ) ) {
					$pdf->AddPage( $setname[ $setting ]['1']['page_orientation'], $page_format, false, false );
				}
				$html = '';
				if ( isset( $setname[ $setting ]['1']['CustomCSS_option'] ) ) {
					$html = '<style>' . $setname[ $setting ]['1']['Customcss'] . '</style>';
				}
				$html .= '<body>';

				if ( isset( $setname[ $setting ]['0']['author_name'] ) and ! $setname[ $setting ]['0']['author_name'] == '' ) {
					$html .= '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ': </strong>' . __( 'Author', 'wp-ultimate-pdf' ) . '</p>';
				}

				if ( isset( $setname[ $setting ]['0']['post_categ'] ) ) {
					$html .= '<p><strong>' . __( 'Category', 'wp-ultimate-pdf' ) . ': </strong>' . __( 'Category', 'wp-ultimate-pdf' ) . '</p>';
				}

				if ( isset( $setname[ $setting ]['0']['post_tags'] ) ) {
					$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ': </strong>';
					$html .= '<a href=" ">' . 'tag1,tag2' . '</a>';
					$html .= '</p>';
				}

				// Display date if set in config.
				if ( isset( $setname[ $setting ]['0']['post_date'] ) ) {

					$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . '21-05-2016' . '</p>';
				}
				// Display date if set in config.
				if ( isset( $setname[ $setting ]['0']['cuurent_date'] ) ) {

					$html .= '<p><strong>' . __( 'Print Date', 'wp-ultimate-pdf' ) . ': </strong>' . gmdate( $setname[ $setting ]['0']['date_formatt'] ) . '</p>';
				}
				// Set some content to print.
				$html .= '<h1>' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';

				// Display featured image if set in config on page/post.

				if ( isset( $setname[ $setting ]['0']['featured'] ) ) {
					$html .= '<img width="100" height="100" src="' . MWBPDF_GEN_URL . '/asset/images/logoa.png">';
				}

				$post_content = '<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'wp-ultimate-pdf' ) . '</p>
								<h2>' . __( 'This Is An H2 Tag', 'wp-ultimate-pdf' ) . '</h2>
								<p>' . __( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'wp-ultimate-pdf' ) . ' </p>
								<p><img width="700px" height="250px" alt="" src="' . MWBPDF_GEN_URL . '/asset/images/wp-ultimate-pdf-banner-blog-768x256.jpg"></p>
								<ul style="padding-left: 1em; text-indent: -1em;">
								<li>' . __( 'First Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Second Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Third Item', 'wp-ultimate-pdf' ) . '</li>
								</ul>
								<ol style="padding-left: 1em; text-indent: -1em;">
								<li>' . __( 'First Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Second Item', 'wp-ultimate-pdf' ) . '</li>
								<li>' . __( 'Third Item', 'wp-ultimate-pdf' ) . '</li>
								</ol>';

				$html .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );

				$html .= '</body>';

				$dom = new simple_html_dom();
				$dom->load( $html );

				foreach ( $dom->find( 'img' ) as $e ) {
					// Start Image check by om.
					$exurl = ''; // external streams.
					$imsize = false;
					$file = $e->src;
					// check if we are passing an image as file or string.
					if ( $file[0] === '@' ) {
						// image from string.
						$imgdata = substr( $file, 1 );
					} else { // image file.
						if ( $file[0] === '*' ) {
							// image as external stream.
							$file = substr( $file, 1 );
							$exurl = $file;
						}
						// check if is local file.
						if ( ! @file_exists( $file ) ) {
							// encode spaces on filename (file is probably an URL).
							$file = str_replace( ' ', '%20', $file );
						}
						if ( @file_exists( $file ) ) {
							// get image dimensions.
							$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

							$get_url_path = ABSPATH . '/' . $get_url_path;
							$imsize = @getimagesize( $get_url_path );
						}
						if ( $imsize === false ) {
							$imgdata = TCPDF_STATIC::fileGetContents( $file );
						}
					}
					if ( isset( $imgdata ) and ( $imgdata !== false ) and ( strpos( $file, '__tcpdf_img' ) === false ) ) {
						// check Image size.
						$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

						$get_url_path = ABSPATH . '/' . $get_url_path;
						$imsize = @getimagesize( $get_url_path );
					}
					if ( $imsize === false ) {
						$e->outertext = '';
					} else {
						// End Image Check.
						if ( preg_match( '/alignleft/i', $e->class ) ) {
							$imgalign = 'left';
						} elseif ( preg_match( '/alignright/i', $e->class ) ) {
							$imgalign = 'right';
						} elseif ( preg_match( '/aligncenter/i', $e->class ) ) {
							$imgalign = 'center';
							$htmlimgalign = 'middle';
						} else {
							$imgalign = 'none';
						}

						$e->class = null;
						$e->align = $imgalign;
						if ( isset( $htmlimgalign ) ) {
							$e->style = 'float:' . $htmlimgalign;
						} else {
							$e->style = 'float:' . $imgalign;
						}

						if ( strtolower( substr( $e->src, -4 ) ) == '.svg' ) {
							$e->src = null;
							$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
						} else {
							$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
						}
					}
				}
				$html = $dom->save();

				$dom->clear();

				$pdf->setFormDefaultProp(
					array(
						'lineWidth' => 1,
						'borderStyle' => 'solid',
						'fillColor' => array(
							255,
							255,
							200,
						),
						'strokeColor' => array(
							255,
							128,
							128,
						),
					)
				);

				// Print text using writeHTML.

				$pdf->writeHTML( $html, true, 0, true, 0 );

				if ( isset( $setname[ $setting ]['3']['add_watermark'] ) ) {
					$no_of_pages = $pdf->getNumPages();
					for ( $i = 1; $i <= $no_of_pages; $i++ ) {
						$pdf->setPage( $i );
						// Get the page width/height.
						$my_page_width = $pdf->getPageWidth();
						$my_page_height = $pdf->getPageHeight();

						// Find the middle of the page and adjust.
						$myX = ( $my_page_width / 2 ) - 75;
						$myY = ( $my_page_height / 2 ) + 25;

						// Set the transparency of the text to really light.
						$pdf->SetAlpha( 0.09 );

						// Rotate 45 degrees and write the watermarking text.
						$pdf->StartTransform();
						$rotate_degr = isset( $setname[ $setting ]['3']['rotate_water'] ) ? $setname[ $setting ]['3']['rotate_water'] : '45';
						$pdf->Rotate( $rotate_degr, $myX, $myY );
						$water_font = isset( $setname[ $setting ]['3']['water_font'] ) ? $setname[ $setting ]['3']['water_font'] : 'courier';
						$pdf->SetFont( $water_font, '', 30 );
						$watermark_text = isset( $setname[ $setting ]['3']['watermark_text'] ) ? $setname[ $setting ]['3']['watermark_text'] : '';
						$pdf->Text( $myX, $myY, $watermark_text );
						$pdf->StopTransform();
						$pdf->SetAlpha( 1 );
					}
				}
				if ( isset( $setname[ $setting ]['3']['add_watermark_image'] ) ) {
					if ( ! empty( $setname[ $setting ]['3']['background_img_url'] ) ) {
						$no_of_pages = $pdf->getNumPages();
						for ( $i = 1; $i <= $no_of_pages; $i++ ) {
							$pdf->setPage( $i );
							$my_page_width = $pdf->getPageWidth();
							$my_page_height = $pdf->getPageHeight();
							$myX = ( $my_page_width / 2 ) - 50; // WaterMark Positioning.
							$myY = ( $my_page_height / 2 ) - 40;
							$ImageT = isset( $setname[ $setting ]['3']['water_img_t'] ) ? $setname[ $setting ]['3']['water_img_t'] : '';
							// Set the transparency of the text to really light.
							$pdf->SetAlpha( $ImageT );

							// Rotate 45 degrees and write the watermarking text.
							$pdf->StartTransform();
							$ImageW = isset( $setname[ $setting ]['3']['water_img_h'] ) ? $setname[ $setting ]['3']['water_img_h'] : '';
							$ImageH = isset( $setname[ $setting ]['3']['water_img_w'] ) ? $setname[ $setting ]['3']['water_img_w'] : '';

							$watermark_img = isset( $setname[ $setting ]['3']['background_img_url'] ) ? $setname[ $setting ]['3']['background_img_url'] : '';
							$pdf->Image( $watermark_img, $myX, $myY, $ImageW, $ImageH, '', '', '', true, 150 );
							$pdf->StopTransform();
							$pdf->SetAlpha( 1 );
						}
					}
				}

				$pdf->Output( $filePath, 'I' );
				exit;
			}

			if ( $setting == 'Default Settings' ) {
				$file = MWBPDF_GEN_PATH . '/asset/previewpdf/default-post.pdf';
				header( 'Content-Type: application/force-download' );
				header( 'Content-Type: application/pdf' );
				header( 'Content-Disposition:inline; filename=' . urlencode( $file ) );
				header( 'Content-Description: File Transfer' );
				header( 'Content-Length: ' . filesize( $file ) );
				header( 'Accept-Ranges: bytes' );
				readfile( $file );
				exit;
			}
		}

		/**
		 * Handles ajax request to save text field value of meta field.
		 *
		 * @name savemetafunction
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function savemetafunction() {
			$opt = get_option( 'ptp_settings' );
			$settingname = $this->options['settingname'];
			if ( array_key_exists( $settingname, $opt ) ) {

				$opt[ $settingname ]['settingname'] = $settingname;
				$opt[ $settingname ]['6'][ $_POST['txtid'] ]  = sanitize_text_field( $_POST['txtval'] );

				update_option( MWBPDF_GEN_PREFIX, array_replace( $this->options, $opt[ $settingname ] ) );
			}
		}

		/**
		 * Handles ajax request to save check box value.
		 *
		 * @name savemeta_function
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */

		function savemeta_function() {
			$opt = get_option( 'ptp_settings' );
			$settingname = $this->options['settingname'];
			if ( array_key_exists( $settingname, $opt ) ) {
				$opt[ $settingname ]['settingname'] = $settingname;
				$opt[ $settingname ]['6'][ $_POST['chkid'] ]  = sanitize_text_field( $_POST['chkval'] );
				update_option( MWBPDF_GEN_PREFIX, array_replace( $this->options, $opt[ $settingname ] ) );
			}
		}

		/**
		 * Handles ajax request for unchecked values.
		 *
		 * @name unsave_meta
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */

		function unsave_meta() {
			$opt = get_option( 'ptp_settings' );
			$settingname = $this->options['settingname'];
			if ( array_key_exists( $settingname, $opt ) ) {
				$opt[ $settingname ]['settingname'] = $settingname;
				$opt[ $settingname ]['6'][ $_POST['chkid'] ]  = '';
				update_option( MWBPDF_GEN_PREFIX, array_replace( $this->options, $opt[ $settingname ] ) );
			}
		}

		/**
		 * Removes settings on delete setting click.
		 *
		 * @name remove
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function remove() {
			 $arr = get_option( 'ptp_settings' );
			if ( array_key_exists( $_POST['data'], $arr ) ) {
				unset( $arr[ $_POST['data'] ] );
				update_option( 'ptp_settings', $arr );

				$default = array();

				$default = array();
				$default['front_end'] = '1';
				$default['show'] = 'guestuser';
				$default['availability'] = 'public';
				$default['admin_panel'] = 1;
				$default['0']['featured'] = 1;
				$default['0']['cache_updation_sch'] = 'none';
				$default['0']['mwb_file_name'] = 'post_name';
				$default['0']['author_name'] = '';
				$default['0']['content_position'] = 'left';
				$default['0']['content_placement'] = 'after';
				$default['0']['link_button'] = 'default';
				$default['0']['custon_link_url'] = '';
				$default['1']['image_scale'] = 1.3;
				$default['1']['margin_top'] = 25;
				$default['1']['margin_left'] = 20;
				$default['1']['margin_right'] = 20;
				$default['1']['page_size'] = 'LETTER';
				$default['1']['page_orientation'] = 'P';
				$default['1']['unitmeasure'] = 'mm';
				$default['1']['content_font_pdf'] = 'helvetica';
				$default['1']['content_font_size'] = 12;
				$default['1']['custom_title'] = '';
				$default['1']['Customcss'] = '';
				$default['2']['page_header'] = 'upload-image';
				$default['2']['logo_img_url'] = MWBPDF_GEN_URL . '/asset/images/default-logo.png';
				$default['2']['image_factor'] = 15;
				$default['2']['logomTop'] = 10;
				$default['2']['header_font_pdf'] = 'helvetica';
				$default['2']['header_font_size'] = 10;
				$default['7']['custom_footer_option'] = 1;
				$default['7']['custom_footer'] = '';
				$default['7']['footer_font_pdf'] = 'helvetica';
				$default['7']['footer_font_size'] = 10;
				$default['7']['footer_cell_width'] = 0;
				$default['7']['footer_min_height'] = 0;
				$default['7']['footer_lcornerX'] = 15;
				$default['7']['footer_font_lcornerY'] = 290;
				$default['7']['footer_font_margin'] = 10;
				$default['7']['footer_cell_fill'] = 1;
				$default['7']['footer_align'] = '';
				$default['7']['footer_cell_auto_padding'] = 1;
				$default['3']['add_watermark'] = 1;
				$default['3']['rotate_water'] = 45;
				$default['3']['watermark_text'] = 'Default Watermark';
				$default['3']['water_img_h'] = '';
				$default['3']['water_img_w'] = '';
				$default['3']['water_img_t'] = 0.5;
				$default['3']['background_img_url'] = '';
				$default['5']['bullet_img_url'] = '';
				$default['5']['custom_image_height'] = 2;
				$default['5']['custom_image_width'] = 3;
				$default['4']['custom_font_for_body'] = '';
				$default['4']['set_rotation'] = 0;
				$default['4']['fontStretching'] = 100;
				$default['4']['fontSpacig'] = 0;
				$default['6']['post']['post'] = 1;
				$default['6']['page']['page'] = 1;
				$default['6']['product']['product'] = 1;
				$default['6']['mwb_wcswr_from_datename'] = '';
				$default['6']['mwb_wcswr_to_datename'] = '';
				$default['6']['mwb_wcswr_from_timename'] = '';
				$default['6']['mwb_wcswr_to_timename'] = '';
				$default['6']['_product_attributesname'] = '';
				$default['6']['_stock_statusname'] = '';
				$default['6']['_downloadablename'] = '';
				$default['6']['_virtualname'] = '';
				$default['6']['_min_variation_pricename'] = '';
				$default['6']['_max_variation_pricename'] = '';
				$default['6']['_min_price_variation_idname'] = '';
				$default['6']['_max_price_variation_idname'] = '';
				$default['6']['_min_variation_regular_pricename'] = '';
				$default['6']['_max_variation_regular_pricename'] = '';
				$default['6']['_min_regular_price_variation_idname'] = '';
				$default['6']['_max_regular_price_variation_idname'] = '';
				$default['6']['_min_variation_sale_pricename'] = '';
				$default['6']['_max_variation_sale_pricename'] = '';
				$default['6']['_min_sale_price_variation_idname'] = '';
				$default['6']['_max_sale_price_variation_idname'] = '';
				$default['6']['_pricename'] = '';
				$default['6']['_default_attributesname'] = '';
				$default['6']['_visibilityname'] = '';
				$default['6']['total_salesname'] = '';
				$default['6']['_purchase_notename'] = '';
				$default['6']['_featuredname'] = '';
				$default['6']['_weightname'] = '';
				$default['6']['_lengthname'] = '';
				$default['6']['_widthname'] = '';
				$default['6']['_heightname'] = '';
				$default['6']['_skuname'] = '';
				$default['6']['_regular_pricename'] = '';
				$default['6']['_sale_pricename'] = '';
				$default['6']['_sale_price_dates_fromname'] = '';
				$default['6']['_sale_price_dates_toname'] = '';
				$default['6']['_sold_individuallyname'] = '';
				$default['6']['_manage_stockname'] = '';
				$default['6']['_backordersname'] = '';
				$default['6']['_stockname'] = '';
				$default['6']['_upsell_idsname'] = '';
				$default['6']['_crosssell_idsname'] = '';
				$default['6']['_product_versionname'] = '';
				$default['6']['_product_image_galleryname'] = '';
				$default['6']['_wc_rating_countname'] = '';
				$default['6']['_wc_review_countname'] = '';
				$default['6']['_wc_average_ratingname'] = '';
				$default['6']['_themex_hiddenname'] = '';
				$default['6']['_themex_ratingname'] = '';
				$default['6']['_themex_salesname'] = '';
				$default['6']['_themex_admirersname'] = '';
				$default['6']['_themex_ratename'] = '';
				$default['6']['_themex_aboutname'] = '';
				$default['6']['_themex_policyname'] = '';

				update_option( MWBPDF_GEN_PREFIX, $default );
			}
		}

		/**
		 * Adds a shortcode textbox over posts add/edit section.
		 *
		 * @name custom_html_post
		 * @author WP Swings <support@wpswings.com>
		 */
		public function custom_html_post( $post ) {
			?>
			<p class="btnfl"><label for="shortcode"><b><?php esc_html_e( 'Shortcode For PDF', 'wp-ultimate-pdf' ); ?></b></label>
				<input type="text" class="shortcode" value='[WPS_PDF_GEN]' />
			</p>
			<?php
			return $post;
		}

		/**
		 * Add custom plugin row meta.
		 *
		 * @name ptp_custom_plugin_row_meta
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param array  $links links .
		 * @param string $file file .
		 */
		public function ptp_custom_plugin_row_meta( $links, $file ) {
			if ( strpos( $file, 'wp-ultimate-pdf-generator/wp-ultimate-pdf.php' ) !== false ) {
				$new_links = array(
					'demo' => '<a href="https://demo.wpswings.com/ultimate-pdf-generator/?utm_source=wpswings-pdf-demo&utm_medium=pdf-cc-backend&utm_campaign=demo" target="_blank"><img src="' . esc_html( MWBPDF_GEN_URL ) . '/asset/images/Demo.svg" class="wps-info-img" alt="Demo image" style="margin-right: 5px;vertical-align: middle;max-width: 15px;">' . __( 'Demo', 'wp-ultimate-pdf' ) . '</a>',
					'doc' => '<a href="https://docs.wpswings.com/wp-ultimate-pdf-generator/?utm_source=wpswings-pdf-doc&utm_medium=pdf-cc-backend&utm_campaign=documentation" target="_blank"><img src="' . esc_html( MWBPDF_GEN_URL ) . '/asset/images/Documentation.svg" class="wps-info-img" alt="documentation image" style="margin-right: 5px;vertical-align: middle;max-width: 15px;">' . __( 'Documentation', 'wp-ultimate-pdf' ) . '</a>',
					'support' => '<a href="https://wpswings.com/submit-query/?utm_source=wpswings-pdf-submit-query&utm_medium=pdf-cc-backend&utm_campaign=submit-query" target="_blank"><img src="' . esc_html( MWBPDF_GEN_URL ) . '/asset/images/Support.svg" class="wps-info-img" alt="support image" style="margin-right: 5px;vertical-align: middle;max-width: 15px;">' . __( 'Support', 'wp-ultimate-pdf' ) . '</a>',
					'services' => '<a href="https://wpswings.com/wordpress-woocommerce-solutions/?utm_source=wpswings-pdf-services+&utm_medium=pdf-cc-backend+&utm_campaign=services-page" target="_blank"><img src="' . esc_html( MWBPDF_GEN_URL ) . '/asset/images/Services.svg" class="wps-info-img" alt="Services image" style="margin-right: 5px;vertical-align: middle;max-width: 15px;">' . __( 'Services', 'wp-ultimate-pdf' ) . '</a>',
					'video' => '<a href="https://www.youtube.com/watch?v=xUHyCg7Xrsg" target="_blank"><img src="' . esc_html( MWBPDF_GEN_URL ) . '/asset/images/YouTube32px.svg" class="wps-info-img" alt="Services image" style="margin-right: 5px;vertical-align: middle;max-width: 15px;">' . __( 'Video', 'wp-ultimate-pdf' ) . '</a>',
				);
				$links = array_merge( $links, $new_links );
			}
			return $links;
		}
		/**
		 * Loads text domain.
		 *
		 * @name load_textdomain
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function load_textdomain() {
			$domain = 'wp-ultimate-pdf';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
			load_textdomain( $domain, MWBPDF_GEN_PATH . '/languages/' . $domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( 'wp-ultimate-pdf', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Step 2: handle the custom Bulk Action.
		 *
		 * Based on the post http://wordpress.stackexchange.com/questions/29822/custom-bulk-action
		 *
		 * @name ptp_custom_bulk_action
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_custom_bulk_action() {
			 global $typenow;
			$post_type = $typenow;
			if ( isset( $this->options['6'][ $post_type ][ $post_type ] ) ) {
				// get the action.
				$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
				// depending on your resource type this could be WP_Users_List_Table, WP_Comments_List_Table, etc.
				$action = $wp_list_table->current_action();
				$allowed_actions = array(
					'export',
				);
				if ( ! in_array( $action, $allowed_actions ) ) {
					return;
				}
				// security check.
				check_admin_referer( 'bulk-posts' );
				// make sure ids are submitted. depending on the resource type, this may be 'media' or 'ids'.
				if ( isset( $_REQUEST['post'] ) ) {
					$post_ids = array_map( 'intval', $_REQUEST['post'] );
				}
				if ( empty( $post_ids ) ) {
					return;
				}
				// this is based on wp-admin/edit.php.
				$sendback = remove_query_arg(
					array(
						'exported',
						'untrashed',
						'deleted',
						'ids',
					),
					wp_get_referer()
				);
				if ( ! $sendback ) {
					$sendback = admin_url( "edit.php?post_type=$post_type" );
				}
				$pagenum = $wp_list_table->get_pagenum();
				$sendback = add_query_arg( 'paged', $pagenum, $sendback );
				switch ( $action ) {
					case 'export':
						$exported = 0;
						foreach ( $post_ids as $post_id ) {
							// add posts to in an array to convert into pdf.
							$exported++;
						}
						if ( $exported ) {
							$this->ptp_generate_pdf_bulk_posts( $post_ids );
						}

						$sendback = add_query_arg(
							array(
								'exported' => $exported,
								'ids' => join( ',', $post_ids ),
							),
							$sendback
						);
						break;

					default:
						return;
				}
				$sendback = remove_query_arg(
					array(
						'action',
						'action2',
						'tags_input',
						'post_author',
						'comment_status',
						'ping_status',
						'_status',
						'post',
						'bulk_edit',
						'post_view',
					),
					$sendback
				);

				wp_redirect( $sendback );
				exit();
			}
		}

		/**
		 * Step 3: display an admin notice on the Posts page after exporting.
		 *
		 * @name ptp_custom_bulk_admin_notices
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_custom_bulk_admin_notices() {
			global $post_type, $pagenow;

			if ( $pagenow == 'edit.php' && isset( $this->options['6'][ $post_type ][ $post_type ] ) && isset( $_REQUEST['exported'] ) && (int) $_REQUEST['exported'] ) {
				$message = sprintf( _n( 'Post exported.', '%s posts exported.', $_REQUEST['exported'], 'wp-ultimate-pdf' ), number_format_i18n( $_REQUEST['exported'] ) );
				echo "<div class=\"updated\"><p>{$message}</p></div>";
			}
		}

		/**
		 * Sends postid to function ptp_generate_pdf_file_bulk and generates output.
		 *
		 * @name  ptp_generate_pdf_bulk_posts
		 * @author WP Swings <support@wpswings.com>
		 * @link http:// wpswings.com
		 * @param array $post_ids
		 * an array of postIDs of which to export into pdf
		 */
		function ptp_generate_pdf_bulk_posts( $post_ids ) {
			$filePath = MWB_CACHE_DIR . '/' . 'bulk' . '.pdf';
			$fileMime = 'pdf';
			$fileName = 'bulk.pdf';
			$this->ptp_generate_pdf_file_bulk( $post_ids );
			$output = $this->output_Post_to_pdf_file( $filePath, $fileName, $fileMime );
		}

		/**
		 * Generates pdf of an array of posts.
		 *
		 * @name ptp_generate_pdf_file_bulk
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param array $post_ids
		 */
		function ptp_generate_pdf_file_bulk( $post_ids ) {

			// include only once class library.
			if ( ! class_exists( 'TCPDF' ) ) {
				require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
			}
			if ( ! class_exists( 'pdfheader' ) ) {
				require_once MWBPDF_GEN_PATH . '/Content/pdf_headercontent.php';
			}
			if ( ! class_exists( 'simple_html_dom_node' ) ) {
				require_once MWBPDF_GEN_PATH . '/Htmlstructure/Htmlstructure.php';
			}
			$filePath = MWB_CACHE_DIR . '/' . 'bulk' . '.pdf';
			require_once MWBPDF_GEN_PATH . '/export/adminbulk.php';
		}

		/**
		 * Adds exportpdf link for post type page and custom types.
		 *
		 * @name ptp_add_export_to_pdf_option_hier
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param array $actions
		 * @param int   $post
		 * @return array|string
		 */
		public function ptp_add_export_to_pdf_option_hier( $actions, $post ) {

			if ( isset( $this->options['admin_panel'] ) ) {

				if ( ! isset( $this->options['6'][ $post->post_type ][ $post->post_type ] ) ) {
					return $actions;
				}
				$url = esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) );
				$actions['exportpdf'] = '<a href="' . $url . '" title="' . esc_attr( __( 'Export to pdf', 'wp-ultimate-pdf' ) ) . '">' . __( 'Exportpdf', 'wp-ultimate-pdf' ) . '</a>';
			}

			return $actions;
		}

		/**
		 * Adds exportpdf link for post type post
		 *
		 * @name ptp_add_export_to_pdf_option_nhier
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param array $actions
		 * @param int   $post
		 * @return array|string
		 */
		public function ptp_add_export_to_pdf_option_nhier( $actions, $post ) {
			if ( isset( $this->options['admin_panel'] ) ) {
				if ( ! isset( $this->options['6'][ $post->post_type ][ $post->post_type ] ) ) {
					return $actions;
				}
				$url = esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) );
				$actions['exportpdf'] = '<a href="' . $url . '" title="' . esc_attr( __( 'Export to pdf', 'wp-ultimate-pdf' ) ) . '">' . __( 'Exportpdf', 'wp-ultimate-pdf' ) . '</a>';
			}
			return $actions;
		}

		public function ptp_init_theme_method() {
			add_thickbox();
		}

		/**
		 * Adds an div for popup to guest users
		 *
		 * @name ptp_hook_div_for_guest
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_hook_div_for_guest() {
			$html = "<div style='float:left;padding:20px 20px 20px 20px;'><h4>" . __( 'Enter your email Address', 'wp-ultimate-pdf' ) . '</h4>';
			$html .= '<input type="text" style="margin-top:10px" name="useremailID" id="useremailID"><input type="hidden" id="emailpostID">';
			$html .= "<input id='guest_email' style='margin-top:10px' class='button-primary' type='submit' name='email_submit' value='submit'></div>";
			$output = '<div id="examplePopup1" style="display:none;">' . $html . '</div>';
			echo $output;
		}

		/**
		 * Enqueue scripts for ajax requests
		 *
		 * @name ptp_mwbpdf_gen_theme_scripts
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_mwbpdf_gen_theme_scripts() {
			wp_enqueue_style( 'mwbpdf_genfrontend', MWBPDF_GEN_URL . '/asset/css/front_end.css' );

			wp_enqueue_script(
				'ajaxsave',
				MWBPDF_GEN_URL . '/asset/js/ajaxsave.js',
				array(
					'jquery',
				)
			);
			wp_localize_script(
				'ajaxsave',
				'postajaxsave',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'baseUrl'  => MWBPDF_GEN_URL,

				)
			);
		}

		/**
		 * Enqueue custom script for admin option pages
		 *
		 * @name ptp_enqueue_custom_script
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_enqueue_custom_script() {
			wp_enqueue_style( 'mwbpdf_genadminstyle', MWBPDF_GEN_URL . '/asset/css/admin.css', false, '1.0', 'all' );
			$post_export = null;
			if ( isset( $this->options['admin_panel'] ) ) {
				global $post_type;
				if ( isset( $this->options['6'][ $post_type ][ $post_type ] ) ) {
					$post_export = true;
				}
			}
			$pointers_generate_1 = false;

			$pointers = '';
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp-ultimate-pdf-generator' ) {
				if ( ( isset( $_GET['tab'] ) && ( $_GET['tab'] == '' || $_GET['tab'] == 'display_setting' ) ) || ( ! isset( $_GET['tab'] ) ) ) {
					$pointers_generate_1 = true;
				}

				$popup_status = get_option( 'mwb_updf_pop_up_status', false );
				if ( $popup_status ) {
					$pointers_generate_1 = false;
				}
				$pointers = array(
					'pointers_generate_first' => array(
						'pointer1' => array(
							'target'       => 'p.front',
							'next'         => 'pointer2',
							'options'      => array(
								'content'  => '<h3>' . __( 'PDF link on front end post types.', 'wp-ultimate-pdf' ) . '</h3>' .
									'<p>' . __( 'Enable this option to apply pdf functionality on front end.', 'wp-ultimate-pdf' ) . '</p>',
								'position' => array(
									'edge'  => 'bottom',
									'align' => 'left',
								),
							),

						),
						'pointer2' => array(
							'target'       => 'p.display_admin',
							'next'         => 'pointer3',
							'options'      => array(
								'content'  => '<h3>' . __( 'PDF link on post types table.', 'wp-ultimate-pdf' ) . '</h3>' .
									'<p>' . __( 'Enable this option to apply pdf functionality on backend end.', 'wp-ultimate-pdf' ) . '</p>',
								'position' => array(
									'edge'  => 'bottom',
									'align' => 'left',
								),
							),
						),
						'pointer3' => array(
							'target'       => 'p.display_shortcode',
							'next'         => 'pointer4',
							'options'      => array(
								'content'  => '<h3>' . __( 'Enable shortcode on post types', 'wp-ultimate-pdf' ) . '</h3>' .
									'<p>' . __( 'Enable this setting if you want to display paste shortcode on posts and pages.', 'wp-ultimate-pdf' ) . '</p>',
								'position' => array(
									'edge'  => 'bottom',
									'align' => 'left',
								),
							),
						),
						'pointer4' => array(
							'target'       => '.setting_name',
							'next'         => 'pointer5',
							'options'      => array(
								'content'  => '<h3>' . __( 'Name of you settings', 'wp-ultimate-pdf' ) . '</h3>' .
									'<p>' . __( 'Enter the name of the setting. You can have more than one setting.', 'wp-ultimate-pdf' ) . '</p>',
								'position' => array(
									'edge'  => 'bottom',
									'align' => 'left',
								),
							),
						),
						'pointer5' => array(
							'target'       => '#sett',
							'next'         => '',
							'options'      => array(
								'content'  => '<h3>' . __( 'Choose settings', 'wp-ultimate-pdf' ) . '</h3>' .
									'<p>' . __( 'You can choose between number of PDF settings.', 'wp-ultimate-pdf' ) . '</p>',
								'position' => array(
									'edge'  => 'left',
									'align' => 'left',
								),
							),
						),
					),
					'pointers_generate_1' => $pointers_generate_1,
				);
			}
			wp_enqueue_script( 'bulk', MWBPDF_GEN_URL . '/asset/js/bulk.js', array( 'jquery' ), time(), true );
			$translation_array = array(
				'txt' => __( 'Export', 'wp-ultimate-pdf' ),
				'alert_remove_txt' => __( 'Are you sure you want to delete this setting', 'wp-ultimate-pdf' ),
				'post_export' => $post_export,
				'reset_nonce' => wp_create_nonce( 'wp-reset-nonce' ),
				'alert_saved_apply' => __( 'Are you sure ? because this will reset your current setting', 'wp-ultimate-pdf' ),
				'not_delete_default_setting' => __( 'Sorry! You can not delete default setting', 'wp-ultimate-pdf' ),
				'deleted_successfully' => __( 'Setting Deleted Successfuly', 'wp-ultimate-pdf' ),
				'select_setting_name' => __( 'Please select a setting name', 'wp-ultimate-pdf' ),
				'enter_setting_name' => __( 'Please enter a setting name', 'wp-ultimate-pdf' ),
				'mwb_pdf_nonce'         => wp_create_nonce( 'mwb-upf-verify-nonce' ),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'pointers' => $pointers,
				'path' => MWBPDF_GEN_URL,
				'site_url' => site_url(),
			);
			wp_localize_script( 'bulk', 'bulk_obj', $translation_array );
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
		}

		/**
		 * Checks for update on admin init
		 *
		 * @name ptp_on_admin_Init
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_on_admin_Init() {
			register_setting(
				MWBPDF_GEN_PREFIX . 'footer_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);

			register_setting(
				MWBPDF_GEN_PREFIX . '_display_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . '_general_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . '_body_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . 'header_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);

			register_setting(
				MWBPDF_GEN_PREFIX . 'advance_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . 'watermark_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . 'bulletstyle_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . 'postmeta_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			register_setting(
				MWBPDF_GEN_PREFIX . 'start_page_option',
				MWBPDF_GEN_PREFIX,
				array(
					$this,
					'ptp_on_update_options',
				)
			);
			add_action(
				'admin_enqueue_scripts',
				array(
					$this,
					'ptp_enqueue_custom_script',
				)
			);
		}

		/**
		 * Print notices.
		 *
		 * @since 1.0.0
		 */

		public function upg_print_notices( $notices = array() ) {
			if ( count( $notices ) ) {
				foreach ( $notices as $notice_array ) {

					$message = isset( $notice_array['message'] ) ? esc_html( $notice_array['message'] ) : '';
					$classes = isset( $notice_array['classes'] ) ? esc_attr( $notice_array['classes'] ) : 'error is-dismissable';
					if ( ! empty( $message ) ) {
						?>
						<div class="xyz <?php echo esc_html( $classes ); ?>">
							<p><?php echo esc_html( $message ); ?></p>
						</div>
						<?php
					}
				}
			}
		}

		/**
		 * Reset PDF Setting currently updating.
		 *
		 * @name ptp_on_update_options
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param int $post
		 * @return int
		 */
		function ptp_on_update_options( $option ) {

			if ( ! empty( $option['settingname'] ) ) {

				$ptpsettings = array();
				$ptpsettings = get_option( 'ptp_settings', array() );
				$array_key_value = ( array_keys( $ptpsettings ) );
				$is_new_setting_name = 'true';
				foreach ( $array_key_value as $key => $value ) {
					if ( $value == $option['settingname'] ) {
						$is_new_setting_name = 'false';
					}
				}
				if ( isset( $is_new_setting_name ) && ! empty( $is_new_setting_name ) && ( $is_new_setting_name == 'true' ) ) {
					$default = $this->ptp_deafult_setting();

					$array_new_value_with_default_settings = array_replace( $default, $option );

					$ptpsettings[ $option['settingname'] ] = $array_new_value_with_default_settings;
					update_option( 'ptp_settings', $ptpsettings );
				} else {
					if ( isset( $option ) ) {
						if ( ! isset( $option['short'] ) ) {
							unset( $ptpsettings[ $option['settingname'] ]['short'] );
						}

						if ( ! isset( $option['front_end'] ) ) {
							unset( $ptpsettings[ $option['settingname'] ]['front_end'] );
						}

						if ( ! isset( $option['showemailfield'] ) ) {
							unset( $ptpsettings[ $option['settingname'] ]['showemailfield'] );
						}
						if ( ! isset( $option['admin_panel'] ) ) {
							unset( $ptpsettings[ $option['settingname'] ]['admin_panel'] );
						}
						$array_merge_value = array_replace( $ptpsettings[ $option['settingname'] ], $option );
						$ptpsettings[ $option['settingname'] ] = $array_merge_value;
					}

					update_option( 'ptp_settings', $ptpsettings );
				}
				$notice['message'] = __( 'Settings Saved and Applied Successfully !', 'wp-ultimate-pdf-generator' );
				$notice['classes'] = 'notice notice-success';
				$validation_notice[] = $notice;
				if ( ! session_id() ) {
					session_start();
				}
				$_SESSION['mwb_notice'] = $validation_notice;
				return $ptpsettings[ $option['settingname'] ];
			} else if ( ! empty( $_POST['settings'] ) ) {
				$ptpsettings = array();
				$ptpsettings = get_option( 'ptp_settings', array() );
				if ( array_key_exists( $_POST['settings'], $ptpsettings ) ) {
					$index = $option['index'];
					if ( isset( $index ) ) {
						if ( $index == '7' ) {
							if ( ! isset( $ptpsettings[ $_POST['settings'] ][ $index ]['footer_cell_fill'] ) ) {
								$ptpsettings[ $_POST['settings'] ][ $index ]['footer_cell_fill']  = 0;
							}
						} elseif ( $index == '8' ) {
							if ( isset( $option['start_page_predefined_template'] ) && $option['start_page_predefined_template'] == 1 ) {
								foreach ( $ptpsettings as $settingkey => $value ) {
									if ( isset( $ptpsettings[ $settingkey ][9]['custom_cover_page'] ) && $ptpsettings[ $settingkey ][9]['custom_cover_page'] == 1 ) {
										unset( $ptpsettings[ $settingkey ][9]['custom_cover_page'] );
									}
								}
							}
						} elseif ( $index == '9' ) {
							if ( isset( $option['custom_cover_page'] ) && $option['custom_cover_page'] == 1 ) {
								foreach ( $ptpsettings as $settingkey => $value ) {
									if ( isset( $ptpsettings[ $settingkey ][8]['start_page_predefined_template'] ) && $ptpsettings[ $settingkey ][8]['start_page_predefined_template'] == 1 ) {
										unset( $ptpsettings[ $settingkey ][8]['start_page_predefined_template'] );
									}
								}
							}
						}
						$ptpsettings[ $_POST['settings'] ][ $index ]  = $option;
					} else {
						$ptpsettings[ $_POST['settings'] ]  = $option;
					}
				} else {
					$ptpsettings[ $_POST['settings'] ]  = $option;
				}
				update_option( 'ptp_settings', $ptpsettings );
			}
			$notice['message'] = __( 'Settings Saved Successfully !', 'wp-ultimate-pdf' );
			$notice['classes'] = 'notice notice-success';
			$validation_notice[] = $notice;
			if ( ! session_id() ) {
				session_start();
			}
			$_SESSION['mwb_notice'] = $validation_notice;

			if ( isset( $_POST['settings'] ) && isset( $ptpsettings[ $_POST['settings'] ] ) && ! empty( $ptpsettings[ $_POST['settings'] ] ) ) {
				return $ptpsettings[ $_POST['settings'] ];
			} else {
				return $option;
			}
		}

		/**
		 * Adds settings link.
		 *
		 * @name ptp_action_links_handler
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_action_links_handler( $action_links ) {
			$settings_link = '<a href="admin.php?page=wp-ultimate-pdf-generator">' . __( 'Settings', 'wp-ultimate-pdf' ) . '</a>';
			array_unshift( $action_links, $settings_link );
			return $action_links;
		}

		/**
		 * Attach option page to submenu.
		 *
		 * @name ptp_option_page_handler
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_option_page_handler() {
			require( MWBPDF_GEN_PATH . '/Content/pdf_content.php' );
		}

		/**
		 * Generates default settings.
		 *
		 * @name ptp_deafult_setting
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		function ptp_deafult_setting() {
			$default = array();
			$default = array();
			$default['front_end'] = '1';
			$default['show'] = 'guestuser';
			$default['availability'] = 'public';
			$default['admin_panel'] = 1;
			$default['0']['featured'] = 1;
			$default['0']['cache_updation_sch'] = 'none';
			$default['0']['mwb_file_name'] = 'post_name';
			$default['0']['author_name'] = '';
			$default['0']['content_position'] = 'left';
			$default['0']['content_placement'] = 'after';
			$default['0']['link_button'] = 'default';
			$default['0']['custon_link_url'] = '';
			$default['1']['image_scale'] = 1.3;
			$default['1']['margin_top'] = 25;
			$default['1']['margin_left'] = 20;
			$default['1']['margin_right'] = 20;
			$default['1']['page_size'] = 'LETTER';
			$default['1']['page_orientation'] = 'P';
			$default['1']['unitmeasure'] = 'mm';
			$default['1']['content_font_pdf'] = 'helvetica';
			$default['1']['content_font_size'] = 12;
			$default['1']['custom_title'] = '';
			$default['1']['Customcss'] = '';
			$default['2']['page_header'] = 'upload-image';
			$default['2']['logo_img_url'] = MWBPDF_GEN_URL . '/asset/images/default-logo.png';
			$default['2']['image_factor'] = 15;
			$default['2']['logomTop'] = 10;
			$default['2']['header_font_pdf'] = 'helvetica';
			$default['2']['header_font_size'] = 10;
			$default['custom_footer_option'] = 1;
			$default['7']['custom_footer'] = '';
			$default['7']['footer_font_pdf'] = 'helvetica';
			$default['7']['footer_font_size'] = 10;
			$default['7']['footer_cell_width'] = 0;
			$default['7']['footer_min_height'] = 0;
			$default['7']['footer_lcornerX'] = 15;
			$default['7']['footer_font_lcornerY'] = 290;
			$default['7']['footer_font_margin'] = 10;
			$default['7']['footer_align'] = '';
			$default['7']['footer_cell_auto_padding'] = 1;
			$default['3']['add_watermark'] = 1;
			$default['3']['rotate_water'] = 45;
			$default['3']['watermark_text'] = 'Default Watermark';
			$default['3']['water_img_h'] = '';
			$default['3']['water_img_w'] = '';
			$default['3']['water_img_t'] = 0.5;
			$default['3']['background_img_url'] = '';
			$default['5']['bullet_img_url'] = '';
			$default['5']['custom_image_height'] = 2;
			$default['5']['custom_image_width'] = 3;
			$default['4']['custom_font_for_body'] = '';
			$default['4']['set_rotation'] = 0;
			$default['4']['fontStretching'] = 100;
			$default['4']['fontSpacig'] = 0;
			$default['6']['post']['post'] = 1;
			$default['6']['page']['page'] = 1;
			$default['6']['product']['product'] = 1;
			$default['6']['mwb_wcswr_from_datename'] = '';
			$default['6']['mwb_wcswr_to_datename'] = '';
			$default['6']['mwb_wcswr_from_timename'] = '';
			$default['6']['mwb_wcswr_to_timename'] = '';
			$default['6']['_product_attributesname'] = '';
			$default['6']['_stock_statusname'] = '';
			$default['6']['_downloadablename'] = '';
			$default['6']['_virtualname'] = '';
			$default['6']['_min_variation_pricename'] = '';
			$default['6']['_max_variation_pricename'] = '';
			$default['6']['_min_price_variation_idname'] = '';
			$default['6']['_max_price_variation_idname'] = '';
			$default['6']['_min_variation_regular_pricename'] = '';
			$default['6']['_max_variation_regular_pricename'] = '';
			$default['6']['_min_regular_price_variation_idname'] = '';
			$default['6']['_max_regular_price_variation_idname'] = '';
			$default['6']['_min_variation_sale_pricename'] = '';
			$default['6']['_max_variation_sale_pricename'] = '';
			$default['6']['_min_sale_price_variation_idname'] = '';
			$default['6']['_max_sale_price_variation_idname'] = '';
			$default['6']['_pricename'] = '';
			$default['6']['_default_attributesname'] = '';
			$default['6']['_visibilityname'] = '';
			$default['6']['total_salesname'] = '';
			$default['6']['_purchase_notename'] = '';
			$default['6']['_featuredname'] = '';
			$default['6']['_weightname'] = '';
			$default['6']['_lengthname'] = '';
			$default['6']['_widthname'] = '';
			$default['6']['_heightname'] = '';
			$default['6']['_skuname'] = '';
			$default['6']['_regular_pricename'] = '';
			$default['6']['_sale_pricename'] = '';
			$default['6']['_sale_price_dates_fromname'] = '';
			$default['6']['_sale_price_dates_toname'] = '';
			$default['6']['_sold_individuallyname'] = '';
			$default['6']['_manage_stockname'] = '';
			$default['6']['_backordersname'] = '';
			$default['6']['_stockname'] = '';
			$default['6']['_upsell_idsname'] = '';
			$default['6']['_crosssell_idsname'] = '';
			$default['6']['_product_versionname'] = '';
			$default['6']['_product_image_galleryname'] = '';
			$default['6']['_wc_rating_countname'] = '';
			$default['6']['_wc_review_countname'] = '';
			$default['6']['_wc_average_ratingname'] = '';
			$default['6']['_themex_hiddenname'] = '';
			$default['6']['_themex_ratingname'] = '';
			$default['6']['_themex_salesname'] = '';
			$default['6']['_themex_admirersname'] = '';
			$default['6']['_themex_ratename'] = '';
			$default['6']['_themex_aboutname'] = '';
			$default['6']['_themex_policyname'] = '';
			return $default;
		}

		/**
		 * Sets default values in plugin activate.
		 *
		 * @name
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_set_default_on_activate( $network_wide ) {
			$this->callback_activator( $network_wide );
		}
		/**
		 * Callback activation .
		 */
		public function callback_activator( $network_wide ) {

			global $wpdb;
			if ( is_multisite() || $network_wide ) {

				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

				foreach ( $blogids as $blog_id ) {

					switch_to_blog( $blog_id );
					$default = $this->ptp_deafult_setting();

					if ( ! get_option( MWBPDF_GEN_PREFIX ) ) {
						add_option( MWBPDF_GEN_PREFIX, $default );
					}
					// create directory and move logo to upload directory.
					if ( ! file_exists( WP_CONTENT_DIR . '/uploads' ) ) {
						@mkdir( WP_CONTENT_DIR . '/uploads' );
					}

					restore_current_blog();
				}
			} else {
				$default = $this->ptp_deafult_setting();

				if ( ! get_option( MWBPDF_GEN_PREFIX ) ) {
					add_option( MWBPDF_GEN_PREFIX, $default );
				}
				// create directory and move logo to upload directory.
				if ( ! file_exists( WP_CONTENT_DIR . '/uploads' ) ) {
					@mkdir( WP_CONTENT_DIR . '/uploads' );
				}
			}
		}
		/**
		 * After plugin activate new site creation code.
		 */

		public function uwfw_standard_plugin_on_create_new_blog( $new_site ) {

			$mwb_pdf_license_hash = get_option( 'mwb_pdf_license_hash' );
			$mwb_pdf_license_key = get_option( 'mwb_pdf_license_key' );
			$mwb_pdf_license_plugin = get_option( 'mwb_pdf_plugin_name' );
			if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			// check if the plugin has been activated on the network.
			if ( is_plugin_active_for_network( 'wp-ultimate-pdf-generator/wp-ultimate-pdf.php' ) ) {
				$blog_id = $new_site->blog_id;
				// switch to newly created site.
				switch_to_blog( $blog_id );

				$this->callback_activator();
				update_option( 'mwb_pdf_license_hash', $mwb_pdf_license_hash );
				update_option( 'mwb_pdf_plugin_name', $mwb_pdf_license_plugin );
				update_option( 'mwb_pdf_license_key', $mwb_pdf_license_key );
				// code to be executed when site is created, call any function from activation file.
				restore_current_blog();
			}
		}


		/**
		 * generates pdf file of current post
		 *
		 * @name ptp_generate_post_to_pdf
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @return boolean
		 */
		function ptp_generate_post_to_pdf() {
			global $post;
			if ( isset( $post ) ) {

				if ( http_response_code() != '404' ) {
					if ( ! isset( $this->options['6'][ $post->post_type ][ $post->post_type ] ) ) {
						return false;
					}

					if ( 'pdf' == ( isset( $_GET['format'] ) ? sanitize_text_field( wp_unslash( $_GET['format'] ) ) : null ) ) {
						global $post;
						$post = get_post();
						$content = $post->the_content;

						if ( $this->options['0']['mwb_file_name'] == 'post_name' ) {
							$filePath = MWB_CACHE_DIR . '/' . $post->post_name . '.pdf';
							$fileName = $post->post_name . '.pdf';
						} else if ( 'custom_name' == $this->options['0']['mwb_file_name'] ) {
							$po = $this->options['0']['mwb_file_name_custom'];
							$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
							if ( empty( $po ) ) {
								$fileName = 'document_' . $post->ID . '.pdf';
							} else {
								$fileName = $po . '_' . $post->ID . '.pdf';
							}
						} else {
							$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
							$fileName = $post->ID . '.pdf';
						}

						$fileMime = 'pdf';
						if ( ! isset( $this->options['0']['includefromCache'] ) ) {
							$this->generate_post_to_pdf_file( $post->ID );
						} else {
							if ( ! file_exists( $filePath ) ) {
								$this->generate_post_to_pdf_file( $post->ID );
							}
						}
						$output = $this->output_Post_to_pdf_file( $filePath, $fileName, $fileMime );
					}
				}
			}
		}

		/**
		 * checks whether pdf for the post is in cache or not.
		 *
		 * @name ptp_email_pdf
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param int    $postID
		 * @param string $useremailID
		 * @return boolean
		 */
		function ptp_email_pdf( $postID, $useremailID ) {

			$post = get_post( $postID );
			$content = $post->post_content;
			if ( $this->options['0']['mwb_file_name'] == 'post_name' ) {
				$filePath = MWB_CACHE_DIR . '/' . $post->post_name . '.pdf';
				$fileName = $post->post_name . '.pdf';
			} else if ( 'custom_name' == $this->options['0']['mwb_file_name'] ) {
				$po = $this->options['0']['mwb_file_name_custom'];
				$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
				if ( empty( $po ) ) {
					$fileName = 'document_' . $post->ID . '.pdf';
				} else {
					$fileName = $po . '_' . $post->ID . '.pdf';
				}
			} else {
				$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
				$fileName = $post->ID . '.pdf';
			}
			$fileMime = 'pdf';
			if ( ! isset( $this->options['0']['includefromCache'] ) ) {
				$this->generate_pdf_file_email( $post->ID, $useremailID );
			} else {
				if ( ! file_exists( $filePath ) ) {
					$this->generate_pdf_file_email( $post->ID, $useremailID );
				}
			}
		}

		/**
		 * Generate output of pdf generated
		 *
		 * @name output_Post_to_pdf_file
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param string $pdffile pdf.
		 * @param string $file_name file_name.
		 * @param string $mimepdftype mimepdftype.
		 * @return boolean
		 */
		public function output_Post_to_pdf_file( $pdffile, $file_name, $mimepdftype = 'application/pdf' ) {

			if ( ! is_readable( $pdffile ) ) {

				return false;
			}
			$size = filesize( $pdffile );

			$file_name = rawurldecode( $file_name );
			$known_mime_types = array(
				'pdf' => 'application/pdf',
				'txt' => 'text/plain',
				'html' => 'text/html',
				'htm' => 'text/html',
				'exe' => 'application/octet-stream',
				'zip' => 'application/zip',
				'doc' => 'application/msword',
				'xls' => 'application/vnd.ms-excel',
				'ppt' => 'application/vnd.ms-powerpoint',
				'gif' => 'image/gif',
				'png' => 'image/png',
				'jpeg' => 'image/jpg',
				'jpg' => 'image/jpg',
				'php' => 'text/plain',
			);

			if ( $mimepdftype == '' ) {
				$file_extension = strtolower( substr( strrchr( $pdffile, '.' ), 1 ) );

				if ( array_key_exists( $file_extension, $known_mime_types ) ) {
					$mimepdftype = $known_mime_types[ $file_extension ];
				} else {
					$mimepdftype = 'application/force-download';
				}
			} else {
				$mimepdftype = 'application/pdf';
			}

			@ob_end_clean();

			if ( ini_get( 'zlib.output_compression' ) ) {
				ini_set( 'zlib.output_compression', 'Off' );
			}
			header( 'Content-Type: ' . $mimepdftype );
			header( 'Content-Disposition: attachment; filename="' . $file_name . '"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Accept-Ranges: bytes' );

			header( 'Cache-control: private' );
			header( 'Pragma: private' );
			header( 'Expires: tue, 26 aug 2015 05:00:00 GMT' );

			if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
				list($a, $range) = explode( '=', sanitize_text_field( wp_unslash( $_SERVER['HTTP_RANGE'] ) ), 2 );
				list($range) = explode( ',', $range, 2 );
				list($range, $range_end) = explode( '-', $range );
				$range = intval( $range );
				if ( ! $range_end ) {
					$range_end = $size - 1;
				} else {
					$range_end = intval( $range_end );
				}
				$new_length = $range_end - $range + 1;
				header( 'HTTP/1.1 206 Partial Content' );
				header( "Content-Length: $new_length" );
				header( "Content-Range: bytes $range-$range_end/$size" );
			} else {
				$new_length = $size;
				header( 'Content-Length: ' . $size );
			}

			$chunksize = 1 * ( 1024 * 1024 );
			$bytes_send = 0;
			if ( $pdffile = fopen( $pdffile, 'r' ) ) {
				if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
					fseek( $pdffile, $range );
				}
				while ( ! feof( $pdffile ) && ( ! connection_aborted() ) && ( $bytes_send < $new_length ) ) {
					$buffer = fread( $pdffile, $chunksize );
					print( $buffer );
					flush();
					$bytes_send += strlen( $buffer );
				}
				fclose( $pdffile );
			} else {
				return false;
			}
			return true;
		}

		/**
		 * Generate pdf of a post and send email to guest user.
		 *
		 * @name generate_pdf_file_email
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param int    $postID
		 * @param string $useremailID
		 */
		function generate_pdf_file_email( $postID, $useremailID ) {
			$post = get_post( $postID );
			$content = $post->post_content;
			if ( ! class_exists( 'TCPDF' ) ) {
				require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
			}
			if ( ! class_exists( 'pdfheader' ) ) {
				require_once MWBPDF_GEN_PATH . '/Content/pdf_headercontent.php';
			}
			if ( ! class_exists( 'simple_html_dom_node' ) ) {
				require_once MWBPDF_GEN_PATH . '/Htmlstructure/Htmlstructure.php';
			}
			$post->post_content = apply_filters( 'the_post_export_content', $post->post_content );
			$post->post_content = wpautop( $post->post_content );
			$post->post_content = do_shortcode( $post->post_content );

			if ( isset( $this->options['0']['mwb_file_name'] ) && ! empty( $this->options['0']['mwb_file_name'] ) && $this->options['0']['mwb_file_name'] == 'post_name' ) {
				$filePath = MWB_CACHE_DIR . '/' . $post->post_name . '.pdf';
			} else {
				$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
			}
			// new PDF document.
			if ( isset( $this->options['1']['page_size'] ) ) {
				$pagesize = ( $this->options['1']['page_size'] );
			} else {
				$pagesize = PDF_PAGE_FORMAT;
			}
			if ( isset( $this->options['1']['unitmeasure'] ) ) {
				$unit = ( $this->options['1']['unitmeasure'] );
			} else {
				$unit = PDF_UNIT;
			}
			if ( isset( $this->options['1']['page_orientation'] ) ) {
				$orientation = ( $this->options['1']['page_orientation'] );
			} else {
				$orientation = PDF_PAGE_ORIENTATION;
			}

			$pdf = new CUSTOMPDF( $orientation, $unit, $pagesize, true, 'UTF-8', false );
			Mwbpdf_Pdf_Security::apply( $pdf, $this->options, $postID );
			// information about doc.
			$pdf->SetCreator( 'Post to PDF plugin by makewebbetter with ' . PDF_CREATOR );
			$pdf->SetAuthor( get_bloginfo( 'name' ) );
			if ( ! empty( $this->options['1']['custom_title'] ) ) {
				$pdf_title = $this->options['1']['custom_title'];
			} else {
				$pdf_title = $post->post_title;
			}

			// logo width calculation.
			if ( $this->options['2']['page_header'] == 'upload-image' ) {
				$logo_image_url = $this->options['2']['logo_img_url'];
			}
			$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

			$get_url_path = ABSPATH . '/' . $get_url_path;
			$infologo = getimagesize( $get_url_path );
			if ( isset( $this->options['2']['image_factor'] ) ) {
				$logo_width = (int) ( ( $this->options['2']['image_factor'] * $infologo[0] ) / $infologo[1] );
			} else {
				$logo_width = (int) ( ( 12 * $infologo[0] ) / $infologo[1] );
			}
			// for PHP 5.4 or below set default header data.
			$site_name = isset( $this->options['2']['site_name_header'] ) ? $this->options['2']['site_name_header'] : 0;
			$site_description = isset( $this->options['2']['site_description_header'] ) ? $this->options['2']['site_description_header'] : 0;
			$site_url = isset( $this->options['2']['site_url_header'] ) ? $this->options['2']['site_url_header'] : 0;
			if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
				if ( $site_name == 0 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_url == 1 && $site_description == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, ' ', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, '', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
			} else {
				if ( $site_name == 0 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_url == 1 && $site_description == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, ' ', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, '', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
			}

			if ( isset( $this->options['2']['page_header'] ) and ( $this->options['2']['page_header'] ) == 'None' ) {
				$pdf->setPrintHeader( false );
			}
			// set header and footer fonts.
			if ( isset( $this->options['2']['header_font_size'] ) && ! empty( $this->options['2']['header_font_size'] ) && ( $this->options['2']['header_font_size'] ) > 0 ) {
				$header_font_size = $this->options['2']['header_font_size'];
			} else {
				$header_font_size = 10;
			}
			if ( isset( $this->options['7']['footer_font_size'] ) && ! empty( $this->options['footer_font_size'] ) && ( $this->options['7']['footer_font_size'] ) > 0 ) {
				$footer_font_size = $this->options['7']['footer_font_size'];
			} else {
				$footer_font_size = 10;
			}
			$pdf->setHeaderFont(
				array(
					$this->options['2']['header_font_pdf'],
					'',
					$header_font_size,
				)
			);
			$pdf->setFooterFont(
				array(
					$this->options['7']['footer_font_pdf'],
					'',
					$footer_font_size,
				)
			);

			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

			if ( isset( $this->options['1']['margin_left'] ) ) {
				$pdf->SetLeftMargin( $this->options['1']['margin_left'] );
			} else {
				$pdf->SetLeftMargin( PDF_MARGIN_LEFT );
			}

			if ( isset( $this->options['margin_right'] ) ) {
				$pdf->SetRightMargin( $this->options['1']['margin_right'] );
			} else {
				$pdf->SetRightMargin( PDF_MARGIN_RIGHT );
			}

			if ( isset( $this->options['1']['margin_top'] ) ) {
				$pdf->SetTopMargin( $this->options['1']['margin_top'] );
			} else {
				$pdf->SetTopMargin( PDF_MARGIN_TOP );
			}
			if ( isset( $this->options['2']['logomTop'] ) ) {
				$pdf->SetHeaderMargin( $this->options['2']['logomTop'] );
			} else {
				$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
			}

			if ( isset( $this->options['7']['footer_font_margin'] ) ) {
				$pdf->SetFooterMargin( $this->options['7']['footer_font_margin'] );
				// set auto page breaks.
				$pdf->SetAutoPageBreak( true, $this->options['7']['footer_font_margin'] );
			} else {
				$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
				// set auto page breaks.
				$pdf->SetAutoPageBreak( true, PDF_MARGIN_FOOTER );
			}

			if ( $this->options['1']['image_scale'] > 0 ) {
				$pdf->setImageScale( $this->options['1']['image_scale'] );
			} else {
				$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
			}

			// set default font subsetting mode.
			$pdf->setFontSubsetting( true );
			$pdf->SetFont( $this->options['1']['content_font_pdf'], '', $this->options['1']['content_font_size'], '', true );

			if ( ! empty( $this->options['5']['bullet_img_url'] ) && isset( $this->options['5']['bullet_img_url'] ) ) {
				$temp = $this->options['5']['bullet_img_url'];
				$temp = ( explode( '/', $temp ) );
				$temp = end( $temp );
				$temp = ( explode( '.', $temp ) );
				$temp = end( $temp );
				$listsymbol = 'img|' . $temp . '|' . $this->options['5']['custom_image_width'] . '|' . $this->options['5']['custom_image_height'] . '|' . $this->options['5']['bullet_img_url'];
				$pdf->setLIsymbol( $listsymbol );
			}

			// Add a page
			// This method has several options, check the source code documentation for more information.

			if ( $this->options['4']['fontStretching'] ) {
				$pdf->setFontStretching( $this->options['4']['fontStretching'] );
			} else {
				$pdf->setFontStretching( '100' );
			}
			if ( isset( $this->options['4']['fontSpacig'] ) && ! empty( $this->options['4']['fontSpacig'] ) ) {
				$pdf->setFontSpacing( $this->options['4']['fontSpacig'] );
			} else {
				$pdf->setFontSpacing( '0' );
			}
			$page_format = array();
			if ( isset( $this->options['4']['set_rotation'] ) && ! empty( $this->options['4']['set_rotation'] ) ) {
				$page_format['Rotate'] = $this->options['4']['set_rotation'];
			} else {
				$page_format['Rotate'] = 0;
			}

			if ( isset( $this->options[8]['start_page_predefined_template'] ) && $this->options[8]['start_page_predefined_template'] == 1 ) {
				$template = isset( $this->options[8]['cover_tmplt'] ) ? $this->options[8]['cover_tmplt'] : '';
				$pdf->setPrintHeader( false );
				$pdf->SetMargins( 0, 0, 0, true );
				$pdf->SetPrintFooter( false );
				$b_margin = $pdf->getBreakMargin();
				$auto_page_break = $pdf->getAutoPageBreak();
				$pdf->SetAutoPageBreak( false, 0 );
				$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
				$watermark_img = isset( $this->options[8]['Cover_image_url'] ) ? $this->options[8]['Cover_image_url'] : 0;

				$logo = isset( $this->options[8]['logo_img_url'] ) ? $this->options[8]['logo_img_url'] : '';
				$companyname = isset( $this->options[8]['company_name'] ) ? $this->options[8]['company_name'] : '';

				$companyname = strtoupper( $companyname );
				$companytagline = isset( $this->options[8]['custom_tagline'] ) ? $this->options[8]['custom_tagline'] : '';
				if ( isset( $this->options[8]['custom_footer_option'] ) && $this->options[8]['custom_footer_option'] == 1 ) {
					if ( isset( $this->options[8]['custom_footer_email'] ) && $this->options[8]['custom_footer_email'] == 1 ) {
						$email = isset( $this->options[8]['custom_footer_email_text'] ) ? $this->options[8]['custom_footer_email_text'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_contact'] ) && $this->options[8]['custom_footer_option_contact'] == 1 ) {
						$contact = isset( $this->options[8]['custom_footer_contact'] ) ? $this->options[8]['custom_footer_contact'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_address'] ) && $this->options[8]['custom_footer_option_address'] == 1 ) {
						$address = isset( $this->options[8]['custom_footer_address'] ) ? $this->options[8]['custom_footer_address'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_url'] ) && $this->options[8]['custom_footer_option_url'] == 1 ) {
						$url = isset( $this->options[8]['custom_footer_url_link'] ) ? $this->options[8]['custom_footer_url_link'] : 0;
					}
				}
				if ( $template == 'tmplt1' || $template == 'tmplt3' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null ) {
						$footer .= $address . '<br>';
					}
					if ( $this->options[8]['custom_footer_email'] !== null ) {
						$footer .= $email . '<br>';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null ) {
						$footer .= $contact . '<br>';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null ) {
						$footer .= $url . '<br>';
					}
				} elseif ( $template == 'tmplt2' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null ) {
						$footer .= '	<tr><td>' . $address . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_email'] !== null ) {
						$footer .= '	<tr><td>' . $email . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null ) {
						$footer .= '	<tr><td>' . $contact . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null ) {
						$footer .= '	<tr><td>' . $url . '</td></tr>';
					}
				} elseif ( $template == 'tmplt4' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null ) {
						$footer .= '<tr><td>' . $address . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_email'] !== null ) {
						$footer .= '<tr><td>' . $email . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null ) {
						$footer .= '<tr><td>' . $contact . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null ) {
						$footer .= '<tr><td>' . $url . '</td></tr>';
					}
				}
				if ( $template == 'tmplt1' ) {
					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" height="1056" style=" margin: 0 auto; font-family: verdana, Sans-Serif;">
								<tbody>
									<tr>
										<td style="width: 50%;">
											<table cellspacing="0" cellpadding="10" border="0" width="100%" style="border-collapse: collaps;background-color: #24B6C5;">
												<tbody>
													<tr>
														<td style="vertical-align: top; height: 100px; background-color: #e1e1e1;">&nbsp;</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 720px; background-color: #24B6C5; color: #ffffff; font-size: 44px; font-weight: bold;">' . $companyname . '</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 232px; background-color: #24B6C5; color: #ffffff; font-size: 26px; font-weight: 500;">' . $companytagline . '</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 50px; background-color: #e1e1e1;">&nbsp;</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td style="width: 50%;">
											<table cellspacing="0" cellpadding="10" border="0" width="100%" height="1056">
												<tbody>
													 <tr>
													 	<td style="vertical-align: top; height: 60px;">&nbsp;</td>
													 </tr>
													<tr>
														<td style="vertical-align: top; height: 800px;">
															<table cellspacing="0" cellpadding="0" border="0" width="100%">
																<tbody>
																	<tr>
																		<td style="width: 15%;">&nbsp;</td>
																		<td style="width: 15%;">&nbsp;</td>
																		<td style="width: 70%; color: #888888; font-size: 42px; font-weight: bold;"><img src="' . $logo . '" style="width:180px;"></td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td style="height: 190px; color: #000000; font-size: 16px;">
															<table cellspacing="0" cellpadding="0" border="0" width="100%">
																<tbody>
																	<tr><td>&nbsp;</td></tr>
																	<tr><td>&nbsp;</td></tr>
																	<tr><td>&nbsp;</td></tr>
																	<tr>
																		<td>
																			<div style="color: #888888; font-size:12px;">
																				' . $footer . '
																			</div>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 50px; border-top: 1px solid #24B6C5;">&nbsp;</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>';
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/195548-OY9OIN-908_2.jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$pdf->Image( $watermark_img, 50, 95, 130, 120, $ext, '', '', true, 500, '' );
					$pdf->writeHTML( $html1, true, 0, true, 0 );
					$pdf->setPageMark();
				} else if ( $template == 'tmplt2' ) {
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/bg-pattern.png';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$html1 .= '<table cellspacing="10" cellpadding="10" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 50%;height:280px;"><img src="' . $logo . '" style="width:150px;></td>
									<td style="width: 50%: height:280px;"></td>
								</tr>
								<tr>
									<td style="width: 35%;">&nbsp;</td>
									<td style="width: 65%;">
										<table cellspacing="0" cellpadding="10" border="0" width="100%" style="margin: 0 auto; text-align: right;">
											<tbody>
												<tr><td>&nbsp;</td></tr>
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td style="border-bottom: 1px solid #000000;">
														<div>
															<span style="font-size: 40px; font-weight: bolder; ">' . $companyname . '</span>
														</div>
													</td>
												</tr>
												
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td>
														<div style="color: #888888; font-size: 16px;">' . $companytagline . '</div>
													</td>
												</tr>
												
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td style="width: 50%; height: 100px;"></td>
									<td style="width: 50%; height: 100px;"></td>
								</tr>
								<tr>
									<td style="width: 50%; height: 100px;"></td>
									<td style="width: 50%; height: 100px;"></td>
								</tr>

								<tr>
									<td style="width:8%;"></td>
									<td style="width: 92%; color: #ffffff;">
									 <table cellspacing="0" cellpadding="3" border="0" width="40%">
											<tbody>
												' . $footer . '
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>';
					$pdf->Image( $watermark_img, 0, 0, 210, 300, '', '', '', true, 10000, '', false, false, 0 );
					$pdf->SetAutoPageBreak( $auto_page_break, $b_margin );
					$pdf->setPageMark();
					$pdf->writeHTML( $html1, true, 0, true, 0 );
				} elseif ( $template == 'tmplt3' ) {
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/temp3.jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$pdf->Image( $watermark_img, 50, 40, 160, 220, $ext, '', '', true, 5000, '' );
					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 70%;">
										<table cellspacing="0" cellpadding="10" border="0" width="100%" style="margin: 0 auto;">
											<tbody>
												<tr><td></td></tr>
												<tr>
													<td style="border-bottom: 1px solid #ffffff; border-top: 1px solid #ffffff;">
														<div>
															<span style="font-size: 52px; font-weight: bolder; color: #ffffff;">' . $companyname . '</span>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div style="color: #ffffff; font-size: 28px;">' . $companytagline . '</div>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</td>
									<td style="width: 30%;">&nbsp;</td>
								</tr>
							</tbody>
						</table>';
					$html2 .= '<table cellspacing="10" cellpadding="10" border="0" width="856px" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 70%"></td>
									<td style="width: 30%"><img src="' . $logo . '" style ="width:150px;"></td>
								</tr>
							</tbody>
						</table>';
					$html3 .= '<table cellspacing="10" cellpadding="10" border="0" width="856px" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
						<tbody>
							<tr>
								<td style="width: 30%"></td>
								<td style="width: 70%">
								<div style="font-size: 10px;">' . $footer . '</div>
								</td>
							</tr>
						</tbody>
					</table>';
					$custom_image = MWBPDF_GEN_PATH . '/asset/images/bg-img.jpg';
					$pdf->Image( $custom_image, 0, 70, 150, 155, $ext, '', '', true, 50, '' );
					$pdf->setPageMark();
					$pdf->writeHTMLCell( 180, 500, 10, 80, $html1 );
					$pdf->writeHTMLCell( 180, 500, 0, 0, $html2 );
					$pdf->writeHTMLCell( 100, 500, 25, 263, $html3 );
				} elseif ( $template == 'tmplt4' ) {

					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/3192(2).jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif; text-align: center; background-color: #FFF34D;">
								<tbody>
									<tr>
										<td height="100px;"><img style="width:150px;" src="' . $logo . '"/></td>
									</tr>
									<tr>
										<td height="150px">
											<div style="font-weight: bolder; font-size: 50px;">' . $companyname . '</div>
										</td>
									</tr>
									<tr>
										<td height="150px">
											<div style="font-style: italic; font-size: 24px;">
												' . $companytagline . '
											</div>
										</td>
									</tr>
									<tr>
										<td height="558px" style="background-color: #ffffff">Image here</td>
									</tr>
									<tr>
										<td height="52px" style="display: table; vertical-align: middle;">
											<div style="font-weight: bolder; font-size: 16px;">' . $footer . '</div>
										</td>
									</tr>
								</tbody>
							</table>';
					$pdf->writeHTML( $html1, true, 0, true, 0 );

					$pdf->Image( $watermark_img, 0, 95, 210, 173, $ext, '', '', true, 50, '' );
				}
			} elseif ( $this->options[9]['custom_cover_page'] == 1 ) {
				$pdf->SetPrintFooter( false );
				$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
				$b_margin = $pdf->getBreakMargin();
				$auto_page_break = $pdf->getAutoPageBreak();
				$pdf->SetAutoPageBreak( false, 0 );
				$watermark_img = isset( $this->options[9]['Cover_image_url'] ) ? $this->options[9]['Cover_image_url'] : '';
				$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
				$pdf->Image( $watermark_img, 0, 0, 210, 300, $ext, '', '', false, 500, '', false, false, 0 );
				$pdf->setPageMark();
				$pdf->writeHTML( $html1, true, 0, true, 0 );
				$pdf->writeHTMLCell( 200, 1, 180, 241, $html2 );
			}

			$pdf->SetMargins( $this->options['1']['margin_left'], $this->options['1']['margin_top'], $this->options['1']['margin_right'], true );
			$pdf->setPrintHeader( true );
			$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
			$pdf->SetPrintFooter( true );

			$html = '';
			if ( isset( $this->options['1']['CustomCSS_option'] ) ) {
				$html = '<style>' . $this->options['1']['Customcss'] . '</style>';
			}
			$html .= '<body>';

			$this->options = get_option( MWBPDF_GEN_PREFIX );
			if ( ! empty( $this->options['1']['rtl_support'] ) ) {
				// set some language dependent data:.
				$lg = array();
				$lg['a_meta_charset'] = 'UTF-8';
				$lg['a_meta_dir'] = 'rtl';
				$lg['a_meta_language'] = 'fa';
				$lg['w_page'] = 'page';

				// set some language-dependent strings (optional).
				$pdf->setLanguageArray( $lg );
				$pdf->setRTL( true );
			}
			if ( isset( $this->options['0']['author_name'] ) and ! $this->options['author_name'] == '' ) {
				$author_id = $post->post_author;
				$author_meta_key = $this->options['0']['author_name'];
				$author = get_user_meta( $author_id );
				$html .= '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ': </strong>' . $author[ $author_meta_key ][0] . '</p>';
			}

			if ( isset( $this->options['0']['post_categ'] ) ) {
				global $product;
				// for posts in wordpress.
				$categories = get_the_category( $post->ID );
				if ( $categories ) {
					$html .= '<p><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ': </strong>' . $categories[0]->cat_name . '</p>';
				}

				// for products in wordpress.
				$categories = get_the_terms( $post->ID, 'product_cat' );

				if ( $categories ) {
					$terms = get_the_terms( $post->ID, 'product_cat' );
					$html .= '<span><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $terms as $term ) {

						$product_cat = $term->name;

						$html .= $product_cat . ',';
					}
					$html .= '</span>';
				}
			}

			$posts = get_post_custom( $postID );

			if ( isset( $this->options['0']['post_tags'] ) ) {

				$tags = get_the_terms( get_the_ID(), 'product_tag' );
				if ( $tags ) {
					$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if ( next( $tags ) ) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
				// for posts.
				$tags = get_the_tags( $post->the_tags );
				if ( $tags ) {
					$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if ( next( $tags ) ) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
			}

			// Set some content to print.

			$post_type_val = get_post_type( $postID );
			if ( $post_type_val == 'product' ) {

				// Display featured image if set in config on page/post.
				$product_template = isset( $this->options['1']['product_tmplt'] ) ? $this->options['1']['product_tmplt'] : 'tmplt1';

				$product = new WC_product( $postID );
				$progallery = $product->get_gallery_image_ids();
				$product_pmeta = isset( $this->options['1']['pro_pmeta'] ) ? $this->options['1']['pro_pmeta'] : 4;
				$html1 = '';
				if ( isset( $this->options['0']['featured'] ) ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$html1 .= get_the_post_thumbnail( $post->ID );
					}
				}
				$html5 = '';
				if ( isset( $progallery ) && ! empty( $progallery ) ) {
					$html5 .= '<table><tr>';
					foreach ( $product->gallery_image_ids as $key => $value ) {
						$html5 .= '<td>';
						$html5 .= wp_get_attachment_image( $value );
						$html5 .= '</td>';
					}
					$html5 .= '</tr></table>';
				}
				$html2 = '';
				$html2 .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';
				$post_content = $post->post_content;
				if ( empty( $post->post_content ) ) {
					$post_content = isset( $this->options['4']['docEntryTpl'] ) ? $this->options['4']['docEntryTpl'] : '';
					$html2 .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
				} else {
					$post_excerpt = $post->post_excerpt;
					$html2 .= htmlspecialchars_decode( htmlentities( $post_excerpt, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
				}
				$html6 = '';
				$html6 .= '<h2>' . __( 'Description', 'wp-ultimate-pdf' ) . '</h2>';
				$html6 .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
				$arr = get_post_meta( $post->ID );
				unset( $arr['_edit_last'] );
				unset( $arr['_edit_lock'] );
				unset( $arr['_thumbnail_id'] );
				$arr_count = count( $arr );
				$count = 0;
				$flag = false;
				$counter = 0;
				$html3 = '';
				foreach ( $arr as $key => $val ) {
					if ( isset( $this->options['6'][ $post_type_val ][ $key ] ) && ! empty( $this->options['6'][ $post_type_val ][ $key ] ) && ( $this->options['6'][ $post_type_val ][ $key ] == 1 ) ) {

						if ( isset( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ! empty( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ( $this->options['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) && ( $this->options['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) ) {
							if ( ( null !== ( get_post_meta( $postID, $key, true ) ) ) && ( ! is_array( get_post_meta( $postID, $key, true ) ) ) ) {

								if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

									$field = get_fields( $postID );
									if ( ( array_key_exists( $key, $field ) ) ) {
										continue;
									}
								}
								if ( $arr_count > $product_pmeta ) {
									$count++;
									$flag = true;
								}
								if ( $this->options['6'][ $post_type_val ][ $key ] == '1' ) {
									$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $this->options['6'][ $post_type_val ][ $key . 'name' ] ) ) . ':' . '</strong>' . get_post_meta( $postID, $key, true ) . '</p> ';
								} elseif ( $this->options['6'][ $post_type_val ][ $key ] == '1' && $this->options['6'][ $post_type_val ][ $key . 'name' ] != '' ) {
									$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . get_post_meta( get_the_ID(), $key, true ) . '</p> ';
								}
							}
							if ( $this->options['6'][ $post_type_val ][ $key ] == '1' && $this->options['6'][ $post_type_val ][ $key . 'name' ] == '' ) {
								$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . get_post_meta( get_the_ID(), $key, true ) . '</p> ';
							}
						}
						$counter++;
						if ( $count == $product_pmeta ) {
							break;
						}
					}
				}
				$html .= $html2 . '<br>';
				$html .= '<table cellpadding="2" cellspacing="2"><tr><td>';
				$html4 = '';
				if ( $product_template == 'tmplt2' ) {
					$html4 .= $html1 . '</td><td>' . $html3 . '</td></tr><tr><td>' . $html5 . '</td></tr></table>';
					$html4 .= "<table cellpadding='2' cellspacing='2'><tr><td>" . $html6;
				} elseif ( $product_template == 'tmplt3' ) {
					$html4 .= $html3 . '</td><td>' . $html1 . '</td></tr><tr><td></td><td>' . $html5 . '</td></tr></table>';
					$html4 .= "<table cellpadding='2' cellspacing='2'><tr><td>" . $html6;
				} elseif ( $product_template == 'tmplt1' ) {
					$html4 .= $html1 . '</td></tr><tr><td>' . $html6 . '</td></tr><tr><td>' . $html3 . '</td></tr>';
					$html4 .= '<tr><td>' . $html5;
				}
				$html .= $html4;
				$html .= '</td></tr></table>';
				// Display date if set in config.
				if ( isset( $this->options['0']['post_date'] ) ) {
					$newDate = gmdate( $this->options['0']['date_formatt'], strtotime( $post->post_date ) );
					$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . $newDate . '</p>';
				}
				// Display current date if set in config.
				if ( isset( $this->options['0']['cuurent_date'] ) ) {
					$current_date = gmdate( $this->options['0']['date_formatt'] );
					$html .= '<p><strong>' . __( 'Print Date', 'wp-ultimate-pdf' ) . ': </strong>' . $current_date . '</p>';
				}
			} else {

				// Set some content to print.
				$html .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';

				// Display featured image if set in config on page/post.

				if ( isset( $this->options['0']['featured'] ) ) {
					if ( has_post_thumbnail( $post->ID ) ) {
						$html .= get_the_post_thumbnail( $post->ID );
					}
				}

				$post_template = isset( $this->options['1']['post_tmplt'] ) ? $this->options['1']['post_tmplt'] : 'tmplt1';
				if ( $post_template == 'tmplt1' ) {

					$html .= $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 1, 111 );
				} elseif ( $post_template == 'tmplt2' ) {
					$html .= $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 2, 84 );
				} elseif ( $post_template == 'tmplt3' ) {
					$html .= $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 3, 57 );
				}
			}

			/*
			 * Setting for displaying ACF field details only.
			*/

			if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

				$field = get_fields( $postID );

				if ( $field ) {
					foreach ( $field as $field_name => $value ) {

						if ( ! ( ( isset( $this->options['6'][ $post_type_val ][ $field_name ] ) && ( $this->options['6'][ $post_type_val ][ $field_name ] == 1 ) ) || ( isset( $this->options['6'][ $post_type_val ][ '_' . $field_name ] ) && ( $this->options['6'][ $post_type_val ][ '_' . $field_name ] == 1 ) ) ) ) {
							continue;
						}

						$field = get_field_object( $field_name, $postID );

						// do not need to display password.
						if ( isset( $field['type'] ) && ( $field['type'] == 'password' ) ) {
							continue;
						}
						// coding to display ACF field image.
						if ( isset( $field['type'] ) && ( $field['type'] == 'image' ) ) {

							if ( $field['save_format'] == 'id' && $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ':</strong><p>';
								$html .= wp_get_attachment_image( $field['value'], 'thumbnail' );
							} elseif ( $field['save_format'] == 'url' ) {

								if ( $field['label'] != '' && $field['value'] != '' ) {

									$html .= '<p><strong>' . $field['label'] . ':</strong><a href=" ' . $field['value'] . '" >' . $field['value'] . '</a><p>';
								}
							} elseif ( $field['save_format'] == 'object' && $field['label'] != '' && $field['value']['id'] != '' ) {

								if ( $field['label'] != '' && $field['value']['id'] != '' ) {

									$html .= $field['label'] . ':';
									$html .= wp_get_attachment_image( $field['value']['id'], 'thumbnail' );
								}
							}

							continue;
						}

						// coding for displaying Taxonomy fields.

						if ( isset( $field['type'] ) && ( $field['type'] == 'taxonomy' ) ) {
							continue;
						}

						// coding for displaying Google map texual address.
						if ( isset( $field['type'] ) && ( $field['type'] == 'google_map' ) ) {
							if ( $field['label'] != '' && $field['value']['address'] != '' ) {

								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value']['address'] . '</p> ';
							}
							continue;
						}

						// coding to display Date Picker.
						if ( isset( $field['type'] ) && ( $field['type'] == 'date_picker' ) ) {

							if ( $field['label'] != '' && $field['value'] != '' && ( $field['date_format'] == 'yymmdd' ) ) {
								// convert the date string in proper format.
								$newstr = substr_replace( $field['value'], ' : ', 4, 0 );
								$newstr = substr_replace( $newstr, ' : ', 9, 0 );

								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $newstr . '</p> ';
							}
							continue;
						}
						// coding to display radio button details.
						if ( ! is_array( $value ) && ( $field['type'] == 'radio' ) && ( isset( $field['choices'] ) ) ) {
							if ( $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value'] . '</p> ';
							}
							continue;
						}

						// coding to display links for product and any post details.
						if ( isset( $field ) && ( $field['type'] == 'page_link' ) ) {
							if ( is_array( $field['value'] ) && ! empty( $field['value'] ) ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

								foreach ( $field['value'] as $current_url_value ) {

									if ( isset( $current_url_value ) && ( $current_url_value != '' ) ) {
										$html .= '<p><a href="' . $current_url_value . '">' . $current_url_value . '</a><p>';
									}
								}
							} else {
								if ( ( $field['label'] != '' ) && ( $field['value'] != '' ) ) {
									$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

									$html .= '<p><a href="' . $field['value'] . '">' . $field['value'] . '</a><p>';
								}
							}
							continue;
						}

						// coding to handle post data Object
						if ( isset( $field ) && ( $field['type'] == 'post_object' ) && ( ! empty( $field['value'] ) ) ) {
							$acf_fetched_post_value = $field['value'];
							if ( isset( $acf_fetched_post_value ) && ! empty( $acf_fetched_post_value ) && is_array( $acf_fetched_post_value ) ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';
								foreach ( $acf_fetched_post_value as $acf_fetched_one_post_value ) {

									if ( ( get_permalink( $acf_fetched_one_post_value->ID ) != '' ) && ( get_the_title( $acf_fetched_one_post_value->ID ) != '' ) ) {

										$html .= '<p>' . $acf_fetched_one_post_value->post_type . '  -->  ' . '<a href="' . get_permalink( $acf_fetched_one_post_value->ID ) . '">' . get_the_title( $acf_fetched_one_post_value->ID ) . '</a></p>';
									}
								}
							} else {
								if ( ( get_permalink( $acf_fetched_post_value->ID ) != '' ) && ( get_the_title( $acf_fetched_post_value->ID ) != '' ) ) {
									$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

									$html .= '<p>' . $acf_fetched_post_value->post_type . '  -->  ' . '<a href="' . get_permalink( $acf_fetched_post_value->ID ) . '">' . get_the_title( $acf_fetched_post_value->ID ) . '</a></p>';
								}
							}

							continue;
						}

						// cofing to display text, textarea, email, (password need to block), any field returning simple string.
						if ( ! is_array( $value ) && ( ! isset( $field['choices'] ) ) && ! is_object( $value ) ) {
							if ( $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value'] . '</p> ';
							}
						}

						// cofing to display radio button, select and checkbox details.
						if ( is_array( $field ) && isset( $field['choices'] ) && ! empty( $field['choices'] ) && ( $field['type'] != 'radio' ) ) {
							$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

							foreach ( $field['value'] as $selected_value ) {
								if ( isset( $field['choices'][ $selected_value ] ) && ( $field['choices'][ $selected_value ] != '' ) ) {

									$html .= '<p>' . $field['choices'][ $selected_value ] . '</p> ';
								}
							}
						}
					}
				}
			}

			/*
			  ACF field details end.
			*/

			$html .= '</body>';
			$dom = new simple_html_dom();
			$dom->load( $html );

			foreach ( $dom->find( 'img' ) as $e ) {
				// Start Image check by om.
				$exurl = ''; // external streams.
				$imsize = false;
				$file = $e->src;
				// check if we are passing an image as file or string.
				if ( $file[0] === '@' ) {
					// image from string.
					$imgdata = substr( $file, 1 );
				} else { // image file
					if ( $file[0] === '*' ) {
						// image as external stream.
						$file = substr( $file, 1 );
						$exurl = $file;
					}
					// check if is local file.
					if ( ! @file_exists( $file ) ) {
						// encode spaces on filename (file is probably an URL).
						$file = str_replace( ' ', '%20', $file );
					}
					if ( @file_exists( $file ) ) {
						// get image dimensions.
						$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

						$get_url_path = ABSPATH . '/' . $get_url_path;
						$imsize = @getimagesize( $get_url_path );
					}
					if ( $imsize === false ) {
						$imgdata = TCPDF_STATIC::fileGetContents( $file );
					}
				}
				if ( isset( $imgdata ) and ( $imgdata !== false ) and ( strpos( $file, '__tcpdf_img' ) === false ) ) {
					// check Image size.
					$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

					$get_url_path = ABSPATH . '/' . $get_url_path;
					$imsize = @getimagesize( $get_url_path );
				}
				if ( $imsize === false ) {
					$e->outertext = '';
				} else {
					// End Image Check.
					if ( preg_match( '/alignleft/i', $e->class ) ) {
						$imgalign = 'left';
					} elseif ( preg_match( '/alignright/i', $e->class ) ) {
						$imgalign = 'right';
					} elseif ( preg_match( '/aligncenter/i', $e->class ) ) {
						$imgalign = 'center';
						$htmlimgalign = 'middle';
					} else {
						$imgalign = 'none';
					}

					$e->class = null;
					$e->align = $imgalign;
					if ( isset( $htmlimgalign ) ) {
						$e->style = 'float:' . $htmlimgalign;
					} else {
						$e->style = 'float:' . $imgalign;
					}

					if ( strtolower( substr( $e->src, -4 ) ) == '.svg' ) {
						$e->src = null;
						$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
					} else {
						$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
					}
				}
			}
			$html = $dom->save();
			$dom->clear();
			$pdf->setFormDefaultProp(
				array(
					'lineWidth' => 1,
					'borderStyle' => 'solid',
					'fillColor' => array(
						255,
						255,
						200,
					),
					'strokeColor' => array(
						255,
						128,
						128,
					),
				)
			);

			// Print text using writeHTML.
			$pdf->writeHTML( $html, true, 0, true, 0 );
			if ( isset( $this->options['3']['add_watermark'] ) ) {
				$no_of_pages = $pdf->getNumPages();
				if ( isset( $this->options[8]['start_page_predefined_template'] ) || isset( $this->options[9]['custom_cover_page'] ) ) {
					$i = 2;
				} else {
					$i = 1;
				}
				for ( $i = 1; $i <= $no_of_pages; $i++ ) {
					$pdf->setPage( $i );

					// Get the page width/height.
					$my_page_width = $pdf->getPageWidth();
					$my_page_height = $pdf->getPageHeight();

					// Find the middle of the page and adjust.
					$myX = ( $my_page_width / 2 ) - 75;
					$myY = ( $my_page_height / 2 ) + 25;

					// Set the transparency of the text to really light.
					$pdf->SetAlpha( 0.09 );

					// Rotate 45 degrees and write the watermarking text.
					$pdf->StartTransform();
					$rotate_degr = isset( $this->options['3']['rotate_water'] ) ? $this->options['3']['rotate_water'] : '45';
					$pdf->Rotate( $rotate_degr, $myX, $myY );
					$water_font = isset( $this->options['3']['water_font'] ) ? $this->options['3']['water_font'] : 'courier';
					$pdf->SetFont( $water_font, '', 30 );
					$watermark_text = isset( $this->options['3']['watermark_text'] ) ? $this->options['3']['watermark_text'] : '';
					$pdf->Text( $myX, $myY, $watermark_text );
					$pdf->StopTransform();

					// Reset the transparency to default.
					$pdf->SetAlpha( 1 );
				}
			}

			if ( isset( $this->options['3']['add_watermark_image'] ) ) {
				if ( ! empty( $this->options['3']['background_img_url'] ) ) {
					$no_of_pages = $pdf->getNumPages();
					if ( isset( $this->options[8]['start_page_predefined_template'] ) || isset( $this->options[9]['custom_cover_page'] ) ) {
						$i = 2;
					} else {
						$i = 1;
					}
					for ( $i = 1; $i <= $no_of_pages; $i++ ) {
						$pdf->setPage( $i );

						$my_page_width = $pdf->getPageWidth();
						$my_page_height = $pdf->getPageHeight();
						$myxaxis = $this->options['3']['water_img_x_axis'];
						$myyaxis = $this->options['3']['water_img_y_axis'];
						$ImageT = isset( $this->options['3']['water_img_t'] ) ? $this->options['3']['water_img_t'] : '';
						// Set the transparency of the text to really light.
						$pdf->SetAlpha( $ImageT );

						// Rotate 45 degrees and write the watermarking text.
						$pdf->StartTransform();
						$ImageH = isset( $this->options['3']['water_img_h'] ) ? $this->options['3']['water_img_h'] : '';
						$ImageW = isset( $this->options['3']['water_img_w'] ) ? $this->options['3']['water_img_w'] : '';

						$watermark_img = isset( $this->options['3']['background_img_url'] ) ? $this->options['3']['background_img_url'] : '';
						$pdf->Image( $watermark_img, $myxaxis, $myyaxis, $ImageW, $ImageH, $ext, $watermark_img, '', true, 500, '' );
						$pdf->StopTransform();

						// Reset the transparency to default.
						$pdf->SetAlpha( 1 );
					}
				}
			}
			// cache directory.
			if ( ! is_dir( MWB_CACHE_DIR ) ) {
				mkdir( MWB_CACHE_DIR, 0755, true );
			}
			$pdf->Output( $filePath, 'F' );

			$to = $useremailID;
			$from = 'wp-Ultimate-Pdf-Generator';
			$subject = 'Here is your pdf attachment';
			$headers = "from: $from ";
			$message = 'Please download attached PDF ';
			$title = get_the_title( $postID );
			$type = get_post_type( $postID );
			if ( wp_mail(
				$to,
				$subject,
				$message,
				$headers = '',
				$attachments = array(
					$filePath,
				)
			) ) {
				$this->export_installdata( $to, 'guest', $title, $type );
				$response = array( 'SENT' => true );
				wp_send_json( $response );
			} else {
				$response = array( 'NOTSENT' => true );
				wp_send_json( $response );
			}
		}

		/**
		 * Generate pdf file of a post given as parameter.
		 *
		 * @name generate_post_to_pdf_file.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param int $postID post .
		 */
		function generate_post_to_pdf_file( $postID ) {
			$footer = '';
			$html1 = '';
			$html2 = '';
			$html3 = '';
			$logo_width = '';
			$post = get_post( $postID );
			$title = get_the_title( $postID );
			$type = get_post_type( $postID );
			if ( ! class_exists( 'TCPDF' ) ) {
				require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
			}
			if ( ! class_exists( 'pdfheader' ) ) {
				require_once MWBPDF_GEN_PATH . '/Content/pdf_headercontent.php';
			}
			if ( ! class_exists( 'simple_html_dom_node' ) ) {
				require_once MWBPDF_GEN_PATH . '/Htmlstructure/Htmlstructure.php';
			}
			$post->post_content = apply_filters( 'the_post_export_content', $post->post_content );

			$post->post_content = wpautop( $post->post_content );
			$post->post_content = do_shortcode( $post->post_content );
			$post->post_content = apply_filters( 'the_content', $post->post_content );
			if ( $this->options['0']['mwb_file_name'] == 'post_name' ) {
				$filePath = MWB_CACHE_DIR . '/' . $post->post_name . '.pdf';
			} else {
				$filePath = MWB_CACHE_DIR . '/' . $post->ID . '.pdf';
			}

			// new PDF document.
			if ( isset( $this->options['1']['page_size'] ) ) {
				$pagesize = ( $this->options['1']['page_size'] );
			} else {
				$pagesize = PDF_PAGE_FORMAT;
			}
			if ( isset( $this->options['1']['unitmeasure'] ) ) {
				$unit = ( $this->options['1']['unitmeasure'] );
			} else {
				$unit = PDF_UNIT;
			}
			if ( isset( $this->options['1']['page_orientation'] ) ) {
				$orientation = ( $this->options['1']['page_orientation'] );
			} else {
				$orientation = PDF_PAGE_ORIENTATION;
			}

			$pdf = new CUSTOMPDF( $orientation, $unit, $pagesize, true, 'UTF-8', false );
			Mwbpdf_Pdf_Security::apply( $pdf, $this->options, $postID );
			// information about doc.
			$pdf->SetCreator( 'Post to PDF plugin by makewebbetter with ' . PDF_CREATOR );
			$pdf->SetAuthor( get_bloginfo( 'name' ) );
			if ( ! empty( $this->options['1']['custom_title'] ) ) {
				$pdf_title = $this->options['1']['custom_title'];
			} else {
				$pdf_title = $post->post_title;
			}

			// logo width calculation.

			if ( $this->options['2']['page_header'] == 'upload-image' ) {
				$logo_image_url = $this->options['2']['logo_img_url'];
			}
			$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

			$get_url_path = ABSPATH . '/' . $get_url_path;
			$infologo = getimagesize( $get_url_path );
			if ( isset( $infologo ) && ! empty( $infologo ) ) {
				if ( isset( $this->options['2']['image_factor'] ) && ! empty( $this->options['2']['image_factor'] ) ) {
					$logo_width = (int) ( ( $this->options['2']['image_factor'] * $infologo[0] ) / $infologo[1] );
				} else {
					$logo_width = (int) ( ( 12 * $infologo[0] ) / $infologo[1] );
				}
			} else {
				$logo_width = 0;
			}

			// for PHP 5.4 or below set default header data
			$site_name = isset( $this->options['2']['site_name_header'] ) ? $this->options['2']['site_name_header'] : 0;
			$site_description = isset( $this->options['2']['site_description_header'] ) ? $this->options['2']['site_description_header'] : 0;
			$site_url = isset( $this->options['2']['site_url_header'] ) ? $this->options['2']['site_url_header'] : 0;
			if ( version_compare( phpversion(), '5.4.0', '<' ) ) {
				if ( $site_name == 0 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_url == 1 && $site_description == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, ' ', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, '', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
			} else {
				if ( $site_name == 0 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_url == 1 && $site_description == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, ' ', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 0 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( get_bloginfo( 'description' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 1 && $site_url == 0 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_QUOTES ), $header_text_color, $header_line_color );
				}
				if ( $site_name == 0 && $site_description == 1 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, '', html_entity_decode( get_bloginfo( 'description' ) . "\n" . home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
				if ( $site_name == 1 && $site_description == 0 && $site_url == 1 ) {
					$pdf->SetHeaderData( $logo_image_url, $logo_width, html_entity_decode( get_bloginfo( 'name' ), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ), html_entity_decode( home_url(), ENT_COMPAT | ENT_HTML401 | ENT_QUOTES ) );
				}
			}

			if ( isset( $this->options['2']['page_header'] ) and ( $this->options['2']['page_header'] ) == 'None' ) {
				$pdf->setPrintHeader( false );
			}
			// set header and footer fonts.
			if ( ( $this->options['2']['header_font_size'] ) > 0 ) {
				$header_font_size = $this->options['2']['header_font_size'];
			} else {
				$header_font_size = 10;
			}
			if ( isset( $this->options['7']['footer_font_size'] ) && ! empty( $this->options['footer_font_size'] ) && ( $this->options['7']['footer_font_size'] ) > 0 ) {
				$footer_font_size = $this->options['7']['footer_font_size'];
			} else {
				$footer_font_size = 10;
			}
			$header_font_pdf_value_dis = isset( $this->options['2']['header_font_pdf'] ) ? $this->options['2']['header_font_pdf'] : 'helvetica';

			$pdf->setHeaderFont(
				array(
					$header_font_pdf_value_dis,
					'',
					$header_font_size,
				)
			);

			$header_font_pdf_value_dis = isset( $this->options['7']['footer_font_pdf'] ) ? $this->options['7']['footer_font_pdf'] : 'helvetica';

			$pdf->setFooterFont(
				array(
					$header_font_pdf_value_dis,
					'',
					$footer_font_size,
				)
			);

			$pdf->SetDefaultMonospacedFont( PDF_FONT_MONOSPACED );

			if ( isset( $this->options['1']['margin_left'] ) ) {
				$pdf->SetLeftMargin( $this->options['1']['margin_left'] );
			} else {
				$pdf->SetLeftMargin( PDF_MARGIN_LEFT );
			}

			if ( isset( $this->options['1']['margin_right'] ) ) {
				$pdf->SetRightMargin( $this->options['1']['margin_right'] );
			} else {
				$pdf->SetRightMargin( PDF_MARGIN_RIGHT );
			}

			if ( isset( $this->options['1']['margin_top'] ) ) {
				$pdf->SetTopMargin( $this->options['1']['margin_top'] );
			} else {
				$pdf->SetTopMargin( PDF_MARGIN_TOP );
			}
			if ( ( isset( $this->options['2']['logomTop'] ) ) ) {
				$pdf->SetHeaderMargin( $this->options['2']['logomTop'] );
			} else {
				$pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
			}

			if ( isset( $this->options['7']['footer_font_margin'] ) ) {
				$pdf->SetFooterMargin( $this->options['7']['footer_font_margin'] );
				// set auto page breaks.
				$pdf->SetAutoPageBreak( true, $this->options['7']['footer_font_margin'] );
			} else {
				$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
				// set auto page breaks.
				$pdf->SetAutoPageBreak( true, PDF_MARGIN_FOOTER );
			}
			// set image scale factor.
			if ( $this->options['1']['image_scale'] > 0 ) {
				$pdf->setImageScale( $this->options['1']['image_scale'] );
			} else {
				$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
			}

			// set default font subsetting mode.
			$pdf->setFontSubsetting( true );
			$pdf->SetFont( $this->options['1']['content_font_pdf'], '', $this->options['1']['content_font_size'], '', true );

			if ( isset( $this->options['5']['bullet_img_url'] ) && ! empty( $this->options['5']['bullet_img_url'] ) ) {

				$temp = $this->options['5']['bullet_img_url'];
				$temp = ( explode( '/', $temp ) );
				$temp = end( $temp );
				$temp = ( explode( '.', $temp ) );
				$temp = end( $temp );
				$listsymbol = 'img|' . $temp . '|' . $this->options['5']['custom_image_width'] . '|' . $this->options['5']['custom_image_height'] . '|' . $this->options['5']['bullet_img_url'];
				$pdf->setLIsymbol( $listsymbol );
			}
			// Add a page.
			// This method has several options, check the source code documentation for more information.

			if ( $this->options['4']['fontStretching'] ) {
				$pdf->setFontStretching( $this->options['4']['fontStretching'] );
			}
			if ( $this->options['4']['fontSpacig'] ) {
				$pdf->setFontSpacing( $this->options['4']['fontSpacig'] );
			}
			$page_format = array();
			if ( isset( $this->options['4']['set_rotation'] ) && ! empty( $this->options['4']['set_rotation'] ) ) {
				$page_format['Rotate'] = $this->options['4']['set_rotation'];
			} else {
				$page_format['Rotate'] = 0;
			}
			$page_orientation_value_dis = isset( $this->options['1']['page_orientation'] ) ? $this->options['1']['page_orientation'] : 'P';
			if ( isset( $this->options[8]['start_page_predefined_template'] ) && $this->options[8]['start_page_predefined_template'] == 1 ) {
				$template = isset( $this->options[8]['cover_tmplt'] ) ? $this->options[8]['cover_tmplt'] : '';
				$pdf->setPrintHeader( false );
				$pdf->SetMargins( 0, 0, 0, true );
				$pdf->SetPrintFooter( false );
				$b_margin = $pdf->getBreakMargin();
				$auto_page_break = $pdf->getAutoPageBreak();
				$pdf->SetAutoPageBreak( false, 0 );
				$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
				$watermark_img = isset( $this->options[8]['Cover_image_url'] ) ? $this->options[8]['Cover_image_url'] : 0;

				$logo = isset( $this->options[8]['logo_img_url'] ) ? $this->options[8]['logo_img_url'] : '';
				$companyname = isset( $this->options[8]['company_name'] ) ? $this->options[8]['company_name'] : '';

				$companyname = strtoupper( $companyname );
				$companytagline = isset( $this->options[8]['custom_tagline'] ) ? $this->options[8]['custom_tagline'] : '';
				if ( isset( $this->options[8]['custom_footer_option'] ) && $this->options[8]['custom_footer_option'] == 1 ) {
					if ( isset( $this->options[8]['custom_footer_email'] ) && $this->options[8]['custom_footer_email'] == 1 ) {
						$email = isset( $this->options[8]['custom_footer_email_text'] ) ? $this->options[8]['custom_footer_email_text'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_contact'] ) && $this->options[8]['custom_footer_option_contact'] == 1 ) {
						$contact = isset( $this->options[8]['custom_footer_contact'] ) ? $this->options[8]['custom_footer_contact'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_address'] ) && $this->options[8]['custom_footer_option_address'] == 1 ) {
						$address = isset( $this->options[8]['custom_footer_address'] ) ? $this->options[8]['custom_footer_address'] : 0;
					}
					if ( isset( $this->options[8]['custom_footer_option_url'] ) && $this->options[8]['custom_footer_option_url'] == 1 ) {
						$url = isset( $this->options[8]['custom_footer_url_link'] ) ? $this->options[8]['custom_footer_url_link'] : 0;
					}
				}

				if ( $template == 'tmplt1' || $template == 'tmplt3' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null && isset( $address ) ) {
						$footer .= $address . '<br>';
					}
					if ( $this->options[8]['custom_footer_email'] !== null && isset( $email ) ) {
						$footer .= $email . '<br>';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null && isset( $contact ) ) {
						$footer .= $contact . '<br>';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null && isset( $url ) ) {
						$footer .= $url . '<br>';
					}
				} elseif ( $template == 'tmplt2' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null && isset( $address ) ) {
						$addurl = MWBPDF_GEN_URL . '/asset/images/address.png';
						$adimg = 'Address:';
						$footer .= "<tr><td><span style='width:150px; display:table;'>" . $adimg . '&nbsp;&nbsp;</span>' . $address . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_email'] !== null && isset( $email ) ) {
						$emailurl = MWBPDF_GEN_URL . '/asset/images/mail.png';
						$mailimg = 'Email:';
						$footer .= "<tr><td><span style='width:150px; display:table;'>" . $mailimg . '&nbsp;&nbsp;</span>' . $email . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null && isset( $contact ) ) {
						$contacturl = MWBPDF_GEN_URL . '/asset/images/contact.png';
						$contactimg = 'Contact:';
						$footer .= "<tr><td><span style='width:150px; display:table;'>" . $contactimg . '&nbsp;&nbsp;</span>' . $contact . '</td></tr>';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null && isset( $url ) ) {
						$urlurl = MWBPDF_GEN_URL . '/asset/images/web.png';
						$urlimg = 'Url:';
						$footer .= "<tr><td><span style='width:150px; display:table;'>" . $urlimg . '&nbsp;&nbsp;</span>' . $url . '</td></tr>';
					}
				} elseif ( $template == 'tmplt4' ) {
					if ( $this->options[8]['custom_footer_option_address'] !== null && $this->options[8]['custom_footer_option'] == 1 ) {
						$footer .= '' . $address . '|';
					}
					if ( $this->options[8]['custom_footer_email'] !== null && $this->options[8]['custom_footer_option'] == 1 ) {
						$footer .= '' . $email . '|';
					}
					if ( $this->options[8]['custom_footer_option_contact'] !== null && $this->options[8]['custom_footer_option'] == 1 ) {
						$footer .= '' . $contact . '|';
					}
					if ( $this->options[8]['custom_footer_option_url'] !== null ) {
						$footer .= '' . $url . '';
					}
				}
				if ( $template == 'tmplt1' ) {
					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" height="1056" style=" margin: 0 auto; font-family: verdana, Sans-Serif;">
								<tbody>
									<tr>
										<td style="width: 50%;">
											<table cellspacing="0" cellpadding="10" border="0" width="100%" style="border-collapse: collaps;background-color: #24B6C5;">
												<tbody>
													<tr>
														<td style="vertical-align: top; height: 100px; background-color: #e1e1e1;">&nbsp;</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 720px; background-color: #24B6C5; color: #ffffff; font-size: 44px; font-weight: bold;">' . $companyname . '</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 232px; background-color: #24B6C5; color: #ffffff; font-size: 26px; font-weight: 500;">' . $companytagline . '</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 50px; background-color: #e1e1e1;">&nbsp;</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td style="width: 50%;">
											<table cellspacing="0" cellpadding="10" border="0" width="100%" height="1056">
												<tbody>
													 <tr>
													 	<td style="vertical-align: top; height: 60px;">&nbsp;</td>
													 </tr>
													<tr>
														<td style="vertical-align: top; height: 800px;">
															<table cellspacing="0" cellpadding="0" border="0" width="100%">
																<tbody>
																	<tr>
																		<td style="width: 15%;">&nbsp;</td>
																		<td style="width: 15%;">&nbsp;</td>
																		<td style="width: 70%; color: #888888; font-size: 42px; font-weight: bold;"><img src="' . $logo . '" style="width:180px;"></td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td style="height: 190px; color: #000000; font-size: 16px;">
															<table cellspacing="0" cellpadding="0" border="0" width="100%">
																<tbody>
																	<tr><td>&nbsp;</td></tr>
																	<tr><td>&nbsp;</td></tr>
																	<tr><td>&nbsp;</td></tr>
																	<tr>
																		<td>
																			<div style="color: #888888; font-size:12px;">
																				' . $footer . '
																			</div>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr>
														<td style="vertical-align: top; height: 50px; border-top: 1px solid #24B6C5;">&nbsp;</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>';
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/195548-OY9OIN-908_2.jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$pdf->Image( $watermark_img, 50, 95, 130, 120, $ext, '', '', true, 500, '' );
					$pdf->writeHTML( $html1, true, 0, true, 0 );

					$pdf->setPageMark();
				} else if ( $template == 'tmplt2' ) {
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/bg-pattern.png';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$html1 .= '<table cellspacing="10" cellpadding="10" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 50%;height:280px;"><img src="' . $logo . '" style="width:150px;"></td>
									<td style="width: 50%: height:280px;"></td>
								</tr>
								<tr>
									<td style="width: 35%;">&nbsp;</td>
									<td style="width: 65%;">
										<table cellspacing="0" cellpadding="10" border="0" width="100%" style="margin: 0 auto; text-align: right;">
											<tbody>
												<tr><td>&nbsp;</td></tr>
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td style="border-bottom: 1px solid #000000;">
														<div>
															<span style="font-size: 40px; font-weight: bolder; ">' . $companyname . '</span>
														</div>
													</td>
												</tr>
												
												<tr><td>&nbsp;</td></tr>
												<tr>
													<td>
														<div style="color: #888888; font-size: 16px;">' . $companytagline . '</div>
													</td>
												</tr>
												
											</tbody>
										</table>
									</td>
								</tr>
								<tr>
									<td style="width: 50%; height: 100px;"></td>
									<td style="width: 50%; height: 100px;"></td>
								</tr>
								<tr>
									<td style="width: 50%; height: 100px;"></td>
									<td style="width: 50%; height: 100px;"></td>
								</tr>

								<tr>
									<td style="width:8%;"></td>
									<td style="width: 92%; color: #ffffff;">
									 <table cellspacing="0" cellpadding="3" border="0" width="42%">
											<tbody>
												' . $footer . '
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>';
					$pdf->Image( $watermark_img, 0, 0, 210, 300, '', '', '', true, 10000, '', false, false, 0 );
					$pdf->SetAutoPageBreak( $auto_page_break, $b_margin );
					$pdf->setPageMark();
					$pdf->writeHTML( $html1, true, 0, true, 0 );
				} elseif ( $template == 'tmplt3' ) {
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/temp3.jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$pdf->Image( $watermark_img, 50, 40, 160, 220, $ext, '', '', true, 5000, '' );
					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 70%;">
										<table cellspacing="0" cellpadding="10" border="0" width="100%" style="margin: 0 auto;">
											<tbody>
												<tr><td></td></tr>
												<tr>
													<td style="border-bottom: 1px solid #ffffff; border-top: 1px solid #ffffff;">
														<div>
															<span style="font-size: 52px; font-weight: bolder; color: #ffffff;">' . $companyname . '</span>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div style="color: #ffffff; font-size: 28px;">' . $companytagline . '</div>
													</td>
												</tr>
												<tr>
													<td>&nbsp;</td>
												</tr>
											</tbody>
										</table>
									</td>
									<td style="width: 30%;">&nbsp;</td>
								</tr>
							</tbody>
						</table>';
					$html2 .= '<table cellspacing="10" cellpadding="10" border="0" width="856px" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
							<tbody>
								<tr>
									<td style="width: 70%"></td>
									<td style="width: 30%"><img src="' . $logo . '" style ="width:150px;"></td>
								</tr>
							</tbody>
						</table>';
					$html3 .= '<table cellspacing="10" cellpadding="10" border="0" width="856px" style="margin: 0 auto; font-family: Verdana, Sans-Serif;">
						<tbody>
							<tr>
								<td style="width: 30%"></td>
								<td style="width: 70%">
								<div style="font-size: 10px;">' . $footer . '</div>
								</td>
							</tr>
						</tbody>
					</table>';
					$custom_image = MWBPDF_GEN_PATH . '/asset/images/bg-img.jpg';
					$pdf->Image( $custom_image, 0, 70, 150, 155, 'jpg', '', '', true, 50, '' );
					$pdf->setPageMark();
					$pdf->writeHTMLCell( 180, 500, 10, 80, $html1 );
					$pdf->writeHTMLCell( 170, 450, 0, 0, $html2 );
					$pdf->writeHTMLCell( 100, 500, 25, 263, $html3 );
				} elseif ( $template == 'tmplt4' ) {
					if ( $watermark_img == '' ) {
						$watermark_img = MWBPDF_GEN_PATH . '/asset/images/3192(2).jpg';
					}
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );

					$html1 .= '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="margin: 0 auto; font-family: Verdana, Sans-Serif; text-align: center; background-color: #FFF34D;">
								<tbody>
								<tr>
									
										<td height="15"></td>
									</tr>
									<tr>
									
										<td height="150"><img style="width:150px;" src="' . $logo . '"/></td>
									</tr>
									<tr>
										<td height="115">
											<div style="font-weight: bolder; font-size: 50px;">' . $companyname . '</div>
										</td>
									</tr>
									<tr>
										<td height="200">
											<div style="font-style: italic; font-size: 24px;">
												' . $companytagline . '
											</div>
										</td>
									</tr>
									<tr>
										<td height="558" style="background-color: #FFF34D">Image here</td>
									</tr>
									<tr>
										<td height="55" style="display: table; vertical-align: middle;">
											<div style="font-weight: bolder; font-size: 16px;">' . $footer . '</div>
										</td>
									</tr>
								</tbody>
							</table>';

					$pdf->writeHTML( $html1, true, 0, true, 0 );
					$pdf->Image( $watermark_img, 0, 95, 210, 173, $ext, '', '', true, 50, '' );
				}
			} elseif ( isset( $this->options[9]['custom_cover_page'] ) ) {
				if ( $this->options[9]['custom_cover_page'] == 1 && ! empty( $this->options[9]['Cover_image_url'] ) ) {
					$pdf->SetPrintFooter( false );
					$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
					$b_margin = $pdf->getBreakMargin();
					$auto_page_break = $pdf->getAutoPageBreak();
					$pdf->SetAutoPageBreak( false, 0 );

					$watermark_img = isset( $this->options[9]['Cover_image_url'] ) ? $this->options[9]['Cover_image_url'] : '';
					$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
					$pdf->Image( $watermark_img, 0, 0, 210, 300, $ext, '', '', false, 500, '', false, false, 0 );
					$pdf->setPageMark();
					$pdf->writeHTML( $html1, true, 0, true, 0 );
					$pdf->writeHTMLCell( 200, 1, 180, 241, $html2 );
				}
			}

			$pdf->SetMargins( $this->options['1']['margin_left'], $this->options['1']['margin_top'], $this->options['1']['margin_right'], true );
			$pdf->setPrintHeader( true );
			$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
			$pdf->AddPage( $page_orientation_value_dis, $page_format, false, false );
			$pdf->SetPrintFooter( true );

			$html = '';
			if ( isset( $this->options['1']['CustomCSS_option'] ) ) {
				$html = '<style>' . $this->options['1']['Customcss'] . '</style>';
			}
			$html .= '<body>';
			$pdf->setFontSubsetting( true );
			$pdf->SetFont( $this->options['1']['content_font_pdf'], '', $this->options['1']['content_font_size'], '', true );
			$this->options = get_option( MWBPDF_GEN_PREFIX );
			if ( ! empty( $this->options['1']['rtl_support'] ) ) {
				// set some language dependent data:.
				$lg = array();
				$lg['a_meta_charset'] = 'UTF-8';
				$lg['a_meta_dir'] = 'rtl';
				$lg['a_meta_language'] = 'fa';
				$lg['w_page'] = 'page';

				// set some language-dependent strings (optional).
				$pdf->setLanguageArray( $lg );
				$pdf->setRTL( true );
			}
			if ( isset( $this->options['0']['author_name'] ) and ! $this->options['0']['author_name'] == '' ) {
				$author_id = $post->post_author;
				$author_meta_key = $this->options['0']['author_name'];
				$author = get_user_meta( $author_id );

				$author_name = $author[ $author_meta_key ][0];
				$html .= '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ': </strong>' . $author[ $author_meta_key ][0] . '</p>';
			}

			if ( isset( $this->options['0']['post_categ'] ) and ! $this->options['0']['post_categ'] == '' ) {
				global $product;
				// for posts in wordpress.
				$categories = get_the_category( $post->ID );
				if ( $categories ) {
					$html .= '<p><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $categories as $cd ) {

						$html .= $cd->cat_name . ',';
					}

					$html .= '</p>';
				}
				$categories = get_the_terms( $post->ID, 'product_cat' );

				// for products in wordpress.
				if ( $categories ) {
					$terms = get_the_terms( $post->ID, 'product_cat' );
					$html .= '<span><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $terms as $term ) {

						$product_cat = $term->name;

						$html .= $product_cat . ',';
					}
					$html .= '</span>';
				}
			}

			$posts = get_post_custom( $postID );

			if ( isset( $this->options['0']['post_tags'] ) ) {
				// for product.
				$tags = get_the_terms( get_the_ID(), 'product_tag' );
				if ( $tags ) {
					$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if ( next( $tags ) ) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
				// for posts.
				$tags = get_the_tags( $post->the_tags );
				if ( $tags ) {
					$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ': </strong>';
					foreach ( $tags as $tag ) {
						$tag_link = get_tag_link( $tag->term_id );
						$html .= '<a href="' . $tag_link . '">' . $tag->name . '</a>';
						if ( next( $tags ) ) {
							$html .= ', ';
						}
					}
					$html .= '</p>';
				}
			}
			$post_type_val = get_post_type( $postID );

			if ( $post_type_val == 'product' ) {

				$html = $this->get_product_html_body( $postID, '', $pdf_title, $post, $post_type_val, $author_name );

				$html = $pdf->writeHTML( $html, true, 0, true, 0 );
			} else {

				// Set some content to print.
				$html .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';

				// Display featured image if set in config on page/post.

				if ( isset( $this->options['0']['featured'] ) ) {

					if ( has_post_thumbnail( $post->ID ) ) {

						$html .= '<div style="text-align:center; ">' . get_the_post_thumbnail( $post->ID ) . '</div>';
					}
				}

				$post_template = isset( $this->options['1']['post_tmplt'] ) ? $this->options['1']['post_tmplt'] : 'tmplt1';
				if ( $post_template == 'tmplt1' ) {
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 1, 111 );
					$html = $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
				} elseif ( $post_template == 'tmplt2' ) {
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 2, 84 );
					$html = $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
				} elseif ( $post_template == 'tmplt3' ) {
					$pdf->writeHTML( $html, true, 0, true, 0 );
					$pdf->setEqualColumns( 3, 57 );
					$html = $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
				}
			}

			/*
				Setting for displaying ACF field details only.
			*/

			if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

				$field = get_fields( $postID );

				if ( $field ) {
					foreach ( $field as $field_name => $value ) {

						if ( ! ( ( isset( $this->options['6'][ $post_type_val ][ $field_name ] ) && ( $this->options['6'][ $post_type_val ][ $field_name ] == 1 ) ) || ( isset( $this->options['6'][ $post_type_val ][ '_' . $field_name ] ) && ( $this->options['6'][ $post_type_val ][ '_' . $field_name ] == 1 ) ) ) ) {
							continue;
						}

						$field = get_field_object( $field_name, $postID );

						// do not need to display password.
						if ( isset( $field['type'] ) && ( $field['type'] == 'password' ) ) {
							continue;
						}
						// coding to display ACF field image.
						if ( isset( $field['type'] ) && ( $field['type'] == 'image' ) ) {

							if ( $field['save_format'] == 'id' && $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ':</strong><p>';
								$html .= wp_get_attachment_image( $field['value'], 'thumbnail' );
							} elseif ( $field['save_format'] == 'url' ) {

								if ( $field['label'] != '' && $field['value'] != '' ) {

									$html .= '<p><strong>' . $field['label'] . ':</strong><a href=" ' . $field['value'] . '" >' . $field['value'] . '</a><p>';
								}
							} elseif ( $field['save_format'] == 'object' && $field['label'] != '' && $field['value']['id'] != '' ) {

								if ( $field['label'] != '' && $field['value']['id'] != '' ) {

									$html .= $field['label'] . ':';
									$html .= wp_get_attachment_image( $field['value']['id'], 'thumbnail' );
								}
							}

							continue;
						}

						// coding for displaying Taxonomy fields.

						if ( isset( $field['type'] ) && ( $field['type'] == 'taxonomy' ) ) {
							continue;
						}

						// coding for displaying Google map texual address.
						if ( isset( $field['type'] ) && ( $field['type'] == 'google_map' ) ) {
							if ( $field['label'] != '' && $field['value']['address'] != '' ) {

								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value']['address'] . '</p> ';
							}
							continue;
						}

						// coding to display Date Picker.
						if ( isset( $field['type'] ) && ( $field['type'] == 'date_picker' ) ) {

							if ( $field['label'] != '' && $field['value'] != '' && ( $field['date_format'] == 'yymmdd' ) ) {
								// convert the date string in proper format.
								$newstr = substr_replace( $field['value'], ' : ', 4, 0 );
								$newstr = substr_replace( $newstr, ' : ', 9, 0 );

								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $newstr . '</p> ';
							}
							continue;
						}
						// coding to display radio button details.
						if ( ! is_array( $value ) && ( $field['type'] == 'radio' ) && ( isset( $field['choices'] ) ) ) {
							if ( $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value'] . '</p> ';
							}
							continue;
						}

						// coding to display links for product and any post details.
						if ( isset( $field ) && ( $field['type'] == 'page_link' ) ) {
							if ( is_array( $field['value'] ) && ! empty( $field['value'] ) ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

								foreach ( $field['value'] as $current_url_value ) {

									if ( isset( $current_url_value ) && ( $current_url_value != '' ) ) {
										$html .= '<p><a href="' . $current_url_value . '">' . $current_url_value . '</a><p>';
									}
								}
							} else {
								if ( ( $field['label'] != '' ) && ( $field['value'] != '' ) ) {
									$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

									$html .= '<p><a href="' . $field['value'] . '">' . $field['value'] . '</a><p>';
								}
							}
							continue;
						}

						// coding to handle post data Object.
						if ( isset( $field ) && ( $field['type'] == 'post_object' ) && ( ! empty( $field['value'] ) ) ) {
							$acf_fetched_post_value = $field['value'];
							if ( isset( $acf_fetched_post_value ) && ! empty( $acf_fetched_post_value ) && is_array( $acf_fetched_post_value ) ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';
								foreach ( $acf_fetched_post_value as $acf_fetched_one_post_value ) {

									if ( ( get_permalink( $acf_fetched_one_post_value->ID ) != '' ) && ( get_the_title( $acf_fetched_one_post_value->ID ) != '' ) ) {

										$html .= '<p>' . $acf_fetched_one_post_value->post_type . '  -->  ' . '<a href="' . get_permalink( $acf_fetched_one_post_value->ID ) . '">' . get_the_title( $acf_fetched_one_post_value->ID ) . '</a></p>';
									}
								}
							} else {
								if ( ( get_permalink( $acf_fetched_post_value->ID ) != '' ) && ( get_the_title( $acf_fetched_post_value->ID ) != '' ) ) {
									$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

									$html .= '<p>' . $acf_fetched_post_value->post_type . '  -->  ' . '<a href="' . get_permalink( $acf_fetched_post_value->ID ) . '">' . get_the_title( $acf_fetched_post_value->ID ) . '</a></p>';
								}
							}

							continue;
						}

						// cofing to display text, textarea, email, (password need to block), any field returning simple string.
						if ( ! is_array( $value ) && ( ! isset( $field['choices'] ) ) && ! is_object( $value ) ) {
							if ( $field['label'] != '' && $field['value'] != '' ) {
								$html .= '<p><strong>' . $field['label'] . ': </strong>' . $field['value'] . '</p> ';
							}
						}

						// cofing to display radio button, select and checkbox details.
						if ( is_array( $field ) && isset( $field['choices'] ) && ! empty( $field['choices'] ) && ( $field['type'] != 'radio' ) ) {
							$html .= '<p><strong>' . $field['label'] . ': </strong></p> ';

							foreach ( $field['value'] as $selected_value ) {
								if ( isset( $field['choices'][ $selected_value ] ) && ( $field['choices'][ $selected_value ] != '' ) ) {

									$html .= '<p>' . $field['choices'][ $selected_value ] . '</p> ';
								}
							}
						}
					}
				}
			}

			/*
			  ACF field details end.
			*/

			$html .= '</body>';
			$dom = new simple_html_dom();
			$dom->load( $html );

			foreach ( $dom->find( 'img' ) as $e ) {
				// Start Image check by om.
				$exurl = ''; // external streams.
				$imsize = false;
				$file = $e->src;
				// check if we are passing an image as file or string.
				if ( $file[0] === '@' ) {
					// image from string.
					$imgdata = substr( $file, 1 );
				} else { // image file
					if ( $file[0] === '*' ) {
						// image as external stream.
						$file = substr( $file, 1 );
						$exurl = $file;
					}
					// check if is local file.
					if ( ! @file_exists( $file ) ) {
						// encode spaces on filename (file is probably an URL).
						$file = str_replace( ' ', '%20', $file );
					}
					if ( @file_exists( $file ) ) {
						// get image dimensions.
						$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

						$get_url_path = ABSPATH . '/' . $get_url_path;
						$imsize = @getimagesize( $get_url_path );
					}
					if ( $imsize === false ) {
						$imgdata = TCPDF_STATIC::fileGetContents( $file );
					}
				}
				if ( isset( $imgdata ) and ( $imgdata !== false ) and ( strpos( $file, '__tcpdf_img' ) === false ) ) {
					// check Image size.
					$get_url_path = substr( $file, strpos( $file, 'wp-content' ) );

					$get_url_path = ABSPATH . '/' . $get_url_path;
					$imsize = @getimagesize( $get_url_path );
				}
				if ( $imsize === false ) {
					$e->outertext = '';
				} else {
					// End Image Check.
					if ( preg_match( '/alignleft/i', $e->class ) ) {
						$imgalign = 'left';
					} elseif ( preg_match( '/alignright/i', $e->class ) ) {
						$imgalign = 'right';
					} elseif ( preg_match( '/aligncenter/i', $e->class ) ) {
						$imgalign = 'center';
						$htmlimgalign = 'middle';
					} else {
						$imgalign = 'none';
					}

					$e->class = null;
					$e->align = $imgalign;
					if ( isset( $htmlimgalign ) ) {
						$e->style = 'float:' . $htmlimgalign;
					} else {
						$e->style = 'float:' . $imgalign;
					}

					if ( strtolower( substr( $e->src, -4 ) ) == '.svg' ) {
						$e->src = null;
						$e->outertext = '<div style="text-align:' . $imgalign . '">[ SVG: ' . $e->alt . ' ]</div><br/>';
					} else {
						$e->outertext = '<div style="text-align:' . $imgalign . '">' . $e->outertext . '</div>';
					}
				}
			}
			/*parsing dom element and passing null action attribute if action attribute is not set */
			foreach ( $dom->find( 'form' ) as $e ) {
				if ( ! isset( $e->attr['action'] ) ) {
					$e->action = '';
				}
			}
			// Print text using writeHTML.
			$html = preg_replace( '/<div class="wps-rssapp-header wps-no-print".*?>.*?<\/div>/si', '', $html );
			$pdf->writeHTML( $html, true, 0, true, 0 );
			$html = $dom->save();

			$dom->clear();
			$pdf->setFormDefaultProp(
				array(
					'lineWidth' => 1,
					'borderStyle' => 'solid',
					'fillColor' => array(
						255,
						255,
						200,
					),
					'strokeColor' => array(
						255,
						128,
						128,
					),
				)
			);

			if ( isset( $this->options['3']['add_watermark'] ) ) {

				$no_of_pages = $pdf->getNumPages();

				if ( isset( $this->options[8]['start_page_predefined_template'] ) || isset( $this->options[9]['custom_cover_page'] ) ) {
					$i = 2;
				} else {
					$i = 1;
				}

				for ( $i; $i <= $no_of_pages; $i++ ) {
					$pdf->setPage( $i );
					// Get the page width/height.
					$my_page_width = $pdf->getPageWidth();
					$my_page_height = $pdf->getPageHeight();

					// Find the middle of the page and adjust.
					$myX = ( $my_page_width / 2 ) - 75;
					$myY = ( $my_page_height / 2 ) + 25;

					// Set the transparency of the text to really light.
					$pdf->SetAlpha( 0.09 );

					// Rotate 45 degrees and write the watermarking text.
					$pdf->StartTransform();
					$rotate_degr = isset( $this->options['3']['rotate_water'] ) ? $this->options['3']['rotate_water'] : '45';
					$pdf->Rotate( $rotate_degr, $myX, $myY );
					$water_font = isset( $this->options['3']['water_font'] ) ? $this->options['3']['water_font'] : 'courier';
					$pdf->SetFont( $water_font, '', 30 );
					$watermark_text = isset( $this->options['3']['watermark_text'] ) ? $this->options['3']['watermark_text'] : '';
					$pdf->Text( $myX, $myY, $watermark_text );
					$pdf->StopTransform();

					// Reset the transparency to default.
					$pdf->SetAlpha( 1 );
				}
			}

			if ( isset( $this->options['3']['add_watermark_image'] ) ) {
				if ( ! empty( $this->options['3']['background_img_url'] ) ) {
					$no_of_pages = $pdf->getNumPages();

					if ( isset( $this->options[8]['start_page_predefined_template'] ) || isset( $this->options[9]['custom_cover_page'] ) ) {
						$i = 2;
					} else {
						$i = 1;
					}

					for ( $i; $i <= $no_of_pages; $i++ ) {
						$pdf->setPage( $i );
						$my_page_width = $pdf->getPageWidth();
						$my_page_height = $pdf->getPageHeight();
						$myxaxis = $this->options['3']['water_img_x_axis'];
						$myyaxis = $this->options['3']['water_img_y_axis'];
						$ImageT = isset( $this->options['3']['water_img_t'] ) ? $this->options['3']['water_img_t'] : '';
						// Set the transparency of the text to really light.
						$pdf->SetAlpha( $ImageT );
						// Rotate 45 degrees and write the watermarking text.
						$pdf->StartTransform();
						$ImageH = isset( $this->options['3']['water_img_h'] ) ? $this->options['3']['water_img_h'] : '';
						$ImageW = isset( $this->options['3']['water_img_w'] ) ? $this->options['3']['water_img_w'] : '';
						$watermark_img = isset( $this->options['3']['background_img_url'] ) ? $this->options['3']['background_img_url'] : '';
						$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
						$pdf->Image( $watermark_img, $myxaxis, $myyaxis, $ImageW, $ImageH, $ext, $watermark_img, '', true, 500, '' );
						$pdf->StopTransform();
						// Reset the transparency to default.
						$pdf->SetAlpha( 1 );
					}
				}
			}
			// cache directory.
			if ( ! is_dir( MWB_CACHE_DIR ) ) {
				mkdir( MWB_CACHE_DIR, 0755, true );
			}
			$pdf->Output( $filePath, 'F' );
			global $current_user;
			if ( is_user_logged_in() ) {
				wp_get_current_user();
				if ( isset( $this->options['settingname'] ) ) {
					$this->options['settingname'];
				} else {
					$this->options['settingname'] = 'defaul';
				}
				$this->export_installdata( $current_user->user_email, $current_user->user_nicename, $title, $type, $this->options['settingname'] );
			}
			if ( ! is_user_logged_in() ) {
				if ( isset( $this->options['settingname'] ) ) {
					$this->options['settingname'];
				} else {
					$this->options['settingname'] = 'defaul';
				}
				$user = 'guest';
				$mail = '';
				$this->export_installdata( $mail, $user, $title, $type, $this->options['settingname'] );
			}
		}
		/**
		 * Get post html body .
		 *
		 * @param string $postID postid.
		 * @param string $html html.
		 * @param string $pdf_title.
		 * @param string $post post.
		 * @param string $post_type_val post type.
		 */
		public function get_post_html_body( $postID, $html, $pdf_title, $post, $post_type_val ) {
			$post_content = $post->post_content;
			if ( empty( $post->post_content ) ) {
				$post_content = isset( $this->options['4']['docEntryTpl'] ) ? $this->options['4']['docEntryTpl'] : '';
			}

			$html .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );

			// Display date if set in config.
			if ( isset( $this->options['0']['post_date'] ) ) {
				$newDate = gmdate( $this->options['0']['date_formatt'], strtotime( $post->post_date ) );
				$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . $newDate . '</p>';
			}
			// Display current date if set in config.
			if ( isset( $this->options['0']['cuurent_date'] ) ) {
				$current_date = gmdate( $this->options['0']['date_formatt'] );
				$html .= '<p><strong>' . __( 'Print Date', 'wp-ultimate-pdf' ) . ': </strong>' . $current_date . '</p>';
			}
			$arr = get_post_custom( $postID );
			unset( $arr['_edit_last'] );
			unset( $arr['_edit_lock'] );
			unset( $arr['_thumbnail_id'] );
			foreach ( $arr as $key => $val ) {

				if ( isset( $this->options['6'][ $post_type_val ][ $key ] ) && ! empty( $this->options['6'][ $post_type_val ][ $key ] ) && ( $this->options['6'][ $post_type_val ][ $key ] == 1 ) ) {

					if ( isset( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ! empty( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ( $this->options['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) && ( $this->options['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) ) {
						if ( ( null !== ( get_post_meta( $postID, $key, true ) ) ) && ( ! is_array( get_post_meta( $postID, $key, true ) ) ) ) {

							if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

								$field = get_fields( $postID );
								if ( ( array_key_exists( $key, $field ) ) ) {
									continue;
								}
							}
							$html .= '<p><strong>' . $key . ':' . '</strong>' . get_post_meta( $postID, $key, true ) . '</p>';
						}
					}
				}
			}
			return $html;
		}


		/**
		 * Get product html body .
		 *
		 * @param string $postID postid.
		 * @param string $html html.
		 * @param string $pdf_title.
		 * @param string $post post.
		 * @param string $post_type_val post type.
		 */
		public function get_product_html_body( $postID, $html, $pdf_title, $post, $post_type_val, $author_name ) {

			// Display featured image if set in config on page/post.
			$product_template = isset( $this->options['1']['product_tmplt'] ) ? $this->options['1']['product_tmplt'] : 'tmplt1';

			$product = new WC_product( $postID );
			$progallery = $product->get_gallery_image_ids();
			$product_pmeta = isset( $this->options['1']['pro_pmeta'] ) ? $this->options['1']['pro_pmeta'] : 4;

			$author_name = '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ': </strong>' . $author_name . '</p>';

			$html_cat = '';

			// // for products in wordpress.

			$terms = get_the_terms( $post->ID, 'product_cat' );

			if ( $terms ) {
				$terms = get_the_terms( $post->ID, 'product_cat' );
				$html_cat .= '<span><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ': </strong>';
				foreach ( $terms as $term ) {

					$product_cat = $term->name;

					$html_cat .= $product_cat . ',';
				}
				$html_cat .= '</span>';
			}

			$html1 = '';

			if ( isset( $this->options['0']['featured'] ) ) {
				if ( has_post_thumbnail( $post->ID ) ) {
					$image_id = $product->get_image_id();

					if ( $image_id ) {
						// Get the image URL
						$image_url = wp_get_attachment_url( $image_id );

						if ( $image_url ) {
							$html1 .= '<img style="height:250px; width:250px;"src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" />';
						}
					}
				}
			}
			$html5 = '';
			if ( isset( $progallery ) && ! empty( $progallery ) ) {
				$html5 .= '<table><tr>';
				foreach ( $progallery as $key => $value ) {
					$html5 .= '<td>';
					$html5 .= wp_get_attachment_image( $value );
					$html5 .= '</td>';
				}
				$html5 .= '</tr></table>';
			}
			$html2 = '';
			$html2 .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';
			$post_content = $post->post_content;
			if ( empty( $post->post_content ) ) {
				$post_content = isset( $this->options['4']['docEntryTpl'] ) ? $this->options['4']['docEntryTpl'] : '';
				$html2 .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
			} else {
				$post_excerpt = $post->post_excerpt;
				$html2 .= htmlspecialchars_decode( htmlentities( $post_excerpt, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
			}
			$html6 = '';
			$html6 .= '<h2>' . __( 'Description', 'wp-ultimate-pdf' ) . '</h2>';
			$html6 .= htmlspecialchars_decode( htmlentities( $post_content, ENT_NOQUOTES, 'UTF-8', false ), ENT_NOQUOTES );
			$arr = get_post_meta( get_the_ID() );
			unset( $arr['_edit_last'] );
			unset( $arr['_edit_lock'] );
			unset( $arr['_thumbnail_id'] );
			$arr_count = count( $arr );
			$count = 0;
			$flag = false;
			$counter = 0;
			$html3 = '';
			if ( isset( $this->options['6'][ $post_type_val ][ $post_type_val ] ) && ! empty( $this->options['6'][ $post_type_val ][ $post_type_val ] ) && ( $this->options['6'][ $post_type_val ][ $post_type_val ] == 1 ) ) {

				foreach ( $arr as $key => $val ) {

					if ( isset( $this->options['6'][ $post_type_val ][ $key ] ) || isset( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ! empty( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ( $this->options['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) ) {

						if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

							$field = get_fields( $postID );

							if ( isset( $field ) && ! empty( $field ) ) {
								if ( ( array_key_exists( $key, $field ) ) ) {
									continue;
								}
							}
						}
						if ( $arr_count > $product_pmeta ) {
							$count++;
							$flag = true;
						}

						if ( $this->options['6'][ $post_type_val ][ $key ] == '1' ) {
							$wps_wpr_test_value = ! empty( trim( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) ) ? trim( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) : '';

							if ( ! empty( $wps_wpr_test_value ) && ! is_null( $wps_wpr_test_value ) ) {

								$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $this->options['6'][ $post_type_val ][ $key . 'name' ] ) ) . ':' . '</strong>' . get_post_meta( get_the_ID(), $key, true ) . '</p> ';
							} else {

								$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . get_post_meta( get_the_ID(), $key, true ) . '</p> ';
							}
						} elseif ( $this->options['6'][ $post_type_val ][ $key ] == '1' && $this->options['6'][ $post_type_val ][ $key . 'name' ] != '' ) {
							$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . get_post_meta( get_the_ID(), $key, true ) . '</p> ';
						}
					}

					$counter++;
					if ( $count == $product_pmeta ) {
						break;
					}
				}
			}

			$html .= '<div style="padding:15px 0 25px;"><table cellpadding="1" cellspacing="1"><tr><td>' . $author_name . '</td></tr> <tr><td>' . $html_cat . '</td></tr></table></div>';

			$html .= '<table cellpadding="2" cellspacing="2"><tr><td>';
			$html4 = '';
			if ( $product_template == 'tmplt2' ) {
				$html4 .= $html1 . '</td><td>' . $html2 . '</td></tr><tr><td>' . $html5 . '</td></tr><tr><td colspan="2" >' . $html6 . '</td></tr>';
				$html4 .= "<tr><td colspan='2'>" . $html3;
			} elseif ( $product_template == 'tmplt3' ) {
				$html4 .= $html2 . '</td><td>' . $html1 . '<br>' . $html5 . '</td></tr><tr><td colspan="2">' . $html6 . '</td></tr>';
				$html4 .= '<tr><td>' . $html3;
			} elseif ( $product_template == 'tmplt1' ) {
				$html4 .= $html2 . '<br><div style="text-align:center;">';
				$html4 .= $html1 . '</div></td></tr><tr><td>' . $html6 . '</td></tr><tr><td>' . $html3 . '</td></tr>';
				$html4 .= '<tr><td>' . $html5;
			}
			$html .= $html4;
			$html .= '</td></tr></table>';

			// Display date if set in config.
			if ( isset( $this->options['0']['post_date'] ) ) {
				$newDate = gmdate( $this->options['0']['date_formatt'], strtotime( $post->post_date ) );
				$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . $newDate . '</p>';
			}
			// Display date if set in config.
			if ( isset( $this->options['0']['cuurent_date'] ) ) {
				$current_date = gmdate( $this->options['0']['date_formatt'] );
				$html .= '<p><strong>' . __( 'Print Date', 'wp-ultimate-pdf' ) . ': </strong>' . $current_date . '</p>';
			}
			return $html;
		}

		/**
		 * Adds a link button for selected post types by admin.
		 * This function adds different link for different sinerios.
		 * Adds a checks whether to add link for the post type or not.
		 *
		 * @name ptp_add_pdf_link.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param string $content content .
		 * @return string
		 */
		public function ptp_add_pdf_link( $content ) {

			if ( ! isset( $this->options['front_end'] ) ) {

				return $content;
			}
			if ( isset( $this->options['0']['content_position'] ) ) {
				if ( 'left' == $this->options['0']['content_position'] ) {
					$style = 'text-align:left;';
				} else if ( 'right' == $this->options['0']['content_position'] ) {
					$style = 'text-align:right;';
				} else if ( 'center' == $this->options['0']['content_position'] ) {

					$style = 'text-align:center;';
				} else {
					$style = '';
				}
			}

			$button = $this->ptp_pdf_icon();
			if ( isset( $this->options['0']['content_placement'] ) ) {
				if ( 'beforeandafter' == $this->options['0']['content_placement'] ) {
					$content = '<div style="min-height: 30px;' . $style . '">' . $button . '</div>' . $content . '<div style="min-height: 30px;' . $style . '">' . $button . '</div>';
				} elseif ( 'after' == $this->options['0']['content_placement'] ) {
					$content = $content . '<div style="min-height: 30px;' . $style . '">' . $button . '</div>';
				} else {
					$content = '<div style="min-height: 30px;' . $style . '">' . $button . '</div>' . $content;
				}
			}

			return $content;
		}

		/**
		 * Function to show nothing when shortcode is inactive.
		 */
		public function ptp_pdf_icon_no_shortcode() {
			// silence is golden.
		}

		/**
		 * Specifies the link to attach to pdf links.
		 *
		 * @name ptp_pdf_icon.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @return void|boolean|string
		 */
		public function ptp_pdf_icon() {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				if ( ( ! isset( $this->options['front_end'] ) ) || is_cart() || is_checkout() || is_shop()
					|| is_account_page()
				) {

					return;
				}
			}

			if ( 'pdf' == ( isset( $_GET['format'] ) ? sanitize_text_field( wp_unslash( $_GET['format'] ) ) : null ) ) {

				return;
			}
			global $post;

			if ( ! isset( $this->options['6'][ $post->post_type ][ $post->post_type ] ) ) {
				return false;
			}

			if ( isset( $this->options['0']['link_button'] ) && ! empty( $this->options['0']['link_button'] ) && 'default' == $this->options['0']['link_button'] ) {
				$link_url = MWBPDF_GEN_URL . '/asset/images/pdf.png';
			} else if ( ! empty( $this->options['0']['custon_link_url'] ) ) {
				$link_url = $this->options['0']['custon_link_url'];
			} else {
				$link_url = MWBPDF_GEN_URL . '/asset/images/pdf.png';
			}
			if ( isset( $this->options['0']['content_position'] ) ) {
				if ( 'left' == $this->options['0']['content_position'] ) {
					$style = 'style="max-width: 50px;"';
				} else if ( 'right' == $this->options['0']['content_position'] ) {
					$style = 'style="max-width: 50px; display:inline-block;"';
				} else if ( 'center' == $this->options['0']['content_position'] ) {

					$style = 'style="margin: 0 auto;max-width: 50px;"';
				} else {
					$style = 'max-width: 50px;';
				}
			} else {
				$style = 'max-width: 50px;';
			}
			global $post;
			$mwbpdf_genoptions = get_option( MWBPDF_GEN_PREFIX );
			$user_roles_check = isset( $mwbpdf_genoptions['1']['check_user_roles'] ) ? 1 : 0;
			$selected_val_arr = get_option( MWBPDF_GEN_PREFIX );
			$user = wp_get_current_user();
			if ( isset( $this->options['show'] ) && 'loggedinuser' == $this->options['show'] ) {
				if ( isset( $this->options['showemailfield'] ) && 'shownonloggedinemail' == $this->options['showemailfield'] ) {
					if ( is_user_logged_in() ) {
						if ( ! is_singular() ) {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
								}
							} else {
								return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
							}
						} else {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px"  ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
								}
							} else {
								return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px"  ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
							}
						}
					} else {
						if ( ! is_singular() ) {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									$html = '<input width="50px" id="' . $post->ID . '" ' . $style . ' src="' . $link_url . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="' . __( 'Export pdf to your Email', 'wp-ultimate-pdf' ) . '" class="thickbox export-pdf" type="image" value="' . __( 'Export to PDF', 'wp-ultimate-pdf' ) . '" />';
								}
							} else {
								$html = '<input width="50px" id="' . $post->ID . '" ' . $style . ' src="' . $link_url . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="' . __( 'Export pdf to your Email', 'wp-ultimate-pdf' ) . '" class="thickbox export-pdf" type="image" value="' . __( 'Export to PDF', 'wp-ultimate-pdf' ) . '" />';
							}
						} else {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									$html = '<input width="50px" id="' . $post->ID . '" ' . $style . ' src="' . $link_url . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="' . __( 'Export pdf to your Email', 'wp-ultimate-pdf' ) . '" class="thickbox export-pdf" type="image" value="' . __( 'Export to PDF', 'wp-ultimate-pdf' ) . '" />';
								}
							} else {
								$html = '<input  width="50px" id="' . $post->ID . '" ' . $style . ' src="' . $link_url . '" alt="#TB_inline?height=230&amp;width=400&amp;inlineId=examplePopup1" title="' . __( 'Export pdf to your Email', 'wp-ultimate-pdf' ) . '" class="thickbox export-pdf" type="image" value="' . __( 'Export to PDF', 'wp-ultimate-pdf' ) . '" />';
							}
						}
						return $html;
					}
				} else {
					if ( is_user_logged_in() ) {
						if ( ! is_singular() ) {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
								}
							} else {
								return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
							}
						} else {
							if ( $user_roles_check == 1 ) {

								if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {

									return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px"  ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
								}
							} else {
								return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px"  ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
							}
						}
					} else {
						$html = '';
					}
				}
			} else if ( 'guestuser' == $this->options['show'] ) {

				if ( ! is_singular() ) {
					if ( $user_roles_check == 1 ) {

						if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {
							return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
						}
					} else {
						return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf', get_permalink( $post->ID ) ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
					}
				} else {
					if ( $user_roles_check == 1 ) {

						if ( isset( $selected_val_arr[1]['user_roles'] ) && array_intersect( $user->roles, $selected_val_arr[1]['user_roles'] ) ) {
							return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
						}
					} else {
						return '<a target="_blank" rel="noindex,nofollow" href="' . esc_url( add_query_arg( 'format', 'pdf' ) ) . '" title="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '"><img  width="50px" ' . $style . ' alt="' . __( 'Download PDF', 'wp-ultimate-pdf' ) . '" src="' . $link_url . '"></a>';
					}
				}
			}
		}

		/**
		 * Adds custom interval for cache updation
		 * can add multiple schedules
		 *
		 * @name ptp_pdfscheduleduration
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param array $schedules
		 * @return multitype:NULL Ambigous <string, mixed>
		 */
		public function ptp_pdfscheduleduration( $schedules ) {
			if ( isset( $this->options['0']['cache_updation_sch'] ) && $this->options['0']['cache_updation_sch'] != 'none' ) {
				$schedules['cacheinterval'] = array(
					'interval' => $this->options['0']['cache_updation_sch'],
					'display' => __( 'PDF Cache Schedule Interval', 'wp-ultimate-pdf' ),
				);
				update_option( 'cron_cacheinterval', $this->options['0']['cache_updation_sch'] );
			}
			return $schedules;
		}

		/**
		 * Schedule an event to update a cache if cache updation is set by admin.
		 * Checks if already scheduled cache updation.
		 *
		 * @name ptp_cronstarter_activation
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_cronstarter_activation() {
			if ( isset( $this->options['0']['cache_updation_sch'] ) && $this->options['0']['cache_updation_sch'] != 'none' ) {
				if ( ! wp_next_scheduled( 'schedulecacheupdate' ) ) {
					wp_schedule_event( time(), 'cacheinterval', 'schedulecacheupdate' );
				}
			}
		}

		/**
		 * Deactive scheduled event on plugin deactivation
		 *
		 * @name ptp_cronstarter_deactivate
		 * @author WP Swings <support@wpswings.com>
		 */
		public function ptp_cronstarter_deactivate() {
			$this->deactivator_callback();
		}
		/**
		 * Deactivation callback function .
		 */
		public function deactivator_callback() {
			global $wpdb;
			if ( is_multisite() ) {
				if ( is_plugin_active_for_network( 'wp-ultimate-pdf-generator/wp-ultimate-pdf.php' ) ) {
					$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

					foreach ( $blogids as $blog_id ) {

						switch_to_blog( $blog_id );
						// find out when the last event was scheduled.
						$timestamp = wp_next_scheduled( 'schedulecacheupdate' );
						// unschedule previous event if any.
						wp_unschedule_event( $timestamp, 'schedulecacheupdate' );
						restore_current_blog();
					}
				} else {
					// find out when the last event was scheduled
					$timestamp = wp_next_scheduled( 'schedulecacheupdate' );
					// unschedule previous event if any
					wp_unschedule_event( $timestamp, 'schedulecacheupdate' );
				}

				// Validate license daily cron.

			}
		}
		/**
		 * Updates cache.
		 * called by scheduled event
		 *
		 * @name ptp_update_cache
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_update_cache() {
			global $wpdb;
			$interval = get_option( 'cron_cacheinterval_prev' );
			if ( empty( $interval ) ) {
				$interval = get_option( 'cron_cacheinterval' );
				update_option( 'cron_cacheinterval_prev', $interval );
			}
			$post_status = 'publish';
			$date_today = gmdate( 'l' );
			$date_from = gmdate( 'Y-m-d H:i:s', strtotime( $date_today ) - $interval );
			$post_types = get_post_types(
				array(
					'public' => true,
				),
				'names'
			);
			$post_types_all;
			foreach ( $post_types as $post_type ) {
				if ( isset( $this->options['6'][ $post_type ][ $post_type ] ) ) {
					$post_types_all .= "'" . $post_type . "',";
				}
			}
			$post_types_all = trim( $post_types_all, ',' );
			$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type IN ( {$post_types_all} ) AND  post_status = %s AND post_date >= %s AND post_date <= %s ", $post_status, $date_from, $date_today ) );
			foreach ( $post_ids as $post_id ) {
				$this->generate_post_to_pdf_file( $post_id );
			}
		}

		/**
		 * hooked for send email to guest users
		 *
		 * @name postajax_exportandmail_handle
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function postajax_exportandmail_handle() {
			if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$mwb_email_to = is_email( sanitize_text_field( wp_unslash( $_POST['mwb_email_to'] ) ) );
				$postID = sanitize_text_field( wp_unslash( $_POST['postid'] ) );
				if ( $mwb_email_to ) {
					$this->ptp_email_pdf( $postID, $mwb_email_to );
				} else {
					$response = array( 'INVALIDEMAIL' => true );
					wp_send_json( $response );
				}
			}
		}

		/**
		 * creates a table to collect record of pdf generated
		 *
		 * @name ptp_export_install
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public function ptp_export_install() {
			global $wpdb;
			$table_name = $wpdb->prefix . 'ExporttoPDFRecord';
			$charset_collate = $wpdb->get_charset_collate();
			$sql = "CREATE TABLE $table_name (
			   id mediumint(9) NOT NULL AUTO_INCREMENT,
			   title varchar(100) NOT NULL,
			   type varchar(100) NOT NULL,
			   emailid varchar(100) NOT NULL,
			   userName varchar(100) NOT NULL,
			   exportdate datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			   useripaddr varchar(100) NOT NULL,
			   UNIQUE KEY id (id)
			   ) $charset_collate";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
			add_option( 'Export_db_version', '1.6' );
		}

		/**
		 * Put data in the table
		 *
		 * @name export_installdata
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 * @param string $emailid
		 * @param string $user_name
		 * @param string $title title .
		 * @param string $type type.
		 */
		function export_installdata( $emailid, $user_name, $title, $type ) {
			// Add some data to table
			global $wpdb;
			$table_name = $wpdb->prefix . 'ExporttoPDFRecord';
			$Remote_addr = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
			if ( ! empty( $_SERVER['X_FORWARDED_FOR'] ) ) {
				$X_forwarded_for = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['X_FORWARDED_FOR'] ) ) );
				if ( ! empty( $X_forwarded_for ) ) {
					$Remote_addr = trim( $X_forwarded_for[0] );
				}
			}
			/*
			 * Some php environments will use the $_SERVER['HTTP_X_FORWARDED_FOR'] variable to capture visitor address information.
			 */ elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$Http_x_forwarded_for = explode( ',', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
				if ( ! empty( $Http_x_forwarded_for ) ) {
					$Remote_addr = trim( $Http_x_forwarded_for[0] );
				}
}
			$user_ip_dddress = preg_replace( '/[^0-9a-f:\., ]/si', '', $Remote_addr );
			$wpdb->insert(
				$table_name,
				array(
					'title' => $title,
					'type' => $type,
					'emailid' => $emailid,
					'userName' => $user_name,
					'exportdate' => current_time( 'mysql' ),
					'useripaddr' => $user_ip_dddress,
				)
			);
		}
	}
	$mwbpdf = new mwbpdf();
}
function enqueue_select2_jquery() {
	 wp_register_style( 'select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
	wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_style( 'select2css' );
	wp_enqueue_script( 'select2' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_select2_jquery' );
// Auto update.
$wp_pdf_license_key_pre = get_option( 'mwb_pdf_license_key', '' );
if ( isset( $_SERVER['HTTP_HOST'] ) ) {
	$wp_pdf_license_key = get_option( 'mwb_pdf_license_key' . sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ), $wp_pdf_license_key_pre );
}
define( 'WP_PDF_LICENSE_KEY', $wp_pdf_license_key );
define( 'WP_PDF_FILE', __FILE__ );
define( 'WP_PDF_PLUGIN_VERSION', '1.3.4' );
$wp_pdf_update_check = 'https://wpswings.com/pluginupdates/codecanyon/wp-ultimate-pdf-generator/update.php';
require_once( 'wp-ultimate-pdf-update.php' );

/**
 * Adding shortcode to show calendly Events anywhere on the page.
 */
if ( 'on' === get_option( 'wps_embed_source_calendly', '' ) ) {
	add_shortcode( 'wps_calendly', 'wps_calendly_embed_shortcode' );
}
if ( 'on' === get_option( 'wps_embed_source_twitch', '' ) ) {
	add_shortcode( 'wps_twitch', 'wps_twitch_stream_with_chat_shortcode' );
}

if ( 'on' === get_option( 'wps_embed_source_strava', '' ) ) {
	add_shortcode( 'wps_strava', 'wps_strava_embed_shortcode' );
}

if ( 'on' === get_option( 'wps_embed_source_ai_chatbot', '' ) ) {
	add_shortcode( 'wps_ai_chatbot', 'wps_chatbot_ai_shortcode' );
}
if ( 'on' === get_option( 'wps_embed_source_rss_feed', '' ) ) {
	add_shortcode( 'wps_rssapp_feed', 'wps_rssapp_feed_shortcode' );
}



/**
 * Shortcode: [wps_twitch].
 * Description: Embeds a Twitch stream with optional live chat side-by-side.
 *
 * @param array $atts post id to print PDF for.
 *
 * Attributes:
 * - channel: (string) Twitch channel username (required).
 * - width: (string) Width of the Twitch player iframe (default: '100%').
 * - height: (string|int) Height of the Twitch player iframe (default: '480').
 * - chat_width: (string) Width of the chat iframe (default: '100%').
 * - chat_height: (string|int) Height of the chat iframe (default: '480').
 * - show_chat: (string) Whether to show chat iframe (yes/no, default: 'yes').
 *
 * Example usage:
 * [wps_twitch channel="your_channel" width="70%" chat_width="30%" show_chat="yes"].
 */
function wps_twitch_stream_with_chat_shortcode( $atts ) {
	// Set default attributes and merge with user-provided values.
	$atts = shortcode_atts(
		array(
			'channel'     => '',          // Twitch channel name.
			'width'       => '100%',      // Player width.
			'height'      => '480',       // Player height.
			'chat_width'  => '100%',      // Chat width.
			'chat_height' => '480',       // Chat height.
			'show_chat'   => 'yes',       // Show chat? 'yes' or 'no'.
		),
		$atts,
		'wps_twitch'
	);

	// Return error if no channel provided.
	if ( empty( $atts['channel'] ) ) {
		return '<p>Please provide a Twitch channel name.</p>';
	}

	// Set the parent domain (required by Twitch embed policy).
	$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
	$parent = esc_attr( $host );

	// Start building output.
	$output = '<div class="wps-twitch-embed" style="display: flex; flex-wrap: wrap; gap: 20px;">';

	// Twitch video stream.
	$output .= '<iframe
        src="https://player.twitch.tv/?channel=' . esc_attr( $atts['channel'] ) . '&parent=' . $parent . '"
        frameborder="0"
        allowfullscreen="true"
        scrolling="no"
        height="' . esc_attr( $atts['height'] ) . '"
        width="' . esc_attr( $atts['width'] ) . '">
    </iframe>';

	// Twitch live chat (optional).
	if ( 'yes' === $atts['show_chat'] ) {
		$output .= '<iframe
            src="https://www.twitch.tv/embed/' . esc_attr( $atts['channel'] ) . '/chat?parent=' . $parent . '"
            frameborder="0"
            scrolling="no"
            height="' . esc_attr( $atts['chat_height'] ) . '"
            width="' . esc_attr( $atts['chat_width'] ) . '">
        </iframe>';
	}

	$output .= '</div>';

	return $output;
}

/**
 * Shortcode: [wps_strava].
 * Description: Embeds a Strava activity or segment using Strava Embeds service.
 *
 * @param array $atts post id to print PDF for.
 * Attributes:
 * - id: (string) The Strava activity or segment ID (required).
 * - type: (string) Type of embed: 'activity', 'segment', etc. (default: 'activity').
 * - style: (string) Display style, e.g., 'standard' (default: 'standard').
 * - from_embed: (string) Optional flag for embed source, e.g., 'true' or 'false' (default: 'false').
 *
 * Example usage:
 * [wps_strava id="1234567890" type="activity" style="standard"].
 */
function wps_strava_embed_shortcode( $atts ) {
	// Merge user-provided attributes with defaults.
	$atts = shortcode_atts(
		array(
			'id' => '', // Strava Activity or Segment ID.
			'type' => 'activity', // Type of embed (activity, segment, etc.).
			'style' => 'standard', // Visual style of embed.
			'from_embed' => 'false', // Optional flag for embed behavior.
		),
		$atts,
		'wps_strava'
	);

	// If no ID is provided, return an error message.
	if ( empty( $atts['id'] ) ) {
		return '<p>Please provide a valid Strava activity ID.</p>';
	}

	// Start capturing output.
	ob_start();
	?>
	<!-- Strava Embed Placeholder -->
	<div class="strava-embed-placeholder"
		data-embed-type="<?php echo esc_attr( $atts['type'] ); ?>"
		data-embed-id="<?php echo esc_attr( $atts['id'] ); ?>"
		data-style="<?php echo esc_attr( $atts['style'] ); ?>"
		data-from-embed="<?php echo esc_attr( $atts['from_embed'] ); ?>">
	</div>

	<!-- Strava Embed Script -->
	<?php
	if ( ! wp_script_is( 'strava-embed', 'enqueued' ) ) {
		wp_enqueue_script( 'strava-embed', 'https://strava-embeds.com/embed.js', array(), time(), true );
	}
	// Return the buffered output.
	return ob_get_clean();
}

/**
 * Shortcode: [wps_ai_chatbot].
 * Description: Embeds a customizable AI chatbot iframe widget.
 *
 * @param array $atts Post ID to print PDF for.
 * Attributes:
 * - url: (string) The chatbot URL to be embedded (required).
 * - height: (string) Height of the chatbot iframe (default: 700px).
 * - header_color: (string) Background color or gradient of the chatbot header (default: #4e54c8).
 * - header_title: (string) Title text shown in the header (default: "AI Chat Assistant").
 *
 * Example:
 * [wps_ai_chatbot url="https://yourbot.com/chat" height="600px" header_title="Support Bot"].
 */
function wps_chatbot_ai_shortcode( $atts ) {
	// Set default values and merge with user-supplied attributes.
	$atts = shortcode_atts(
		array(
			'url' => '',
			'height' => '700px',
			'header_color' => '#4e54c8',
			'header_title' => 'AI Chat Assistant',
		),
		$atts
	);

	// Show an error message if the required URL is missing.
	if ( empty( $atts['url'] ) ) {
		return '<div style="color: red; font-weight: bold;">Chatbot URL is missing.</div>';
	}

	// Start output buffering to return generated HTML.
	ob_start();
	?>
	<style>
		/* Main chatbot wrapper styling */
		.wps-chatbot-wrapper {
			max-width: 960px;
			margin: 40px auto;
			border-radius: 16px;
			overflow: hidden;
			box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
			background: #fff;
			animation: fadeIn 1s ease-in-out;
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
		}

		/* Header styling with dynamic background color */
		.wps-chatbot-header {
			background: <?php echo esc_attr( $atts['header_color'] ); ?>;
			color: #fff;
			padding: 20px 30px;
			font-size: 20px;
			font-weight: bold;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		/* Optional icon style */
		.wps-chatbot-header svg {
			width: 24px;
			height: 24px;
			fill: #fff;
		}

		/* Iframe container */
		.wps-chatbot-iframe-container iframe {
			width: 100%;
			min-height: <?php echo esc_attr( $atts['height'] ); ?>;
			border: none;
			display: block;
		}

		/* Simple fade-in animation */
		@keyframes fadeIn {
			from {
				opacity: 0;
				transform: translateY(20px);
			}

			to {
				opacity: 1;
				transform: translateY(0);
			}
		}
	</style>

	<!-- Chatbot Embed Structure -->
	<div class="wps-chatbot-wrapper wps-no-print">
		<!-- Header with title and optional icon -->
		<div class="wps-chatbot-header">
			<svg viewBox="0 0 24 24">
				<path d="M12 2a10 10 0 0 0-10 10 9.99 9.99 0 0 0 5.29 8.75c-.1.75-.32 1.84-.79 3.01 0 0-.04.09.01.14.05.05.13.02.13.02 1.72-.24 3.05-.99 3.58-1.33A10.01 10.01 0 0 0 22 12 10 10 0 0 0 12 2z" />
			</svg>
			<?php echo esc_html( $atts['header_title'] ); ?>
		</div>

		<!-- Embedded chatbot iframe -->
		<div class="wps-chatbot-iframe-container">
			<iframe src="<?php echo esc_url( $atts['url'] ); ?>" style="min-height: <?php echo esc_attr( $atts['height'] ); ?>;"></iframe>
		</div>
	</div>
	<?php
	// Return the output buffer content.
	return ob_get_clean();
}

/**
 * Shortcode: [wps_rssapp_feed].
 * Description: Embeds an RSS.app widget feed with customizable styles via shortcode attributes.
 *
 * @param array $atts array.
 *
 * Attributes:
 * - url: (string) The RSS.app embed URL (required).
 * - height: (string) Height of the embedded iframe (default: 600px).
 * - title: (string) Title displayed above the feed (default: "📰 Latest News").
 * - bg_color: (string) Background color of the header (default: #ffffff).
 * - text_color: (string) Text color of the header and content (default: #333333).
 * - border_color: (string) Border color for the widget container (default: #eeeeee).
 *
 * Usage:
 * [wps_rssapp_feed url="https://rss.app/embed/your-widget" height="500px" title="My Feed"].
 */
function wps_rssapp_feed_shortcode( $atts ) {
	// Define default attribute values and merge with user-supplied attributes.
	$atts = shortcode_atts(
		array(
			'url' => '',
			'height' => '600px',
			'title' => '📰 Latest News',
			'bg_color' => '#ffffff',
			'text_color' => '#333333',
			'border_color' => '#eeeeee',
		),
		$atts
	);

	// If URL is missing, show a helpful message.
	if ( empty( $atts['url'] ) ) {
		return '<p>Please provide a valid RSS.app widget URL.</p>';
	}

	// Start output buffering to return HTML content as a string.
	ob_start();
	?>
	<div class="wps-rssapp-news-wrapper wps-no-print" style="
		color: <?php echo esc_attr( $atts['text_color'] ); ?>;
		border: 1px solid <?php echo esc_attr( $atts['border_color'] ); ?>;
		border-radius: 12px;
		overflow: hidden;
		box-shadow: 0 5px 15px rgba(0, 0, 0, 0.07);
		margin: 30px auto;
		max-width: 1000px;
	">
		<!-- Feed Header -->
		<div class="wps-rssapp-header wps-no-print" style="
			padding: 16px 24px;
			font-size: 22px;
			font-weight: bold;
			border-bottom: 1px solid <?php echo esc_attr( $atts['border_color'] ); ?>;
			background-color: <?php echo esc_attr( $atts['bg_color'] ); ?>;
			color: <?php echo esc_attr( $atts['text_color'] ); ?>;
		">
			<?php echo esc_html( $atts['title'] ); ?>
		</div>

		<!-- RSS App Iframe -->
		<iframe
			src="<?php echo esc_url( $atts['url'] ); ?>"
			width="100%"
			height="<?php echo esc_attr( $atts['height'] ); ?>"
			style="border: none; overflow-y: auto;"
			scrolling="yes"></iframe>
	</div>
	<?php
	// Return the buffered content.
	return ob_get_clean();
}
?>
