<?php
namespace BAddon;

use BAddon\Widgets\bae_pdf_native_embedder;
use BAddon\Widgets\bae_doc_embedder;
use BAddon\Widgets\bae_excel_embedder;
use BAddon\Widgets\bae_pp_embedder;
use BAddon\Widgets\bae_word_viewer;
use BAddon\Widgets\bae_excel_viewer;
use BAddon\Widgets\bae_powerpoint_viewer;
use BAddon\Widgets\bae_google_docs;
use BAddon\Widgets\bae_google_sheets;
use BAddon\Widgets\bae_google_slides;
/**
 * Class Plugin
 *
 * Main Plugin class
 * @since 1.2.0
 */
class Bae_BAddon {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2.0
	 * @access private
	 */
	private function include_widgets_files() {
		require_once( __DIR__ . '/public/widgets/bae-pdf-native-embedder.php' );
		require_once( __DIR__ . '/public/widgets/bae-doc-embedder.php' );
		require_once( __DIR__ . '/public/widgets/bae-excel-embedder.php' );
		require_once( __DIR__ . '/public/widgets/bae-powerpoint-embedder.php' );
		require_once( __DIR__ . '/public/widgets/bae-word-viewer.php' );
		require_once( __DIR__ . '/public/widgets/bae-excel-viewer.php' );
		require_once( __DIR__ . '/public/widgets/bae-powerpoint-viewer.php' );
		require_once( __DIR__ . '/public/widgets/bae-google-docs.php' );
		require_once( __DIR__ . '/public/widgets/bae-google-sheets.php' );
		require_once( __DIR__ . '/public/widgets/bae-google-slides.php' );
	}

	//editor scripts
	function editor_scripts() {
		wp_register_style("ua-aa",plugins_url("/admin/assets/css/style.css",__FILE__));
		wp_enqueue_style( 'ua-aa' );
	}
	
	/**
	 * widget_styles
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function widget_styles(){

		wp_register_style("bae-main",plugins_url("/admin/assets/css/main.css",__FILE__));
		wp_enqueue_style( 'bae-main' );
	}
	
	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_widgets() {
		// Its is now safe to include Widgets files
		$this->include_widgets_files();

		// Register Widgets
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_doc_embedder() ); 
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_word_viewer() ); 
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_excel_embedder() );
		 \Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_excel_viewer() );
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_pp_embedder() );  
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_powerpoint_viewer() ); 
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_google_docs() );  
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_google_sheets() );  
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_google_slides() );  
		\Elementor\Plugin::instance()->widgets_manager->register( new Widgets\bae_pdf_native_embedder() ); 
	}
	
	//category registered
	public function add_elementor_widget_categories( $elements_manager ) {

		$elements_manager->add_category(
			'b-addon',
			[
				'title' => __( 'B Addon', 'b-addon' ),
				'icon' => 'fa fa-plug',
			]
		);
	}

	public function frontend_assets_scripts(){
		wp_register_script( 'b-addon-script', plugin_dir_url( __FILE__ ). 'public/js/script.js' , array(), '1.0.1', true );
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		// Enqueue widget styles
        add_action( 'elementor/frontend/after_register_styles', [ $this, 'widget_styles' ] , 100 );
        add_action( 'admin_enqueue_scripts', [ $this, 'widget_styles' ] , 100 );

		//Register Frontend Script
		add_action( "elementor/frontend/after_register_scripts", [ $this, 'frontend_assets_scripts' ] );
        
		// Register widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		//category registered
		add_action( 'elementor/elements/categories_registered',  [ $this,'add_elementor_widget_categories' ]);
		add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'editor_scripts' ] );
		

	}
}

// Instantiate Plugin Class
Bae_BAddon::instance();
