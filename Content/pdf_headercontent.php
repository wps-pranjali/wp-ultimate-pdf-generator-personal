<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}
/**
 * Pdf_headercontent.php.
 *
 * This file contains header functions.
 */
if (! class_exists('TCPDF')) {
	require_once MWBPDF_GEN_PATH . '/tcpdf/tcpdf.php';
}
if (! isset($this->options['2']['page_header']) || ($this->options['2']['page_header'] == 'None')) {
	define('PRINT_HEADER', '1');
}
/**
 *  Custompdf extendes to tcpdf.
 */
class CUSTOMPDF extends TCPDF
{
	/**
	 * Resolve fonts installed from the admin (stored in uploads) before TCPDF's core font folder.
	 *
	 * @name AddFont
	 * @param string $family font family.
	 * @param string $style font style.
	 * @param string $fontfile font definition file.
	 * @param mixed  $subset subsetting mode.
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function AddFont($family, $style = '', $fontfile = '', $subset = 'default')
	{
		if ('' === (string) $fontfile && class_exists('Mwbpdf_Custom_Fonts')) {
			$fontfile = Mwbpdf_Custom_Fonts::locate_font_file($family, $style);
		}
		return parent::AddFont($family, $style, $fontfile, $subset);
	}

	/**
	 * Make admin-installed fonts usable through CSS font-family in HTML content.
	 *
	 * @name getFontsList
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	protected function getFontsList()
	{
		parent::getFontsList();
		if (class_exists('Mwbpdf_Custom_Fonts')) {
			foreach ((array) glob(Mwbpdf_Custom_Fonts::get_font_dir() . '*.php') as $file) {
				$name = strtolower(basename($file, '.php'));
				if ('index' !== $name && ! in_array($name, $this->fontlist, true)) {
					$this->fontlist[] = $name;
				}
			}
		}
	}

	/**
	 * General header settings.
	 *
	 * @name header
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function header()
	{
		if (defined('PRINT_HEADER')) {
			$this->setPrintHeader(false);
		} else {

			$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);

			if (!empty($mwbpdf_genoptions['0']['page_color'])) {
				$wps_color = $mwbpdf_genoptions['0']['mwb_pdf_page_color'];
				list($r, $g, $b) = sscanf($wps_color, '#%02x%02x%02x');
				if ('L' == $mwbpdf_genoptions['1']['page_orientation']) {
					$this->Rect(0, 0, 297, 210, 'F', '', $fill_color = array($r, $g, $b));
				} else {
					$this->Rect(0, 0, 210, 297, 'F', '', $fill_color = array($r, $g, $b));
				}
			}

			if (! isset($mwbpdf_genoptions['2']['qr_code_header'])) {
				$url = esc_url(get_permalink(get_the_ID()));
				$style = array(
					'border' => false,
					'padding' => 'auto',
					'fgcolor' => array(0, 0, 0),
					'bgcolor' => false
				);
				$this->write2DBarcode($url, 'QRCODE,L', 162, 1, 25, 25, $style, 'N');
			}
			parent::header();
		}
	}

	/**
	 * General Footer settings
	 *
	 * @name Footer
	 * @author WP Swings <support@wpswings.com>
	 * @link http://wpswings.com
	 */
	public function Footer()
	{
		$options = get_option(MWBPDF_GEN_PREFIX);
		if (isset($this->options[8]['start_page_predefined_template']) || isset($this->options[9]['custom_cover_page'])) {

			if (isset($options['7']['custom_footer_option']) && 1 !== $this->page) {
				if (! empty($options['7']['custom_footer'])) {
					$this->writeHTMLCell($options['7']['footer_cell_width'], $options['7']['footer_min_height'], $options['7']['footer_lcornerX'], $options['7']['footer_font_lcornerY'], $options['7']['custom_footer'], '', '', isset($options['7']['footer_cell_fill']) ? $options['7']['footer_cell_fill'] : '', true, $options['7']['footer_align'], isset($options['7']['footer_cell_auto_padding']) ? $options['7']['footer_cell_auto_padding'] : '');
				}
				parent::Footer();
			} else {
				// Call parent footer method for default footer.
				parent::Footer();
			}
		} else {
			if (isset($options['7']['custom_footer_option'])) {
				if (! empty($options['7']['custom_footer'])) {
					$this->writeHTMLCell($options['7']['footer_cell_width'], $options['7']['footer_min_height'], $options['7']['footer_lcornerX'], $options['7']['footer_font_lcornerY'], $options['7']['custom_footer'], '', '', isset($options['7']['footer_cell_fill']) ? $options['7']['footer_cell_fill'] : '', true, $options['7']['footer_align'], isset($options['7']['footer_cell_auto_padding']) ? $options['7']['footer_cell_auto_padding'] : '');
				}
				parent::Footer();
			} else {
				// call parent footer method for default footer.
				parent::Footer();
			}
		}
	}
}
