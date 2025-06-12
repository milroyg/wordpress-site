<?php

/**
 * Plugin Name:       Post Timeline
 * Plugin URI:        https://posttimeline.com/
 * Description:       Create, enhance & display unlimited beautiful Post Timeline variations such as Vertical, One Side, Gutenberg & Elementor Timelines on your website.
 * Version:           2.3.11
 * Author:            AgileLogix
 * Author URI:        https://posttimeline.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-timeline
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}




if ( !class_exists( 'Post_Timeline' ) ) {

  class Post_Timeline {
        
    /**
     * Class constructor
     */          
    function __construct() {
                                
        $this->define_constants();
        $this->includes();

        register_activation_hook( __FILE__, array( $this, 'activate') );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate') );
    }
    
    /**
     * Setup plugin constants.
     *
     * @since 1.0.0
     * @return void
     */
    public function define_constants() {

      global $wpdb;

      $upload_dir       = wp_upload_dir();
      
      define( 'POST_TIMELINE_URL_PATH', plugin_dir_url( __FILE__ ) );
      define( 'POST_TIMELINE_PLUGIN_PATH', plugin_dir_path(__FILE__) );
      define( 'POST_TIMELINE_VERSION', "2.3.11" );
      define( 'POST_TIMELINE_BASE_PATH', dirname( plugin_basename( __FILE__ ) ) );
      define( 'POST_TIMELINE_SLUG', 'post-timeline');
      define( 'POST_TIMELINE_EMAIL', 'support@agilelogix.com');
      define( 'POST_TIMELINE_SITE', 'https://posttimeline.com/');
      define( 'POST_TIMELINE_PRO_LNK', 'https://agilelogix.com/product/post-timeline/?utm_source=WordPress&utm_medium=Banner&utm_campaign=WP.org');
    }
    
    /**
     * Include the required files.
     *
     * @since 1.0.0
     * @return void
     */
    public function includes() {


      require_once POST_TIMELINE_PLUGIN_PATH . 'includes/plugin.php';
      
      $asl_core = new \PostTimeline\Plugin();
      $asl_core->run();
    }
    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-agile-store-locator-activator.php
     */
    public function activate() {
      
      \PostTimeline\Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-agile-store-locator-deactivator.php
     */
    public function deactivate() {
      
      \PostTimeline\Deactivator::deactivate();
    }
  }


  $asl_ptl = new Post_Timeline();
}