<?php

namespace PostTimeline;

use PostTimeline\Loader;
use PostTimeline\Frontend\App;
use PostTimeline\Admin\Manager;
use PostTimeline\components\Like;
use PostTimeline\Admin\Feedback;


/**
 * The file that defines the core plugin class
 *
 * @since      5.5
 *
 * @package    Post Timeline
 * @subpackage PostTimeline/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      5.5
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     agilelogix <support@agilelogix.com>
 */
class Plugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      PostTimeline\Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $public_plugin 
	 */
	protected $public_plugin;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $admin_plugin 
	 */
	protected $admin_plugin;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $like_plugin 
	 */
	protected $like_plugin;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $feedback_plugin 
	 */
	protected $feedback_plugin;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function __construct() {

		$this->plugin_name 	= 'post-timeline';
		$this->version 			= POST_TIMELINE_VERSION;

		$this->load_dependencies();

		$this->set_locale();
		
		//	Define constants
		$this->define_constants();

		$this->public_plugin 		= new App( $this->get_plugin_name(), $this->get_version() );
		$this->admin_plugin 		= new Manager( $this->get_plugin_name(), $this->get_version() );
		$this->like_plugin 			= new Like( $this->get_plugin_name(), $this->get_version() );
		$this->feedback_plugin 	= new Feedback( $this->get_plugin_name(), $this->get_version() );

		$this->define_admin_hooks();
		
		$this->define_public_hooks();

		$this->define_3rd_party();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PostTimeline\Loader. Orchestrates the hooks of the plugin.
	 * - PostTimeline\i18n. Defines internationalization functionality.
	 * - PostTimeline\Admin. Defines all hooks for the admin area.
	 * - PostTimeline\Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for autoloading the classes 
		 */
		require_once POST_TIMELINE_PLUGIN_PATH . '/includes/autoloader.php';

		Autoloader::run();
		
		$this->loader = new Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PostTimeline\i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new \PostTimeline\i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_admin_hooks() {

		// $this->admin_plugin = new PostTimeline\Admin( $this->get_plugin_name(), $this->get_version() );

		//add_action('admin_menu', array($this,'add_admin_menu'));
	  $this->loader->add_action( 'init', $this->admin_plugin, 'register_post_timeline' );

		//add_action('wp_ajax_nopriv_ajax_post_timeline_deactivate_feedback',  array($this->feedback_plugin,'ajax_post_timeline_deactivate_feedback'));

	  if(is_admin()) {
		    
	  	add_action('wp_ajax_ajax_post_timeline_deactivate_feedback',  array($this->feedback_plugin,'ajax_post_timeline_deactivate_feedback'));
			
			require_once POST_TIMELINE_PLUGIN_PATH.'admin/blocks/index.php';

			$this->save_google_fonts();

			require_once POST_TIMELINE_PLUGIN_PATH.'includes/helper.php';

			$this->loader->add_filter( 'ot_header_logo_link', $this->admin_plugin, 'filter_header_logo_link',100 );
			$this->loader->add_filter( 'ot_header_version_text', $this->admin_plugin, 'filter_header_version_text' ,100);
			$this->loader->add_filter( 'ot_list_item_title_label', $this->admin_plugin, 'filter_post_list_item_title_label', 10, 2 );

			//add_action('wp_ajax_render_template', array($this->admin_plugin, 'render_template'));	

			// add meta box to admin page
			add_action('add_meta_boxes', array($this->admin_plugin, 'fields_metabox'));

			// Save Meta Data admin page
			add_action('save_post', array($this->admin_plugin, 'save_ptl_meta_fields'));
			
  		//	Initialize the Admin Ajax Request handler
			$admin_router = new \PostTimeline\Admin\AjaxHandler();
			
			//	Add all the admin routes
			$admin_router->add_routes();

		}
	}

		
	/**
	 * [define_3rd_party Integration with 3rd party plugins]
	 * @return [type] [description]
	 */
	private function define_3rd_party() {
		
		//	Add the Elementor
		if ( class_exists( '\Elementor\Plugin' ) ) {
      
      		$elementor_addon = new \PostTimeline\Vendors\Elementor\Addon(); 
    	}
    
	}	

  /**
	 * Setup plugin constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function define_constants() {

	  global $wpdb;

	  define( 'PTL_PREFIX', $wpdb->prefix."ptl_" );

	}
	/**
	 * [save_google_fonts Save the Google Fonts if not exist]
	 * @return [type] [description]
	 */
	private function save_google_fonts() {

		$ot_google_fonts = get_theme_mod( 'ot_google_fonts', array() );

		if ( empty( $ot_google_fonts ) ) {
			
			$ot_google_fonts = file_get_contents(POST_TIMELINE_PLUGIN_PATH.'/includes/gfonts.json');
			$ot_google_fonts = json_decode($ot_google_fonts, true);

			$ot_google_fonts_cache_key = apply_filters( 'ot_google_fonts_cache_key', 'ot_google_fonts_cache' );

			set_theme_mod( 'ot_google_fonts', $ot_google_fonts );
			set_transient( $ot_google_fonts_cache_key, $ot_google_fonts, WEEK_IN_SECONDS );
		}
	}



	/*All Admin Callbacks*/
	public function add_admin_menu() {

		//activate_plugins
		if (current_user_can('delete_posts')) {
			

			$svg = 'dashicons-folder';

		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = $this->public_plugin;


		$this->loader->add_action( 'init', $plugin_public, 'add_shortcodes' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'single_temp_style' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'register_scripts' );
		$this->loader->add_action( 'wp_head', $plugin_public, 'output_header_css' );
		
		//////////////////////
		///////Public Hooks //
		//////////////////////
		add_action('wp_ajax_ptl_load_posts', array($this->public_plugin, 'ajax_load_posts'));	
		add_action('wp_ajax_nopriv_ptl_load_posts', array($this->public_plugin, 'ajax_load_posts'));

		add_action('wp_ajax_ptl_popup_gallery', array($this->public_plugin, 'popup_gallery'));	
		add_action('wp_ajax_nopriv_ptl_popup_gallery', array($this->public_plugin, 'popup_gallery'));

		add_action('wp_ajax_timeline_ajax_load_posts',  array($this->public_plugin,'timeline_ajax_load_posts'));
		add_action('wp_ajax_nopriv_timeline_ajax_load_posts',  array($this->public_plugin,'timeline_ajax_load_posts'));

		/* Like Button Hooks*/
		add_action('wp_ajax_ptl_save_post_like', array($this->like_plugin, 'save_post_like'));	
		add_action('wp_ajax_nopriv_ptl_save_post_like', array($this->like_plugin, 'save_post_like'));		
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.0.1
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     0.0.1
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     0.0.1
	 * @return    PostTimeline\Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     0.0.1
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
