<?php
/**
 * Self-serve custom fonts for PDF generation.
 *
 * Lets admins upload .ttf / .woff / .woff2 fonts or install Google Fonts, and
 * converts them into TCPDF font definitions stored in the uploads directory so
 * they survive plugin updates.
 *
 * @package wp-ultimate-pdf-generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Mwbpdf_Custom_Fonts' ) ) {

	/**
	 * Class Mwbpdf_Custom_Fonts.
	 */
	class Mwbpdf_Custom_Fonts {

		/**
		 * Option holding fonts managed by this class.
		 */
		const OPTION = 'mwbpdf_custom_fonts';

		/**
		 * Legacy option (label => tcpdf font name) used by older versions.
		 */
		const LEGACY_OPTION = 'ptp_custom_fonts';

		/**
		 * Nonce action for all font ajax requests.
		 */
		const NONCE = 'mwbpdf-custom-fonts';

		/**
		 * Folder name inside uploads where font definitions are stored.
		 */
		const DIR_NAME = 'wp-ultimate-pdf-fonts';

		/**
		 * Maximum accepted font file size in bytes.
		 */
		const MAX_FILE_SIZE = 15728640;

		/**
		 * Supported variants: key => TCPDF style suffix.
		 *
		 * @var array
		 */
		public static $variants = array(
			'regular'    => '',
			'bold'       => 'b',
			'italic'     => 'i',
			'bolditalic' => 'bi',
		);

		/**
		 * Register hooks.
		 *
		 * @name init
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function init() {
			add_action( 'wp_ajax_mwbpdf_upload_font', array( __CLASS__, 'ajax_upload_font' ) );
			add_action( 'wp_ajax_mwbpdf_install_google_font', array( __CLASS__, 'ajax_install_google_font' ) );
			add_action( 'wp_ajax_mwbpdf_delete_font', array( __CLASS__, 'ajax_delete_font' ) );
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue font manager assets on the plugin settings page.
		 *
		 * @name enqueue_scripts
		 * @param string $hook current admin page hook.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function enqueue_scripts( $hook ) {
			if ( 'toplevel_page_wp-ultimate-pdf-generator' !== $hook ) {
				return;
			}
			wp_register_script( 'mwbpdf-woff2', MWBPDF_GEN_URL . '/asset/js/vendor/wawoff2-decompress.js', array(), '2.0.1', true );
			wp_enqueue_script( 'mwbpdf-custom-fonts', MWBPDF_GEN_URL . '/asset/js/custom-fonts.js', array( 'jquery', 'mwbpdf-woff2' ), WP_PDF_PLUGIN_VERSION, true );
			wp_localize_script(
				'mwbpdf-custom-fonts',
				'mwbpdf_fonts_obj',
				array(
					'ajaxurl'        => admin_url( 'admin-ajax.php' ),
					'nonce'          => wp_create_nonce( self::NONCE ),
					'max_size'       => self::MAX_FILE_SIZE,
					'fonts'          => self::get_fonts_for_js(),
					'i18n'           => array(
						'uploading'          => __( 'Uploading and converting font…', 'wp-ultimate-pdf' ),
						'converting'         => __( 'Converting WOFF2 to TrueType…', 'wp-ultimate-pdf' ),
						'installing'         => __( 'Downloading from Google Fonts…', 'wp-ultimate-pdf' ),
						'installed'          => __( 'Font installed. It is now available in every font dropdown.', 'wp-ultimate-pdf' ),
						'deleted'            => __( 'Font removed.', 'wp-ultimate-pdf' ),
						'confirm_delete'     => __( 'Remove this font? PDFs using it will fall back to Helvetica.', 'wp-ultimate-pdf' ),
						'choose_file'        => __( 'Please choose a .ttf, .woff or .woff2 file.', 'wp-ultimate-pdf' ),
						'bad_type'           => __( 'Only .ttf, .woff and .woff2 font files are supported.', 'wp-ultimate-pdf' ),
						'too_big'            => __( 'The font file is too large (max 15 MB).', 'wp-ultimate-pdf' ),
						'woff2_failed'       => __( 'Could not convert this WOFF2 file. Please upload the .ttf version instead.', 'wp-ultimate-pdf' ),
						'enter_family'       => __( 'Please enter a Google Font family name.', 'wp-ultimate-pdf' ),
						'pick_variant'       => __( 'Please select at least one style.', 'wp-ultimate-pdf' ),
						'error'              => __( 'Something went wrong. Please try again.', 'wp-ultimate-pdf' ),
						'no_fonts'           => __( 'No custom fonts installed yet.', 'wp-ultimate-pdf' ),
						'remove'             => __( 'Remove', 'wp-ultimate-pdf' ),
						'source_upload'      => __( 'Uploaded', 'wp-ultimate-pdf' ),
						'source_google'      => __( 'Google Fonts', 'wp-ultimate-pdf' ),
						'source_legacy'      => __( 'Legacy', 'wp-ultimate-pdf' ),
						'variant_regular'    => __( 'Regular', 'wp-ultimate-pdf' ),
						'variant_bold'       => __( 'Bold', 'wp-ultimate-pdf' ),
						'variant_italic'     => __( 'Italic', 'wp-ultimate-pdf' ),
						'variant_bolditalic' => __( 'Bold Italic', 'wp-ultimate-pdf' ),
					),
				)
			);
		}

		/**
		 * Absolute path (with trailing slash) of the font storage directory.
		 *
		 * @name get_font_dir
		 * @param bool $create whether to create the directory if missing.
		 * @return string
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function get_font_dir( $create = false ) {
			$upload = wp_upload_dir( null, false );
			$dir    = trailingslashit( $upload['basedir'] ) . self::DIR_NAME . '/';
			if ( $create && ! is_dir( $dir ) ) {
				wp_mkdir_p( $dir );
			}
			if ( $create && is_dir( $dir ) ) {
				if ( ! file_exists( $dir . 'index.php' ) ) {
					file_put_contents( $dir . 'index.php', "<?php\n// Silence is golden.\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				}
				if ( ! file_exists( $dir . '.htaccess' ) ) {
					file_put_contents( $dir . '.htaccess', "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				}
			}
			return $dir;
		}

		/**
		 * Managed fonts.
		 *
		 * @name get_fonts
		 * @return array key => array( label, source, variants => array( variant => tcpdf name ), added )
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function get_fonts() {
			$fonts = get_option( self::OPTION, array() );
			return is_array( $fonts ) ? $fonts : array();
		}

		/**
		 * Fonts for the PDF font dropdowns (label => tcpdf font name), including legacy fonts.
		 *
		 * @name get_dropdown_fonts
		 * @return array
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function get_dropdown_fonts() {
			$list   = array();
			$legacy = get_option( self::LEGACY_OPTION, array() );
			if ( is_array( $legacy ) ) {
				$list = $legacy;
			}
			$labels = array(
				'regular'    => '',
				'bold'       => ' ' . __( 'Bold', 'wp-ultimate-pdf' ),
				'italic'     => ' ' . __( 'Italic', 'wp-ultimate-pdf' ),
				'bolditalic' => ' ' . __( 'Bold Italic', 'wp-ultimate-pdf' ),
			);
			foreach ( self::get_fonts() as $font ) {
				foreach ( self::$variants as $variant => $suffix ) {
					if ( ! empty( $font['variants'][ $variant ] ) ) {
						$list[ $font['label'] . $labels[ $variant ] ] = $font['variants'][ $variant ];
					}
				}
			}
			return $list;
		}

		/**
		 * Font list shaped for the admin script.
		 *
		 * @name get_fonts_for_js
		 * @return array
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function get_fonts_for_js() {
			$rows = array();
			foreach ( self::get_fonts() as $key => $font ) {
				$rows[] = array(
					'key'      => $key,
					'label'    => $font['label'],
					'source'   => $font['source'],
					'name'     => $font['variants']['regular'] ?? reset( $font['variants'] ),
					'variants' => array_keys( array_filter( $font['variants'] ) ),
				);
			}
			$legacy = get_option( self::LEGACY_OPTION, array() );
			if ( is_array( $legacy ) ) {
				foreach ( $legacy as $label => $name ) {
					$rows[] = array(
						'key'      => 'legacy:' . $label,
						'label'    => $label,
						'source'   => 'legacy',
						'name'     => $name,
						'variants' => array( 'regular' ),
					);
				}
			}
			return array(
				'rows'     => $rows,
				'dropdown' => self::get_dropdown_fonts(),
			);
		}

		/**
		 * Locate the definition file path TCPDF should use for a custom font.
		 *
		 * Returns a path inside the font directory (the file itself may not exist,
		 * TCPDF then falls back to the regular variant in the same directory), or
		 * an empty string when the family is not a custom font.
		 *
		 * @name locate_font_file
		 * @param string $family font family.
		 * @param string $style  font style (B, I, BI...).
		 * @return string
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function locate_font_file( $family, $style = '' ) {
			$family = strtolower( str_replace( ' ', '', (string) $family ) );
			if ( '' === $family || preg_match( '/[^a-z0-9_\-]/', $family ) ) {
				return '';
			}
			$dir = self::get_font_dir();
			if ( ! file_exists( $dir . $family . '.php' ) ) {
				return '';
			}
			$style = str_replace( array( 'U', 'D', 'O' ), '', strtoupper( (string) $style ) );
			if ( 'IB' === $style ) {
				$style = 'BI';
			}
			return $dir . $family . strtolower( $style ) . '.php';
		}

		/**
		 * Build a TCPDF-safe family key from a label.
		 *
		 * @name make_key
		 * @param string $label font label.
		 * @return string
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function make_key( $label ) {
			$key = strtolower( remove_accents( (string) $label ) );
			$key = preg_replace( '/[^a-z0-9]/', '', $key );
			// TCPDF rewrites these words when naming fonts; drop them up-front so variant names stay predictable.
			$key = str_replace( array( 'bold', 'oblique', 'italic', 'regular' ), '', $key );
			if ( '' === $key ) {
				$key = 'customfont';
			}
			// Never shadow the bundled core fonts.
			$core = array( 'courier', 'helvetica', 'times', 'symbol', 'zapfdingbats' );
			foreach ( $core as $core_font ) {
				if ( 0 === strpos( $key, $core_font ) ) {
					$key = 'custom' . $key;
					break;
				}
			}
			return $key;
		}

		/**
		 * Convert raw font data into TrueType (sfnt) data.
		 *
		 * @name to_truetype
		 * @param string $data raw font bytes.
		 * @return string|WP_Error
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function to_truetype( $data ) {
			if ( strlen( $data ) < 12 ) {
				return new WP_Error( 'mwbpdf_font_invalid', __( 'This is not a valid font file.', 'wp-ultimate-pdf' ) );
			}
			$magic = substr( $data, 0, 4 );
			if ( 'wOFF' === $magic ) {
				$data = self::woff_to_sfnt( $data );
				if ( is_wp_error( $data ) ) {
					return $data;
				}
				$magic = substr( $data, 0, 4 );
			}
			if ( "\x00\x01\x00\x00" === $magic || 'true' === $magic ) {
				return $data;
			}
			if ( 'wOF2' === $magic ) {
				return new WP_Error( 'mwbpdf_font_woff2', __( 'WOFF2 fonts are converted in your browser before upload. Please reload the settings page and try again, or upload the .ttf version.', 'wp-ultimate-pdf' ) );
			}
			if ( 'OTTO' === $magic ) {
				return new WP_Error( 'mwbpdf_font_otf', __( 'This font uses PostScript (CFF) outlines, which the PDF engine cannot embed. Please upload a TrueType (.ttf) version.', 'wp-ultimate-pdf' ) );
			}
			if ( 'ttcf' === $magic ) {
				return new WP_Error( 'mwbpdf_font_ttc', __( 'Font collections (.ttc) are not supported. Please upload a single .ttf file.', 'wp-ultimate-pdf' ) );
			}
			return new WP_Error( 'mwbpdf_font_invalid', __( 'This is not a valid TrueType, WOFF or WOFF2 font file.', 'wp-ultimate-pdf' ) );
		}

		/**
		 * Unpack a WOFF 1.0 file into an sfnt (TTF) binary.
		 *
		 * @name woff_to_sfnt
		 * @param string $data WOFF bytes.
		 * @return string|WP_Error
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function woff_to_sfnt( $data ) {
			$invalid = new WP_Error( 'mwbpdf_font_invalid', __( 'This WOFF file is damaged and could not be converted.', 'wp-ultimate-pdf' ) );
			$size    = strlen( $data );
			if ( $size < 44 ) {
				return $invalid;
			}
			$header     = unpack( 'Nsignature/Nflavor/Nlength/nnumTables', substr( $data, 0, 14 ) );
			$num_tables = $header['numTables'];
			if ( $num_tables < 1 || 44 + $num_tables * 20 > $size ) {
				return $invalid;
			}
			$tables = array();
			for ( $i = 0; $i < $num_tables; $i++ ) {
				$entry = unpack( 'a4tag/Noffset/NcompLength/NorigLength/NorigChecksum', substr( $data, 44 + $i * 20, 20 ) );
				if ( $entry['offset'] + $entry['compLength'] > $size || $entry['compLength'] > $entry['origLength'] ) {
					return $invalid;
				}
				$table = substr( $data, $entry['offset'], $entry['compLength'] );
				if ( $entry['compLength'] < $entry['origLength'] ) {
					$table = function_exists( 'gzuncompress' ) ? @gzuncompress( $table ) : false; // phpcs:ignore WordPress.PHP.NoSilencedErrors
					if ( false === $table || strlen( $table ) !== $entry['origLength'] ) {
						return $invalid;
					}
				}
				$tables[] = array(
					'tag'      => $entry['tag'],
					'checksum' => $entry['origChecksum'],
					'data'     => $table,
				);
			}
			$entry_selector = (int) floor( log( $num_tables, 2 ) );
			$search_range   = ( 1 << $entry_selector ) * 16;
			$out            = pack( 'Nnnnn', $header['flavor'], $num_tables, $search_range, $entry_selector, $num_tables * 16 - $search_range );
			$offset         = 12 + 16 * $num_tables;
			$body           = '';
			foreach ( $tables as $table ) {
				$length = strlen( $table['data'] );
				$out   .= $table['tag'] . pack( 'NNN', $table['checksum'], $offset, $length );
				$padded = str_pad( $table['data'], ( $length + 3 ) & ~3, "\0" );
				$body  .= $padded;
				$offset += strlen( $padded );
			}
			return $out . $body;
		}

		/**
		 * Convert TrueType data into a TCPDF font definition inside the font directory.
		 *
		 * @name build_tcpdf_font
		 * @param string $ttf  TrueType bytes.
		 * @param string $name target TCPDF font name.
		 * @param bool   $italic whether the font is italic.
		 * @return string|WP_Error created font name.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function build_tcpdf_font( $ttf, $name, $italic = false ) {
			if ( ! class_exists( 'TCPDF' ) ) {
				require_once MWBPDF_GEN_PATH . '/tcpdf_min/tcpdf.php';
			}
			$dir = self::get_font_dir( true );
			if ( ! is_dir( $dir ) || ! wp_is_writable( $dir ) ) {
				/* translators: %s: directory path. */
				return new WP_Error( 'mwbpdf_font_dir', sprintf( __( 'The font folder %s is not writable. Please check the uploads folder permissions.', 'wp-ultimate-pdf' ), $dir ) );
			}
			$tmp_dir = trailingslashit( get_temp_dir() ) . 'mwbpdf-font-' . wp_generate_password( 12, false ) . '/';
			if ( ! wp_mkdir_p( $tmp_dir ) ) {
				return new WP_Error( 'mwbpdf_font_tmp', __( 'Could not create a temporary folder for font conversion.', 'wp-ultimate-pdf' ) );
			}
			// TCPDF names the generated font after the source file name.
			$tmp_file = $tmp_dir . $name . '.ttf';
			file_put_contents( $tmp_file, $ttf ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			self::remove_font_files( $name );

			$result = false;
			try {
				$result = TCPDF_FONTS::addTTFfont( $tmp_file, 'TrueTypeUnicode', '', $italic ? 96 : 32, $dir );
			} catch ( Throwable $e ) {
				$result = false;
			}
			wp_delete_file( $tmp_file );
			@rmdir( $tmp_dir ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions

			if ( ! $result || ! file_exists( $dir . $result . '.php' ) ) {
				self::remove_font_files( $name );
				return new WP_Error( 'mwbpdf_font_convert', __( 'The font could not be converted. It may be damaged or use an unsupported format.', 'wp-ultimate-pdf' ) );
			}
			return $result;
		}

		/**
		 * Delete the generated files of a TCPDF font.
		 *
		 * @name remove_font_files
		 * @param string $name TCPDF font name.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function remove_font_files( $name ) {
			if ( ! preg_match( '/^[a-z0-9_]+$/', $name ) ) {
				return;
			}
			$dir = self::get_font_dir();
			foreach ( array( '.php', '.z', '.ctg.z' ) as $ext ) {
				if ( file_exists( $dir . $name . $ext ) ) {
					wp_delete_file( $dir . $name . $ext );
				}
			}
		}

		/**
		 * Install a font variant from raw bytes and register it.
		 *
		 * @name install_variant
		 * @param string $data    raw font bytes (ttf / woff).
		 * @param string $label   family label.
		 * @param string $variant one of self::$variants keys.
		 * @param string $source  upload|google.
		 * @return string|WP_Error family key.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function install_variant( $data, $label, $variant, $source ) {
			if ( ! isset( self::$variants[ $variant ] ) ) {
				$variant = 'regular';
			}
			$ttf = self::to_truetype( $data );
			if ( is_wp_error( $ttf ) ) {
				return $ttf;
			}
			$key   = self::make_key( $label );
			$fonts = self::get_fonts();
			// A bold/italic file on its own still needs a regular entry for TCPDF lookups.
			if ( 'regular' !== $variant && empty( $fonts[ $key ]['variants']['regular'] ) ) {
				$variant = 'regular';
			}
			$name = self::build_tcpdf_font( $ttf, $key . self::$variants[ $variant ], false !== strpos( $variant, 'italic' ) );
			if ( is_wp_error( $name ) ) {
				return $name;
			}
			if ( ! isset( $fonts[ $key ] ) ) {
				$fonts[ $key ] = array(
					'label'    => $label,
					'source'   => $source,
					'variants' => array(),
					'added'    => time(),
				);
			}
			$fonts[ $key ]['variants'][ $variant ] = $name;
			update_option( self::OPTION, $fonts, false );
			return $key;
		}

		/**
		 * Delete a managed (or legacy) font.
		 *
		 * @name delete_font
		 * @param string $key font key.
		 * @return bool
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function delete_font( $key ) {
			if ( 0 === strpos( $key, 'legacy:' ) ) {
				$label  = substr( $key, 7 );
				$legacy = get_option( self::LEGACY_OPTION, array() );
				if ( ! is_array( $legacy ) || ! isset( $legacy[ $label ] ) ) {
					return false;
				}
				unset( $legacy[ $label ] );
				update_option( self::LEGACY_OPTION, $legacy );
				return true;
			}
			$fonts = self::get_fonts();
			if ( ! isset( $fonts[ $key ] ) ) {
				return false;
			}
			foreach ( $fonts[ $key ]['variants'] as $name ) {
				self::remove_font_files( $name );
			}
			unset( $fonts[ $key ] );
			update_option( self::OPTION, $fonts, false );
			return true;
		}

		/**
		 * Download a Google Font family as TrueType files and install the requested variants.
		 *
		 * @name install_google_font
		 * @param string $family   Google Font family name.
		 * @param array  $variants variant keys.
		 * @return array|WP_Error installed variant keys.
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function install_google_font( $family, $variants ) {
			$family = trim( preg_replace( '/\s+/', ' ', $family ) );
			if ( '' === $family || ! preg_match( '/^[A-Za-z0-9 ]{1,80}$/', $family ) ) {
				return new WP_Error( 'mwbpdf_gf_name', __( 'Please enter a valid Google Font family name, e.g. "Open Sans".', 'wp-ultimate-pdf' ) );
			}
			$variants = array_values( array_intersect( array_keys( self::$variants ), (array) $variants ) );
			if ( empty( $variants ) ) {
				$variants = array( 'regular' );
			}
			// Always fetch regular so TCPDF has a base face to fall back to.
			if ( ! in_array( 'regular', $variants, true ) ) {
				array_unshift( $variants, 'regular' );
			}
			$axes = array(
				'regular'    => '400',
				'bold'       => '700',
				'italic'     => '400italic',
				'bolditalic' => '700italic',
			);
			$query = array();
			foreach ( $variants as $variant ) {
				$query[] = $axes[ $variant ];
			}
			// The legacy CSS API returns TrueType URLs for non-browser user agents and skips missing styles.
			$url      = 'https://fonts.googleapis.com/css?family=' . rawurlencode( $family ) . ':' . implode( ',', $query );
			$response = wp_remote_get(
				$url,
				array(
					'timeout'    => 20,
					'user-agent' => 'WordPress/' . get_bloginfo( 'version' ) . '; ' . home_url(),
				)
			);
			if ( is_wp_error( $response ) ) {
				return $response;
			}
			if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
				/* translators: %s: font family. */
				return new WP_Error( 'mwbpdf_gf_missing', sprintf( __( '"%s" was not found on Google Fonts. Check the spelling (names are case sensitive, e.g. "Open Sans").', 'wp-ultimate-pdf' ), $family ) );
			}
			$faces = self::parse_google_css( wp_remote_retrieve_body( $response ) );
			if ( empty( $faces['regular'] ) ) {
				// Some families (e.g. icon or display fonts) have no 400 weight; use the first available face as regular.
				if ( empty( $faces ) ) {
					return new WP_Error( 'mwbpdf_gf_empty', __( 'Google Fonts did not return any downloadable TrueType files for this family.', 'wp-ultimate-pdf' ) );
				}
				$faces = array( 'regular' => reset( $faces ) ) + $faces;
			}

			$installed = array();
			foreach ( array_keys( self::$variants ) as $variant ) {
				if ( empty( $faces[ $variant ] ) || ! in_array( $variant, $variants, true ) ) {
					continue;
				}
				$file = wp_remote_get( $faces[ $variant ], array( 'timeout' => 30 ) );
				if ( is_wp_error( $file ) ) {
					return $file;
				}
				$body = wp_remote_retrieve_body( $file );
				if ( 200 !== (int) wp_remote_retrieve_response_code( $file ) || '' === $body || strlen( $body ) > self::MAX_FILE_SIZE ) {
					return new WP_Error( 'mwbpdf_gf_download', __( 'Downloading the font from Google Fonts failed.', 'wp-ultimate-pdf' ) );
				}
				$key = self::install_variant( $body, $family, $variant, 'google' );
				if ( is_wp_error( $key ) ) {
					return $key;
				}
				$installed[] = $variant;
			}
			return $installed;
		}

		/**
		 * Extract variant => TTF url from a Google Fonts stylesheet.
		 *
		 * @name parse_google_css
		 * @param string $css stylesheet.
		 * @return array
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function parse_google_css( $css ) {
			$faces = array();
			if ( ! preg_match_all( '/@font-face\s*{([^}]*)}/', (string) $css, $blocks ) ) {
				return $faces;
			}
			foreach ( $blocks[1] as $block ) {
				if ( ! preg_match( '#url\((https://fonts\.gstatic\.com/[^)\s\'"]+\.ttf)\)#', $block, $url ) ) {
					continue;
				}
				$italic  = (bool) preg_match( '/font-style:\s*italic/', $block );
				$weight  = preg_match( '/font-weight:\s*(\d+)/', $block, $w ) ? (int) $w[1] : 400;
				$variant = ( $weight >= 600 ? 'bold' : '' ) . ( $italic ? 'italic' : '' );
				if ( '' === $variant ) {
					$variant = 'regular';
				}
				if ( ! isset( $faces[ $variant ] ) ) {
					$faces[ $variant ] = $url[1];
				}
			}
			return $faces;
		}

		/**
		 * Common permission and nonce check for ajax handlers.
		 *
		 * @name verify_request
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		private static function verify_request() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error( array( 'message' => __( 'You are not allowed to do this.', 'wp-ultimate-pdf' ) ), 403 );
			}
			if ( ! check_ajax_referer( self::NONCE, 'nonce', false ) ) {
				wp_send_json_error( array( 'message' => __( 'Your session has expired. Please reload the page.', 'wp-ultimate-pdf' ) ), 403 );
			}
		}

		/**
		 * Ajax: upload a font file.
		 *
		 * @name ajax_upload_font
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function ajax_upload_font() {
			self::verify_request();
			if ( empty( $_FILES['font_file']['tmp_name'] ) || ! empty( $_FILES['font_file']['error'] ) ) {
				wp_send_json_error( array( 'message' => __( 'No font file was received. The file may exceed the server upload limit.', 'wp-ultimate-pdf' ) ) );
			}
			$tmp  = $_FILES['font_file']['tmp_name']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			$name = sanitize_file_name( wp_unslash( $_FILES['font_file']['name'] ?? '' ) );
			if ( ! is_uploaded_file( $tmp ) ) {
				wp_send_json_error( array( 'message' => __( 'Invalid upload.', 'wp-ultimate-pdf' ) ) );
			}
			if ( ! preg_match( '/\.(ttf|woff2?)$/i', $name ) ) {
				wp_send_json_error( array( 'message' => __( 'Only .ttf, .woff and .woff2 font files are supported.', 'wp-ultimate-pdf' ) ) );
			}
			if ( filesize( $tmp ) > self::MAX_FILE_SIZE ) {
				wp_send_json_error( array( 'message' => __( 'The font file is too large (max 15 MB).', 'wp-ultimate-pdf' ) ) );
			}
			$label = isset( $_POST['font_label'] ) ? sanitize_text_field( wp_unslash( $_POST['font_label'] ) ) : '';
			if ( '' === $label ) {
				$label = ucwords( trim( preg_replace( '/[-_]+/', ' ', preg_replace( '/\.(ttf|woff2?)$/i', '', $name ) ) ) );
			}
			$variant = isset( $_POST['font_variant'] ) ? sanitize_key( wp_unslash( $_POST['font_variant'] ) ) : 'regular';
			$data    = file_get_contents( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			$result  = self::install_variant( (string) $data, $label, $variant, 'upload' );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}
			wp_send_json_success( self::get_fonts_for_js() );
		}

		/**
		 * Ajax: install a Google Font.
		 *
		 * @name ajax_install_google_font
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function ajax_install_google_font() {
			self::verify_request();
			$family   = isset( $_POST['family'] ) ? sanitize_text_field( wp_unslash( $_POST['family'] ) ) : '';
			$variants = isset( $_POST['variants'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['variants'] ) ) : array();
			$result   = self::install_google_font( $family, $variants );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( array( 'message' => $result->get_error_message() ) );
			}
			wp_send_json_success( self::get_fonts_for_js() );
		}

		/**
		 * Ajax: delete a font.
		 *
		 * @name ajax_delete_font
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function ajax_delete_font() {
			self::verify_request();
			$key = isset( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';
			if ( ! self::delete_font( $key ) ) {
				wp_send_json_error( array( 'message' => __( 'Font not found.', 'wp-ultimate-pdf' ) ) );
			}
			wp_send_json_success( self::get_fonts_for_js() );
		}

		/**
		 * Popular Google Fonts offered as suggestions (any family name can be typed).
		 *
		 * @name popular_google_fonts
		 * @return array
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function popular_google_fonts() {
			return array(
				'Roboto', 'Open Sans', 'Lato', 'Montserrat', 'Poppins', 'Inter', 'Noto Sans', 'Noto Serif',
				'Raleway', 'Oswald', 'Nunito', 'Ubuntu', 'Merriweather', 'Playfair Display', 'Roboto Slab',
				'Roboto Mono', 'PT Sans', 'PT Serif', 'Source Sans 3', 'Work Sans', 'Rubik', 'Mulish',
				'Fira Sans', 'Barlow', 'DM Sans', 'Libre Baskerville', 'Lora', 'EB Garamond', 'Josefin Sans',
				'Quicksand', 'Cabin', 'Arimo', 'Tinos', 'Cousine', 'Karla', 'Manrope', 'Heebo', 'Cairo',
				'Noto Sans Arabic', 'Noto Sans Devanagari', 'Noto Sans Hebrew', 'Noto Sans Thai',
				'Hind', 'Mukta', 'Pacifico', 'Dancing Script', 'Great Vibes', 'Caveat', 'Bebas Neue', 'Anton',
			);
		}

		/**
		 * Render the font manager UI.
		 *
		 * @name render_manager
		 * @author WP Swings <support@wpswings.com>
		 * @link https://wpswings.com
		 */
		public static function render_manager() {
			?>
			<div id="mwbpdf-font-manager" class="mwbpdf-font-manager">
				<h3><?php esc_html_e( 'Custom Fonts', 'wp-ultimate-pdf' ); ?></h3>
				<p class="descr"><?php esc_html_e( 'Upload your own font or install one from Google Fonts. Installed fonts appear in the Content, Header, Footer and Watermark font dropdowns, and can be used in HTML/CSS via font-family.', 'wp-ultimate-pdf' ); ?></p>

				<div class="mwbpdf-font-panel">
					<h4><?php esc_html_e( 'Upload a font file', 'wp-ultimate-pdf' ); ?></h4>
					<p>
						<input type="file" id="mwbpdf-font-file" accept=".ttf,.woff,.woff2" />
					</p>
					<p>
						<label for="mwbpdf-font-label"><?php esc_html_e( 'Font name', 'wp-ultimate-pdf' ); ?></label>
						<input type="text" id="mwbpdf-font-label" placeholder="<?php esc_attr_e( 'Defaults to the file name', 'wp-ultimate-pdf' ); ?>" />
						<label for="mwbpdf-font-variant"><?php esc_html_e( 'Style', 'wp-ultimate-pdf' ); ?></label>
						<select id="mwbpdf-font-variant">
							<option value="regular"><?php esc_html_e( 'Regular', 'wp-ultimate-pdf' ); ?></option>
							<option value="bold"><?php esc_html_e( 'Bold', 'wp-ultimate-pdf' ); ?></option>
							<option value="italic"><?php esc_html_e( 'Italic', 'wp-ultimate-pdf' ); ?></option>
							<option value="bolditalic"><?php esc_html_e( 'Bold Italic', 'wp-ultimate-pdf' ); ?></option>
						</select>
						<button type="button" class="button button-primary" id="mwbpdf-font-upload"><?php esc_html_e( 'Upload Font', 'wp-ultimate-pdf' ); ?></button>
					</p>
					<p class="descr"><?php esc_html_e( 'Supported: .ttf, .woff, .woff2 (max 15 MB). To add bold or italic, upload each file with the same font name and pick its style.', 'wp-ultimate-pdf' ); ?></p>
				</div>

				<div class="mwbpdf-font-panel">
					<h4><?php esc_html_e( 'Install from Google Fonts', 'wp-ultimate-pdf' ); ?></h4>
					<p>
						<input type="text" id="mwbpdf-google-family" list="mwbpdf-google-list" placeholder="<?php esc_attr_e( 'e.g. Open Sans', 'wp-ultimate-pdf' ); ?>" />
						<datalist id="mwbpdf-google-list">
							<?php foreach ( self::popular_google_fonts() as $family ) : ?>
								<option value="<?php echo esc_attr( $family ); ?>"></option>
							<?php endforeach; ?>
						</datalist>
						<label><input type="checkbox" class="mwbpdf-google-variant" value="regular" checked disabled /> <?php esc_html_e( 'Regular', 'wp-ultimate-pdf' ); ?></label>
						<label><input type="checkbox" class="mwbpdf-google-variant" value="bold" checked /> <?php esc_html_e( 'Bold', 'wp-ultimate-pdf' ); ?></label>
						<label><input type="checkbox" class="mwbpdf-google-variant" value="italic" /> <?php esc_html_e( 'Italic', 'wp-ultimate-pdf' ); ?></label>
						<label><input type="checkbox" class="mwbpdf-google-variant" value="bolditalic" /> <?php esc_html_e( 'Bold Italic', 'wp-ultimate-pdf' ); ?></label>
						<button type="button" class="button button-primary" id="mwbpdf-google-install"><?php esc_html_e( 'Install Font', 'wp-ultimate-pdf' ); ?></button>
					</p>
					<p class="descr">
						<?php esc_html_e( 'Type any family name from', 'wp-ultimate-pdf' ); ?>
						<a href="https://fonts.google.com/" target="_blank" rel="noopener noreferrer">fonts.google.com</a>.
						<?php esc_html_e( 'The font files are downloaded once to your server; PDFs never contact Google.', 'wp-ultimate-pdf' ); ?>
					</p>
				</div>

				<p class="mwbpdf-font-status" role="status" aria-live="polite"></p>

				<table class="widefat striped mwbpdf-font-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Font', 'wp-ultimate-pdf' ); ?></th>
							<th><?php esc_html_e( 'PDF font name', 'wp-ultimate-pdf' ); ?></th>
							<th><?php esc_html_e( 'Styles', 'wp-ultimate-pdf' ); ?></th>
							<th><?php esc_html_e( 'Source', 'wp-ultimate-pdf' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<?php
		}
	}

	Mwbpdf_Custom_Fonts::init();
}
