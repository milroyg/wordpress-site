<?php
/**
 * Function Custom meta box for Premium
 * 
 * @package Timeline and History slider
 * @since 1.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!-- <div class="pro-notice"><strong><?php //esc_html_e('Try This PRO Features with ', 'timeline-and-history-slider'); ?><a href="<?php //echo esc_url(WPOSTAHS_PLUGIN_LINK_UNLOCK); ?>" target="_blank"><?php //esc_html_e('Early Back Friday Deals ', 'timeline-and-history-slider'); ?></a> <?php //esc_html_e('on lifetime plan. FLAT $100 USD OFF.', 'timeline-and-history-slider') ?></strong></div> -->

<strong style="color:#2ECC71; font-weight: 700;"><?php echo sprintf( __( ' <a href="%s" target="_blank" style="color:#2ECC71;">Upgrade To Pro</a> and Get Designs, Optimization, Security, Backup, Migration Solutions @ one stop.', 'timeline-and-history-slider'), WPOSTAHS_PLUGIN_LINK_UNLOCK); ?></strong>

<table class="form-table wpostahs-metabox-table">
	<tbody>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Timeline Custom Icon ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>

			<td>
				<input type="text" name="wpostahs_custom_icon" value="" class="regular-text" disabled="" />
				<input type="button" name="wpostahs_custom_icon" class="button-secondary" value="<?php esc_html_e( 'Upload Image', 'timeline-and-history-slider'); ?>" disabled="" /> <input type="button" name="wpostahs_custom_icon_clear" class="button button-secondary" value="<?php esc_html_e( 'Clear', 'timeline-and-history-slider'); ?>" disabled="" /> <br />
				<span class="description"><?php esc_html_e('Upload custom icon that you want to show for your timeline instead of fa icon.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Timeline Fa Icon ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>

			<td>
				<input type="text" name="wpostahs_timeline_icon" value="" class="regular-text" disabled="" /><br />
				<span class="description"><?php esc_html_e('For example :', 'timeline-and-history-slider'); ?> fa fa-bluetooth-b</span>
			</td>
		</tr>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Read More Link ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>

			<td>
				<input type="text" name="wpostahs_timeline_link" value="" class="regular-text" disabled="" /><br />
				<span class="description"><?php esc_html_e('Enter read more link. You can add external link also. Leave empty to use default post link. ie ', 'timeline-and-history-slider'); ?>https://www.essentialplugin.com</span>
			</td>
		</tr>

	</tbody>
</table><!-- end .wpostahs-metabox-table -->