<?php if (! defined('ABSPATH')) {
	exit;
} // Exit if accessed directly. 
?>
<?php
/*
** Page to display for admin settings.
*/
$value_check = isset($_POST['s_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['s_wpnonce'])) : '';
if (isset($_POST['currentsetting']) && wp_verify_nonce($value_check, 'wp-reset-nonce')) {

	$val = sanitize_text_field(wp_unslash($_POST['currentsetting']));

	$arr = get_option('ptp_settings');


	if ('Default Settings' != $val) {

		$set = $arr[$val];


		update_option(MWBPDF_GEN_PREFIX, $set);
	} else {
		// set to display defult settings of every html elements.
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


		update_option(MWBPDF_GEN_PREFIX, $default);
	}
}
?>
<?php
// set defaults available fonts.
global $fonts;
$fonts = array(
	'Courier' => 'courier',
	'Courier Bold' => 'courierB',
	'Courier Bold Italic' => 'courierBI',
	'Courier Italic' => 'courierI',
	'Helvetica' => 'helvetica',
	'Helvetica Bold' => 'helveticaB',
	'Helvetica Bold Italic' => 'helveticaBI',
	'Helvetica Italic' => 'helveticaI',
	'Symbol' => 'symbol',
	'Times New Roman' => 'times',
	'Times New Roman Bold' => 'timesB',
	'Times New Roman Bold Italic' => 'timesBI',
	'Times New Roman Italic' => 'timesI',
	'Zapf Dingbats' => 'zapfdingbats',
);

$custom_fonts = Mwbpdf_Custom_Fonts::get_dropdown_fonts();
if (! empty($custom_fonts)) {
	$fonts = array_merge($fonts, $custom_fonts);
}

// availble page size types.
global $page_size;
$page_size = array(
	'ISO 216 A Series + 2 SIS 014711 extensions (default: A4)' => 'A4',
	'A0 (841x1189 mm ; 33.11x46.81 in)' => 'A0',
	'A1 (594x841 mm ; 23.39x33.11 in)' => 'A1',
	'A2 (420x594 mm ; 16.54x23.39 in)' => 'A2',
	'A3 (297x420 mm ; 11.69x16.54 in)' => 'A3',
	'A4 (210x297 mm ; 8.27x11.69 in)' => 'A4',
	'A5 (148x210 mm ; 5.83x8.27 in)' => 'A5',
	'A6 (105x148 mm ; 4.13x5.83 in)' => 'A6',
	'A7 (74x105 mm ; 2.91x4.13 in)' => 'A7',
	'A8 (52x74 mm ; 2.05x2.91 in)' => 'A8',
	'A9 (37x52 mm ; 1.46x2.05 in)' => 'A9',
	'A10 (26x37 mm ; 1.02x1.46 in)' => 'A10',
	'A11 (18x26 mm ; 0.71x1.02 in)' => 'A11',
	'A12 (13x18 mm ; 0.51x0.71 in)' => 'A12',
	'ISO 216 B Series + 2 SIS 014711 extensions (default: B4)' => 'B4',
	'B0 (1000x1414 mm ; 39.37x55.67 in)' => 'B0',
	'B1 (707x1000 mm ; 27.83x39.37 in)' => 'B1',
	'B2 (500x707 mm ; 19.69x27.83 in)' => 'B2',
	'B3 (353x500 mm ; 13.90x19.69 in)' => 'B3',
	'B4 (250x353 mm ; 9.84x13.90 in)' => 'B4',
	'B5 (176x250 mm ; 6.93x9.84 in)' => 'B5',
	'B6 (125x176 mm ; 4.92x6.93 in)' => 'B6',
	'B7 (88x125 mm ; 3.46x4.92 in)' => 'B7',
	'B8 (62x88 mm ; 2.44x3.46 in)' => 'B8',
	'B9 (44x62 mm ; 1.73x2.44 in)' => 'B9',
	'B10 (31x44 mm ; 1.22x1.73 in)' => 'B10',
	'B11 (22x31 mm ; 0.87x1.22 in)' => 'B11',
	'B12 (15x22 mm ; 0.59x0.87 in)' => 'B12',
	'ISO 216 C Series + 2 SIS 014711 extensions + 2 EXTENSION (default: C4)' => 'C4',
	'C0 (917x1297 mm ; 36.10x51.06 in)' => 'C0',
	'C1 (648x917 mm ; 25.51x36.10 in)' => 'C1',
	'C2 (458x648 mm ; 18.03x25.51 in)' => 'C2',
	'C3 (324x458 mm ; 12.76x18.03 in)' => 'C3',
	'C4 (229x324 mm ; 9.02x12.76 in)' => 'C4',
	'C5 (162x229 mm ; 6.38x9.02 in)' => 'C5',
	'C6 (114x162 mm ; 4.49x6.38 in)' => 'C6',
	'C7 (81x114 mm ; 3.19x4.49 in)' => 'C7',
	'C8 (57x81 mm ; 2.24x3.19 in)' => 'C8',
	'C9 (40x57 mm ; 1.57x2.24 in)' => 'C9',
	'C10 (28x40 mm ; 1.10x1.57 in)' => 'C10',
	'C11 (20x28 mm ; 0.79x1.10 in)' => 'C11',
	'C12 (14x20 mm ; 0.55x0.79 in)' => 'C12',
	'C76 (81x162 mm ; 3.19x6.38 in)' => 'C76',
	'DL (110x220 mm ; 4.33x8.66 in)' => 'DL',
	'SIS 014711 E Series (default: E4)' => 'E4',
	'E0 (879x1241 mm ; 34.61x48.86 in)' => 'E0',
	'E1 (620x879 mm ; 24.41x34.61 in)' => 'E1',
	'E2 (440x620 mm ; 17.32x24.41 in)' => 'E2',
	'E3 (310x440 mm ; 12.20x17.32 in)' => 'E3',
	'E4 (220x310 mm ; 8.66x12.20 in)' => 'E4',
	'E5 (155x220 mm ; 6.10x8.66 in)' => 'E5',
	'E6 (110x155 mm ; 4.33x6.10 in)' => 'E6',
	'E7 (78x110 mm ; 3.07x4.33 in)' => 'E7',
	'E8 (55x78 mm ; 2.17x3.07 in)' => 'E8',
	'E9 (39x55 mm ; 1.54x2.17 in)' => 'E9',
	'E10 (27x39 mm ; 1.06x1.54 in)' => 'E10',
	'E11 (19x27 mm ; 0.75x1.06 in)' => 'E11',
	'E12 (13x19 mm ; 0.51x0.75 in)' => 'E12',
	'SIS 014711 G Series (default: G4)' => 'G4',
	'G0 (958x1354 mm ; 37.72x53.31 in)' => 'G0',
	'G1 (677x958 mm ; 26.65x37.72 in)' => 'G1',
	'G2 (479x677 mm ; 18.86x26.65 in)' => 'G2',
	'G3 (338x479 mm ; 13.31x18.86 in)' => 'G3',
	'G4 (239x338 mm ; 9.41x13.31 in)' => 'G4',
	'G5 (169x239 mm ; 6.65x9.41 in)' => 'G5',
	'G6 (119x169 mm ; 4.69x6.65 in)' => 'G6',
	'G7 (84x119 mm ; 3.31x4.69 in)' => 'G7',
	'G8 (59x84 mm ; 2.32x3.31 in)' => 'G8',
	'G9 (42x59 mm ; 1.65x2.32 in)' => 'G9',
	'G10 (29x42 mm ; 1.14x1.65 in)' => 'G10',
	'G11 (21x29 mm ; 0.83x1.14 in)' => 'G11',
	'G12 (14x21 mm ; 0.55x0.83 in)' => 'G12',
	'ISO Press (default: RA4)' => 'RA4',
	'RA0 (860x1220 mm ; 33.86x48.03 in)' => 'RA0',
	'RA1 (610x860 mm ; 24.02x33.86 in)' => 'RA1',
	'RA2 (430x610 mm ; 16.93x24.02 in)' => 'RA2',
	'RA3 (305x430 mm ; 12.01x16.93 in)' => 'RA3',
	'RA4 (215x305 mm ; 8.46x12.01 in)' => 'RA4',
	'SRA0 (900x1280 mm ; 35.43x50.39 in)' => 'SRA0',
	'SRA1 (640x900 mm ; 25.20x35.43 in)' => 'SRA1',
	'SRA2 (450x640 mm ; 17.72x25.20 in)' => 'SRA2',
	'SRA3 (320x450 mm ; 12.60x17.72 in)' => 'SRA3',
	'SRA4 (225x320 mm ; 8.86x12.60 in)' => 'SRA4',
	'German DIN 476 (default: 4A0)' => '4A0',
	'4A0 (1682x2378 mm ; 66.22x93.62 in)' => '4A0',
	'2A0 (1189x1682 mm ; 46.81x66.22 in)' => '2A0',
	'Variations on the ISO Standard (default: A4_EXTRA)' => 'A4_EXTRA',
	'A2_EXTRA (445x619 mm ; 17.52x24.37 in)' => 'A2_EXTRA',
	'A3+ (329x483 mm ; 12.95x19.02 in)' => 'A3+',
	'A3_EXTRA (322x445 mm ; 12.68x17.52 in)' => 'A3_EXTRA',
	'A3_SUPER (305x508 mm ; 12.01x20.00 in)' => 'A3_SUPER',
	'SUPER_A3 (305x487 mm ; 12.01x19.17 in)' => 'SUPER_A3',
	'A4_EXTRA (235x322 mm ; 9.25x12.68 in)' => 'A4_EXTRA',
	'A4_SUPER (229x322 mm ; 9.02x12.68 in)' => 'A4_SUPER',
	'SUPER_A4 (227x356 mm ; 8.94x14.02 in)' => 'SUPER_A4',
	'A4_LONG (210x348 mm ; 8.27x13.70 in)' => 'A4_LONG',
	'F4 (210x330 mm ; 8.27x12.99 in)' => 'F4',
	'SO_B5_EXTRA (202x276 mm ; 7.95x10.87 in)' => 'SO_B5_EXTRA',
	'A5_EXTRA (173x235 mm ; 6.81x9.25 in)' => 'A5_EXTRA',
	'ANSI Series (default: ANSI_A)' => 'ANSI_A',
	'ANSI_E (864x1118 mm ; 34.00x44.00 in)' => 'ANSI_E',
	'ANSI_D (559x864 mm ; 22.00x34.00 in)' => 'ANSI_D',
	'ANSI_C (432x559 mm ; 17.00x22.00 in)' => 'ANSI_C',
	'ANSI_B (279x432 mm ; 11.00x17.00 in)' => 'ANSI_B',
	'ANSI_A (216x279 mm ; 8.50x11.00 in)' => 'ANSI_A',
	'Traditional \'Loose\' North American Paper Sizes (default: LETTER)' => 'LETTER',
	'LEDGER, USLEDGER (432x279 mm ; 17.00x11.00 in)' => 'LEDGER',
	'TABLOID, USTABLOID, BIBLE, ORGANIZERK (279x432 mm ; 11.00x17.00 in)' => 'TABLOID',
	'LETTER, USLETTER, ORGANIZERM (216x279 mm ; 8.50x11.00 in)' => 'LETTER',
	'LEGAL, USLEGAL (216x356 mm ; 8.50x14.00 in)' => 'LEGAL',
	'GLETTER, GOVERNMENTLETTER (203x267 mm ; 8.00x10.50 in)' => 'GLETTER',
	'JLEGAL, JUNIORLEGAL (203x127 mm ; 8.00x5.00 in)' => 'JLEGAL',
	'Other North American Paper Sizes (default: FOLIO)' => 'FOLIO',
	'QUADDEMY (889x1143 mm ; 35.00x45.00 in)' => 'QUADDEMY',
	'SUPER_B (330x483 mm ; 13.00x19.00 in)' => 'SUPER_B',
	'QUARTO (229x279 mm ; 9.00x11.00 in)' => 'QUARTO',
	'FOLIO, GOVERNMENTLEGAL (216x330 mm ; 8.50x13.00 in)' => 'FOLIO',
	'EXECUTIVE, MONARCH (184x267 mm ; 7.25x10.50 in)' => 'EXECUTIVE',
	'MEMO, STATEMENT, ORGANIZERL (140x216 mm ; 5.50x8.50 in)' => 'MEMO',
	'FOOLSCAP (210x330 mm ; 8.27x13.00 in)' => 'FOOLSCAP',
	'COMPACT (108x171 mm ; 4.25x6.75 in)' => 'COMPACT',
	'ORGANIZERJ (70x127 mm ; 2.75x5.00 in)' => 'ORGANIZERJ',
	'Canadian standard CAN 2-9.60M (default: P4)' => 'P4',
	'P1 (560x860 mm ; 22.05x33.86 in)' => 'P1',
	'P2 (430x560 mm ; 16.93x22.05 in)' => 'P2',
	'P3 (280x430 mm ; 11.02x16.93 in)' => 'P3',
	'P4 (215x280 mm ; 8.46x11.02 in)' => 'P4',
	'P5 (140x215 mm ; 5.51x8.46 in)' => 'P5',
	'P6 (107x140 mm ; 4.21x5.51 in)' => 'P6',
	'North American Architectural Sizes (default: ARCH_A)' => 'ARCH_A',
	'ARCH_E (914x1219 mm ; 36.00x48.00 in)' => 'ARCH_E',
	'ARCH_E1 (762x1067 mm ; 30.00x42.00 in)' => 'ARCH_E1',
	'ARCH_D (610x914 mm ; 24.00x36.00 in)' => 'ARCH_D',
	'ARCH_C, BROADSHEET (457x610 mm ; 18.00x24.00 in)' => 'ARCH_C',
	'ARCH_B (305x457 mm ; 12.00x18.00 in)' => 'ARCH_B',
	'ARCH_A (229x305 mm ; 9.00x12.00 in)' => 'ARCH_A',
	'Announcement Envelopes (default: ANNENV_A2)' => 'ANNENV_A2',
	'ANNENV_A2 (111x146 mm ; 4.37x5.75 in)' => 'ANNENV_A2',
	'ANNENV_A6 (121x165 mm ; 4.75x6.50 in)' => 'ANNENV_A6',
	'ANNENV_A7 (133x184 mm ; 5.25x7.25 in)' => 'ANNENV_A7',
	'ANNENV_A8 (140x206 mm ; 5.50x8.12 in)' => 'ANNENV_A8',
	'ANNENV_A10 (159x244 mm ; 6.25x9.62 in)' => 'ANNENV_A10',
	'ANNENV_SLIM (98x225 mm ; 3.87x8.87 in)' => 'ANNENV_SLIM',
	'Commercial Envelopes (default: COMMENV_N10)' => 'COMMENV_N10',
	'COMMENV_N6_1/4 (89x152 mm ; 3.50x6.00 in)' => 'COMMENV_N6_1/4',
	'COMMENV_N6_3/4 (92x165 mm ; 3.62x6.50 in)' => 'COMMENV_N6_3/4',
	'COMMENV_N8 (98x191 mm ; 3.87x7.50 in)' => 'COMMENV_N8',
	'COMMENV_N9 (98x225 mm ; 3.87x8.87 in)' => 'COMMENV_N9',
	'COMMENV_N10 (105x241 mm ; 4.12x9.50 in)' => 'COMMENV_N10',
	'COMMENV_N11 (114x263 mm ; 4.50x10.37 in)' => 'COMMENV_N11',
	'COMMENV_N12 (121x279 mm ; 4.75x11.00 in)' => 'COMMENV_N12',
	'COMMENV_N14 (127x292 mm ; 5.00x11.50 in)' => 'COMMENV_N14',
	'Catalogue Envelopes (default: CATENV_N10_1/2)' => 'CATENV_N10_1/2',
	'CATENV_N1 (152x229 mm ; 6.00x9.00 in)' => 'CATENV_N1',
	'CATENV_N1_3/4 (165x241 mm ; 6.50x9.50 in)' => 'CATENV_N1_3/4',
	'CATENV_N2 (165x254 mm ; 6.50x10.00 in)' => 'CATENV_N2',
	'CATENV_N3 (178x254 mm ; 7.00x10.00 in)' => 'CATENV_N3',
	'CATENV_N6 (191x267 mm ; 7.50x10.50 in)' => 'CATENV_N6',
	'CATENV_N7 (203x279 mm ; 8.00x11.00 in)' => 'CATENV_N7',
	'CATENV_N8 (210x286 mm ; 8.25x11.25 in)' => 'CATENV_N8',
	'CATENV_N9_1/2 (216x267 mm ; 8.50x10.50 in)' => 'CATENV_N9_1/2',
	'CATENV_N9_3/4 (222x286 mm ; 8.75x11.25 in)' => 'CATENV_N9_3/4',
	'CATENV_N10_1/2 (229x305 mm ; 9.00x12.00 in)' => 'CATENV_N10_1/2',
	'CATENV_N12_1/2 (241x318 mm ; 9.50x12.50 in)' => 'CATENV_N12_1/2',
	'CATENV_N13_1/2 (254x330 mm ; 10.00x13.00 in)' => 'CATENV_N13_1/2',
	'CATENV_N14_1/4 (286x311 mm ; 11.25x12.25 in)' => 'CATENV_N14_1/4',
	'CATENV_N14_1/2 (292x368 mm ; 11.50x14.50 in)' => 'CATENV_N14_1/2',
	'Japanese (JIS P 0138-61) Standard B-Series (default: JIS_B5)' => 'JIS_B5',
	'JIS_B0 (1030x1456 mm ; 40.55x57.32 in)' => 'JIS_B0',
	'JIS_B1 (728x1030 mm ; 28.66x40.55 in)' => 'JIS_B1',
	'JIS_B2 (515x728 mm ; 20.28x28.66 in)' => 'JIS_B2',
	'JIS_B3 (364x515 mm ; 14.33x20.28 in)' => 'JIS_B3',
	'JIS_B4 (257x364 mm ; 10.12x14.33 in)' => 'JIS_B4',
	'JIS_B5 (182x257 mm ; 7.17x10.12 in)' => 'JIS_B5',
	'JIS_B6 (128x182 mm ; 5.04x7.17 in)' => 'JIS_B6',
	'JIS_B7 (91x128 mm ; 3.58x5.04 in)' => 'JIS_B7',
	'JIS_B8 (64x91 mm ; 2.52x3.58 in)' => 'JIS_B8',
	'JIS_B9 (45x64 mm ; 1.77x2.52 in)' => 'JIS_B9',
	'JIS_B10 (32x45 mm ; 1.26x1.77 in)' => 'JIS_B10',
	'JIS_B11 (22x32 mm ; 0.87x1.26 in)' => 'JIS_B11',
	'JIS_B12 (16x22 mm ; 0.63x0.87 in)' => 'JIS_B12',
	'PA Series (default: PA4)' => 'PA4',
	'PA0 (840x1120 mm ; 33.07x44.09 in)' => 'PA0',
	'PA1 (560x840 mm ; 22.05x33.07 in)' => 'PA1',
	'PA2 (420x560 mm ; 16.54x22.05 in)' => 'PA2',
	'PA3 (280x420 mm ; 11.02x16.54 in)' => 'PA3',
	'PA4 (210x280 mm ; 8.27x11.02 in)' => 'PA4',
	'PA5 (140x210 mm ; 5.51x8.27 in)' => 'PA5',
	'PA6 (105x140 mm ; 4.13x5.51 in)' => 'PA6',
	'PA7 (70x105 mm ; 2.76x4.13 in)' => 'PA7',
	'PA8 (52x70 mm ; 2.05x2.76 in)' => 'PA8',
	'PA9 (35x52 mm ; 1.38x2.05 in)' => 'PA9',
	'PA10 (26x35 mm ; 1.02x1.38 in)' => 'PA10',
	'Standard Photographic Print Sizes (default: 8R, 6P)' => '8R',
	'PASSPORT_PHOTO (35x45 mm ; 1.38x1.77 in)' => 'PASSPORT_PHOTO',
	'E (82x120 mm ; 3.25x4.72 in)' => 'E',
	'3R, L (89x127 mm ; 3.50x5.00 in)' => '3R',
	'4R, KG (102x152 mm ; 4.02x5.98 in)' => '4R',
	'4D (120x152 mm ; 4.72x5.98 in)' => '4D',
	'5R, 2L (127x178 mm ; 5.00x7.01 in)' => '5R',
	'6R, 8P (152x203 mm ; 5.98x7.99 in)' => '6R',
	'8R, 6P (203x254 mm ; 7.99x10.00 in)' => '8R',
	'S8R, 6PW (203x305 mm ; 7.99x12.01 in)' => 'S8R',
	'10R, 4P (254x305 mm ; 10.00x12.01 in)' => '10R',
	'S10R, 4PW (254x381 mm ; 10.00x15.00 in)' => 'S10R',
	'11R (279x356 mm ; 10.98x14.02 in)' => '11R',
	'S11R (279x432 mm ; 10.98x17.01 in)' => 'S11R',
	'12R (305x381 mm ; 12.01x15.00 in)' => '12R',
	'S12R (305x456 mm ; 12.01x17.95 in)' => 'S12R',
	'Common Newspaper Sizes (default: NEWSPAPER_TABLOID)' => 'NEWSPAPER_TABLOID',
	'NEWSPAPER_BROADSHEET (750x600 mm ; 29.53x23.62 in)' => 'NEWSPAPER_BROADSHEET',
	'NEWSPAPER_BERLINER (470x315 mm ; 18.50x12.40 in)' => 'NEWSPAPER_BERLINER',
	'NEWSPAPER_COMPACT, NEWSPAPER_TABLOID (430x280 mm ; 16.93x11.02 in)' => 'NEWSPAPER_TABLOID',
	'Business Cards (default: BUSINESS_CARD)' => 'BUSINESS_CARD',
	'CREDIT_CARD, BUSINESS_CARD, BUSINESS_CARD_ISO7810 (54x86 mm ; 2.13x3.37 in)' => 'BUSINESS_CARD',
	'BUSINESS_CARD_ISO216 (52x74 mm ; 2.05x2.91 in)' => 'BUSINESS_CARD_ISO216',
	'BUSINESS_CARD_IT, UK, FR, DE, ES (55x85 mm ; 2.17x3.35 in)' => 'BUSINESS_CARD_IT',
	'BUSINESS_CARD_US, CA (51x89 mm ; 2.01x3.50 in)' => 'BUSINESS_CARD_US',
	'BUSINESS_CARD_JP (55x91 mm ; 2.17x3.58 in)' => 'BUSINESS_CARD_JP',
	'BUSINESS_CARD_HK (54x90 mm ; 2.13x3.54 in)' => 'BUSINESS_CARD_HK',
	'BUSINESS_CARD_AU, DK, SE (55x90 mm ; 2.17x3.54 in)' => 'BUSINESS_CARD_AU',
	'BUSINESS_CARD_RU, CZ, FI, HU, IL (50x90 mm ; 1.97x3.54 in)' => 'BUSINESS_CARD_RU',
	'Billboards (default: 4SHEET)' => '4SHEET',
	'4SHEET (1016x1524 mm ; 40.00x60.00 in)' => '4SHEET',
	'6SHEET (1200x1800 mm ; 47.24x70.87 in)' => '6SHEET',
	'12SHEET (3048x1524 mm ; 120.00x60.00 in)' => '12SHEET',
	'16SHEET (2032x3048 mm ; 80.00x120.00 in)' => '16SHEET',
	'32SHEET (4064x3048 mm ; 160.00x120.00 in)' => '32SHEET',
	'48SHEET (6096x3048 mm ; 240.00x120.00 in)' => '48SHEET',
	'64SHEET (8128x3048 mm ; 320.00x120.00 in)' => '64SHEET',
	'96SHEET (12192x3048 mm ; 480.00x120.00 in)' => '96SHEET',
	'Old Imperial English (default: EN_ATLAS)' => 'EN_ATLAS',
	'EN_EMPEROR (1219x1829 mm ; 48.00x72.00 in)' => 'EN_EMPEROR',
	'EN_ANTIQUARIAN (787x1346 mm ; 31.00x53.00 in)' => 'EN_ANTIQUARIAN',
	'EN_GRAND_EAGLE (730x1067 mm ; 28.75x42.00 in)' => 'EN_GRAND_EAGLE',
	'EN_DOUBLE_ELEPHANT (679x1016 mm ; 26.75x40.00 in)' => 'EN_DOUBLE_ELEPHANT',
	'EN_ATLAS (660x864 mm ; 26.00x34.00 in)' => 'EN_ATLAS',
	'EN_COLOMBIER (597x876 mm ; 23.50x34.50 in)' => 'EN_COLOMBIER',
	'EN_ELEPHANT (584x711 mm ; 23.00x28.00 in)' => 'EN_ELEPHANT',
	'EN_DOUBLE_DEMY (572x902 mm ; 22.50x35.50 in)' => 'EN_DOUBLE_DEMY',
	'EN_IMPERIAL (559x762 mm ; 22.00x30.00 in)' => 'EN_IMPERIAL',
	'EN_PRINCESS (546x711 mm ; 21.50x28.00 in)' => 'EN_PRINCESS',
	'EN_CARTRIDGE (533x660 mm ; 21.00x26.00 in)' => 'EN_CARTRIDGE',
	'EN_DOUBLE_LARGE_POST (533x838 mm ; 21.00x33.00 in)' => 'EN_DOUBLE_LARGE_POST',
	'EN_ROYAL (508x635 mm ; 20.00x25.00 in)' => 'EN_ROYAL',
	'EN_SHEET, EN_HALF_POST (495x597 mm ; 19.50x23.50 in)' => 'EN_SHEET, EN_HALF_POST',
	'EN_SUPER_ROYAL (483x686 mm ; 19.00x27.00 in)' => 'EN_SUPER_ROYAL',
	'EN_DOUBLE_POST (483x775 mm ; 19.00x30.50 in)' => 'EN_DOUBLE_POST',
	'EN_MEDIUM (445x584 mm ; 17.50x23.00 in)' => 'EN_MEDIUM',
	'EN_DEMY (445x572 mm ; 17.50x22.50 in)' => 'EN_DEMY',
	'EN_LARGE_POST (419x533 mm ; 16.50x21.00 in)' => 'EN_LARGE_POST',
	'EN_COPY_DRAUGHT (406x508 mm ; 16.00x20.00 in)' => 'EN_COPY_DRAUGHT',
	'EN_POST (394x489 mm ; 15.50x19.25 in)' => 'EN_POST',
	'EN_CROWN (381x508 mm ; 15.00x20.00 in)' => 'EN_CROWN',
	'EN_PINCHED_POST (375x470 mm ; 14.75x18.50 in)' => 'EN_PINCHED_POST',
	'EN_BRIEF (343x406 mm ; 13.50x16.00 in)' => 'EN_BRIEF',
	'EN_FOOLSCAP (343x432 mm ; 13.50x17.00 in)' => 'EN_FOOLSCAP',
	'EN_SMALL_FOOLSCAP (337x419 mm ; 13.25x16.50 in)' => 'EN_SMALL_FOOLSCAP',
	'EN_POTT (318x381 mm ; 12.50x15.00 in)' => 'EN_POTT',
	'Old Imperial Belgian (default: BE_ELEPHANT)' => 'BE_ELEPHANT',
	'BE_GRAND_AIGLE (700x1040 mm ; 27.56x40.94 in)' => 'BE_GRAND_AIGLE',
	'BE_COLOMBIER (620x850 mm ; 24.41x33.46 in)' => 'BE_COLOMBIER',
	'BE_DOUBLE_CARRE (620x920 mm ; 24.41x36.22 in)' => 'BE_DOUBLE_CARRE',
	'BE_ELEPHANT (616x770 mm ; 24.25x30.31 in)' => 'BE_ELEPHANT',
	'BE_PETIT_AIGLE (600x840 mm ; 23.62x33.07 in)' => 'BE_PETIT_AIGLE',
	'BE_GRAND_JESUS (550x730 mm ; 21.65x28.74 in)' => 'BE_GRAND_JESUS',
	'BE_JESUS (540x730 mm ; 21.26x28.74 in)' => 'BE_JESUS',
	'BE_RAISIN (500x650 mm ; 19.69x25.59 in)' => 'BE_RAISIN',
	'BE_GRAND_MEDIAN (460x605 mm ; 18.11x23.82 in)' => 'BE_GRAND_MEDIAN',
	'BE_DOUBLE_POSTE (435x565 mm ; 17.13x22.24 in)' => 'BE_DOUBLE_POSTE',
	'BE_COQUILLE (430x560 mm ; 16.93x22.05 in)' => 'BE_COQUILLE',
	'BE_PETIT_MEDIAN (415x530 mm ; 16.34x20.87 in)' => 'BE_PETIT_MEDIAN',
	'BE_RUCHE (360x460 mm ; 14.17x18.11 in)' => 'BE_RUCHE',
	'BE_PROPATRIA (345x430 mm ; 13.58x16.93 in)' => 'BE_PROPATRIA',
	'BE_LYS (317x397 mm ; 12.48x15.63 in)' => 'BE_LYS',
	'BE_POT (307x384 mm ; 12.09x15.12 in)' => 'BE_POT',
	'BE_ROSETTE (270x347 mm ; 10.63x13.66 in)' => 'BE_ROSETTE',
	'Old Imperial French (default: FR_PETIT_AIGLE)' => 'FR_PETIT_AIGLE',
	'FR_UNIVERS (1000x1300 mm ; 39.37x51.18 in)' => 'FR_UNIVERS',
	'FR_DOUBLE_COLOMBIER (900x1260 mm ; 35.43x49.61 in)' => 'FR_DOUBLE_COLOMBIER',
	'FR_GRANDE_MONDE (900x1260 mm ; 35.43x49.61 in)' => 'FR_GRANDE_MONDE',
	'FR_DOUBLE_SOLEIL (800x1200 mm ; 31.50x47.24 in)' => 'FR_DOUBLE_SOLEIL',
	'FR_DOUBLE_JESUS (760x1120 mm ; 29.92x44.09 in)' => 'FR_DOUBLE_JESUS',
	'FR_GRAND_AIGLE (750x1060 mm ; 29.53x41.73 in)' => 'FR_GRAND_AIGLE',
	'FR_PETIT_AIGLE (700x940 mm ; 27.56x37.01 in)' => 'FR_PETIT_AIGLE',
	'FR_DOUBLE_RAISIN (650x1000 mm ; 25.59x39.37 in)' => 'FR_DOUBLE_RAISIN',
	'FR_JOURNAL (650x940 mm ; 25.59x37.01 in)' => 'FR_JOURNAL',
	'FR_COLOMBIER_AFFICHE (630x900 mm ; 24.80x35.43 in)' => 'FR_COLOMBIER_AFFICHE',
	'FR_DOUBLE_CAVALIER (620x920 mm ; 24.41x36.22 in)' => 'FR_DOUBLE_CAVALIER',
	'FR_CLOCHE (600x800 mm ; 23.62x31.50 in)' => 'FR_CLOCHE',
	'FR_SOLEIL (600x800 mm ; 23.62x31.50 in)' => 'FR_SOLEIL',
	'FR_DOUBLE_CARRE (560x900 mm ; 22.05x35.43 in)' => 'FR_DOUBLE_CARRE',
	'FR_DOUBLE_COQUILLE (560x880 mm ; 22.05x34.65 in)' => 'FR_DOUBLE_COQUILLE',
	'FR_JESUS (560x760 mm ; 22.05x29.92 in)' => 'FR_JESUS',
	'FR_RAISIN (500x650 mm ; 19.69x25.59 in)' => 'FR_RAISIN',
	'FR_CAVALIER (460x620 mm ; 18.11x24.41 in)' => 'FR_CAVALIER',
	'FR_DOUBLE_COURONNE (460x720 mm ; 18.11x28.35 in)' => 'FR_DOUBLE_COURONNE',
	'FR_CARRE (450x560 mm ; 17.72x22.05 in)' => 'FR_CARRE',
	'FR_COQUILLE (440x560 mm ; 17.32x22.05 in)' => 'FR_COQUILLE',
	'FR_DOUBLE_TELLIERE (440x680 mm ; 17.32x26.77 in)' => 'FR_DOUBLE_TELLIERE',
	'FR_DOUBLE_CLOCHE (400x600 mm ; 15.75x23.62 in)' => 'FR_DOUBLE_CLOCHE',
	'FR_DOUBLE_POT (400x620 mm ; 15.75x24.41 in)' => 'FR_DOUBLE_POT',
	'FR_ECU (400x520 mm ; 15.75x20.47 in)' => 'FR_ECU',
	'FR_COURONNE (360x460 mm ; 14.17x18.11 in)' => 'FR_COURONNE',
	'FR_TELLIERE (340x440 mm ; 13.39x17.32 in)' => 'FR_TELLIERE',
	'FR_POT (310x400 mm ; 12.20x15.75 in)' => 'FR_POT',
);

// check here current active tab and display corresponding settings fields.
$active_mwbpdf_gen_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'display_setting';
?>
<div class="wrap" id="<?php echo esc_html(MWBPDF_GEN_PREFIX) . '_wrap'; ?>">


	<?php if ('display_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Display Settings', 'wp-ultimate-pdf'); ?></h2>
	<?php }
	if ('general_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('General Settings', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('body_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Body', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('header_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Header', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('footer_settings' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Footer', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('advance_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Advance Settings', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>

	<?php if ('watermark_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Watermark', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('bulletstyle_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Custom Bullet Style', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('post_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Post Meta Details', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('embed_source_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('Post Embed Source', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<?php if ('first_page_setting' == $active_mwbpdf_gen_tab) { ?>
		<h2><?php echo esc_html__('first page setting', 'wp-ultimate-pdf'); ?></h2>
	<?php } ?>
	<div class="mwb_pdf_main_wrapper">
		<nav class="mwbpdf_gen_nav wid-095 nav-tab-wrapper woo-nav-tab-wrapper">

			<a href="?page=wp-ultimate-pdf-generator&tab=display_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'display_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Display Settings', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=general_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'general_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('General Settings', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=body_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'body_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Body', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=header_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'header_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Header', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=footer_settings" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'footer_settings' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Footer', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=watermark_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'watermark_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Watermark', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=advance_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'advance_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Advance Settings', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=bulletstyle_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'bulletstyle_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Custom Bullet Style', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=post_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'post_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Post Meta Details', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=embed_source_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'embed_source_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('Post Embed Source', 'wp-ultimate-pdf'); ?></a>
			<a href="?page=wp-ultimate-pdf-generator&tab=first_page_setting" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'first_page_setting' == $active_mwbpdf_gen_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__('First page settings', 'wp-ultimate-pdf'); ?></a>
		</nav>
		<?php
		// check current active tab and call corresponding function to render html.
		if ('display_setting' == $active_mwbpdf_gen_tab) {
			display_settings();
		} elseif ('general_setting' == $active_mwbpdf_gen_tab) {
			general_settings();
		} elseif ('body_setting' == $active_mwbpdf_gen_tab) {
			body_settings();
		} elseif ('header_setting' == $active_mwbpdf_gen_tab) {
			header_settings();
		} elseif ('footer_settings' == $active_mwbpdf_gen_tab) {
			footer_settings();
		} elseif ('advance_setting' == $active_mwbpdf_gen_tab) {
			advance_settings();
		} elseif ('watermark_setting' == $active_mwbpdf_gen_tab) {
			watermark_settings();
		} elseif ('bulletstyle_setting' == $active_mwbpdf_gen_tab) {
			bulletstyle_settings();
		} elseif ('post_setting' == $active_mwbpdf_gen_tab) {
			postmeta_settings();
		} elseif ('embed_source_setting' == $active_mwbpdf_gen_tab) {
			post_embed_settings();
		} elseif ('first_page_setting' == $active_mwbpdf_gen_tab) {
			$active_mwbpdf_gen_section = isset($_GET['section']) ? sanitize_text_field(wp_unslash($_GET['section'])) : 'Predefined Templates';
		?>
			<form form method="post" action="options.php" class="wid-092" style="float: right;" name="advance_form" id="advance_form_display">
				<div class="mwb_pdf_cover_tabs">
					<a href="?page=wp-ultimate-pdf-generator&tab=first_page_setting&section=Predefined Templates" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'Predefined Templates' == $active_mwbpdf_gen_section ? 'active' : ''; ?>"><?php echo esc_html__('Predefined Templates', 'wp-ultimate-pdf'); ?></a>
					<a href="?page=wp-ultimate-pdf-generator&tab=first_page_setting&section=custom Templates" id="mwbpdf_gen_nav-tab" class="nav-tab <?php echo 'custom Templates' == $active_mwbpdf_gen_section ? 'active' : ''; ?>"><?php echo esc_html__('Upload Custom Cover', 'wp-ultimate-pdf'); ?></a>
				</div>
			<?php
			if ('custom Templates' == $active_mwbpdf_gen_section) {
				custom_first_page_settings();
			}
			if ('Predefined Templates' == $active_mwbpdf_gen_section) {

				predefined_first_page_settings();
			}
		}

			?>
	</div>
</div>
<?php

/**
 * Display settings tab details
 *
 * @name display_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function display_settings()
{
	// function to get current pdf settings details.
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	// print the notification for success message on settings saved.
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}
	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
?>
	<form method="post" action="options.php" class="wid-092" style="float: right;" name="advance_form" id="advance_form_display">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . '_display_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . '_display_option');
		?>
		<table class="sect-table">
			<tr>
				<td><?php esc_html_e('Display Option', 'wp-ultimate-pdf'); ?></td>
				<td>
					<p class="front">
						<input class='clickfront' name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[front_end]" value="1" <?php echo (isset($mwbpdf_genoptions['front_end'])) ? 'checked="checked"' : ''; ?> type="checkbox" onclick="showHideCheck('frontsetting', this);" />
						<span><?php esc_html_e('Front End', 'wp-ultimate-pdf'); ?></span> &nbsp; &nbsp;
					</p>
					<p class='showall display-none'>
						<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[show]"
							<?php
							if (isset($mwbpdf_genoptions['show']) && $mwbpdf_genoptions['show'] == 'guestuser') :
								echo "checked='checked'";
							endif;
							?>
							value='guestuser' type="radio" />
						<span><?php esc_html_e('Show for all guest user', 'wp-ultimate-pdf'); ?></span>&nbsp;&nbsp;
					</p>
					<p class='showloggedin display-none'>
						<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[show]"
							<?php
							if (isset($mwbpdf_genoptions['show']) && $mwbpdf_genoptions['show'] == 'loggedinuser') :
								echo "checked='checked'";
							endif;
							?>
							value='loggedinuser' type="radio" />
						<span><?php esc_html_e('Show for logged in user', 'wp-ultimate-pdf'); ?></span>&nbsp;&nbsp;
					</p>
					<p class='showemail display-none'>
						<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[showemailfield]"
							<?php
							if (isset($mwbpdf_genoptions['showemailfield']) && $mwbpdf_genoptions['showemailfield'] == 'shownonloggedinemail') :
								echo "checked='checked'";
							endif;
							?>
							value='shownonloggedinemail' type="checkbox" />
						<span><?php esc_html_e('Email option for guest in user', 'wp-ultimate-pdf'); ?></span>&nbsp;&nbsp;
					</p>
					<p class="display_admin">
						<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[admin_panel]" value="1" <?php echo (isset($mwbpdf_genoptions['admin_panel'])) ? 'checked="checked"' : ''; ?>type="checkbox" />
						<span><?php esc_html_e('Admin Panel', 'wp-ultimate-pdf'); ?></span>&nbsp;&nbsp;
					</p>
					<p class="display_shortcode">
						<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[short]" value="0" <?php echo (isset($mwbpdf_genoptions['short'])) ? 'checked="checked"' : ''; ?>type="checkbox" />
						<span><?php esc_html_e('Enable Shortcode', 'wp-ultimate-pdf'); ?></span>&nbsp;&nbsp;
						<b class='codeshort'> [WPS_PDF_GEN] </b>
						<span class='enable'> <?php esc_html_e('If you enable shortcode then you will get authority to apply shortcode anywhere in post content while adding or editing post.', 'wp-ultimate-pdf'); ?></span>
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<p class='btnfl'><input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
						<select name="settings" id="sett" class="btn-f3_9pdf setting-name__select">


							<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
							<?php
							$arr = get_option('ptp_settings');
							if (isset($arr) && ! empty($arr)) {
								foreach ($arr as $key => $value) {
									$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
							?>
									<option value="<?php echo esc_html($key); ?>"
										<?php
										if ($key == $currentsetting) :
											echo "Selected='selected'";
										endif;
										?>><?php echo esc_html($key); ?></option>
							<?php
								}
							}
							?>
						</select>
						<input type="button" name="remove" value="<?php esc_html_e('Remove Setting', 'wp-ultimate-pdf'); ?>" class="settingbuttonremove" />
					</p>
					<div class='prset'>
						<input type='submit' name='preview' value="<?php esc_html_e('Preview Setting', 'wp-ultimate-pdf'); ?>" class='viewsetting' />
					</div>
				</td>
			</tr>
			<tr id="custom_font" class='saveset'>
				<td class="tr1"><?php esc_html_e('Save My Setting :', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2 setting_name'><input type="text" class='settingname' name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[settingname]" value="<?php echo (isset($mwbpdf_genoptions['settingname']) ? esc_html($mwbpdf_genoptions['settingname']) : ''); ?>" />&nbsp;&nbsp;<span><?php esc_html_e('Set your new setting name', 'wp-ultimate-pdf'); ?></span></td>
			</tr>
		</table>
		<input type="submit" id="mwb_submit_saved_setting_display" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>

<?php
}
?>


<?php

/**
 * General settings tab details
 *
 * @name general_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function general_settings()
{
	// function to get current pdf settings.
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}

	$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';

	?>
	<form method="post" action="options.php" style="float: right;" name="general_form" id="general_form">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . '_general_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . '_general_option');
		?>
		<table class='sect-table'>
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">
						<!-- <option><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option> -->

						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Featured Image', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[featured]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['featured']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Include Featured Image.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Include Cache', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[includefromCache]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['includefromCache']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('By default PDF will be generated on fly. Select to generate PDF from cache.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Schedule Cache Updation', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[cache_updation_sch]">
						<?php
						$cache_updation_sch_value = isset($mwbpdf_genoptions['0']['cache_updation_sch']) ? $mwbpdf_genoptions['0']['cache_updation_sch'] : 'none';

						mwbpdf_gen_profile_option($cache_updation_sch_value, 'none', __('None', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($cache_updation_sch_value, '86400', __('Daily', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($cache_updation_sch_value, '604800', __('Weekly', 'wp-ultimate-pdf'));
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Default File Name', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<?php
					$mwb_file_name_value = isset($mwbpdf_genoptions['0']['mwb_file_name']) ? $mwbpdf_genoptions['0']['mwb_file_name'] : 'postID';

					echo '<select name="' . esc_html(MWBPDF_GEN_PREFIX) . '[mwb_file_name]" id="display_file_name">';
					mwbpdf_gen_profile_option($mwb_file_name_value, 'postID', __('Post ID', 'wp-ultimate-pdf'));
					mwbpdf_gen_profile_option($mwb_file_name_value, 'post_name', __('Post Name', 'wp-ultimate-pdf'));
					mwbpdf_gen_profile_option($mwb_file_name_value, 'custom_name', __('CustomName_ID', 'wp-ultimate-pdf'));
					?>
				</td>
			</tr>
			<tr id="display_file_name_input" style="display:none;">
				<td class="tr1"><?php esc_html_e('Custom file name', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="3"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[mwb_file_name_custom]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[mwb_file_name_custom]" value="<?php echo (isset($mwbpdf_genoptions['0']['mwb_file_name_custom']) ? esc_html($mwbpdf_genoptions['0']['mwb_file_name_custom']) : ''); ?>" />
					<span class="descr">
						<?php esc_html_e('set the custom name of the downloaded pdf .', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<?php ////////////////////////////////////////////////////////////color picker ///////////////////
			?>\
			<tr>
				<td class='tr1'><?php esc_html_e('Check to fill color ', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[page_color]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['page_color'])) ? 'checked="checked"' : ''; ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('check this if you want to set the pdf page color .', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Set pdf page color', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="3"><input type="color" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[mwb_pdf_page_color]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[mwb_pdf_page_color]" value="<?php echo (isset($mwbpdf_genoptions['0']['mwb_pdf_page_color']) ? esc_html($mwbpdf_genoptions['0']['mwb_pdf_page_color']) : ''); ?>" />
					<span class="descr">
						<?php esc_html_e('set the page color of the downloaded pdf .', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<?php ////////////////////////////////////////////////////////////end picker //////////////////////
			?>
			<?php

			$author = array(
				__('None', 'wp-ultimate-pdf') => '',
				__('First Name', 'wp-ultimate-pdf') => 'first_name',
				__('Last Name', 'wp-ultimate-pdf') => 'last_name',
				__('Nickname', 'wp-ultimate-pdf') => 'nickname',
			);
			?>
			<tr>
				<td class='tr1'><?php esc_html_e('Display Author Detail', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<?php
					echo '<select name="' . esc_html(MWBPDF_GEN_PREFIX) . '[author_name]">';
					$author_name_value = isset($mwbpdf_genoptions['0']['author_name']) ? $mwbpdf_genoptions['0']['author_name'] : '';

					foreach ($author as $key => $value) {
						if ($author_name_value == '') {
							'selected="None"';
							$checked = ($author_name_value == $value) ? 'selected="selected"' : '';
							echo '<option value="' . esc_html($value) . '" ' . esc_html($checked) . ' >' . esc_html($key) . '</option>';
						} else {
							if ($author_name_value) {
								$checked = ($author_name_value == $value) ? 'selected="selected"' : '';
							}
							echo '<option value="' . esc_html($value) . '" ' . esc_html($checked) . ' >' . esc_html($key) . '</option>';
						}
					}
					echo '</select>';
					?>
					<span class="descr"><?php esc_html_e('Check this if you want to display the author name', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class='tr1'><?php esc_html_e('Display Post Category List', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[post_categ]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['post_categ'])) ? 'checked="checked"' : ''; ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Check this if you want to add post category in pdf.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class='tr1'><?php esc_html_e('Display Post Tag List', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[post_tags]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['post_tags'])) ? 'checked="checked"' : ''; ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Check this if you want to add post tag in pdf.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class='tr1'><?php esc_html_e('Display Post Date', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[post_date]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['post_date'])) ? 'checked="checked"' : ''; ?>type="checkbox" />
					<span class="descr"><?php esc_html_e('Check this if you want to add postdate in pdf.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class='tr1'><?php esc_html_e('Display Print Date', 'wp-ultimate-pdf'); ?></td>
				<td class='tr2' colspan="2"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[cuurent_date]" value="1" <?php echo (isset($mwbpdf_genoptions['0']['cuurent_date'])) ? 'checked="checked"' : ''; ?>type="checkbox" />
					<span class="descr"><?php esc_html_e('Check this if you want to add current date in pdf.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>

				<td class="tr1"><?php esc_html_e('Select Date Format', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>_date_formatt" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[date_formatt]">
						<?php
						$date_format_value = isset($mwbpdf_genoptions['0']['date_formatt']) ? $mwbpdf_genoptions['0']['date_formatt'] : 'Y-m-d';
						mwbpdf_gen_profile_option($date_format_value, 'Y/m/d', __('yyyy/mm/dd', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($date_format_value, 'm/d/Y', __('mm/dd/yyyy', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($date_format_value, 'd M Y', __('d MM yyyy', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($date_format_value, 'l, d M Y', __('DD, d MM yyyy', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($date_format_value, 'Y-m-d', __('yyyy-mm-dd', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($date_format_value, 'd/m/Y', __('dd/mm/yyyy', 'wp-ultimate-pdf'));
						?>
					</select>
				</td>
			</tr>

			<tr>

				<td class="tr1"><?php esc_html_e('Button Alignment', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>_content_position" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[content_position]">
						<?php
						$content_position_value = isset($mwbpdf_genoptions['0']['content_position']) ? $mwbpdf_genoptions['0']['content_position'] : 'left';
						mwbpdf_gen_profile_option($content_position_value, 'left', __('Left Align', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($content_position_value, 'right', __('Right Align', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($content_position_value, 'center', __('Center', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($content_position_value, 'none', __('None', 'wp-ultimate-pdf'));
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Button Placement', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select id="pf_content_placement" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[content_placement]">
						<?php
						$content_placement_value = isset($mwbpdf_genoptions['0']['content_placement']) ? $mwbpdf_genoptions['0']['content_placement'] : 'before';
						mwbpdf_gen_profile_option($content_placement_value, 'before', __('Above Content', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($content_placement_value, 'after', __('Below Content', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($content_placement_value, 'beforeandafter', __('Below and Above Content', 'wp-ultimate-pdf'));
						?>
					</select>
					<span class="descr"><?php esc_html_e('Place your pdf icon', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Select Button', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[link_button]" id="link_button">
						<?php
						$link_button_value = isset($mwbpdf_genoptions['0']['link_button']) ? $mwbpdf_genoptions['0']['link_button'] : 'default';
						mwbpdf_gen_profile_option($link_button_value, 'default', __('Default', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($link_button_value, 'custom', __('Custom', 'wp-ultimate-pdf'));
						?>

					</select>
					<br>
					<button type="button" class="upload_link_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
				</td>
				<td id="custom_link">
					<div id="customlink">
						<img id="imglink" src="<?php echo (isset($mwbpdf_genoptions['0']['custon_link_url'])) ? esc_html($mwbpdf_genoptions['0']['custon_link_url']) : ''; ?>" width="80px" height="80px" />
					</div>
					<div id="remove_link_button">
						<input type="hidden" id="custon_link_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custon_link_url]" value="<?php echo (isset($mwbpdf_genoptions['0']['custon_link_url'])) ? esc_html($mwbpdf_genoptions['0']['custon_link_url']) : ''; ?>" />
						<button type="button" class="remove_link_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
				<p>
					<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="0">
				</p>
			</tr>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>

<?php
}
?>
<?php

/**
 * Body settings tab details
 *
 * @name body_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function body_settings()
{
	global $fonts;
	global $page_size;

	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}

	?>
	<form class="pdf_body-css_09" method="post" action="options.php" style="float: right;" name="body_form">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . '_body_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . '_body_option');
		?>
		<table class="sect-table">
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>

					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Post Template', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<?php
					if (! isset($mwbpdf_genoptions['1']['post_tmplt'])) {
						$mwbpdf_genoptions['1']['post_tmplt'] = 'tmplt1';
					}
					?>
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[post_tmplt]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[post_tmplt]" class="mwb_post_select_tmplt">
						<option value="tmplt1" <?php selected($mwbpdf_genoptions['1']['post_tmplt'], 'tmplt1'); ?>><?php esc_html_e('Template 1', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt2" <?php selected($mwbpdf_genoptions['1']['post_tmplt'], 'tmplt2'); ?>><?php esc_html_e('Template 2', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt3" <?php selected($mwbpdf_genoptions['1']['post_tmplt'], 'tmplt3'); ?>><?php esc_html_e('Template 3', 'wp-ultimate-pdf'); ?></option>
					</select>
					<input type="submit" name="preview_post_template" value="<?php esc_html_e('Preview Template', 'wp-ultimate-pdf'); ?>" class="previewposttemplate button" />
					<span class="descr">
						<?php esc_html_e('Please choose the post template', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Product Template', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<?php
					if (! isset($mwbpdf_genoptions['1']['product_tmplt'])) {
						$mwbpdf_genoptions['1']['product_tmplt'] = 'tmplt1';
					}
					?>
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[product_tmplt]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[product_tmplt]" class="mwb_product_select_tmplt">
						<option value="tmplt1" <?php selected($mwbpdf_genoptions['1']['product_tmplt'], 'tmplt1'); ?>><?php esc_html_e('Template 1', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt2" <?php selected($mwbpdf_genoptions['1']['product_tmplt'], 'tmplt2'); ?>><?php esc_html_e('Template 2', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt3" <?php selected($mwbpdf_genoptions['1']['product_tmplt'], 'tmplt3'); ?>><?php esc_html_e('Template 3', 'wp-ultimate-pdf'); ?></option>
					</select>
					<input type="button" name="previewtemplate" value="<?php esc_html_e('Preview Template', 'wp-ultimate-pdf'); ?>" class="previewproducttemplate button" />
					<span class="descr">
						<?php esc_html_e('Please choose the product template', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Number of Product PostMeta', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="number" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[pro_pmeta]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[pro_pmeta]" value="<?php echo (isset($mwbpdf_genoptions['1']['pro_pmeta']) ? esc_html($mwbpdf_genoptions['1']['pro_pmeta']) : '4'); ?>" />
					<span class="descr">
						<?php esc_html_e('Please enter number of postmeta fields to be displayed along with short description.', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Image Scaling Ratio', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[image_scale]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[image_scale]" value="<?php echo (isset($mwbpdf_genoptions['1']['image_scale']) ? esc_html($mwbpdf_genoptions['1']['image_scale']) : '1.3'); ?>" />
					<span class="descr">
						<?php esc_html_e('Please enter image ratio here(default is 1.3)', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Top Margin', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_top]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_top]" value="<?php echo (isset($mwbpdf_genoptions['1']['margin_top']) ? esc_html($mwbpdf_genoptions['1']['margin_top']) : '25'); ?>" />
					<span class="descr"><?php esc_html_e('Please enter top margin here(default is 25', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Left Margin', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_left]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_left]" value="<?php echo (isset($mwbpdf_genoptions['1']['margin_left']) ? esc_html($mwbpdf_genoptions['1']['margin_left']) : '20'); ?>" />
					<span class="descr"><?php esc_html_e('Please enter left margin here (default is 20', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Right Margin', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_right]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[margin_right]" value="<?php echo (isset($mwbpdf_genoptions['1']['margin_right']) ? esc_html($mwbpdf_genoptions['1']['margin_right']) : '20'); ?>" />
					<span class="descr"><?php esc_html_e('Please enter your right margin here(default is 20', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Page Size', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select class="pdf-size_page09" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[page_size]">
						<?php
						$page_size_value = isset($mwbpdf_genoptions['1']['page_size']) ? $mwbpdf_genoptions['1']['page_size'] : 'LETTER';

						foreach ($page_size as $key => $value) {
							if ($page_size_value) {
								mwbpdf_gen_profile_option($page_size_value, $value, __($key, 'wp-ultimate-pdf'));
							}
						}
						?>
					</select>
					<span class="descr"><?php esc_html_e('Size of your Page (default is A4).', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Orientation', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[page_orientation]" value="P"
						<?php
						if (isset($mwbpdf_genoptions['1']['page_orientation']) && 'P' == ($mwbpdf_genoptions['1']['page_orientation'])) {
							echo 'checked="checked"';
						}
						?>
						type="radio" /><?php esc_html_e('Portrait', 'wp-ultimate-pdf'); ?>&nbsp;&nbsp;&nbsp;
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[page_orientation]" value="L"
						<?php
						if (isset($mwbpdf_genoptions['1']['page_orientation']) && 'L' == ($mwbpdf_genoptions['1']['page_orientation'])) {
							echo 'checked="checked"';
						}
						?>
						type="radio" /> <?php esc_html_e('Landscape', 'wp-ultimate-pdf'); ?> <br />
					<span class="descr"><?php esc_html_e('Select the desired page orientation (default is Portrait).', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Unit of Measurement', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<?php
					$unitmeasure_value = isset($mwbpdf_genoptions['1']['unitmeasure']) ? $mwbpdf_genoptions['1']['unitmeasure'] : 'mm';

					$unit = array(
						__('Point', 'wp-ultimate-pdf') => 'pt',
						__('Millimeter', 'wp-ultimate-pdf') => 'mm',
						__('Centimeter', 'wp-ultimate-pdf') => 'cm',
						__('Inch', 'wp-ultimate-pdf') => 'in',
					);
					echo '<select name="' . esc_html(MWBPDF_GEN_PREFIX) . '[unitmeasure]">';
					foreach ($unit as $key => $value) {

						if ($unitmeasure_value == '') {
							'selected="Millimeter"';
							$checked = ($unitmeasure_value == $value) ? 'selected="selected"' : '';
							echo '<option value="' . esc_html($value) . '" ' . esc_html($checked) . ' >' . esc_html($key) . '</option>';
						} else {
							if ($unitmeasure_value) {
								$checked = ($unitmeasure_value == $value) ? 'selected="selected"' : '';
							}
							echo '<option value="' . esc_html($value) . '" ' . esc_html($checked) . ' >' . esc_html($key) . '</option>';
						}
					}
					echo '</select>';
					?>
					<span class="descr"><?php esc_html_e('Select the desired unit of measurement (default is mm).', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>

				<td class="tr1"><?php esc_html_e('Content Font', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[content_font_pdf]" id="content_font_pdf">
						<?php
						$content_font_pdf_value = isset($mwbpdf_genoptions['1']['content_font_pdf']) ? $mwbpdf_genoptions['1']['content_font_pdf'] : 'helvetica';

						foreach ($fonts as $key => $value) {
							mwbpdf_gen_profile_option($content_font_pdf_value, $value, __($key, 'wp-ultimate-pdf'));
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Content Font Size', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[content_font_size]"
						value="<?php echo (isset($mwbpdf_genoptions['1']['content_font_size']) ? esc_html($mwbpdf_genoptions['1']['content_font_size']) : '12'); ?>" />

				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom Title', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_title]" value="<?php echo (isset($mwbpdf_genoptions['1']['custom_title']) ? esc_html($mwbpdf_genoptions['1']['custom_title']) : ''); ?>" />
					<span class="descr"><?php esc_html_e('Enter custom title to replace with post title.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Show pdf icon according to user roles', 'wp-ultimate-pdf'); ?></td>
				<td class="tr1"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[check_user_roles]" value="1" <?php echo (isset($mwbpdf_genoptions['1']['check_user_roles'])) ? 'checked="checked"' : ''; ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Check this if you want to show download pdf icon according to the user roles', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Select user role for which you want to show the pdf icon', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<?php
					global $wp_roles;
					$all_roles = $wp_roles->roles;
					$roles_array = array();
					$selected_val_arr = get_option(MWBPDF_GEN_PREFIX);;
					?>
					<select style="width: 200px;" class="js-example-basic-multiple" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[user_roles][]" multiple="multiple">
						<?php

						foreach ($all_roles as $role => $value) {

						?>
							<option value="<?php echo $role; ?>"
								<?php
								if (isset($selected_val_arr[1]['user_roles']) && in_array($role, $selected_val_arr[1]['user_roles'])) {
									echo 'selected';
								}
								?>><?php echo $role; ?></option>
						<?php

						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('RTL Support', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[rtl_support]" value="1"
						<?php echo (isset($mwbpdf_genoptions['1']['rtl_support']) ? 'checked="checked"' : ''); ?>
						type="checkbox" />
					<span class="descr"><?php esc_html_e('Select if you want to generate pdf for Post in Persian and Arabic language', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom CSS', 'wp-ultimate-pdf'); ?></td>
				<td class="tr1"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[CustomCSS_option]" value="1" <?php echo (isset($mwbpdf_genoptions['1']['CustomCSS_option'])) ? 'checked="checked"' : ''; ?> type="checkbox" onclick="showHideCheck('docCustomCSS', this);" />
					<span class="descr"><?php esc_html_e('Check this if you want to apply your CSS.', 'wp-ultimate-pdf'); ?></span>
					<table id="docCustomCSS" class="<?php echo isset($mwbpdf_genoptions['1']['CustomCSS_option']) ? '' : 'noDis'; ?>">
						<tr>

							<td class="tr2" colspan="2">
								<textarea rows="6" cols="24" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[Customcss]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['1']['Customcss'])) ? esc_html($mwbpdf_genoptions['1']['Customcss']) : ''; ?></textarea>
							</td>
						</tr>
						<p>
							<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="1">
						</p>
					</table>
				</td>

			</tr>

		</table>

		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
<?php
}
?>

<?php
/**
 * Header settings tab details
 *
 * @name header_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function header_settings()
{
	global $fonts;
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>

		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>

				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}

	?>

	<form method="post" action="options.php" style="float: right;" name="header_form" id="header_form">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'header_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'header_option');
		?>

		<table class="sect-table">
			<tr>
				<td>
					<?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?>
				</td>
				<td colspan="2">
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>

			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Page Header', 'wp-ultimate-pdf'); ?></td>
				<td colspan="1" class="tr2"><select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[page_header]" id="page_header">
						<?php

						if (isset($mwbpdf_genoptions['page_header'])) {
							$page_header_value = $mwbpdf_genoptions['page_header'];
						} else {
							$page_header_value = 'upload-image';
						}

						mwbpdf_gen_profile_option($page_header_value, 'upload-image', __('Upload an Image', 'wp-ultimate-pdf'));
						?>
					</select><br>
					<button type="button" class="upload_imglogo_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>

				</td>
				<td id="custom_logo">
					<div id="customlogo" style="float: left; margin-left: 12px;">
						<img id="imglogo" src="<?php echo (! empty($mwbpdf_genoptions['2']['logo_img_url'])) ? esc_html($mwbpdf_genoptions['2']['logo_img_url']) : ''; ?>" width="80px" height="80px" />
					</div>
					<div style="float: right;">
						<input type="hidden" id="logo_img_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[logo_img_url]" value="<?php echo (isset($mwbpdf_genoptions['2']['logo_img_url'])) ? esc_html($mwbpdf_genoptions['2']['logo_img_url']) : ''; ?>" />
						<button type="button" class="remove_logo_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Hide QR code ', 'wp-ultimate-pdf'); ?>
				</td>
				<td colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[qr_code_header]" value="1" <?php echo (isset($mwbpdf_genoptions['2']['qr_code_header']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Hide Qr code in header.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Hide site Name', 'wp-ultimate-pdf'); ?>
				</td>
				<td colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[site_name_header]" value="1" <?php echo (isset($mwbpdf_genoptions['2']['site_name_header']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Hide site name in header.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Hide site description', 'wp-ultimate-pdf'); ?>
				</td>
				<td colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[site_description_header]" value="1" <?php echo (isset($mwbpdf_genoptions['2']['site_description_header']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Hide site description in header.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Hide site URL', 'wp-ultimate-pdf'); ?>
				</td>
				<td colspan="2">
					<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[site_url_header]" value="1" <?php echo (isset($mwbpdf_genoptions['2']['site_url_header']) ? 'checked="checked"' : ''); ?> type="checkbox" />
					<span class="descr"><?php esc_html_e('Hide site url in header.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Logo Image Factor', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[image_factor]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[image_factor]" value="<?php echo (isset($mwbpdf_genoptions['2']['image_factor']) ? esc_html($mwbpdf_genoptions['2']['image_factor']) : '10'); ?>" />
					<span class="descr">
						<?php esc_html_e('This will be applied to logo width/height to provide logo image some surrounding space.', 'wp-ultimate-pdf'); ?>
					</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Logo Top Margin', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[logomTop]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[logomTop]" value="<?php echo (isset($mwbpdf_genoptions['2']['logomTop']) ? esc_html($mwbpdf_genoptions['2']['logomTop']) : '2'); ?>" />
					<span class="descr"><?php esc_html_e('Enter your desired top margin (default is 2', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>

			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Header Font', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[header_font_pdf]" id="header_font_pdf">
						<?php
						if (! empty($fonts)) {
							$header_font_pdf_value = isset($mwbpdf_genoptions['2']['header_font_pdf']) ? $mwbpdf_genoptions['2']['header_font_pdf'] : 'helvetica';

							foreach ($fonts as $key => $value) {
								mwbpdf_gen_profile_option($header_font_pdf_value, $value, __($key, 'wp-ultimate-pdf'));
							}
						}
						?>
					</select>
				</td>

			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Header Font Size', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[header_font_size]" value="<?php echo (isset($mwbpdf_genoptions['2']['header_font_size']) ? esc_html($mwbpdf_genoptions['2']['header_font_size']) : '10'); ?>" />
				</td>

			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="2">
			</p>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
<?php
}
?>
<?php
/**
 * Footer settings tab details
 *
 * @name footer_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function footer_settings()
{
	global $fonts;
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}
	?>
	<form method="post" action="options.php" style="float: right;" name="footer_form" id="footer_form">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'footer_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'footer_option');
		?>

		<table class="sect-table">
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">

						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
				<td>

				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom Footer Text', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_option]" value='1'
						<?php echo (isset($mwbpdf_genoptions['7']['custom_footer_option'])) ? 'checked = "checked"' : ' '; ?>
						onclick="showHideCheck('custom_footer_text', this);" /></td>
			</tr>
			<tr id="custom_footer_text"
				class="<?php echo isset($mwbpdf_genoptions['custom_footer_option']) ? '' : 'noDis'; ?>">

				<td class="tr1"></td>
				<td class="tr2"><textarea id='custom_footer'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['7']['custom_footer']) ? esc_html($mwbpdf_genoptions['7']['custom_footer']) : ' '); ?></textarea></td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Font', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_font_pdf]" id="footer_font_pdf">
						<?php
						$footer_font_value = isset($mwbpdf_genoptions['7']['footer_font_pdf']) ? $mwbpdf_genoptions['7']['footer_font_pdf'] : 'Courier';

						foreach ($fonts as $key => $value) {
							mwbpdf_gen_profile_option($footer_font_value, $value, __($key, 'wp-ultimate-pdf'));
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Font Size', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_font_size]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_font_size']) ? esc_html($mwbpdf_genoptions['7']['footer_font_size']) : '10'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired footer font size(default is 10', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Cell Width', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="text"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_cell_width]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_cell_width']) ? esc_html($mwbpdf_genoptions['7']['footer_cell_width']) : '0'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired Footer Cell Width(default is 0', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Cell Minimum Height', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input type="text"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_min_height]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_min_height']) ? esc_html($mwbpdf_genoptions['7']['footer_min_height']) : '0'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired Footer Cell Minimum Height(default is 0', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Cell Upper Left Corner (X)', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_lcornerX]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_lcornerX']) ? esc_html($mwbpdf_genoptions['7']['footer_lcornerX']) : '15'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired Footer Cell Upper Left Corner (X)(default is 15', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Cell Upper Left Corner (Y)', 'wp-ultimate-pdf'); ?></td>

				<td class="tr2"><input type="text"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_font_lcornerY]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_font_lcornerY']) ? esc_html($mwbpdf_genoptions['7']['footer_font_lcornerY']) : '290'); ?>" />
					<span class="descr"><?php esc_html_e('Select your desired Footer Cell Upper Left Corner (Y)(default is 290', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Margin', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="text"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_font_margin]"
						value="<?php echo (isset($mwbpdf_genoptions['7']['footer_font_margin']) ? esc_html($mwbpdf_genoptions['7']['footer_font_margin']) : '10'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired footer font margin(default is 10', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>


			<tr>
				<?php
				$footeralign = array(
					'Auto' => '',
					'Left' => 'L',
					'Right' => 'R',
					'Center' => 'C',
				);
				?>
				<td class="tr1"><?php esc_html_e('Footer Cell Text Alignment', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_align]">
						<?php
						foreach ($footeralign as $key => $value) {
							mwbpdf_gen_profile_option($mwbpdf_genoptions['7']['footer_align'], $value, __($key, 'wp-ultimate-pdf'));
						}
						?>
					</select>
					<span class="descr"><?php esc_html_e('Select your desired text alignment for the footer cell..', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Footer Cell Auto-padding', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><input name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[footer_cell_auto_padding]"
						value="1"
						<?php echo (isset($mwbpdf_genoptions['7']['footer_cell_auto_padding']) ? 'checked="checked"' : ''); ?>
						type="checkbox" /></td>
			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="7">
			</p>

		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />


	</form>





	<?php

}
/**
 * Watermark settings tab details
 *
 * @name watermark_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function watermark_settings()
{
	global $fonts;
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
	?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}
	?>

	<form method="post" action="options.php" style="float: right;" name="watermark_form" id="watermark_form">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'watermark_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'watermark_option');
		?>
		<p class="descr1"><?php esc_html_e('This option gives you the possibility to add watermark text to PDF pages.', 'wp-ultimate-pdf'); ?></p>
		<table class="sect-table">
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Watermark Text', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input id="watermark_text_pdf" type="checkbox" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[add_watermark]" value='1' <?php echo (isset($mwbpdf_genoptions['3']['add_watermark'])) ? 'checked = "checked"' : ''; ?> />
				</td>
			</tr>
		</table>
		<table id="doc_add_watermark" class="sect-table">
			<tr>
				<td class="tr1"><?php esc_html_e('Watermark Font', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_font]" id="water_font">
						<?php
						$watermark_font_value = isset($mwbpdf_genoptions['3']['water_font']) ? $mwbpdf_genoptions['3']['water_font'] : 'Courier';

						foreach ($fonts as $key => $value) {
							mwbpdf_gen_profile_option($watermark_font_value, $value, __($key, 'wp-ultimate-pdf'));
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Rotation', 'wp-ultimate-pdf'); ?></td>
				<td>
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[rotate_water]" value="<?php echo (isset($mwbpdf_genoptions['3']['rotate_water']) ? esc_html($mwbpdf_genoptions['3']['rotate_water']) : '45'); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Watermark Text:', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<textarea id='docEntryTpl' name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[watermark_text]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['3']['watermark_text'])) ? esc_html($mwbpdf_genoptions['3']['watermark_text']) : ''; ?></textarea>
				</td>
			</tr>
		</table>
		<!-- Image watermark -->
		<table class="sect-table">
			<tr>
				<td class="tr1"><?php esc_html_e('Image', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input id="watermark_image_pdf" type="checkbox" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[add_watermark_image]" value='1' <?php echo (isset($mwbpdf_genoptions['3']['add_watermark_image'])) ? 'checked = "checked"' : ' '; ?> onclick="showHideCheck('doc_add_watermark_image', this);" />
				</td>
			</tr>
		</table>
		<table id="doc_add_watermark_image" class="<?php isset($mwbpdf_genoptions['3']['add_watermark_image']) ? 'sect-table' : 'noDis'; ?> sect-table">
			<tr>
				<td class="tr1"><?php esc_html_e('Image Height', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_img_h]" value="<?php echo (isset($mwbpdf_genoptions['3']['water_img_h']) ? esc_html($mwbpdf_genoptions['3']['water_img_h']) : 100); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Image Width', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_img_w]" value="<?php echo (isset($mwbpdf_genoptions['3']['water_img_w']) ? esc_html($mwbpdf_genoptions['3']['water_img_w']) : 100); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Image X Coordinate', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_img_x_axis]" value="<?php echo (isset($mwbpdf_genoptions['3']['water_img_x_axis']) ? esc_html($mwbpdf_genoptions['3']['water_img_x_axis']) : 70); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Image Y Coordinate', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_img_y_axis]" value="<?php echo (isset($mwbpdf_genoptions['3']['water_img_y_axis']) ? esc_html($mwbpdf_genoptions['3']['water_img_y_axis']) : 120); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Image Transparency', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[water_img_t]" value="<?php echo (isset($mwbpdf_genoptions['3']['water_img_t']) ? esc_html($mwbpdf_genoptions['3']['water_img_t']) : '0.1'); ?>" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Watermark Image', 'wp-ultimate-pdf'); ?></td>
				<td>
					<?php $src_waterark = isset($mwbpdf_genoptions['3']['background_img_url']) ? $mwbpdf_genoptions['3']['background_img_url'] : ''; ?>
					<div id="Watermark_Background" style="float: left; margin-right: 10px;">
						<img id="wbgimg" src="<?php echo esc_html($src_waterark); ?>" width="60px" height="60px" />
					</div>
					<div style="line-height: 60px;">
						<input type="hidden" id="background_img_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[background_img_url]" value="<?php echo esc_html($src_waterark); ?>" />
						<button type="button" class="upload_imgbg_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
						<button type="button" class="remove_img_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
					<p>
						<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="3">
					</p>
				</td>
			</tr>

		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
<?php
}
?>
<?php
/**
 * Advance settings tab details
 *
 * @name advance_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function advance_settings()
{

	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}
	?>


	<form method="post" action="options.php" style="float: right;" name="advance_form" id="advance_form_setting">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'advance_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'advance_option');
		?>
		<table class="sect-table">
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '';

						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr id="custom_font">
				<td colspan="3">
					<?php Mwbpdf_Custom_Fonts::render_manager(); ?>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Set Page Rotation', 'wp-ultimate-pdf'); ?>
				</td>
				<td>
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[set_rotation]">
						<?php
						$set_rotation_value = isset($mwbpdf_genoptions['4']['set_rotation']) ? $mwbpdf_genoptions['4']['set_rotation'] : '0';

						mwbpdf_gen_profile_option($set_rotation_value, '0', __('No Rotation', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($set_rotation_value, '90', __('90 degree', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($set_rotation_value, '180', __('180 degree', 'wp-ultimate-pdf'));
						mwbpdf_gen_profile_option($set_rotation_value, '270', __('270 degree', 'wp-ultimate-pdf'));
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Set FontStretching', 'wp-ultimate-pdf'); ?>
				</td>
				<td class="tr2" colspan="2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[fontStretching]" value="<?php echo (isset($mwbpdf_genoptions['4']['fontStretching']) ? esc_html($mwbpdf_genoptions['4']['fontStretching']) : '100'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired font Stretching(default is 100', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1">
					<?php esc_html_e('Set FontSpacing', 'wp-ultimate-pdf'); ?>
				</td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[fontSpacig]" value="<?php echo (isset($mwbpdf_genoptions['4']['fontSpacig']) ? esc_html($mwbpdf_genoptions['4']['fontSpacig']) : '0'); ?>" />
					<span class="descr"><?php esc_html_e('Select desired font Spacing(default is 0', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<?php Mwbpdf_Pdf_Security::render_settings($mwbpdf_genoptions); ?>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="4">
			</p>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
<?php
}
?>
<?php
/**
 * Bullet settings tab details
 *
 * @name bulletstyle_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function bulletstyle_settings()
{
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}
	?>


	<form method="post" action="options.php" style="float: right;" name="advance_form" id="advance_form_bullet">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'bulletstyle_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'bulletstyle_option');
		?>

		<p class="descr1"><?php esc_html_e('choose the bullet symbol for the PDF.', 'wp-ultimate-pdf'); ?></p>
		<table class="sect-table">
			<tr>
				<td><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td>
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Bullet Symbol', 'wp-ultimate-pdf'); ?></td>
				<td>
					<div id="Watermark_bullet" style="float: left; margin-right: 10px;">
						<img id="bulletimg" src="<?php echo (isset($mwbpdf_genoptions['5']['bullet_img_url'])) ? esc_html($mwbpdf_genoptions['5']['bullet_img_url']) : ''; ?>" width="60px" height="60px" />
					</div>
					<div style="line-height: 60px;">
						<input type="hidden" id="bullet_img_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[bullet_img_url]" value="<?php echo (isset($mwbpdf_genoptions['5']['bullet_img_url'])) ? esc_html($mwbpdf_genoptions['5']['bullet_img_url']) : ''; ?>" />
						<button type="button" class="upload_bullet_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
						<button type="button" class="remove_bullet_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom Bullet Image Height', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_image_height]" value="<?php echo (isset($mwbpdf_genoptions['5']['custom_image_height']) ? esc_html($mwbpdf_genoptions['5']['custom_image_height']) : '3'); ?>" />
					<span class="descr"><?php esc_html_e('Select custom Bullet Image Height (default 3 ', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom Bullet Image Width', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_image_width]" value="<?php echo (isset($mwbpdf_genoptions['5']['custom_image_width']) ? esc_html($mwbpdf_genoptions['5']['custom_image_width']) : '3'); ?>" />
					<span class="descr"><?php esc_html_e('Select custom Bullet Image Width (default 3 ', 'wp-ultimate-pdf'); ?><?php echo (isset($mwbpdf_genoptions['1']['unitmeasure']) ? esc_html($mwbpdf_genoptions['1']['unitmeasure']) : ''); ?>).</span>
				</td>
			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="5">
			</p>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
<?php
}
?>
<?php

/**
 * Postembed sources.
 *
 * @name post_embed_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function post_embed_settings()
{ ?>
	<form method="post" action="options.php" style="float: right;" name="advance_form" id="advance_form_post_meta">
		<?php $sources = array('linkedin', 'loom', 'twitch', 'ai_chatbot', 'canva', 'reddit', 'google_elements', 'calendly', 'strava', 'rss_feed', 'x', 'pdf_embed');
		?>
		<div class="wrap">
			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
				<h1 style="margin:0;"><?php echo esc_html__('Embed Sources', 'wp-ultimate-pdf'); ?></h1>
				<div id="wps-toast-msg">✅ <?php echo esc_html('Setting saved!', 'wp-ultimate-pdf'); ?></div>
			</div>
			<div class="wps-embed-grid">
				<?php
				foreach ($sources as $source) :
					$value = get_option("wps_embed_source_{$source}", 'off');
					$label = ucfirst(str_replace('_', ' ', $source));
				?>
					<div class="wps-embed-item">
						<img width="30" height="30" src=<?php echo esc_url(MWBPDF_GEN_URL . "/asset/images/{$source}.png"); ?> alt=<?php echo $source; ?> />
						<span style="display: block; margin-bottom: 10px; font-weight: bold; font-size: 14px;">
							<?php echo esc_html($label); ?>
						</span>

						<div style="display: flex; justify-content: center; align-items: center; gap: 8px;">
							<?php
							// translators: %s is a dynamic label like "generate PDF" or a feature name.
							$title_one = esc_html(sprintf(__('Enable this to %s on your posts, pages, or custom post types', 'wp-ultimate-pdf'), $label));

							// translators: %s is a label like "PDF" or "iframe".
							$title_two = esc_html(sprintf(__('Enable this to embed %s on your posts, pages, or custom post types', 'wp-ultimate-pdf'), $label));

							?>
							<?php if ('pdf_embed' === $source) { ?>
								<span
									title="<?php echo esc_html($title_one); ?>"
									style="cursor: help; color: #888; font-size: 13px;">?</span>
							<?php } else { ?>
								<span
									title="<?php echo esc_html($title_two); ?>"
									style="cursor: help; color: #888; font-size: 13px;">?</span>
							<?php } ?>


							<label class="wps-switch <?php echo $is_disabled ? 'wps-switch-disable' : ''; ?>">
								<input type="checkbox" data-source="<?php echo esc_attr($source); ?>" <?php echo 'on' === $value ? 'checked' : ''; ?>>
								<span class="slider"></span>
							</label>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p style="margin-top: 15px; font-style: italic; color: #666;">
				🔹<?php echo esc_html__('Prefer a quick guide ? ', 'wp-ultimate-pdf'); ?><a href="https://docs.wpswings.com/wp-ultimate-pdf-generator/?utm_source=wpswings-pdf-doc&utm_medium=pdf-cc-page&utm_campaign=documentation" target="_blank"><?php echo esc_html__('Check out our documentation to understand how these settings work.', 'wp-ultimate-pdf'); ?></a>
			</p>
		</div>

		<style>
			.disabled-item {
				opacity: 0.5;

			}

			.wps-switch-disable {
				pointer-events: none;
				user-select: none;
			}

			#wps-toast-msg {
				background-color: #46b450;
				color: white;
				padding: 6px 12px;
				border-radius: 4px;
				display: none;
				font-size: 13px;
				font-weight: 500;
			}

			.wps-embed-grid {
				display: grid;
				grid-template-columns: repeat(6, 1fr);
				gap: 20px;
				margin-top: 13px;
			}

			.wps-embed-item {
				position: relative;
				background: #fff;
				border: 1px solid #ddd;
				padding: 20px;
				border-radius: 8px;
				overflow: hidden;
			}

			.wps-pro-strip-embed {
				position: absolute;
				top: 8px;
				right: -40px;
				background: #0aa000;
				color: #fff;
				font-size: 12px;
				font-weight: bold;
				padding: 4px 40px;
				transform: rotate(45deg);
				box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
				z-index: 2;
			}

			.wps-embed-item span {
				display: block;
				margin-bottom: 10px;
				font-weight: bold;
				font-size: 14px;
			}

			.wps-switch {
				position: relative;
				display: inline-block;
				width: 50px;
				height: 33px;
			}

			.wps-switch input {
				opacity: 0;
				width: 0;
				height: 0;
			}

			.slider {
				position: absolute;
				cursor: pointer;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background-color: #ccc;
				transition: .4s;
				border-radius: 24px;
			}

			.slider:before {
				position: absolute;
				content: "";
				height: 18px;
				width: 18px;
				left: 4px;
				bottom: 3px;
				background-color: white;
				transition: .4s;
				border-radius: 50%;
			}

			input:checked+.slider {
				background-color: #2196F3;
			}

			input:checked+.slider:before {
				transform: translateX(26px);
			}
		</style>
		</script>
	</form>
	<?php }

/**
 * Post Meta settings tab details
 *
 * @name postmeta_settings
 * @author WP Swings <support@wpswings.com>
 * @link http://wpswings.com
 */
function postmeta_settings()
{
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}

	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
	?>
		<table class="sect-table mwb_pdf_add_settings_message">

			<tr>
				<td></td>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>

	<?php
		wp_die();
	}
	?>

	<form method="post" action="options.php" style="float: right;" name="advance_form" id="advance_form_post_meta">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'postmeta_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'postmeta_option');
		?>
		<table class="sect-table">
			<tr>
				<td class="tr1"><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {

							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="6">
			</p>
		</table>
		<p class="tr2"><b><?php esc_html_e('Allowed Post Types', 'wp-ultimate-pdf'); ?></b></p>
		<p class="pd-10"><?php esc_html_e('Select Post type for which You want to export PDF. On click, you can see what meta fields post type have just checked the post type and display it over PDF.', 'wp-ultimate-pdf'); ?></p>
		<div class='dr'>
			<?php
			$counter = 0;
			$posttypes = get_post_types(array('public'   => true), 'names');
			unset($posttypes['attachment']);
			foreach ($posttypes as $posttype) {
				$counter++;
			?>
				<div class="metainfo">
					<p>
						<input class="meta-detail" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[<?php echo esc_html($posttype); ?>][<?php echo esc_html($posttype); ?>]" value="1" <?php echo (isset($mwbpdf_genoptions['6'][$posttype][$posttype]) ? 'checked="checked"' : ''); ?>type="checkbox" id="<?php echo esc_html($posttype); ?>" /><span class="posttype" id="<?php echo esc_html($posttype); ?>_post"><?php echo esc_html($posttype); ?></span>
						<input type="hidden" value='<?php echo esc_html($posttype); ?>' class='hiddentype<?php echo esc_html($posttype); ?>' />
					</p>
				</div>
				<?php
				if ($counter % 3 == 0) {
				?>
		</div>
		<div class='dr'>
	<?php
				}
			}
	?>
		</div>
		<div class='er'>
			<?php
			$posttypes = get_post_types(array('public'   => true), 'names');
			unset($posttypes['attachment']);
			$count = 0;
			foreach ($posttypes as $posttype) {
				$arr = get_posts(array('post_type' => $posttype));
				foreach ($arr as $key => $value) {
					$custom = get_post_custom($value->ID);
					unset($custom['_edit_last']);
					unset($custom['_edit_lock']);
					unset($custom['_thumbnail_id']);
			?>

					<div class="meta<?php echo esc_html($posttype); ?> meta ">
						<h4><strong class='details-meta'><?php echo esc_html($posttype); ?><?php esc_html_e(' Meta Details', 'wp-ultimate-pdf'); ?></strong></h4>
						<?php
						foreach ($custom as $key => $val) {
						?>
							<p class='metakey'>
								<input name="<?php echo esc_html(MWBPDF_GEN_PREFIX) . '[' . esc_html($posttype) . ']' . '[' . esc_html($key) . ']'; ?>" value="1" <?php echo (isset($mwbpdf_genoptions['6'][$posttype][$key])) ? 'checked="checked"' : ''; ?>type="checkbox" class='product-inc' /><label><?php esc_html_e($key, 'wp-ultimate-pdf'); ?></label>
								<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX) . '[' . esc_html($posttype) . ']' . '[' . esc_html($key) . 'name]'; ?>" value="<?php echo (isset($mwbpdf_genoptions['6'][$posttype][$key . 'name']) ? esc_html($mwbpdf_genoptions['6'][$posttype][$key . 'name']) : ' '); ?>" />
							</p>
						<?php
						}
						break;
						?>
					</div>
				<?php } ?>
		</div>
	<?php } ?>
	<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>

	<?php
}
function predefined_first_page_settings()
{
	global $fonts;
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}
	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
	?>
		<table class="sect-table ">

			<tr>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>
	<?php
		wp_die();
	}
	?>

	<form method="post" action="options.php" style="float: right;" name="first_page_form" id="first_page_pdf">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'start_page_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'start_page_option');
		?>

		<table class="sect-table" id="firstpage">
			<tr>
				<td class="tr1"><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {
							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
				<td></td>
			</tr>
			<tr>
				<td colspan="3">
					<h1><?php echo esc_html__('predefined Template PDF', 'wp-ultimate-pdf'); ?></h1>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Enable Predefined Templates', 'wp-ultimate-pdf'); ?>
				</td>
				<td class="tr2">
					<input type="checkbox" id="watermark_text_first_page" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[start_page_predefined_template]" value='1' <?php echo (isset($mwbpdf_genoptions['8']['start_page_predefined_template'])) ? 'checked = "checked"' : ''; ?> />
					<span class="descr"><?php esc_html_e('Enable and choose the template for cover page.', 'wp-ultimate-pdf'); ?></span>
				</td>
				<td></td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Cover Template', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<?php
					if (! isset($mwbpdf_genoptions['8']['cover_tmplt'])) {
						$mwbpdf_genoptions['8']['cover_tmplt'] = 'tmplt1';
					}
					?>
					<select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[cover_tmplt]" id="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[cover_tmplt]" class="mwb_cover_select_tmplt">
						<option value="tmplt1" <?php selected($mwbpdf_genoptions['8']['cover_tmplt'], 'tmplt1'); ?>><?php esc_html_e('Template 1', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt2" <?php selected($mwbpdf_genoptions['8']['cover_tmplt'], 'tmplt2'); ?>><?php esc_html_e('Template 2', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt3" <?php selected($mwbpdf_genoptions['8']['cover_tmplt'], 'tmplt3'); ?>><?php esc_html_e('Template 3', 'wp-ultimate-pdf'); ?></option>
						<option value="tmplt4" <?php selected($mwbpdf_genoptions['8']['cover_tmplt'], 'tmplt4'); ?>><?php esc_html_e('Template 4', 'wp-ultimate-pdf'); ?></option>
					</select>
					<input type="submit" name="preview_post_template" value="<?php esc_html_e('Preview Template', 'wp-ultimate-pdf'); ?>" class="previewcovertemplate button" />
					<span class="descr">
						<?php esc_html_e('Please choose the post template', 'wp-ultimate-pdf'); ?>
					</span>
					<div id="coverModal" class="coverpagemodal">
						<div class="cover-modal-content">
							<span class="close">&times;</span>
							<image class="Cover_image_preview" src="" />
						</div>
					</div>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('cover page Image', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<?php $src_waterark = isset($mwbpdf_genoptions['8']['Cover_image_url']) ? $mwbpdf_genoptions['8']['Cover_image_url'] : ''; ?>
					<div id="cover_Background" style="float: left; margin-right: 10px;">
						<img id="wbgcoverimg" src="<?php echo esc_html($src_waterark); ?>" width="60px" height="60px" />
					</div>
					<div style="line-height: 60px;">
						<input type="hidden" id="Cover_image_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[Cover_image_url]" value="<?php echo esc_html($src_waterark); ?>" />
						<button type="button" class="upload_bgcoverimage_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
						<button type="button" class="remove_cover_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('company name', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2">
					<input type="text" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[company_name]" value="<?php if (isset($mwbpdf_genoptions['8']['company_name'])) {
																													echo esc_html($mwbpdf_genoptions['8']['company_name']);
																												} else {
																													echo '';
																												} ?>" />
				</td>

				<td></td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Logo', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2"><select name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[Logo]" id="page_header">
						<?php
						if (isset($mwbpdf_genoptions['Logo'])) {
							$page_header_value = $mwbpdf_genoptions['Logo'];
						} else {
							$page_header_value = 'upload-image';
						}

						mwbpdf_gen_profile_option($page_header_value, 'upload-image', esc_html__('Upload an Image', 'wp-ultimate-pdf'));
						?>
					</select>
					<span class="descr"><?php esc_html_e('Upload your organization’s logo here', 'wp-ultimate-pdf'); ?></span>
					<br>
					<button type="button" class="upload_imglogo_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
				</td>
				<td id="custom_logo">
					<div id="customlogo">
						<img id="imglogo" src="<?php echo (! empty($mwbpdf_genoptions['8']['logo_img_url'])) ? esc_html($mwbpdf_genoptions['8']['logo_img_url']) : ''; ?>" width="80px" height="80px" />
					</div>
					<div>
						<input type="hidden" id="logo_img_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[logo_img_url]" value="<?php echo (isset($mwbpdf_genoptions['8']['logo_img_url'])) ? esc_html($mwbpdf_genoptions['8']['logo_img_url']) : ''; ?>" />
						<button type="button" class="remove_logo_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Company Tagline', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<textarea id='custom_footer'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_tagline]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['8']['custom_tagline']) ? esc_html($mwbpdf_genoptions['8']['custom_tagline']) : ''); ?>
						</textarea>
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Custom Footer Text', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_option]" value='1'
						<?php echo (isset($mwbpdf_genoptions['8']['custom_footer_option'])) ? 'checked = "checked"' : ''; ?> />
				</td>
			</tr>

			<tr class="start_page_custom_footer">
				<td class="tr1"><?php esc_html_e('Enable for email', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text_email"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_email]" value='1'
						<?php echo (isset($mwbpdf_genoptions['8']['custom_footer_email'])) ? 'checked = "checked"' : ''; ?> />
					<span class="descr"><?php esc_html_e('Enable and enter your email.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"></td>
				<td class="tr2" colspan="2"><textarea id='custom_footer_email_start'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_email_text]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['8']['custom_footer_email_text']) ? esc_html($mwbpdf_genoptions['8']['custom_footer_email_text']) : ''); ?></textarea>

				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"><?php esc_html_e('Enable for contact', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text_contact"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_option_contact]" value='1'
						<?php echo (isset($mwbpdf_genoptions['8']['custom_footer_option_contact'])) ? 'checked = "checked"' : ''; ?> />
					<span class="descr"><?php esc_html_e('Enable and enter your contact.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"></td>
				<td class="tr2" colspan="2"><textarea id='custom_footer_contact'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_contact]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['8']['custom_footer_contact']) ? esc_html($mwbpdf_genoptions['8']['custom_footer_contact']) : ''); ?></textarea>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"><?php esc_html_e('Enable for Address', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text_address"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_option_address]" value='1'
						<?php echo (isset($mwbpdf_genoptions['8']['custom_footer_option_address'])) ? 'checked = "checked"' : ''; ?> />
					<span class="descr"><?php esc_html_e('Enable and enter your Address.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"></td>
				<td class="tr2" colspan="2"><textarea id='custom_footer_address_start'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_address]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['8']['custom_footer_address']) ? esc_html($mwbpdf_genoptions['8']['custom_footer_address']) : ''); ?></textarea>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"><?php esc_html_e('Enable for website URL', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text_url"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_option_url]" value='1'
						<?php echo (isset($mwbpdf_genoptions['8']['custom_footer_option_url'])) ? 'checked = "checked"' : ''; ?> />
					<span class="descr"><?php esc_html_e('Enable and enter your Site Url.', 'wp-ultimate-pdf'); ?></span>
				</td>
			</tr>
			<tr class="start_page_custom_footer">
				<td class="tr1"></td>
				<td class="tr2" colspan="2"><textarea id='custom_footer_url'
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_footer_url_link]" class="cusDocEntryTpl"><?php echo (isset($mwbpdf_genoptions['8']['custom_footer_url_link']) ? esc_html($mwbpdf_genoptions['8']['custom_footer_url_link']) : ''); ?></textarea>
				</td>
			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="8">
			</p>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
	</form>
	<?php
}
/**
 * Custom first page settings.
 */
function custom_first_page_settings()
{
	global $fonts;
	$mwbpdf_genoptions = get_option(MWBPDF_GEN_PREFIX);
	$mwbpdf = new mwbpdf();
	if (! session_id()) {
		session_start();
	}
	$notice = isset($_SESSION['mwb_notice']) ? $_SESSION['mwb_notice'] : array();
	$mwbpdf->upg_print_notices($notice);
	unset($_SESSION['mwb_notice']);
	if ((! isset($mwbpdf_genoptions['settingname'])) || ($mwbpdf_genoptions['settingname'] == '')) {
	?>
		<table class="sect-table ">

			<tr>
				<td>
					<h1><?php echo esc_html__('Please Create  at least one custom Setting or use existing one to make changes !!', 'wp-ultimate-pdf'); ?><h1>
				</td>
			</tr>
		</table>
	<?php
		wp_die();
	}
	?>
	<form method="post" action="options.php" style="float: right;" name="first_page_form" id="first_page_pdf">
		<?php
		settings_fields(MWBPDF_GEN_PREFIX . 'start_page_option');
		do_settings_sections(MWBPDF_GEN_PREFIX . 'start_page_option');
		?>
		<table class="sect-table" id="firstpage">
			<tr>
				<td class="tr1"><?php echo esc_html__('Select Setting', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<select name="settings" id="sett">
						<option selected><?php esc_html_e('Default Settings', 'wp-ultimate-pdf'); ?></option>
						<?php
						$arr = get_option('ptp_settings');
						foreach ($arr as $key => $value) {
							$currentsetting = isset($mwbpdf_genoptions['settingname']) ? $mwbpdf_genoptions['settingname'] : '-----------';
						?>
							<option value="<?php echo esc_html($key); ?>"
								<?php
								if ($key == $currentsetting) :
									echo "Selected='selected'";
								endif;
								?>><?php echo esc_html($key); ?></option>
						<?php
						}
						?>
					</select>
					<input type="button" name="setsetting" value="<?php esc_html_e('Apply Setting', 'wp-ultimate-pdf'); ?>" class="settingbutton" />
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h1><?php esc_html_e('Upload your Cover Page Image', 'wp-ultimate-pdf'); ?></h1>
				</td>
				<td></td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('Enable for custom cover page', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2"><input type="checkbox" id="start_custom_footer_text_url"
						name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[custom_cover_page]" value='1'
						<?php echo (isset($mwbpdf_genoptions['9']['custom_cover_page'])) ? 'checked = "checked"' : ''; ?> />
				</td>
			</tr>
			<tr>
				<td class="tr1"><?php esc_html_e('cover page Image', 'wp-ultimate-pdf'); ?></td>
				<td class="tr2" colspan="2">
					<?php $src_waterark = isset($mwbpdf_genoptions['9']['Cover_image_url']) ? $mwbpdf_genoptions['9']['Cover_image_url'] : ''; ?>
					<div id="cover_Background" style="float: left; margin-right: 10px;">
						<img id="wbgcoverimg" src="<?php echo esc_html($src_waterark); ?>" width="60px" height="60px" />
					</div>
					<div style="line-height: 60px;">
						<input type="hidden" id="Cover_image_url" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[Cover_image_url]" value="<?php echo esc_html($src_waterark); ?>" />
						<button type="button" class="upload_coverimage_button button"><?php esc_html_e('Upload/Add image', 'wp-ultimate-pdf'); ?></button>
						<button type="button" class="remove_cover_button button"><?php esc_html_e('Remove image', 'wp-ultimate-pdf'); ?></button>
					</div>
				</td>
			</tr>
			<p>
				<input type="hidden" name="<?php echo esc_html(MWBPDF_GEN_PREFIX); ?>[index]" value="9">
			</p>
		</table>
		<input type="submit" id="mwb_submit_saved_setting" name="mwb_submit_saved_setting" class="mwb_submit_saved_setting settingbutton_color" value="<?php esc_html_e('Save Settings', 'wp-ultimate-pdf'); ?>" />
	</form>
	</form>
<?php

}
?>