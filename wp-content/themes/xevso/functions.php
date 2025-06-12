<?php
/**
 * xevso functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package xevso 
 */
define( "xevso_VERSION", time() );
define( "xevso_ASSETS_DIR", get_template_directory_uri() . "/assets/" );
define( "xevso_FILE_DIR", get_template_directory() . "/" );
require_once xevso_FILE_DIR . 'inc/function/theme-setup.php';
require_once xevso_FILE_DIR . 'inc/function/theme-widget.php';
require_once xevso_FILE_DIR . 'inc/function/theme-filter.php';

/**
 * TGM Plugin 
 */
require_once xevso_FILE_DIR . 'inc/plugins-activation.php';
/**
 * Demo Content 
 */
require_once xevso_FILE_DIR . 'inc/demo.php';
/**
 * Blog Comment List
 */
require_once xevso_FILE_DIR . 'inc/comments-list.php';
/**
 * Enqueue scripts and styles.
 */
require_once xevso_FILE_DIR . 'inc/theme-style.php';
/**
 * Implement the Custom Header feature.
 */
require_once xevso_FILE_DIR . 'inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require_once xevso_FILE_DIR . 'inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require_once xevso_FILE_DIR . 'inc/template-functions.php';
require_once xevso_FILE_DIR . 'inc/xevso-default-options.php';

/**
 * Customizer additions.
 */
require_once xevso_FILE_DIR . 'inc/customizer.php';
require_once xevso_FILE_DIR . 'inc/theme-and-options/ini.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require_once xevso_FILE_DIR . 'inc/jetpack.php';
}
/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require_once xevso_FILE_DIR . 'inc/woocommerce.php';
}
if( class_exists( 'CSF' ) ) {
	require_once xevso_FILE_DIR . 'inc/theme-and-options/metabox-and-options.php';
	require_once xevso_FILE_DIR . 'inc/css.php';
	require_once xevso_FILE_DIR . 'inc/js.php';
}



function load_bootstrap_and_custom_css() {
    // Load Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css');
	  wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js');

    // Load Bootstrap JS
    wp_enqueue_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js', array('jquery'), null, true);

    // Load Custom CSS
//     wp_enqueue_style('custom-style', get_stylesheet_directory_uri() . '/custom-style.css');
}
add_action('wp_enqueue_scripts', 'load_bootstrap_and_custom_css');


function allow_json_uploads($mimes) {
    $mimes['json'] = 'application/json';
    return $mimes;
}
add_filter('upload_mimes', 'allow_json_uploads');


/**
 * Feedback
 * */
function add_feedback_image() {
    $feedback_image_url = get_site_url() . '/wp-content/uploads/2025/01/feedback_simple.gif';

    // Use the post ID of the "contact-us" page in the default language (e.g., English)
    $default_contact_page_id = 614; // ← Replace this with the actual page ID of "contact-us" in your WordPress

    // Get the correct translation ID for the current language
    if (function_exists('pll_get_post')) {
        $translated_id = pll_get_post($default_contact_page_id);
        $feedback_url = get_permalink($translated_id);
    } else {
        $feedback_url = get_permalink($default_contact_page_id); // fallback
    }

    ?>
    <div id="feedback-image" style="position: fixed; bottom: 150px; right: 0px; z-index: 9999;">
        <a href="<?php echo esc_url($feedback_url); ?>" target="_blank" title="Give Feedback">
            <img src="<?php echo esc_url($feedback_image_url); ?>" alt="Feedback">
        </a>
    </div>
    <?php
}
add_action('wp_footer', 'add_feedback_image');



/**
 * Station chart
 * */
function enqueue_chart_js() {
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', [], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_chart_js');

function render_station_chart() {
	 if (!(is_page('live-calls') || is_page('रिअल टाइम घटना सूचना'))) {
        return ''; 
    }
    ob_start(); ?>
    <canvas id="stationChart" width="400" height="500"></canvas>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fetch live call data
            fetch('https://dfes.goa.gov.in/disaster-management/live-calls/data')
                .then(response => response.json())
                .then(data => {
                    // Process data: Count calls by station and types
                    const stationTypeCounts = {};
                    const colorMap = {
                        "Fire related": "rgba(255, 0, 0)",    // Red for Fire
                        "Emergency/ Accidents": "rgb(0, 0, 255)",        // Blue for Accident
                        "Meteorological": "rgb(255, 149, 0)", // 
                        "Biological": "	rgb(0, 255, 238)", // 
                        "Climatological": "	rgb(5, 15, 64)",         
						 "Hydrological": "rgb(255, 247, 0)",
						 "Geophysical": "rgb(92, 17, 12)",
						 "Others": "	rgb(0, 0, 0)"
                    };

                    // Group by stations and count incidents
                    data.forEach(call => {
                        const station = call.station || 'Unknown Station';
                        const type = call.type || 'Other';

                        if (!stationTypeCounts[station]) {
                            stationTypeCounts[station] = { total: 0, types: {} };
                        }

                        stationTypeCounts[station].total += 1;
                        stationTypeCounts[station].types[type] = 
                            (stationTypeCounts[station].types[type] || 0) + 1;
                    });

                    // Prepare data for the chart
                    const labels = Object.keys(stationTypeCounts); // Station names
                    const dataset = [];

                    // Generate datasets for each type
                    Object.keys(colorMap).forEach(type => {
                        const data = labels.map(station => 
                            (stationTypeCounts[station].types[type] || 0)
                        );

                        dataset.push({
                            label: type,
                            data: data,
                            backgroundColor: colorMap[type],
                            borderColor: colorMap[type],
                            borderWidth: 1
                        });
                    });

                    // Render the chart
                    const ctx = document.getElementById('stationChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar', // You can use 'pie' or 'doughnut' for other views
                        data: {
                            labels: labels,
                            datasets: dataset
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching API data:', error));
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('station_calls_chart', 'render_station_chart');

function render_taluka_chart() {
    if (!(is_page('live-calls') || is_page('रिअल टाइम घटना सूचना'))) {
        return ''; 
    }
    ob_start(); ?>
    <canvas id="talukaChart" width="400" height="500"></canvas>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fetch live call data
            fetch('https://dfes.goa.gov.in/disaster-management/live-calls/data')
                .then(response => response.json())
                .then(data => {
                    // Process data: Count calls by taluka and types
                    const talukaTypeCounts = {};
                    const colorMap = {
                        "Fire related": "rgba(255, 0, 0)",    // Red for Fire
                        "Emergency/ Accidents": "rgb(0, 0, 255)",        // Blue for Accident
                        "Meteorological": "rgb(255, 149, 0)", // 
                        "Biological": "rgb(0, 255, 238)", 
                        "Climatological": "rgb(5, 15, 64)",         
                        "Hydrological": "rgb(255, 247, 0)",
                        "Geophysical": "rgb(92, 17, 12)",
                        "Others": "rgb(0, 0, 0)"
                    };

                    // Group by taluka and count incidents
                    data.forEach(call => {
                        const taluka = call.taluka || 'Unknown Taluka';
                        const type = call.type || 'Other';

                        if (!talukaTypeCounts[taluka]) {
                            talukaTypeCounts[taluka] = { total: 0, types: {} };
                        }

                        talukaTypeCounts[taluka].total += 1;
                        talukaTypeCounts[taluka].types[type] = 
                            (talukaTypeCounts[taluka].types[type] || 0) + 1;
                    });

                    // Prepare data for the chart
                    const labels = Object.keys(talukaTypeCounts); // Taluka names
                    const dataset = [];

                    // Generate datasets for each type
                    Object.keys(colorMap).forEach(type => {
                        const data = labels.map(taluka => 
                            (talukaTypeCounts[taluka].types[type] || 0)
                        );

                        dataset.push({
                            label: type,
                            data: data,
                            backgroundColor: colorMap[type],
                            borderColor: colorMap[type],
                            borderWidth: 1
                        });
                    });

                    // Render the chart
                    const ctx = document.getElementById('talukaChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar', // You can use 'pie' or 'doughnut' for other views
                        data: {
                            labels: labels,
                            datasets: dataset
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching API data:', error));
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('taluka_calls_chart', 'render_taluka_chart');


function render_category_chart() {
	 if (!(is_page('live-calls') || is_page('रिअल टाइम घटना सूचना'))) {
        return ''; 
    }
    ob_start(); ?>
    <canvas id="categoryChart" width="400" height="200"></canvas>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Fetch live call data
            fetch('https://dfes.goa.gov.in/disaster-management/live-calls/data')
                .then(response => response.json())
                .then(data => {
                    // Process data: Count calls by categories
                    const categoryCounts = {};
                    const colorMap = {
                        "Fire related": "rgba(255, 0, 0)",    // Red for Fire
                        "Emergency/ Accidents": "rgb(0, 0, 255)",        // Blue for Accident
                        "Meteorological": "rgb(255, 149, 0)", // 
                        "Biological": "rgb(0, 255, 238)", 
                        "Climatological": "rgb(5, 15, 64)",         
                        "Hydrological": "rgb(255, 247, 0)",
                        "Geophysical": "rgb(92, 17, 12)",
                        "Others": "rgb(0, 0, 0)"
                    };

                    // Group by category and count incidents
                    data.forEach(call => {
                        const type = call.type || 'Others';

                        categoryCounts[type] = (categoryCounts[type] || 0) + 1;
                    });

                    // Prepare data for the chart
                    const labels = Object.keys(categoryCounts); // Categories
                    const dataset = [{
                        label: 'Incident Count',
                        data: Object.values(categoryCounts),
                        backgroundColor: labels.map(label => colorMap[label] || 'rgba(128, 128, 128, 0.6)'),
                        borderColor: labels.map(label => colorMap[label] || 'rgba(128, 128, 128, 1)'),
                        borderWidth: 1
                    }];

                    // Render the chart
                    const ctx = document.getElementById('categoryChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie', // You can use 'bar', 'pie', or 'doughnut'
                        data: {
                            labels: labels,
                            datasets: dataset
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top'
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching API data:', error));
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('category_calls_chart', 'render_category_chart');

/**
 * Date
 * */
// function display_current_date() {
//     return date('d M, Y');
// }
// add_shortcode('current_date', 'display_current_date');

/**
 * Alert Box
 * */

function add_external_link_confirmation_script() {
    // Enqueue jQuery if it's not already loaded
    wp_enqueue_script('jquery'); // Ensures jQuery is loaded

    // Add custom JavaScript
    wp_add_inline_script('jquery', '
        jQuery(document).on("click", "a", function(e) {
            var link = jQuery(this);
            var href = link.attr("href");

            // Check if it\'s an external link
            if (href && !href.startsWith("mailto:") && !href.startsWith("tel:")&&!href.startsWith("https://www.youtube.com/watch?v=HTWmLUyOk_k")   && link[0].hostname !== window.location.hostname) {
                
                // Custom confirmation message with all three languages
                var message = "This link will take you to an external website!\n\n" +
                              "ही लिंक तुम्हाला बाह्य वेब साइटवर घेऊन जाईल!\n\n" +
                              "यह लिंक आपको एक बाहरी वेब साइट पर ले जाएगा।";

                // Show the confirmation box with all the languages
                var confirmLeave = confirm(message);

                if (!confirmLeave) {
                    e.preventDefault();
                }
            }
        });
    ');
}
add_action('wp_enqueue_scripts', 'add_external_link_confirmation_script');


// add_filter('bcn_after_fill', 'remove_custom_breadcrumbs');
// function remove_custom_breadcrumbs($trail) {
//     foreach ($trail->breadcrumbs as $key => $crumb) {
//         if (in_array($crumb->get_title(), ['Test', 'PUBLIC DISCLOSURES', 'Content Archive'])) {
//             unset($trail->breadcrumbs[$key]);
//         }
//     }
//     $trail->breadcrumbs = array_values($trail->breadcrumbs); // Reindex array
//     return $trail;
// }

function enqueue_leaflet_assets() {
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_leaflet_assets');

function dmrp_leaflet_map_shortcode() {
    ob_start();
    ?>
    <div id="map-container" style="display: flex ;height: 600px; width: 100%;">
        <div id="filter-container" style="width: 350px; background-color: #f8f9fa; padding: 20px; border-right: 1px solid #ccc; box-shadow: 2px 0 5px rgba(0,0,0,0.1); overflow-y: auto; font-family: Arial, sans-serif;">
            <div id="filter-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Filter Categories</h3>
                <button id="select-none" style="background: none; border: none; color: #2072AF; cursor: pointer; font-size: 14px;">Select None</button>
            </div>
            <div id="filter-controls" style="margin-top: 10px;"></div>
            <button id="apply-filter" style="margin-top: 15px; display: block; width: 100%; padding: 12px; background: #2072AF; color: white; border: none; font-size: 16px; cursor: pointer; transition: 0.3s;">Apply</button>
        </div>
        <div id="dmrpmap" style="flex: 1;"></div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('dmrp_map', 'dmrp_leaflet_map_shortcode');


function dmrp_leaflet_map_init() {
    if (is_singular() && has_shortcode(get_post()->post_content, 'dmrp_map')) {
        wp_enqueue_script('dmrp-map-init', get_template_directory_uri() . '/js/dmrp-map.js', array('leaflet-js'), null, true);
    }
}
add_action('wp_enqueue_scripts', 'dmrp_leaflet_map_init');

function enqueue_leaflet_cluster_scripts() {
    // Load Leaflet core
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css');
    wp_enqueue_script('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js', [], null, true);

    // Load MarkerCluster plugin
    wp_enqueue_style('leaflet-markercluster-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css');
    wp_enqueue_style('leaflet-markercluster-default-css', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css');
    wp_enqueue_script('leaflet-markercluster-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js', ['leaflet-js'], null, true);

    // Your custom script (placed in your theme or plugin directory)
//     wp_enqueue_script('custom-live-map', get_stylesheet_directory_uri() . '/js/live-map.js', ['leaflet-js', 'leaflet-markercluster-js'], null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_leaflet_cluster_scripts');



