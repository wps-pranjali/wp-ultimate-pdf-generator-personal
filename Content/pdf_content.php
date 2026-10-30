<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

?>
<div class="wrap">

	<h2 class="pdf_head"> <?php esc_html_e('Wp Ultimate Pdf Generator Settings', 'wp-ultimate-pdf'); ?></h2>
	<div class="updated below-h2" id="mwb_pdf_gen_message"></div>
	<div id="gde-tabcontent">

		<div id="gencontent" class="gde-tab gde-tab-active">
			<?php
			$mwb_pdf_license_hash = get_option('mwb_pdf_license_hash');
			$mwb_pdf_license_key = get_option('mwb_pdf_license_key');
			$mwb_pdf_license_plugin = get_option('mwb_pdf_plugin_name');
			$mwb_pdf_hash = md5(wp_unslash($_SERVER['HTTP_HOST']) . $mwb_pdf_license_plugin . $mwb_pdf_license_key);

			if ($mwb_pdf_license_hash == $mwb_pdf_hash) {
				mwbpdf_gen_show_tab('general');
			} else {
				mwbpdf_gen_show_tab('license');
			}

			?>
		</div>

	</div>
</div>
<?php
/**
 * Post Meta settings tab details
 *
 * @param string $name name .
 * @name mwbpdf_gen_show_tab
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function mwbpdf_gen_show_tab($name)
{
	if (! session_id()) {
		session_start();
	}
	$tabfile = MWBPDF_GEN_PATH . "/libs/mwbpdf-tab-$name.php";
	if (file_exists($tabfile)) {
		include_once($tabfile);
	}
}

/**
 * General profile option
 *
 * @param string $option option.
 * @param string $value value .
 * @param string $label lable .
 * @param string $helptext helptxt.
 * @name mwbpdf_gen_profile_option
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function mwbpdf_gen_profile_option($option, $value, $label, $helptext = '')
{
	echo '<option value="' . esc_attr($value) . '"';
	if (! empty($helptext)) {
		echo ' title="' . esc_attr($helptext) . '"';
	}
	if ($option == $value) {
		echo ' selected="selected"';
	}
	echo "> $label &nbsp;</option>\n";
}

/**
 * General profile checkbox
 *
 * @param string $val val .
 * @param string $field field.
 * @param string $default default.
 * @param string $wrap wrap.
 * @param string $label label.
 * @param string $br br.
 * @param string $disabled disabled.
 * @name mwbpdf_gen_profile_checkbox
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function mwbpdf_gen_profile_checkbox($val, $field, $default = '1', $wrap = '', $label = '', $br = '', $disabled = false)
{
	if (! empty($wrap)) {
		echo '<span id="' . esc_attr($wrap) . '">';
	}
	echo '<input type="checkbox" id="' . esc_attr($field) . '" name="' . esc_attr($field) . '"';
	if (($val == $default) || ($disabled)) {
		echo ' checked="checked"';
	}
	if ($disabled) {
		// used only for dx logging option due to global override in functions.php.
		echo ' disabled="disabled"';
	}

	echo ' value="' . esc_attr($default) . '"> <label for="' . esc_attr($field) . '">' . esc_html(htmlentities($label)) . '</label>';
	if (! empty($br)) {
		echo '<br/>';
	}
	if (! empty($wrap)) {
		echo '</span>';
	}
}
