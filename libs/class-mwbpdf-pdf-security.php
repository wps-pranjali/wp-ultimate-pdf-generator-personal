<?php
/**
 * Password protection and permission restrictions for generated PDFs.
 *
 * @package wp-ultimate-pdf-generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Mwbpdf_Pdf_Security' ) ) {

	/**
	 * Class Mwbpdf_Pdf_Security.
	 */
	class Mwbpdf_Pdf_Security {

		/**
		 * Supported encryption modes: setting value => TCPDF mode.
		 *
		 * @var array
		 */
		public static $modes = array(
			'aes128' => 2,
			'aes256' => 3,
		);

		/**
		 * Register hooks.
		 *
		 * @name init
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function init() {
			add_action( 'update_option_' . MWBPDF_GEN_PREFIX, array( __CLASS__, 'maybe_purge_cache' ), 10, 2 );
		}

		/**
		 * Normalised security settings from a plugin settings array.
		 *
		 * @name get_settings
		 * @param array $options plugin settings (the mwbpdf_gen option or a saved setting).
		 * @return array
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function get_settings( $options ) {
			$section = ( is_array( $options ) && isset( $options['4'] ) && is_array( $options['4'] ) ) ? $options['4'] : array();
			$mode    = isset( $section['pdf_encryption'] ) ? (string) $section['pdf_encryption'] : 'aes128';
			return array(
				'enabled'        => ! empty( $section['pdf_protection'] ),
				'user_password'  => isset( $section['pdf_user_password'] ) ? (string) $section['pdf_user_password'] : '',
				'owner_password' => isset( $section['pdf_owner_password'] ) ? (string) $section['pdf_owner_password'] : '',
				'disable_print'  => ! empty( $section['pdf_disable_print'] ),
				'disable_copy'   => ! empty( $section['pdf_disable_copy'] ),
				'disable_modify' => ! empty( $section['pdf_disable_modify'] ),
				'encryption'     => isset( self::$modes[ $mode ] ) ? $mode : 'aes128',
			);
		}

		/**
		 * Apply password protection / permission restrictions to a PDF document.
		 *
		 * Must be called before the document is output.
		 *
		 * @name apply
		 * @param TCPDF $pdf     document.
		 * @param array $options plugin settings used to build the document.
		 * @param int   $post_id post being exported (0 for previews and bulk exports).
		 * @return bool whether protection was applied.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function apply( $pdf, $options, $post_id = 0 ) {
			$settings = self::get_settings( $options );

			/**
			 * Filter the PDF protection settings, e.g. to use a per-post or per-user password.
			 *
			 * @param array $settings normalised settings (see Mwbpdf_Pdf_Security::get_settings()).
			 * @param int   $post_id  post being exported, 0 when not tied to a single post.
			 * @param array $options  plugin settings used to build the document.
			 */
			$settings = apply_filters( 'mwbpdf_pdf_protection_settings', $settings, $post_id, $options );

			if ( empty( $settings['enabled'] ) || ! is_object( $pdf ) || ! method_exists( $pdf, 'SetProtection' ) ) {
				return false;
			}

			// TCPDF expects the permissions to block.
			$blocked = array();
			if ( ! empty( $settings['disable_print'] ) ) {
				$blocked[] = 'print';
				$blocked[] = 'print-high';
			}
			if ( ! empty( $settings['disable_copy'] ) ) {
				$blocked[] = 'copy';
			}
			if ( ! empty( $settings['disable_modify'] ) ) {
				$blocked[] = 'modify';
				$blocked[] = 'annot-forms';
				$blocked[] = 'fill-forms';
				$blocked[] = 'assemble';
			}

			$user_password = (string) $settings['user_password'];
			if ( '' === $user_password && empty( $blocked ) ) {
				// Nothing to protect.
				return false;
			}

			// Without an owner password anyone could lift the restrictions, so fall back to a random one.
			$owner_password = '' !== (string) $settings['owner_password'] ? (string) $settings['owner_password'] : wp_generate_password( 32, true, true );
			$mode           = isset( self::$modes[ $settings['encryption'] ] ) ? self::$modes[ $settings['encryption'] ] : self::$modes['aes128'];

			$pdf->SetProtection( $blocked, $user_password, $owner_password, $mode );
			return true;
		}

		/**
		 * Cached PDFs were built with the old security settings; drop them when those settings change.
		 *
		 * @name maybe_purge_cache
		 * @param mixed $old_value previous option value.
		 * @param mixed $new_value new option value.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function maybe_purge_cache( $old_value, $new_value ) {
			if ( self::get_settings( $old_value ) === self::get_settings( $new_value ) ) {
				return;
			}
			self::purge_cache();
		}

		/**
		 * Delete cached PDF files.
		 *
		 * @name purge_cache
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function purge_cache() {
			if ( ! defined( 'MWB_CACHE_DIR' ) || ! is_dir( MWB_CACHE_DIR ) ) {
				return;
			}
			foreach ( (array) glob( trailingslashit( MWB_CACHE_DIR ) . '*.pdf' ) as $file ) {
				if ( is_file( $file ) ) {
					wp_delete_file( $file );
				}
			}
		}

		/**
		 * Render the protection settings rows for the Advance Settings form.
		 *
		 * @name render_settings
		 * @param array $options plugin settings.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function render_settings( $options ) {
			$s      = self::get_settings( $options );
			$prefix = MWBPDF_GEN_PREFIX;
			?>
			<tr class="mwbpdf-security-heading">
				<td colspan="3"><h3><?php esc_html_e( 'PDF Security', 'wp-ultimate-pdf' ); ?></h3></td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e( 'Protect Generated PDFs', 'wp-ultimate-pdf' ); ?></td>
				<td class="tr2" colspan="2">
					<label>
						<input type="checkbox" id="mwbpdf_pdf_protection" name="<?php echo esc_attr( $prefix ); ?>[pdf_protection]" value="1" <?php checked( $s['enabled'] ); ?> />
						<?php esc_html_e( 'Encrypt PDFs with a password and/or restrict what readers can do', 'wp-ultimate-pdf' ); ?>
					</label>
				</td>
			</tr>
			<tr class="mwbpdf-security-field">
				<td class="tr1"><label for="mwbpdf_pdf_user_password"><?php esc_html_e( 'Open Password', 'wp-ultimate-pdf' ); ?></label></td>
				<td class="tr2" colspan="2">
					<input type="password" id="mwbpdf_pdf_user_password" name="<?php echo esc_attr( $prefix ); ?>[pdf_user_password]" value="<?php echo esc_attr( $s['user_password'] ); ?>" autocomplete="new-password" />
					<span class="descr"><?php esc_html_e( 'Readers must enter this password to open the PDF. Leave empty to let anyone open it (restrictions below still apply).', 'wp-ultimate-pdf' ); ?></span>
				</td>
			</tr>
			<tr class="mwbpdf-security-field">
				<td class="tr1"><label for="mwbpdf_pdf_owner_password"><?php esc_html_e( 'Permissions Password', 'wp-ultimate-pdf' ); ?></label></td>
				<td class="tr2" colspan="2">
					<input type="password" id="mwbpdf_pdf_owner_password" name="<?php echo esc_attr( $prefix ); ?>[pdf_owner_password]" value="<?php echo esc_attr( $s['owner_password'] ); ?>" autocomplete="new-password" />
					<span class="descr"><?php esc_html_e( 'Opens the PDF with full rights and lets you lift the restrictions. Leave empty to use a random password nobody knows.', 'wp-ultimate-pdf' ); ?></span>
				</td>
			</tr>
			<tr class="mwbpdf-security-field">
				<td class="tr1"><?php esc_html_e( 'Restrictions', 'wp-ultimate-pdf' ); ?></td>
				<td class="tr2" colspan="2">
					<label><input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[pdf_disable_print]" value="1" <?php checked( $s['disable_print'] ); ?> /> <?php esc_html_e( 'Disable printing', 'wp-ultimate-pdf' ); ?></label><br />
					<label><input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[pdf_disable_copy]" value="1" <?php checked( $s['disable_copy'] ); ?> /> <?php esc_html_e( 'Disable copying text and images', 'wp-ultimate-pdf' ); ?></label><br />
					<label><input type="checkbox" name="<?php echo esc_attr( $prefix ); ?>[pdf_disable_modify]" value="1" <?php checked( $s['disable_modify'] ); ?> /> <?php esc_html_e( 'Disable editing, annotations and form filling', 'wp-ultimate-pdf' ); ?></label>
					<p class="descr"><?php esc_html_e( 'Restrictions are enforced by PDF readers such as Adobe Acrobat, Chrome and Preview.', 'wp-ultimate-pdf' ); ?></p>
				</td>
			</tr>
			<tr class="mwbpdf-security-field">
				<td class="tr1"><label for="mwbpdf_pdf_encryption"><?php esc_html_e( 'Encryption', 'wp-ultimate-pdf' ); ?></label></td>
				<td class="tr2" colspan="2">
					<select id="mwbpdf_pdf_encryption" name="<?php echo esc_attr( $prefix ); ?>[pdf_encryption]">
						<option value="aes128" <?php selected( $s['encryption'], 'aes128' ); ?>><?php esc_html_e( 'AES 128-bit (most compatible)', 'wp-ultimate-pdf' ); ?></option>
						<option value="aes256" <?php selected( $s['encryption'], 'aes256' ); ?>><?php esc_html_e( 'AES 256-bit (strongest, Acrobat 9+)', 'wp-ultimate-pdf' ); ?></option>
					</select>
				</td>
			</tr>
			<?php
		}
	}

	Mwbpdf_Pdf_Security::init();
}
