<?php
/**
 * Weather Footer Template File
 *
 * This template displays the weather footer for a specific location.
 *
 * @package Location_Weather
 */

$lw_openweather_map_link = isset( $splw_meta['lw-openweather-links'] ) ? $splw_meta['lw-openweather-links'] : false;
?>
<!-- weather detailed and updated html area start -->
<?php if ( $show_weather_detailed || $show_weather_updated_time || $show_weather_attr ) : ?>
	<div class="lw-footer">
	<?php if ( $show_weather_detailed || $show_weather_updated_time ) : ?>
	<div class="splw-weather-detailed-updated-time">
		<?php if ( $show_weather_detailed ) : ?>
		<div class='splw-weather-detailed'>
			<a href="https://openweathermap.org/city/<?php echo esc_attr( $weather_data['city_id'] ); ?>" target="_blank">
				<?php echo esc_html( __( 'Detailed weather', 'location-weather' ) ); ?>
			</a>
		</div>
		<?php endif ?>
		<?php if ( $show_weather_updated_time ) : ?>
		<div class='splw-weather-updated-time'>
				<?php echo esc_html( __( 'Last updated:', 'location-weather' ) ); ?>
				<?php echo esc_html( $weather_data['updated_time'] ); ?>
		</div>
		<?php endif ?>
	</div>
<?php endif; ?><!-- weather detailed and updated html area end -->
<!-- weather attribute html area start -->
	<?php if ( $show_weather_attr && $open_api_key ) : ?>
	<div class="splw-weather-attribution">
		<?php if ( $lw_openweather_map_link ) : ?>
		<a href="https://openweathermap.org/" target="_blank">
			<?php endif ?>
			<?php echo esc_html( __( 'Weather from OpenWeatherMap', 'location-weather' ) ); ?>
		<?php if ( $lw_openweather_map_link ) : ?>
		</a>
		<?php endif ?>
	</div>
<?php endif; ?><!-- weather attribute html area end -->
</div>
<?php endif; ?><!-- weather attribute html area end -->
