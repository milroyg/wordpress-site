<?php
/**
 * Function Custom meta box for Premium
 * 
 * @package Timeline and History slider
 * @since 1.4.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} ?>

<!-- <div class="pro-notice"><strong><?php //esc_html_e('Utilize this ', 'timeline-and-history-slider'); ?><a href="<?php //echo esc_url(WPOSTAHS_PLUGIN_LINK_UNLOCK); ?>" target="_blank"><?php //esc_html_e('Premium Features (With Risk-Free 30 days money back guarantee)', 'timeline-and-history-slider'); ?></a> <?php //esc_html_e('to get best of this plugin with Annual or Lifetime bundle deal.', 'timeline-and-history-slider') ?></strong></div> -->

<!-- <div class="wpostahs-black-friday-banner-wrp">
	<a href="<?php // echo esc_url( WPOSTAHS_PLUGIN_LINK_UNLOCK ); ?>" target="_blank"><img style="width: 100%;" src="<?php // echo esc_url( WPOSTAHS_URL ); ?>assets/images/black-friday-banner.png" alt="black-friday-banner" /></a>
</div> -->

<strong style="color:#2ECC71; font-weight: 700;"><?php echo sprintf( __( ' <a href="%s" target="_blank" style="color:#2ECC71;">Upgrade To Pro</a> and Get Designs, Optimization, Security, Backup, Migration Solutions @ one stop.', 'timeline-and-history-slider'), WPOSTAHS_PLUGIN_LINK_UNLOCK); ?></strong>

<table class="form-table wpostahs-metabox-table">
	<tbody>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Layouts ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('2 (Verical And Horizontal). In lite version only 1 layout.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Designs ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('12+. In lite version only one design.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Separate Field for Custom icon ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Seprate field availabe if you want to add custom icon.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Show/hide post link ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Option Show/hide post link.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>

		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('WP Templating Features ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('You can modify plugin html/designs in your current theme.', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Shortcode Generator ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Play with all shortcode parameters with preview panel. No documentation required.' , 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Drag & Drop Slide Order Change ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Arrange your desired slides with your desired order and display.' , 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Page Builder Support ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Gutenberg Block, Elementor, Bevear Builder, SiteOrigin, Divi, Visual Composer and Fusion Page Builder Support', 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
		<tr class="wpostahs-pro-feature">
			<th>
				<?php esc_html_e('Exclude Timeline and Exclude Some Categories ', 'timeline-and-history-slider'); ?><span class="wpostahs-pro-tag"><?php esc_html_e('PRO','timeline-and-history-slider');?></span>
			</th>
			<td>
				<span class="description"><?php esc_html_e('Do not display the timeline & Do not display the timeline for particular categories.' , 'timeline-and-history-slider'); ?></span>
			</td>
		</tr>
	</tbody>
</table><!-- end .wpostahs-metabox-table -->