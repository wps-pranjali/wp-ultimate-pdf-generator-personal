<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
$page_format = array();
if ( $this->options['4']['set_rotation'] ) {
	$page_format['Rotate'] = $this->options['4']['set_rotation'];
} else {
	$page_format['Rotate'] = 0;
}
$pdf = new CUSTOMPDF( $orientation, $unit, $pagesize, true, 'UTF-8', false );
Mwbpdf_Pdf_Security::apply( $pdf, $this->options );
$pdf->SetCreator( 'WP Post to PDF plugin by makewebbetter with ' . PDF_CREATOR );
$pdf->SetAuthor( get_bloginfo( 'name' ) );

// logo width calculation.
if ( isset( $this->options['2']['page_header'] ) && ( $this->options['2']['page_header'] ) != 'None' && ! empty( $this->options['2']['logo_img_url'] ) ) {

	if ( $this->options['2']['page_header'] == 'upload-image' ) {
		$logo_image_url = $this->options['2']['logo_img_url'];
	}
	$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

	$get_url_path = ABSPATH . '/' . $get_url_path;
	$infologo = getimagesize( $get_url_path );
	if ( isset( $this->options['2']['image_factor'] ) ) {
		$logo_width = (int) ( ( $this->options['2']['image_factor'] * $infologo [0] ) / $infologo [1] );
	} else {
		$logo_width = (int) ( ( 12 * $infologo [0] ) / $infologo [1] );
	}
	// for PHP 5.4 or below set default header data.

	if ( $this->options['2']['page_header'] == 'upload-image' ) {
		$logo_image_url = $this->options['2']['logo_img_url'];
	}
	$get_url_path = substr( $logo_image_url, strpos( $logo_image_url, 'wp-content' ) );

	$get_url_path = ABSPATH . '/' . $get_url_path;
	$infologo = getimagesize( $get_url_path );
	if ( isset( $this->options['2']['image_factor'] ) ) {
		$logo_width = (int) ( ( $this->options['2']['image_factor'] * $infologo [0] ) / $infologo [1] );
	} else {
		$logo_width = (int) ( ( 12 * $infologo [0] ) / $infologo [1] );
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
}

if ( isset( $this->options['2']['page_header'] ) && ( $this->options['2']['page_header'] ) == 'None' ) {
	$pdf->setPrintHeader( false );
}

// set header and footer fonts.
if ( ( $this->options['2']['header_font_size'] ) > 0 ) {
	$header_font_size = $this->options['2']['header_font_size'];
} else {
	$header_font_size = 10;
}
if ( isset( $this->options['footer_font_size'] ) && ! empty( $this->options['footer_font_size'] ) && ( ( $this->options['footer_font_size'] ) > 0 ) ) {
	$footer_font_size = $this->options ['footer_font_size'];
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

$header_font_pdf_value_dis = isset( $this->options ['footer_font_pdf'] ) ? $this->options ['footer_font_pdf'] : 'helvetica';

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

if ( isset( $this->options['footer_font_margin'] ) ) {
	$pdf->SetFooterMargin( $this->options['footer_font_margin'] );
	// set auto page breaks.
	$pdf->SetAutoPageBreak( true, $this->options['footer_font_margin'] );
} else {
	$pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
	// set auto page breaks.
	$pdf->SetAutoPageBreak( true, PDF_MARGIN_FOOTER );
}
if ( ! empty( $this->options['5']['bullet_img_url'] ) ) {
	$temp = $this->options['5']['bullet_img_url'];
	$temp = end( ( explode( '/', $temp ) ) );
	$temp = end( ( explode( '.', $temp ) ) );
	$listsymbol = 'img|' . $temp . '|' . $this->options['5']['custom_image_width'] . '|' . $this->options['5']['custom_image_height'] . '|' . $this->options['5']['bullet_img_url'];
	$pdf->setLIsymbol( $listsymbol );
}

// set image scale factor.

if ( isset( $this->options['1']['image_scale'] ) && ! empty( $this->options['1']['image_scale'] ) && ( $this->options['1']['image_scale'] > 0 ) ) {
	$pdf->setImageScale( $this->options['1']['image_scale'] );
} else {
	$pdf->setImageScale( PDF_IMAGE_SCALE_RATIO );
}

// set default font subsetting mode.
$pdf->setFontSubsetting( true );
$pdf->SetFont( $this->options['1']['content_font_pdf'], '', $this->options['1']['content_font_size'], '', true );
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
			$footer .= '' . $address . '|';
		}
		if ( $this->options[8]['custom_footer_email'] !== null ) {
			$footer .= '' . $email . '|';
		}
		if ( $this->options[8]['custom_footer_option_contact'] !== null ) {
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
										<td height="180px">
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
			$pdf->Image( $watermark_img, 0, 110, 210, 173, $ext, '', '', true, 50, '' );
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

	$pdf->setFontSubsetting( true );
	$pdf->SetFont( $this->options['1']['content_font_pdf'], '', $this->options['1']['content_font_size'], '', true );
foreach ( $post_ids as $post_id ) {
	$post = get_post( $post_id );

	$content = $post->the_content;
	{
	if ( has_shortcode( $content, 'mwbpdf' ) ) {
		if ( ! $this->options['6'][ $post->post_type ][ $post->post_type ] ) {
			return false;
		}
	}
	}
	if ( $this->options['4']['fontStretching'] ) {
		$pdf->setFontStretching( $this->options['4']['fontStretching'] );
	}
	if ( $this->options['4']['fontSpacig'] ) {
		$pdf->setFontSpacing( $this->options['4']['fontSpacig'] );
	}
	$page_format = array();
	if ( $this->options['4']['set_rotation'] ) {
		$page_format['Rotate'] = $this->options['4']['set_rotation'];
	} else {
		$page_format['Rotate'] = 0;
	}
	$pdf->SetMargins( $this->options['1']['margin_left'], $this->options ['1']['margin_top'], $this->options['1']['margin_right'], true );
			$pdf->setPrintHeader( true );
	$pdf->AddPage( $this->options['1']['page_orientation'], $page_format, false, false );
	$pdf->SetAutoPageBreak( true, PDF_MARGIN_BOTTOM );
	$pdf->SetPrintFooter( true );
	$pdf->Bookmark( $post->post_title, 1, -1, '', '', array( 0, 0, 0 ) );
	$html = '';
	if ( isset( $this->options ['applyCSS'] ) && ! empty( $this->options ['applyCSS'] ) && $this->options ['applyCSS'] ) {
		$html .= '<style>' . $this->options['1']['Customcss'] . '</style>';
	}
	$html .= '<body>';

	if ( ! empty( $this->options['1']['custom_title'] ) && $this->options['1']['custom_title'] != '' ) {
		$pdf_title = $this->options['1']['custom_title'];

	} else {

		$pdf_title = $post->post_title;
	}

	if ( ! empty( $this->options['1']['rtl_support'] ) ) {
		// set some language dependent data.
		$lg = array();
		$lg['a_meta_charset'] = 'UTF-8';
		$lg['a_meta_dir'] = 'rtl';
		$lg['a_meta_language'] = 'fa';
		$lg['w_page'] = 'page';

		// set some language-dependent strings (optional).
		$pdf->setLanguageArray( $lg );
		$pdf->setRTL( true );
	}
	if ( isset( $this->options['0']['author_name'] ) && ! $this->options['0']['author_name'] == '' ) {
		$author_id = $post->post_author;
		$author_meta_key = $this->options['0']['author_name'];
		$author = get_user_meta( $author_id );
		$html .= '<p><strong>' . __( 'Author', 'wp-ultimate-pdf' ) . ' : </strong>' . $author [ $author_meta_key ] [0] . '</p>';
	}

	if ( isset( $this->options['0']['post_categ'] ) ) {
		$categories = get_the_category( $post->ID );
		if ( $categories ) {
			$html .= '<p><strong>' . __( 'Categories', 'wp-ultimate-pdf' ) . ' : </strong>' . $categories [0]->cat_name . '</p>';
		}
	}

	if ( isset( $this->options['0']['post_tags'] ) ) {
		$tags = get_the_tags( $post->the_tags );
		if ( $tags ) {
			$html .= '<p><strong>' . __( 'Tagged as', 'wp-ultimate-pdf' ) . ' : </strong>';
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

	$post->post_content = apply_filters( 'the_post_export_content', $post->post_content );

	$post->post_content = wpautop( $post->post_content );
	$post->post_content = do_shortcode( $post->post_content );
	$post->post_content = apply_filters( 'the_content', $post->post_content );
	$post_type_val = $post->post_type;
	if ( $post_type_val == 'product' ) {
		$product_template = isset( $this->options['1']['product_tmplt'] ) ? $this->options['1']['product_tmplt'] : 'tmplt1';
		$product = wc_get_product( $post->ID );
		$progallery  = $product->get_gallery_attachment_ids();
		$product_pmeta = isset( $this->options['1']['pro_pmeta'] ) ? $this->options['1']['pro_pmeta'] : 4;
		$html1 = '';
		if ( isset( $this->options['0']['featured'] ) ) {
			if ( has_post_thumbnail( $post->ID ) ) {
				$html1 .= get_the_post_thumbnail( $post->ID );
			}
		}
		$html5 = '';
		if ( isset( $progallery ) ) {
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
		$arr = get_post_custom( $post_id );
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
				if ( isset( $this->options['6'][ $post_type_val ][ $key . 'name' ] ) && ( $this->options ['6'][ $post_type_val ][ $key . 'name' ] != ' ' ) ) {


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
					$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . $this->options['6'][ $post_type_val ][ $key . 'name' ] . '</p> ';
				} elseif ( isset( $this->options['6'][ $post_type_val ][ $key ] ) && ( $this->options ['6'][ $post_type_val ][ $key ] == 1 ) ) {
					if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {
						$field = get_fields( $post_id );
						if ( ( array_key_exists( $key, $field ) ) ) {
							continue;
						}
					}

					if ( $arr_count > $product_pmeta ) {
						$count++;
						$flag = true;
					}

					$html3 .= '<p><strong>' . ucfirst( str_replace( '_', '', $key ) ) . ':' . '</strong>' . get_post_meta( $post_id, $key, true ) . '</p> ';
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
			$new_date = gmdate( 'd-m-Y', strtotime( $post->post_date ) );
			$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . $new_date . '</p>';
		}
		// Display date if set in config.
		if ( isset( $this->options['0']['cuurent_date'] ) ) {
			$current_date = gmdate( 'd-m-Y' );
			$html .= '<p><strong>' . __( 'Print Date', 'wp-ultimate-pdf' ) . ': </strong>' . $current_date . '</p>';
		}
	} elseif ( $post_type_val == 'post' || $post_type_val == 'page' ) {
		$postID = $post->ID;

		$html .= '<h1 style="text-align:center">' . html_entity_decode( $pdf_title, ENT_QUOTES ) . '</h1>';
		if ( isset( $this->options['0']['featured'] ) ) {
			if ( has_post_thumbnail( $post->ID ) ) {
				$html .= get_the_post_thumbnail( $post->ID );
			}
		}

		$post_template = isset( $this->options['1']['post_tmplt'] ) ? $this->options['1']['post_tmplt'] : 'tmplt1';
		if ( $post_template == 'tmplt1' ) {
			$html = $this->get_post_html_body( $postID, $html, $pdf_title, $post, $post_type_val );
		} elseif ( $post_template == 'tmplt2' ) {
			$pdf->writeHTML( $html, true, 0, true, 0 );
			$pdf->setEqualColumns( 2, 84 );
			$html = $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
		} elseif ( $post_template == 'tmplt3' ) {
			$pdf->writeHTML( $html, true, 0, true, 0 );
			$pdf->setEqualColumns( 3, 57 );
			$html = $this->get_post_html_body( $postID, '', $pdf_title, $post, $post_type_val );
		}
		if ( isset( $post->post_date ) ) {
			$new_date = gmdate( 'd-m-Y', strtotime( $post->post_date ) );
			$html .= '<p><strong>' . __( 'Date', 'wp-ultimate-pdf' ) . ': </strong>' . $new_date . '</p>';
		}
	}

	/*
		Setting for displaying ACF field details only.
	*/

	if ( function_exists( 'get_fields' ) && class_exists( 'acf' ) ) {

		$field = get_fields( $post_id );
		if ( $field ) {
			foreach ( $field as $field_name => $value ) {

				if ( ! ( ( isset( $this->options['6'][ $post_type_val ][ $field_name ] ) && ( $this->options['6'][ $post_type_val ][ $field_name ] == 1 ) ) || ( isset( $this->options['6'][ $post_type_val ][ '_' . $field_name ] ) && ( $this->options['6'][ $post_type_val ][ '_' . $field_name ] == 1 ) ) ) ) {
					continue;
				}

				$field = get_field_object( $field_name, $post_id );

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

							$html .= '<p><strong>' . $field['label'] . ':</strong><a href=" ' . $field['value'] . '" >' . $field ['value'] . '</a><p>';

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


	// Print text using writeHTML.
	$pdf->writeHTML( $html, true, 0, true, 0 );
}

if ( isset( $this->options['3']['add_watermark'] ) ) {

				$no_of_pages = $pdf->getNumPages();

	if ( isset( $this->options[8]['start_page_predefined_template'] ) || isset( $this->options[9]['custom_cover_page'] ) ) {
		$i = 2;
	} else {
		$i = 1;
	}



	for ( $i; $i <= $no_of_pages; $i ++ ) {
		$pdf->setPage( $i );
		// Get the page width/height.
		$my_page_width = $pdf->getPageWidth();
		$my_page_height = $pdf->getPageHeight();

		// Find the middle of the page and adjust.
		$my_x = ( $my_page_width / 2 ) - 75;
		$my_y = ( $my_page_height / 2 ) + 25;
		// Set the transparency of the text to really light.
		$pdf->SetAlpha( 0.09 );

		// Rotate 45 degrees and write the watermarking text.
		$pdf->StartTransform();
		$rotate_degr = isset( $this->options['3']['rotate_water'] ) ? $this->options['3']['rotate_water'] : '45';
		$pdf->Rotate( $rotate_degr, $my_x, $my_y );
		$water_font = isset( $this->options['3']['water_font'] ) ? $this->options['3']['water_font'] : 'courier';
		$pdf->SetFont( $water_font, '', 30 );
		$watermark_text = isset( $this->options['3']['watermark_text'] ) ? $this->options['3']['watermark_text'] : '';
		$pdf->Text( $my_x, $my_y, $watermark_text );
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


		for ( $i; $i <= $no_of_pages; $i ++ ) {
			$pdf->setPage( $i );
			$my_page_width = $pdf->getPageWidth();
			$my_page_height = $pdf->getPageHeight();
			$myxaxis = $this->options['3']['water_img_x_axis'];
			$myyaxis = $this->options['3']['water_img_y_axis'];
			$image_t = isset( $this->options['3']['water_img_t'] ) ? $this->options['3']['water_img_t'] : '';
			// Set the transparency of the text to really light.
			$pdf->SetAlpha( $image_t );
			// Rotate 45 degrees and write the watermarking text.
			$pdf->StartTransform();
			$image_h = isset( $this->options['3']['water_img_h'] ) ? $this->options['3']['water_img_h'] : '';
			$image_w = isset( $this->options['3']['water_img_w'] ) ? $this->options['3']['water_img_w'] : '';
			$watermark_img = isset( $this->options['3']['background_img_url'] ) ? $this->options['3']['background_img_url'] : '';
			$ext = pathinfo( $watermark_img, PATHINFO_EXTENSION );
			$pdf->Image( $watermark_img, $myxaxis, $myyaxis, $image_w, $image_h, $ext, $watermark_img, '', true, 500, '' );
			$pdf->StopTransform();
			// Reset the transparency to default.
			$pdf->SetAlpha( 1 );
		}
	}
}
if ( ! is_dir( MWB_CACHE_DIR ) ) {
	mkdir( MWB_CACHE_DIR, 0777, true );
}
$pdf->Output( $filePath, 'F' );
