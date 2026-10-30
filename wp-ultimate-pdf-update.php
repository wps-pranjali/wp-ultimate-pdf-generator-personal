<?php
/**
 * Update file .
 *
 * @package woocommerce ultimate gift card.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WP_Ultimate_Pdf_Update' ) ) {
	/**
	 * Update class.
	 */
	class WP_Ultimate_Pdf_Update {
		/**
		 * Contructor.
		 */
		public function __construct() {
			register_activation_hook( WP_PDF_FILE, array( $this, 'wp_pdf_check_activation' ) );
			add_action( 'wp_pdf_check_event', array( $this, 'wp_pdf_check_update' ) );
			add_filter( 'http_request_args', array( $this, 'wp_pdf_updates_exclude' ), 5, 2 );
			add_action( 'install_plugins_pre_plugin-information', array( $this, 'wp_pdf_plugin_details' ) );
			register_deactivation_hook( WP_PDF_FILE, array( $this, 'wp_pdf_check_deactivation' ) );
		}
		/**
		 * Deactivation .
		 */
		public function wp_pdf_check_deactivation() {
			wp_clear_scheduled_hook( 'wp_pdf_check_event' );
		}
		/**
		 * Activation .
		 */
		public function wp_pdf_check_activation() {
			wp_schedule_event( time(), 'daily', 'wp_pdf_check_event' );
		}
		/**
		 * Check update.
		 */
		public function wp_pdf_check_update() {
			global $wp_version;
			global $wp_pdf_update_check;
			$plugin_folder = plugin_basename( dirname( WP_PDF_FILE ) );
			$plugin_file = basename( ( WP_PDF_FILE ) );
			if ( defined( 'WP_INSTALLING' ) ) {
				return false;
			}
			$postdata = array(
				'action' => 'check_update',
				'current_version' => WP_PDF_PLUGIN_VERSION,
				'purchase_code' => WP_PDF_LICENSE_KEY,
			);

			$args = array(
				'method' => 'POST',
				'body' => $postdata,
			);

			$response = wp_remote_post( $wp_pdf_update_check, $args );
			if ( empty( $response['body'] ) ) {
				return false;
			}
			list($version, $url) = explode( '~', $response['body'] );

			if ( $this->wp_pdf_plugin_get( 'Version' ) >= $version ) {
				return false;
			}

			$plugin_transient = get_site_transient( 'update_plugins' );
			$a = array(
				'slug' => $plugin_folder,
				'new_version' => $version,
				'url' => $this->wp_pdf_plugin_get( 'AuthorURI' ),
				'package' => $url,
			);
			$o = (object) $a;
			$plugin_transient->response[ $plugin_folder . '/' . $plugin_file ] = $o;
			set_site_transient( 'update_plugins', $plugin_transient );
		}
		/**
		 * Updates excludes.
		 *
		 * @param string $r r.
		 * @param string $url url .
		 */
		public function wp_pdf_updates_exclude( $r, $url ) {
			if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ) {
				return $r;
			}
			$plugins = unserialize( $r['body']['plugins'] );

			unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
			if ( isset( $plugins->active ) && is_array( $plugins->active ) ) {
				$key = array_search( plugin_basename( __FILE__ ), $plugins->active );
				if ( false !== $key ) {
					unset( $plugins->active[ $key ] );
				}
			}
			
			$r['body']['plugins'] = serialize( $plugins );
			return $r;
		}
		/**
		 * Returns current plugin info.
		 *
		 * @param string $i i .
		 */
		public function wp_pdf_plugin_get( $i ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			$plugin_folder = get_plugins( '/' . plugin_basename( dirname( WP_PDF_FILE ) ) );
			$plugin_file = basename( ( WP_PDF_FILE ) );
			return $plugin_folder[ $plugin_file ][ $i ];
		}
		/**
		 * Plugin detils.
		 */
		public function wp_pdf_plugin_details() {
			global $tab;
			if ( 'plugin-information' == $tab && 'wp-ultimate-pdf-generator' == $_REQUEST['plugin'] ) {

				$url = 'https://wpswings.com/pluginupdates/codecanyon/wp-ultimate-pdf-generator/update.php';

				$postdata = array(
					'action' => 'check_update',
					'license_code' => WP_PDF_LICENSE_KEY,
				);

				$args = array(
					'method' => 'POST',
					'body' => $postdata,
				);

				$data = wp_remote_post( $url, $args );
				if ( is_wp_error( $data ) ) {
					return;
				}

				if ( isset( $data['body'] ) ) {
					$all_data = json_decode( $data['body'], true );
					if ( is_array( $all_data ) && ! empty( $all_data ) ) {
						$this->create_html_data( $all_data );
						die;
					}
				}
			}
		}
		/**
		 * Html data.
		 *
		 * @param string $all_data all_data.
		 */
		public function create_html_data( $all_data ) {
			?>
			<style>
				#TB_window{
					top : 4% !important;
				}
				.mwb_plugin_banner > img {
					height: 55%;
					width: 100%;
					border: 1px solid;
					border-radius: 7px;
				}
				.mwb_plugin_description > h4 {
					background-color: #3779B5;
					padding: 5px;
					color: #ffffff;
					border-radius: 5px;
				}
				.mwb_plugin_requirement > h4 {
					background-color: #3779B5;
					padding: 5px;
					color: #ffffff;
					border-radius: 5px;
				}
				#error-page > p {
					display: none;
				}
			</style>
			<div class="mwb_plugin_details_wrapper">
				<div class="mwb_plugin_banner">
					<img src="<?php echo esc_html( $all_data['banners']['low'] ); ?>">	
				</div>
				<div class="mwb_plugin_description">
					<h4><?php esc_html_e( 'Plugin Description', 'wp-ultimate-pdf' ); ?></h4>
					<span><?php echo esc_html( $all_data['sections']['description'] ); ?></span>
				</div>
				<div class="mwb_plugin_requirement">
					<h4><?php esc_html_e( 'Plugin Change Log', 'wp-ultimate-pdf' ); ?></h4>
					<span><?php echo wp_kses_post( $all_data['sections']['changelog'] ); ?></span>
				</div> 
			</div>
			<?php
		}
	}
	new WP_Ultimate_Pdf_Update();
}
?>
