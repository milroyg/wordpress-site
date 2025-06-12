<?php
/**
 * The style setup configuration.
 *
 * @package Location_Weather
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

SPLW::createSection(
	'sp_location_weather_generator',
	array(
		'title'  => __( 'Style Settings', 'location-weather' ),
		'icon'   => '<span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" ><g clip-path="url(#A)" fill="#fff"><path d="M7.324 4.939L11.051.678c.251-.347.628-.583 1.05-.656a1.61 1.61 0 0 1 .643.024 1.61 1.61 0 0 1 .582.274c.18.127.332.288.447.475s.193.395.226.612.023.439-.031.652-.151.413-.284.588L9.669 7.249c.073.431.042.874-.09 1.291a2.75 2.75 0 0 1-.671 1.107c-.431.417-.946.737-1.51.939a4.22 4.22 0 0 1-1.763.233 5.17 5.17 0 0 1-2.826-.963.55.55 0 0 1-.184-.534.55.55 0 0 1 .341-.437c.796-.315.989-.875 1.234-1.557a3.77 3.77 0 0 1 .875-1.593C5.592 5.235 6.28 4.95 7 4.939l.324.026v-.026zm1.103.394c.174.111.335.24.481.385a2.44 2.44 0 0 1 .306.367l3.596-4.147a.51.51 0 0 0 .087-.385c-.015-.107-.063-.208-.136-.288s-.169-.137-.274-.162-.216-.019-.318.02-.19.106-.252.195L8.426 5.333zm-.298 1.19A1.53 1.53 0 0 0 7 6.042a1.69 1.69 0 0 0-1.199.49c-.274.335-.463.731-.551 1.155-.17.642-.512 1.226-.989 1.689a3.5 3.5 0 0 0 1.417.324 3.17 3.17 0 0 0 2.502-.875c.294-.314.453-.731.443-1.161s-.188-.839-.496-1.14z"/><path fill-rule="evenodd" d="M2.275 1.35c-.245 0-.481.097-.654.271s-.271.409-.271.654v9.45c0 .245.097.481.271.654s.409.271.654.271h9.45c.245 0 .481-.098.654-.271s.271-.409.271-.654v-.522a.65.65 0 0 1 1.3 0v.522c0 .59-.234 1.156-.652 1.573s-.983.652-1.573.652h-9.45c-.59 0-1.156-.234-1.573-.652S.05 12.315.05 11.725v-9.45c0-.59.234-1.156.652-1.573S1.685.05 2.275.05h6.281a.65.65 0 1 1 0 1.3H2.275z"/><rect x="12.65" y="4.5" width="1.3" height="4.5" rx=".65"/></g><defs><clipPath id="A"><path fill="#fff" d="M0 0h14v14H0z"/></clipPath></defs></svg></span>',
		'class'  => 'splw-weather-settings-meta-box',
		'fields' => array(
			array(
				'id'         => 'lw-background-type',
				'class'      => 'splw_background_type',
				'type'       => 'button_set',
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div><a class="lw-open-docs" href="https://locationweather.io/docs/how-to-configure-weather-details-background-type/" target="_blank">%s</a><a class="lw-open-live-demo" href="https://locationweather.io/demos/weather-background-type/" target="_blank">%s</a>', __( 'Background Type', 'location-weather' ), __( 'Customize color or add video for your weather forecast background. You can also set an automated image based on weather conditions. See how it works.', 'location-weather' ), __( 'Open Docs', 'location-weather' ), __( 'Live Demo', 'location-weather' ) ),
				'title'      => __( ' Background Type ', 'location-weather' ),
				'options'    => array(
					'solid' => __( 'Color', 'location-weather' ),
					'2'     => __( 'Weather-based Image', 'location-weather' ),
					'3'     => __( 'Video', 'location-weather' ),
				),
				'default'    => 'solid',
			),
			array(
				'id'         => 'lw-background-color-type',
				'type'       => 'button_set',
				'title'      => __( ' Color Type', 'location-weather' ),
				'class'      => 'lw-background-color-type splw-first-fields',
				'options'    => array(
					'solid'    => __( 'Solid', 'location-weather' ),
					'gradient' => __( 'Gradient', 'location-weather' ),
				),
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div>', __( 'Color Type', 'location-weather' ), __( 'Choose a  color styles for customizing the appearance of your weather display. Solid provides a single color, while Gradient allows a blend of colors for a visually dynamic effect.', 'location-weather' ) ),
				'default'    => 'solid',
				'dependency' => array( 'lw-background-type', '==', 'solid', true ),
			),
			array(
				'id'         => 'lw-bg-solid',
				'type'       => 'color',
				'title'      => __( 'Solid Color', 'location-weather' ),
				'default'    => '#F05800',
				'dependency' => array( 'lw-background-type|lw-background-color-type', '==|==', 'solid|solid', true ),
			),
			array(
				'id'         => 'lw_content_padding',
				'type'       => 'spacing',
				'class'      => 'lw_content_padding',
				'title'      => __( 'Content Padding ', 'location-weather' ),
				'all'        => false,
				'min'        => 0,
				'max'        => 100,
				'units'      => array( 'px', '%' ),
				'default'    => array(
					'top'    => '16',
					'right'  => '20',
					'bottom' => '10',
					'left'   => '20',
				),
				'title_info' => '<div class="lw-img-tag"><img src="' . SPLW::include_plugin_url( 'assets/images/weather-content-padding.webp' ) . '" alt="weather-content-padding"></div><div class="lw-info-label img">' . __( 'Weather Content Padding', 'location-weather' ) . '</div>',
			),
			array(
				'id'      => 'lw_bg_border',
				'type'    => 'border',
				'title'   => __( 'Border', 'location-weather' ),
				'all'     => true,
				'default' => array(
					'all'   => '0',
					'style' => 'solid',
					'color' => '#e2e2e2',
				),
			),
			array(
				'id'        => 'lw_bg_border_radius',
				'type'      => 'spacing',
				'title'     => __( 'Radius', 'location-weather' ),
				'all'       => true,
				'all_title' => __( 'Radius', 'location-weather' ),
				'min'       => 0,
				'max'       => 100,
				'units'     => array( 'px', '%' ),
				'default'   => array(
					'all' => '8',
				),
			),
			array(
				'id'         => 'lw_box_shadow_type',
				'type'       => 'button_set',
				'title'      => __( 'Box-Shadow', 'location-weather' ),
				'options'    => array(
					'none'   => __( 'None', 'location-weather' ),
					'outset' => __( 'Outset', 'location-weather' ),
					'inset'  => __( 'Inset', 'location-weather' ),
				),
				'default'    => 'none',
				'dependency' => array( 'weather-view', 'any', 'vertical,horizontal', true ),
			),
			array(
				'id'         => 'weather_box_shadow',
				'type'       => 'box_shadow',
				'title'      => __( 'Box-Shadow Values', 'location-weather' ),
				'style'      => false,
				'default'    => array(
					'vertical'   => '4',
					'horizontal' => '4',
					'blur'       => '16',
					'spread'     => '0',
					'color'      => 'rgba(0,0,0,0.30)',
				),
				'dependency' => array( 'weather-view|lw_box_shadow_type', 'any|!=', 'vertical,horizontal|none', true ),
			),
			array(
				'id'         => 'lw_max_width',
				'class'      => 'lw_max_width',
				'type'       => 'spacing',
				'title'      => __( 'Weather Maximum Width', 'location-weather' ),
				'all'        => true,
				'all_icon'   => '<i class="fas fa-arrows-alt-h"></i>',
				'all_title'  => __( 'Width', 'location-weather' ),
				'min'        => 0,
				'max'        => 1920,
				'units'      => array( 'px', '%' ),
				'default'    => array(
					'all' => '320',
				),
				'title_info' => sprintf( '<div class="lw-info-label">%s</div><div class="lw-short-content">%s</div><a class="lw-open-docs" href="https://locationweather.io/docs/how-to-configure-weather-view-maximum-width/" target="_blank">%s</a>', __( 'Weather Maximum Width', 'location-weather' ), __( 'You can customize the weather widget’s maximum width to align it with your website content area perfectly.', 'location-weather' ), __( 'Open Docs', 'location-weather' ) ),
			),
			array(
				'type'    => 'notice',
				'style'   => 'normal',
				'class'   => 'notice-padding',
				/* translators: %1$s: anchor tag start, %2$s: first anchor tag end,%3$s: second anchor tag start, %4$s: second anchor tag end. */
				'content' => sprintf( __( 'To craft your desired %1$sWeather View%2$s with advanced customizations, %3$sUpgrade to Pro!%4$s', 'location-weather' ), '<a class="lw-open-live-demo" href="https://locationweather.io/demos/weather-background-type/" target="_blank"><strong>', '</strong></a>', '<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank"><strong>', '</strong></a>' ),
			),
		),
	)
);

