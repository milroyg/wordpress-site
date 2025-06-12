<?php
/**
 * Shortcode class file.
 *
 * @package Location_Weather
 */

namespace ShapedPlugin\Weather\Frontend;

use ShapedPlugin\Weather\Frontend\Scripts;
use ShapedPlugin\Weather\Frontend\WeatherData\CurrentWeather;
use ShapedPlugin\Weather\Frontend\WeatherData\Exception as LWException;

/**
 * Shortcode handler class.
 */
class Shortcode {

	/**
	 * The basic api URL.
	 *
	 * @var string The basic api url to fetch weather data from.
	 */
	private static $weather_url = 'https://api.openweathermap.org/data/2.5/weather?';

	/**
	 * The api key.
	 *
	 * @var string
	 */
	private static $api_key = '';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_shortcode( 'location-weather', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Full html show.
	 *
	 * @param array $shortcode_id Shortcode ID.
	 * @param array $splw_option get all options.
	 * @param array $splw_meta get all meta options.
	 * @param array $layout_meta get all layout meta options.
	 */
	public static function splw_html_show( $shortcode_id, $splw_option, $splw_meta, $layout_meta ) {
		// Weather option meta area.
		$open_api_key = isset( $splw_option['open-api-key'] ) ? $splw_option['open-api-key'] : '';
		$appid        = ! empty( $open_api_key ) ? $open_api_key : '';
		// Set default API key if not found any API.
		if ( ! $appid ) {
			$default_api_calls = (int) get_option( 'splw_default_call', 0 );
			if ( $default_api_calls < 20 ) {
				$appid          = 'e930dd32085dea457d1d66d01cd89f50';
				$transient_name = 'sp_open_weather_data' . $shortcode_id;
				$weather_data   = self::splw_get_transient( $transient_name );
				if ( false === $weather_data ) {
					++$default_api_calls;
					update_option( 'splw_default_call', $default_api_calls );
				}
			}
		}
		if ( ! $appid ) {
			$weather_output = sprintf( '<div id="splw-location-weather-%1$s" class="splw-main-wrapper"><div class="splw-weather-title">%2$s</div><div class="splw-lite-wrapper"><div class="splw-warning">%3$s</div> <div class="splw-weather-attribution"><a href = "https://openweathermap.org/" target="_blank">' . __( 'Weather from OpenWeatherMap', 'location-weather' ) . '</a></div></div></div>', esc_attr( $shortcode_id ), esc_html( get_the_title( $shortcode_id ) ), 'Please enter your OpenWeatherMap <a href="' . admin_url( 'edit.php?post_type=location_weather&page=lw-settings#tab=api-settings' ) . '" target="_blank">API key.</a>' );
			echo $weather_output; // phpcs:ignore
			return;
		}
		$layout                        = isset( $layout_meta['weather-view'] ) && ! wp_is_mobile() ? $layout_meta['weather-view'] : 'vertical';
		$active_additional_data_layout = isset( $splw_meta['weather-additional-data-layout'] ) ? $splw_meta['weather-additional-data-layout'] : 'center';
		$show_comport_data_position    = isset( $splw_meta['lw-comport-data-position'] ) ? $splw_meta['lw-comport-data-position'] : false;

		// Weather setup meta area .
		$custom_name     = isset( $splw_meta['lw-custom-name'] ) ? $splw_meta['lw-custom-name'] : '';
		$pressure_unit   = isset( $splw_meta['lw-pressure-unit'] ) ? $splw_meta['lw-pressure-unit'] : 'mb';
		$visibility_unit = isset( $splw_meta['lw-visibility-unit'] ) ? $splw_meta['lw-visibility-unit'] : 'km';
		$wind_speed_unit = isset( $splw_meta['lw-wind-speed-unit'] ) ? $splw_meta['lw-wind-speed-unit'] : '';
		$lw_language     = isset( $splw_meta['lw-language'] ) ? $splw_meta['lw-language'] : 'en';

		// Display settings meta section.
		$show_weather_title = isset( $splw_meta['lw-title'] ) ? $splw_meta['lw-title'] : '';
		$time_format        = isset( $splw_meta['lw-time-format'] ) ? $splw_meta['lw-time-format'] : 'g:i a';
		$utc_timezone       = isset( $splw_meta['lw-utc-time-zone'] ) && ! empty( $splw_meta['lw-utc-time-zone'] ) ? (float) str_replace( 'UTC', '', $splw_meta['lw-utc-time-zone'] ) * 3600 : '';

		$lw_modify_date_format = isset( $splw_meta['lw_client_date_format'] ) ? $splw_meta['lw_client_date_format'] : 'F j, Y';
		$lw_custom_date_format = preg_replace( '/\s*,?\s*\b(?:g:i a|g:i A|H:i|h:i|g:ia|g:iA)\b\s*,?\s*/i', '', $lw_modify_date_format );
		$lw_client_date_format = isset( $splw_meta['lw_date_format'] ) && 'custom' !== $splw_meta['lw_date_format'] ? $splw_meta['lw_date_format'] : $lw_custom_date_format;
		$show_date             = isset( $splw_meta['lw-date'] ) ? $splw_meta['lw-date'] : true;
		$show_time             = isset( $splw_meta['lw-show-time'] ) ? $splw_meta['lw-show-time'] : true;
		$show_icon             = isset( $splw_meta['lw-icon'] ) ? $splw_meta['lw-icon'] : '';

		// Temperature show hide meta.
		$show_temperature  = isset( $splw_meta['lw-temperature'] ) ? $splw_meta['lw-temperature'] : '';
		$temperature_scale = isset( $splw_meta['lw-display-temp-scale'] ) ? $splw_meta['lw-display-temp-scale'] : '';
		$short_description = isset( $splw_meta['lw-short-description'] ) ? $splw_meta['lw-short-description'] : '';

		// Units show hide meta.
		$weather_units     = isset( $splw_meta['lw-units'] ) ? $splw_meta['lw-units'] : '';
		$temperature_scale = $temperature_scale || 'none' !== $weather_units ? true : false;
		if ( 'auto_temp' === $weather_units || 'auto' === $weather_units || 'none' === $weather_units ) {
			$weather_units = 'metric';
		}
		$show_pressure        = isset( $splw_meta['lw-pressure'] ) ? $splw_meta['lw-pressure'] : '';
		$show_humidity        = isset( $splw_meta['lw-humidity'] ) ? $splw_meta['lw-humidity'] : '';
		$show_clouds          = isset( $splw_meta['lw-clouds'] ) ? $splw_meta['lw-clouds'] : '';
		$show_wind            = isset( $splw_meta['lw-wind'] ) ? $splw_meta['lw-wind'] : '';
		$show_wind_gusts      = isset( $splw_meta['lw-wind-gusts'] ) ? $splw_meta['lw-wind-gusts'] : '';
		$show_visibility      = isset( $splw_meta['lw-visibility'] ) ? $splw_meta['lw-visibility'] : '';
		$show_sunrise_sunset  = isset( $splw_meta['lw-sunrise-sunset'] ) ? $splw_meta['lw-sunrise-sunset'] : '';
		$lw_current_icon_type = isset( $splw_meta['weather-current-icon-type'] ) ? $splw_meta['weather-current-icon-type'] : 'forecast_icon_set_one';

		$show_weather_attr         = isset( $splw_meta['lw-attribution'] ) ? $splw_meta['lw-attribution'] : '';
		$show_weather_detailed     = isset( $splw_meta['lw-weather-details'] ) ? $splw_meta['lw-weather-details'] : false;
		$show_weather_updated_time = isset( $splw_meta['lw-weather-update-time'] ) ? $splw_meta['lw-weather-update-time'] : false;

		$weather_by = $splw_meta['get-weather-by'] ?? 'city_name';

		switch ( $weather_by ) {
			case 'city_name':
				$city  = trim( $splw_meta['lw-city-name'] ?? '' );
				$query = ! empty( $city ) ? $city : 'london';
				break;

			case 'city_id':
				$city_id = $splw_meta['lw-city-id'] ?? '';
				$query   = ! empty( $city_id ) ? (int) $city_id : 2643743;
				break;

			case 'latlong':
				$latlong_raw = $splw_meta['lw-latlong'] ?? '';
				$default     = array(
					'lat' => 51.509865,
					'lon' => -0.118092,
				);

				if ( ! empty( $latlong_raw ) && strpos( $latlong_raw, ',' ) !== false ) {
					$latlong = explode( ',', str_replace( ' ', '', trim( $latlong_raw ) ) );
					$lat     = $latlong[0] ?? null;
					$lon     = $latlong[1] ?? null;
					$query   = ( is_numeric( $lat ) && is_numeric( $lon ) ) ? array(
						'lat' => (float) $lat,
						'lon' => (float) $lon,
					) : $default;
				} else {
					$query = $default;
				}
				break;

			case 'zip':
				$zip   = trim( $splw_meta['lw-zip'] ?? '' );
				$query = ! empty( $zip ) ? 'zip:' . $zip . '' : 'zip:77070,US';
				break;

			default:
				$query = 'london';
				break;
		}

		$data = self::get_weather( $query, $weather_units, $lw_language, $appid, $shortcode_id );

		if ( is_array( $data ) && isset( $data['code'] ) && ( 401 === $data['code'] || 404 === $data['code'] ) ) {
			$weather_output = sprintf( '<div id="splw-location-weather-%1$s" class="splw-main-wrapper"><div class="splw-weather-title">%2$s</div><div class="splw-lite-wrapper"><div class="splw-warning">%3$s</div> <div class="splw-weather-attribution"><a href = "https://openweathermap.org/" target="_blank">' . __( 'Weather from OpenWeatherMap', 'location-weather' ) . '</a></div></div></div>', esc_attr( $shortcode_id ), esc_html( get_the_title( $shortcode_id ) ), $data['message'] );

			echo $weather_output; // phpcs:ignore
			return;
		}

		$weather_data = self::current_weather_data( $data, $time_format, $temperature_scale, $wind_speed_unit, $weather_units, $pressure_unit, $visibility_unit, $lw_client_date_format, $utc_timezone );
		ob_start();
		include self::lw_locate_template( 'main-template.php' );
		$weather_output = ob_get_clean();
		echo $weather_output;// phpcs:ignore.
	}

	/**
	 * Shortcode render class.
	 *
	 * @param array  $attribute The shortcode attributes.
	 * @param string $content Shortcode content.
	 * @return void
	 */
	public function render_shortcode( $attribute, $content = '' ) {
		if ( empty( $attribute['id'] ) || 'location_weather' !== get_post_type( $attribute['id'] ) || ( get_post_status( $attribute['id'] ) === 'trash' ) ) {
			return;
		}
		$shortcode_id = esc_attr( intval( $attribute['id'] ) );
		$splw_option  = get_option( 'location_weather_settings', true );
		$splw_meta    = get_post_meta( $shortcode_id, 'sp_location_weather_generator', true );
		$layout_meta  = get_post_meta( $shortcode_id, 'sp_location_weather_layout', true );
		// Stylesheet loading problem solving here. Shortcode id to push page id option for getting how many shortcode in the page.
		$get_page_data      = Scripts::get_page_data();
		$found_generator_id = $get_page_data['generator_id'];
		ob_start();
		// This shortcode id not in page id option. Enqueue stylesheets in shortcode.
		if ( ! is_array( $found_generator_id ) || ! $found_generator_id || ! in_array( $shortcode_id, $found_generator_id ) ) {
			wp_enqueue_style( 'splw-fontello' );
			wp_enqueue_style( 'splw-styles' );
			wp_enqueue_style( 'splw-old-styles' );
			/* Load dynamic style in the header based on found shortcode on the page. */
			$dynamic_style = Scripts::load_dynamic_style( $shortcode_id, $splw_meta );
			echo '<style id="sp_lw_dynamic_css' . $shortcode_id . '">' . wp_strip_all_tags( $dynamic_style['dynamic_css'] ) . '</style>';//phpcs:ignore
		}
		// Update options if the existing shortcode id option not found.
		Scripts::lw_db_options_update( $shortcode_id, $get_page_data );
		self::splw_html_show( $shortcode_id, $splw_option, $splw_meta, $layout_meta );
		wp_enqueue_script( 'splw-old-script' );
		return ob_get_clean();
	}
	// Shortcode render method end.

	/**
	 * Retrieves and formats current weather data.
	 *
	 * @param stdClass $data              The weather data object.
	 * @param string   $time_format       The time format (12-hour or 24-hour).
	 * @param string   $temperature_scale The temperature scale (e.g., 'C' or 'F').
	 * @param string   $wind_speed_unit   The wind speed unit (e.g., 'm/s' or 'mph').
	 * @param string   $weather_units     The units for weather data.
	 * @param string   $pressure_unit     The unit for pressure (e.g., 'hPa' or 'inHg').
	 * @param string   $visibility_unit   The unit for visibility (e.g., 'km' or 'mi').
	 * @param string   $lw_client_date_format The date format for the client's timezone.
	 * @param int|null $utc_timezone      The UTC timezone offset.
	 *
	 * @return array|null An array containing formatted weather data or null if the input data is not an object.
	 */
	public static function current_weather_data( $data, $time_format, $temperature_scale, $wind_speed_unit, $weather_units, $pressure_unit, $visibility_unit, $lw_client_date_format, $utc_timezone = null ) {
		if ( ! is_object( $data->city ) ) {
			return;
		}
		$scale       = self::temperature_scale( $temperature_scale, $weather_units );
		$temp        = '<span class="current-temperature">' . round( $data->temperature->now->value ) . '</span>' . $scale;
		$sunrise     = $data->sun->rise;
		$sunset      = $data->sun->set;
		$last_update = $data->last_update;
		$timezone    = $utc_timezone && ! empty( $utc_timezone ) || '' !== $utc_timezone ? (int) $utc_timezone : (int) $data->timezone;
		$wind        = self::get_wind_speed( $weather_units, $wind_speed_unit, $data, false );
		$gust        = self::get_wind_speed( $weather_units, $wind_speed_unit, $data, true );
		$now         = new \DateTime();

		// Check date and time format.
		if ( $time_format && null !== $last_update ) {
			$time         = date_i18n( $time_format, strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$date         = date_i18n( $lw_client_date_format, strtotime( $now->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$sunrise_time = gmdate( $time_format, strtotime( $sunrise->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$sunset_time  = gmdate( $time_format, strtotime( $sunset->format( 'Y-m-d g:i:sa' ) ) + $timezone );
			$updated_time = gmdate( $time_format, strtotime( $last_update->format( 'Y-m-d g:i:sa' ) ) + $timezone );
		}
		return array(
			'city_id'      => $data->city->id,
			'city'         => $data->city->name,
			'country'      => $data->city->country,
			'temp'         => $temp,
			'pressure'     => self::get_pressure( $pressure_unit, $data ),
			'humidity'     => $data->humidity,
			'wind'         => $wind,
			'gust'         => $gust,
			'visibility'   => self::get_visibility( $visibility_unit, $data ),
			'clouds'       => $data->clouds->value . '%',
			'desc'         => $data->weather->description,
			'icon'         => $data->weather->icon,
			'time'         => $time,
			'date'         => $date,
			'updated_time' => $updated_time,
			'sunrise_time' => $sunrise_time,
			'sunset_time'  => $sunset_time,
		);
	}

	/**
	 * Get the forecast weather data.
	 *
	 * @param string $temperature_scale Can be either 'F' or 'C' (default).
	 * @param string $weather_units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 *
	 * @return scale The weather temperature scale object.
	 */
	public static function temperature_scale( $temperature_scale, $weather_units ) {
		$scale = '°';
		if ( $temperature_scale && 'imperial' === $weather_units ) {
			$scale = '°F';
		} elseif ( $temperature_scale && 'metric' === $weather_units ) {
			$scale = '°C';
		} else {
			$scale = '°';
		}
		return '<span class="temperature-scale">' . $scale . '</span>';
	}

	/**
	 * Get the weather wind speed unit.
	 *
	 * @param string            $weather_units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 * @param string            $wind_speed_unit Can be either 'mph', 'kmh','kts'  or 'mph' (default). This affects almost all units returned.
	 * @param object|int|string $data The place to get weather information for. For possible values see below.
	 * @param string            $gust Can be either 'mph', 'kmh','kts'  or 'mph' (default). This affects almost all units returned.
	 * @return wind The weather object
	 */
	public static function get_wind_speed( $weather_units, $wind_speed_unit, $data, $gust = false ) {
		if ( $gust ) {
			$winds = $data->gusts->value;
		} else {
			$winds = $data->wind->speed->value;
		}
		if ( 'imperial' === $weather_units ) {
			switch ( $wind_speed_unit ) {
				case 'kmh':
					$wind = round( $winds * 1.61 ) . ' Km/h';
					break;
				default:
					$wind = round( $winds ) . ' mph';
					break;
			}
		} else {
			switch ( $wind_speed_unit ) {
				case 'kmh':
					$wind = round( $winds * 3.6 ) . ' Km/h';
					break;
				default:
					$wind = round( $winds * 2.2 ) . ' mph';
					break;
			}
		}
		return $wind;
	}

	/**
	 * Get the weather wind speed unit.
	 *
	 * @param string            $pressure_unit Can be either 'mb' or 'kpa' (default). This affects almost all units returned.
	 * @param object|int|string $data The place to get weather information for. For possible values see below.
	 * @return Pressure The weather object.
	 **/
	public static function get_pressure( $pressure_unit, $data ) {
		$pressures = $data->pressure->value;
		if ( 'hpa' === $pressure_unit ) {
			$pressure = round( $pressures ) . __( ' hPa', 'location-weather' );
		} else {
			$pressure = round( $pressures ) . __( ' mb', 'location-weather' );
		}
		return $pressure;
	}

	/**
	 * Get and format visibility data based on the specified unit.
	 *
	 * @param string   $visibility_unit The unit for visibility data ('km' or 'mi').
	 * @param stdClass $data           The weather data object containing visibility information.
	 *
	 * @return string Formatted visibility data based on the specified unit.
	 */
	public static function get_visibility( $visibility_unit, $data ) {
		$visibility_value = $data->visibility->value;

		if ( 'km' === $visibility_unit ) {
			$visibility = $visibility_value . __( ' km', 'location-weather' );
		} else {
			$visibility = round( $visibility_value * 0.621371 ) . __( ' mi', 'location-weather' );
		}
		return $visibility;
	}

	/**
	 * Get current weather data for a location.
	 *
	 * @param string $query        The location or query for weather data.
	 * @param string $units        The units for temperature and other weather data (e.g., 'metric' or 'imperial').
	 * @param string $lang         The language for weather data responses (e.g., 'en' for English).
	 * @param string $appid        The API key for authentication (optional).
	 * @param string $shortcode_id The shortcode ID (optional).
	 *
	 * @return CurrentWeather|array An instance of CurrentWeather containing weather data, or an error message as an array.
	 */
	public static function get_weather( $query, $units = 'imperial', $lang = 'en', $appid = '', $shortcode_id = '' ) {
		$answer    = self::get_raw_weather_data( $query, $units, $lang, $appid, 'xml', $shortcode_id );
		$value     = self::parse_xml( $answer );
		$arr_value = (array) $value;
		if ( isset( $value['cod'] ) && 401 === $value['cod'] ) {
			$value = array(
				'code'    => 401,
				'message' => 'Your API key is not activated yet. Remember that newly created API keys will need ~ 15 minutes to be activated and show data, so you might see an API error in the meantime. <br/>Or<br/> Invalid API key. Please see <a href="http://openweathermap.org/faq#error401" target="_blank">http://openweathermap.org/faq#error401</a> for more info.',
			);
			return $value;
		} elseif ( isset( $arr_value['message'] ) && 'city not found' === $arr_value['message'] ) {
			$value = array(
				'code'    => 404,
				'message' => esc_html__( 'Please set your valid city name and country code.', 'location-weather' ),
			);
			return $value;
		}
		return new CurrentWeather( $value, $units );
	}

	/**
	 * Returns the current weather for a group of city ids.
	 *
	 * @param array|int|string $query The place to get weather information for. For possible values see ::getWeather.
	 * @param string           $units Can be either 'metric' or 'imperial' (default). This affects almost all units returned.
	 * @param string           $lang  The language to use for descriptions, default is 'en'. For possible values see http://openweathermap.org/current#multi.
	 * @param string           $appid Your app id, default ''. See http://openweathermap.org/appid for more details.
	 * @param string           $mode  The format of the data fetched. Possible values are 'json', 'html' and 'xml' (default).
	 * @param string           $shortcode_id get the existing shortcode id from the page.
	 * @return CurrentWeather
	 *
	 * @api
	 */
	public static function get_raw_weather_data( $query, $units = 'imperial', $lang = 'en', $appid = '', $mode = 'xml', $shortcode_id = '' ) {
		$transient_name = 'sp_open_weather_data' . $shortcode_id;
		$weather_data   = self::splw_get_transient( $transient_name );
		// Check if the transient exists and has not expired.
		if ( false === $weather_data ) {
			$url      = self::build_url( $query, $units, $lang, $appid, $mode, self::$weather_url );
			$response = wp_remote_get( $url );
			$data     = wp_remote_retrieve_body( $response );
			// Save the data in the transient.
			if ( $data && strpos( $data, '"cod":401' ) === false ) {
				self::splw_set_transient( $transient_name, $data );
			}
		} else {
			$data = $weather_data;
		}
		return $data;
	}

	/**
	 * Directly returns the SimpleXMLElement string returned by OpenWeatherMap.
	 *
	 * @param string $answer The content returned by OpenWeatherMap  OpenWeatherMap.
	 *
	 * @throws LWException If the content isn't valid JSON.
	 */
	private static function parse_xml( $answer ) {

		// Disable default error handling of SimpleXML (Do not throw E_WARNINGs).
		libxml_use_internal_errors( true );
		try {
			return new \SimpleXMLElement( $answer );
		} catch ( \Exception $e ) {
			// Invalid xml format. This happens in case OpenWeatherMap returns an error.
			// OpenWeatherMap always uses json for errors, even if one specifies xml as format.
			$error = json_decode( $answer, true );
			if ( isset( $error['message'] ) ) {
				return $error;
			}
		}
		libxml_clear_errors();
	}

	/**
	 * Build a complete API URL for weather data retrieval.
	 *
	 * @param string $query   The location or query for weather data.
	 * @param string $units   The units for temperature and other weather data (e.g., 'metric' or 'imperial').
	 * @param string $lang    The language for weather data responses (e.g., 'en' for English).
	 * @param string $appid   The API key for authentication (optional, can be provided as a parameter or from a class property).
	 * @param string $mode    The mode for weather data (e.g., 'json' or 'xml').
	 * @param string $url     The base URL for the weather API.
	 *
	 * @return string The complete API URL for weather data retrieval.
	 */
	private static function build_url( $query, $units, $lang, $appid, $mode, $url ) {
		// Build the query URL parameter.
		$query_url = self::build_query_url_parameter( $query );

		// Build the complete API URL with all parameters.
		$url = $url . "$query_url&units=$units&lang=$lang&mode=$mode&APPID=";

		// Append the API key (either provided or from a class property).
		$url .= empty( $appid ) ? self::$api_key : $appid;

		return $url;
	}

	/**
	 * Builds the query string for the url.
	 *
	 * @param mixed $query query of the URL parameter.
	 *
	 * @return string The built query string for the url.
	 *
	 * @throws \InvalidArgumentException If the query parameter is invalid.
	 */
	private static function build_query_url_parameter( $query ) {
		switch ( $query ) {
			case is_array( $query ) && isset( $query['lat'] ) && isset( $query['lon'] ) && is_numeric( $query['lat'] ) && is_numeric( $query['lon'] ):
				return "lat={$query['lat']}&lon={$query['lon']}";
			case is_array( $query ) && is_numeric( $query[0] ):
				return 'id=' . implode( ',', $query );
			case is_numeric( $query ):
				return "id=$query";
			case is_string( $query ) && strpos( $query, 'zip:' ) === 0:
				$sub_query = str_replace( 'zip:', '', $query );
				return 'zip=' . urlencode( $sub_query );
			case is_string( $query ):
				return 'q=' . urlencode( $query );
			default:
				return "lat={$query['lat']}&lon={$query['lon']}";
		}
	}

	/**
	 * Custom set transient
	 *
	 * @param  mixed $cache_key Key.
	 * @param  mixed $cache_data data.
	 * @return void
	 */
	public static function splw_set_transient( $cache_key, $cache_data ) {
		$cache_expire_time = apply_filters( 'sp_open_weather_api_cache_time', 600 ); // 10 minutes
		if ( ! is_admin() ) {
			if ( is_multisite() ) {
				$cache_key = $cache_key . '_' . get_current_blog_id();
				set_site_transient( $cache_key, $cache_data, $cache_expire_time );
			} else {
				set_transient( $cache_key, $cache_data, $cache_expire_time );
			}
		}
	}

	/**
	 * Custom get transient.
	 *
	 * @param  mixed $cache_key Cache key.
	 * @return content
	 */
	public static function splw_get_transient( $cache_key ) {
		if ( is_admin() ) {
			return false;
		}

		if ( is_multisite() ) {
			$cache_key   = $cache_key . '_' . get_current_blog_id();
			$cached_data = get_site_transient( $cache_key );
		} else {
			$cached_data = get_transient( $cache_key );
		}
		return $cached_data;
	}

	/**
	 * Locates the template file for the specified template name.
	 *
	 * Searches for the template file in the given template path or falls back to the default path.
	 *
	 * @param string $template_name The name of the template file to locate.
	 * @param string $template_path Optional. The path where the template file should be searched. Defaults to 'location-weather-pro/templates'.
	 * @param  mixed  $default_path default path.
	 * @return string The path to the located template file.
	 */
	public static function lw_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = 'location-weather/templates';
		}
		if ( ! $default_path ) {
			$default_path = LOCATION_WEATHER_TEMPLATE_PATH . 'Frontend/templates/';
		}
		$template = locate_template( trailingslashit( $template_path ) . $template_name );
		// Get default template.
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}
		// Return what we found.
		return $template;
	}
}
