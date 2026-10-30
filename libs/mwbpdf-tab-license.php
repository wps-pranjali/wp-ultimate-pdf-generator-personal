<?php
/**
 * This file is for license panel. Include this file if license is not validated.
 * If license is validated then show you setting page.
 * Otherwise show the same file.
 *
 * @package wp-ultimate-pdf-generator
 */

global $wp_version;
global $current_user;

?>

<hr/>
<div style="text-align: justify; float: left; width: 66%; font-size: 16px; line-height: 25px; padding-right: 4%;">
<?php
 echo sprintf( __( 'This is the License Activation Panel. After purchasing extension from Codecanyon you will get the purchase code of this extension. Please verify your purchase below so that you can use feature of this plugin. You can access your purchase code for this plugin from <a  href="%s" >Here</a>', 'wp-ultimate-pdf' ), 'https://codecanyon.net/downloads' );
?>
	
 </div>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th class="titledesc" scope="row">
				<label><?php esc_html_e( 'Enter Purchase Code', 'wp-ultimate-pdf' ); ?></label>
			</th>
			<td class="forminp">
				<fieldset>
					<input type="text" id="mwb_updf_license_key" class="input-text regular-input" placeholder="Enter your Purchase code here...">
					<input type="button" value="Validate" class="button-primary" id="mwb_updf_license_save">
					<img class="loading_image" src="<?php echo esc_html( MWBPDF_GEN_URL ); ?>/asset/images/loading.gif" style="height: 28px;vertical-align: middle;display:none;">
					<b class="licennse_notification"></b>
				</fieldset>
			</td>
		</tr>
	</tbody>
</table>
