<?php
/**
 * The Layout_Setup file.
 *
 * @package Location_Weather_free
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

//
// Set a unique slug-like ID.
//
$layout_prefix = 'sp_location_weather_layout';

SPLW::createMetabox(
	$layout_prefix,
	array(
		'title'        => __( 'Location Weather Generation Layout', 'location-weather' ),
		'post_type'    => 'location_weather',
		'show_restore' => true,
		'preview'      => false,
		'class'        => 'splw-shortcode-options',
	)
);

SPLW::createSection(
	$layout_prefix,
	array(
		'fields' => array(
			array(
				'type'    => 'heading',
				'image'   => esc_url( LOCATION_WEATHER_ASSETS ) . '/images/icons/location-weather-logo.svg',
				'after'   => '<i class="fa fa-life-ring"></i> Support',
				'link'    => 'https://shapedplugin.com/support/',
				'class'   => 'splw-admin-header',
				'version' => LOCATION_WEATHER_VERSION,
			),
			array(
				'id'      => 'weather-view',
				'type'    => 'image_select',
				'class'   => 'weather_view splw-first-fields',
				'title'   => __( 'Weather Layout', 'location-weather' ),
				'options' => array(
					'vertical'   => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/vertical.svg' ),
						'name'            => __( 'Vertical Card', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/vertical-card/',
					),
					'horizontal' => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/horizontal.svg' ),
						'name'            => __( 'Horizontal', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/horizontal/',
					),
					'tabs'       => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/tabs.svg' ),
						'name'            => __( 'Tabs', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/tabs/',
						'pro_only'        => true,
					),
					'table'      => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/table.svg' ),
						'name'            => __( 'Table', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/table/',
						'pro_only'        => true,
					),
					'map'        => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/maps.svg' ),
						'name'            => __( 'Map', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/weather-map/',
						'pro_only'        => true,
					),
					'combined'   => array(
						'image'           => SPLW::include_plugin_url( 'assets/images/weather-view/combined.svg' ),
						'name'            => __( 'Combined', 'location-weather' ),
						'option_demo_url' => 'https://locationweather.io/demos/combined-weather/',
						'pro_only'        => true,
					),
				),
				'default' => 'vertical',
			),
			array(
				'id'         => 'weather-template',
				'type'       => 'image_select',
				'class'      => 'weather-template',
				'title'      => __( 'Templates', 'location-weather' ),
				'options'    => array(
					'template-one'   => array(
						'image' => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-one.svg' ),
						'name'  => __( 'Template One', 'location-weather' ),
					),
					'template-two'   => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-two.svg' ),
						'name'     => __( 'Template Two', 'location-weather' ),
						'pro_only' => true,
					),
					'template-three' => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-three.svg' ),
						'name'     => __( 'Template Three', 'location-weather' ),
						'pro_only' => true,
					),
					'template-four'  => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-four.svg' ),
						'name'     => __( 'Template Four', 'location-weather' ),
						'pro_only' => true,
					),
					'template-five'  => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-five.svg' ),
						'name'     => __( 'Template Five', 'location-weather' ),
						'pro_only' => true,
					),
					'template-six'   => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/vertical-layout/template-six.svg' ),
						'name'     => __( 'Template Six', 'location-weather' ),
						'pro_only' => true,
					),
				),
				'default'    => 'template-one',
				'dependency' => array( 'weather-view', '==', 'vertical' ),
			),
			array(
				'id'         => 'weather-horizontal-template',
				'type'       => 'image_select',
				'class'      => 'weather-horizontal-template',
				'title'      => __( 'Templates', 'location-weather' ),
				'options'    => array(
					'horizontal-one'   => array(
						'image' => SPLW::include_plugin_url( 'assets/images/horizontal-layout/template-one.svg' ),
						'name'  => __( 'Template One', 'location-weather' ),
					),
					'horizontal-two'   => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/horizontal-layout/template-two.svg' ),
						'name'     => __( 'Template Two', 'location-weather' ),
						'pro_only' => true,
					),
					'horizontal-three' => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/horizontal-layout/template-three.svg' ),
						'name'     => __( 'Template Three', 'location-weather' ),
						'pro_only' => true,
					),
					'horizontal-four'  => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/horizontal-layout/template-four.svg' ),
						'name'     => __( 'Template Four', 'location-weather' ),
						'pro_only' => true,
					),
				),
				'default'    => 'horizontal-one',
				'dependency' => array( 'weather-view', '==', 'horizontal' ),
			),
			array(
				'id'         => 'weather-tabs-template',
				'type'       => 'image_select',
				'only_pro'   => true,
				'class'      => 'weather-tabs-template',
				'title'      => __( 'Templates', 'location-weather' ),
				'options'    => array(
					'tabs-one' => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/tabs-layout/template-one.svg' ),
						'name'     => __( 'Template One', 'location-weather' ),
						'pro_only' => true,
					),
					'tabs-two' => array(
						'image'    => SPLW::include_plugin_url( 'assets/images/tabs-layout/template-two.svg' ),
						'name'     => __( 'Template Two', 'location-weather' ),
						'pro_only' => true,
					),
				),
				'default'    => 'tabs-one',
				'dependency' => array( 'weather-view', '==', 'tabs' ),
			),
			array(
				'id'         => 'lw-enable-map-in-tabs',
				'class'      => 'lw-enable-map-in-tabs splw_show_hide',
				'type'       => 'switcher',
				'only_pro'   => true,
				'title'      => __( 'Enable Weather Map in Tabs', 'location-weather' ),
				'default'    => false,
				'text_on'    => __( 'Enabled', 'location-weather' ),
				'text_off'   => __( 'Disabled', 'location-weather' ),
				'text_width' => 100,
				'dependency' => array( 'weather-view', '==', 'tabs' ),
			),
			array(
				'id'         => 'weather-map-types',
				'type'       => 'button_set',
				'class'      => 'weather-map-type',
				'only_pro'   => true,
				'title'      => __( 'Map Type', 'location-weather' ),
				'options'    => array(
					'2' => __( 'OpenWeather Map', 'location-weather' ),
					'3' => __( 'Windy Map', 'location-weather' ),
				),
				'default'    => '2',
				'dependency' => array( 'weather-view', 'any', 'map,combined' ),
			),
			array(
				'id'      => 'map-notice',
				'class'   => 'splw-map-notice',
				'type'    => 'notice',
				/* translators: %1$s: anchor tag start, %2$s: first anchor tag end,%3$s: second anchor tag start, %4$s: second anchor tag end. */
				'content' => sprintf( __( 'To create eye-catching %1$s Weather Layouts%2$s with %5$s Graph Charts%6$s and access to advanced customizations, %3$sUpgrade to Pro!%4$s', 'location-weather' ), '<a class="lw-open-live-demo" href="https://locationweather.io/#weather-showcase" target="_blank"><strong>', '</strong></a>', '<a class="lw-open-live-demo" href="https://locationweather.io/pricing/?ref=1" target="_blank"><strong>', '</strong></a>', '<a class="lw-open-live-demo" href="https://locationweather.io/demos/weather-graph-chart/" target="_blank"><strong>', '</strong></a>' ),
				// 'dependency' => array( 'weather-view', 'any', 'map,combined', true ),
			),
		),
	)
);
