<?php

namespace PostTimeline\Admin;


// use PostTimeline\Admin\Feedback;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    PostTimeline
 * @subpackage PostTimeline/admin
 * @author     agilelogix <support@agilelogix.com>
 */
class Manager {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
	
	/**
	 * The settings of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $settings    The settings of this plugin.
	 */
	private $settings;

	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $core_plugin 
	 */
	protected $core_plugin;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $default_settings 
	 */
	protected $default_settings;
	/**
	 * @since    0.0.1
	 * @access   protected
	 * @var      string    $scripts_data 
	 */
	protected $scripts_data;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = function_exists('wp_get_environment_type') && wp_get_environment_type() == 'development' ? time(): $version;

		$this->core_plugin = new \PostTimeline\Core( $this->plugin_name, $this->version );
		
		$this->admin_hooks();

		// Change Pricing if option is enable
		$this->default_settings = \PostTimeline\Helper::get_default_settings();
		
		$options 				= get_option('post_timeline_global_settings');
		$this->settings = (!empty($options) ? array_merge($this->default_settings, $options) :  $this->default_settings );
	}


	/**
	 * [admin_hooks]
	 * Define all admin hooks
	 *
	 * @since    0.0.1
	 */
	public function admin_hooks()
	{
		global $pagenow;

		add_action('admin_menu', array($this,'ptl_menu'));		

		add_filter('manage_edit-post-timeline_columns',array(&$this,'timeline_columns'));
		add_action( 'manage_post-timeline_posts_custom_column', array(&$this,'timeline_column_details'), 10, 2 );

		// ptl tag sorting by term_order 
		add_filter( 'get_terms', array($this, 'ptl_tag_get_object_terms') , 10, 3 );

		// add ptl_drag column to ptl_tag 
		add_filter('manage_edit-ptl_tag_columns', array(&$this,'manage_tag_columns') );

		// add ptl_drag column fields to ptl_tag 
		add_filter ('manage_ptl_tag_custom_column', array(&$this,'manage_tag_column_field'), 10,3);

		
		$post_type 	= (isset($_GET['post_type'])) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : '';
        $page 		    = (isset($_GET['page'])) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        $taxonomy 	 = (isset($_GET['taxonomy'])) ? sanitize_text_field(wp_unslash($_GET['taxonomy'])) : '';

		if (($post_type == 'post-timeline' && $pagenow == 'edit.php' ) || $pagenow == 'post.php' || $pagenow == 'post-new.php' || $pagenow == 'plugins.php' || $page == 'ptl-dashboard' || ($pagenow == 'edit-tags.php' && $taxonomy == 'ptl_tag') ) {
			add_action('admin_enqueue_scripts', array($this, 'register_assets')); 
		}
		// Core File hooks
		add_action('media_buttons', array($this->core_plugin,'add_shortcode_button'), 15); 
		add_action('admin_head', array($this->core_plugin,'shortcode_gen_popup'));
		add_filter('register_post_type_args', array($this->core_plugin,'change_post_slug'), 10, 2 );
		add_action('do_meta_boxes', array($this->core_plugin,'remove_default_custom_fields_meta_box'), 1, 3 );
		add_filter('wp_terms_checklist_args', array($this->core_plugin,'ptl_taglist_radio') );
		add_filter('posts_orderby', array($this->core_plugin,'ptl_tag_orderby'), 10, 2 );

		// Add PTL Category and Tag Filters Select in admin page
		add_action('restrict_manage_posts',array($this->core_plugin, 'ptl_custom_filter_admin'));

		// Query PTL Category Filter Select in admin page
		add_action('pre_get_posts',array($this->core_plugin, 'ptl_custom_filter_list'));

		add_filter('get_user_option_edit_ptl_tag_per_page', function ($result, $option, $user) {
            return 9999; // Adjust this number as needed
        }, 10, 3);

	}

	/**
	 * [ptl_menu]
	 * Register Menus 
	 *
	 * @since    0.0.1
	 */
	public function ptl_menu() {

		wp_enqueue_style( 'wp-color-picker' );
		
  	add_submenu_page( '/edit.php?post_type=post-timeline', 'Dashboard', 'Dashboard', 'delete_posts', 'ptl-dashboard', array($this,'admin_template_page'),-10);

  	add_submenu_page( '/edit.php?post_type=post-timeline','Settings', 'Timeline Settings', 'delete_posts', 'timeline-settings', array($this, 'ptl_settings_dashboard'));

  	add_submenu_page( 'options-writing.php','Create Custom Post Types', 'Create Custom Post Type', 'delete_posts', 'ptl-cpt-form', array($this, 'ptl_create_posttypes'));
	}

	function validate_text_input($post_id) {
		
		global $errors;

		if (!isset($_POST['ptl-post-date']) || $_POST['ptl-post-date'] == '') {
			set_transient( 'post-timeline-err', 'Please add Post Date for the Timeline', 30 );
			return false;
		}

	  return true;
	}


	function my_admin_notice() {

		if ( $error = get_transient( "post-timeline-err" ) ) { ?>
		    <div class="error">
		        <p><?php echo $error; ?></p>
		    </div><?php

		    delete_transient("post-timeline-err");
		}
		
	}

	/**
	 * Register Timeline custom post type
	 *
	 * @since    0.0.1
	 */
	public function register_post_timeline() {

    $labels = array(
      'name'               => _x( 'Post Timelines', 'post type general name', 'post-timeline' ),
      'singular_name'      => _x( 'Post Timeline', 'post type singular name', 'post-timeline' ),
      'menu_name'          => _x( 'Post Timelines', 'admin menu', 'post-timeline' ),
      'name_admin_bar'     => _x( 'Post Timeline', 'add new on admin bar', 'post-timeline' ),
      'add_new'            => _x( 'Add New', 'Timeline Post', 'post-timeline' ),
      'add_new_item'       => __( 'Add New Timeline Post', 'post-timeline' ),
      'new_item'           => __( 'New Timeline Post', 'post-timeline' ),
      'edit_item'          => __( 'Edit Timeline Post', 'post-timeline' ),
      'view_item'          => __( 'View Timeline Post', 'post-timeline' ),
      'all_items'          => __( 'All Timeline Post', 'post-timeline' ),
      'search_items'       => __( 'Search Timelines', 'post-timeline' ),
      'parent_item_colon'  => __( 'Parent Timelines:', 'post-timeline' ),
      'not_found'          => __( 'No timelines found.', 'post-timeline' ),
      'not_found_in_trash' => __( 'No timelines found in Trash.', 'post-timeline' ),
      "parent"  					 => __( 'Parent Timeline', 'post-timeline' ),
    );

    $args = array(
      'labels'            => $labels,
      'public'            => true,
      'publicly_queryable'=> true,
      'show_ui'           => true,
      'show_in_menu'      => true,
      'query_var'         => true,
      'rewrite'           => array( 'slug' => POST_TIMELINE_SLUG ),
      'capability_type'   => 'post',
      'has_archive'       => true,
      'hierarchical'      => true,
      'taxonomies'		  	=> array('topics','ptl_tag'),
      'exclude_from_search' => false,
      'menu_position'     	=> 5,
      'menu_icon'         	=> POST_TIMELINE_URL_PATH . 'admin/images/dashboard/timeline-block.png',
      'supports'          	=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'page-attributes'),
    );

  	register_post_type( 'post-timeline', $args );

  	register_taxonomy(
	    'ptl_categories',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	    'post-timeline',             // post type name
	    array(
	        'hierarchical' => true,
	        'label' => 'Categories', // display name
	        'query_var' => true,
	        'rewrite' => array(
	            'slug' => 'ptl_category',    // This controls the base slug that will display before each term
	            'with_front' => false  // Don't display the category base before
	        )
	    )
  	);
  	register_taxonomy(
	    'ptl_tag',  // The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces).
	    'post-timeline',             // post type name
	    array(
	        'hierarchical' => true,
	        'labels' => array(
		        'name' => 'Tags',
		        'add_new'            => _x( 'Add New Tag', 'timeline', 'post-timeline' ),
		        'add_new_item'       => __( 'Add New Tag', 'post-timeline' ),
		        'new_item'           => __( 'New Tag', 'post-timeline' ),
		        'edit_item'          => __( 'Edit Tag', 'post-timeline' ),
		        'view_item'          => __( 'View Tag', 'post-timeline' ),
		        'all_items'          => __( 'All Tags', 'post-timeline' ),
		        'search_items'       => __( 'Search Tag', 'post-timeline' ),
		        'parent_item_colon'  => __( 'Parent Tag:', 'post-timeline' ),
		        'not_found'          => __( 'No Tag found.', 'post-timeline' ),
		        'not_found_in_trash' => __( 'No Tag found in Trash.', 'post-timeline' ),
		        "parent"  					 => __( 'Parent Tag', 'post-timeline' ),
		        ), // display name
	        'query_var' => true,
	        'rewrite' => array(
	            'slug' => 'ptl_tag',    // This controls the base slug that will display before each term
	            'with_front' => false  // Don't display the category base before
	        )
	    )
  	);
  
  	add_filter( 'template_include', array($this,'include_template_function'), 1 );
	}

	/*Multiple Template Page*/
	function admin_template_page() {
		
		$ptl_templates =  array(
			array('id' => '0',   'template_name' => 'Template 0', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-0.png'),
			array('id' => '1', 	 'template_name' => 'Template 1', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1.png'),
			array('id' => '2',   'template_name' => 'Template 2', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2.png'),
			array('id' => '3',   'template_name' => 'Template 3', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-3.png'),
			array('id' => '4',   'template_name' => 'Template 4', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-4.png'),
			array('id' => '5',   'template_name' => 'Template 5', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5.png'),
			array('id' => '6',   'template_name' => 'Template 6', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6.png'),
			array('id' => '7',   'template_name' => 'Template 7', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-7.png'),
			array('id' => '8',   'template_name' => 'Template 8', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-8.png'),
			
			array('id' => '1', 	 'template_name' => 'Template 1 One Side', 'class'=> 'one-side-left', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1.png'),
			array('id' => '2',   'template_name' => 'Template 2 One Side', 'class'=> 'one-side-left', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2.png'),
			array('id' => '5',   'template_name' => 'Template 5 One Side', 'class'=> 'one-side-left', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5.png'),
			array('id' => '6',   'template_name' => 'Template 6 One Side',  'class'=> 'one-side-left', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6.png'),
			array('id' => '8',   'template_name' => 'Template 8 One Side', 'class' => 'one-side-left', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-8.png'),
			
			array('id' => '0-h', 'template_name' => 'Template 0 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-0h.png'),
			array('id' => '1-h', 'template_name' => 'Template 1 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1h.png'),
			array('id' => '2-h', 'template_name' => 'Template 2 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2h.png'),
			array('id' => '3-h', 'template_name' => 'Template 3 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-3h.png'),
			array('id' => '4-h', 'template_name' => 'Template 4 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-4h.png'),
			array('id' => '5-h', 'template_name' => 'Template 5 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5h.png'),
			array('id' => '6-h', 'template_name' => 'Template 6 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6h.png'),
			array('id' => '7-h', 'template_name' => 'Template 7 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-7h.png'),
			array('id' => '8-h', 'template_name' => 'Template 8 Slider', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-9h.png'),
		);

		include POST_TIMELINE_PLUGIN_PATH.'admin/partials/multiple-templates.php';
	}

	/**
	 * [ptl_settings_dashboard description]
	 */
	public function ptl_settings_dashboard()
	{	

		
		$options = $this->settings;

		$null_val = array('none' => 'none' );
		$single_temps = \PostTimeline\Helper::get_single_template();
		$single_temps = array_merge($null_val, $single_temps);

		include POST_TIMELINE_PLUGIN_PATH.'admin/partials/ptl-settings.php';
	}	

	/**
	 * [ptl_cpts_listing description]
	 */
	public function ptl_cpts_listing()
	{	
		include POST_TIMELINE_PLUGIN_PATH.'admin/partials/admin-shortcode-listing.php';
	}

	/**
	 * [ptl_create_posttypes description]
	 */
	public function ptl_create_posttypes()
	{	
		include POST_TIMELINE_PLUGIN_PATH.'admin/partials/create-cpt-form.php';		
	}



	/**
	 * [include_template_function description]
	 * @param  [type] $template_path [description]
	 * @return [type]                [description]
	 */
	function include_template_function( $template_path ) {
	 	

		if ( get_post_type() == 'post-timeline' ) {

			if ( is_single() ) {
				
				$template = $this->settings['ptl-single-temp'];

				// checks if the file exists in the theme first,
				// otherwise serve the file from the plugin
				if ( $theme_file = locate_template( array ( $template.'.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = POST_TIMELINE_PLUGIN_PATH . 'public/partials/post-timeline-page.php';
				}
					wp_enqueue_style( $this->plugin_name.'-fontawesome');
			}
		}
	   
	   return $template_path;
	}


	/**
	 * Add Meta Box to admin page
	 *
	 * @since    0.0.1
	 */
	public function fields_metabox(){
	 
	    add_meta_box(
        'ptl_meta_post_fields_box',
        'Post Timeline Fields',
        array( $this, 'ptl_meta_post_fields' ),
        'post-timeline'
	    );
	}

	/**
     * Create Meta Fields
     *
     * @since    0.0.1
     */
    public function ptl_meta_post_fields()
    {
        global $post;
        // Get Value of Fields From Database
        $ptl_fav_icon 			 = (!empty(get_post_meta($post->ID, 'ptl-fav-icon', true))) ? get_post_meta($post->ID, 'ptl-fav-icon', true) : '';
        $ptl_post_date 			= get_post_meta($post->ID, 'ptl-post-date', true);
        // $ptl_post_tag 			= get_post_meta( $post->ID, 'ptl-post-tag', true);
        $ptl_post_link 			 = get_post_meta($post->ID, 'ptl-post-link', true);
        $ptl_video_url 			 = get_post_meta($post->ID, 'ptl-video-url', true);
        $ptl_tag_line 			  = get_post_meta($post->ID, 'ptl-tag-line', true);
        $ptl_post_order 		 = get_post_meta($post->ID, 'ptl-post-order', true);
        $ptl_post_color 		 = (!empty(get_post_meta($post->ID, 'ptl-post-color', true))) ? get_post_meta($post->ID, 'ptl-post-color', true) : $this->settings['ptl-post-bg-color-text'];
        $ptl_icon_type 			 = (!empty(get_post_meta($post->ID, 'ptl-icon-type', true))) ? get_post_meta($post->ID, 'ptl-icon-type', true) : $this->settings['ptl-icon-type'];
        $ptl_media_type 		 = get_post_meta($post->ID, 'ptl-media-type', true);
        $plt_gallery 			   = $this->ptl_gallery_field('ptl_gallery', get_post_meta($post->ID, 'ptl_gallery', true));
        $ptl_custom_icon 		= $this->ptl_upload_icon('ptl-custom-icon', get_post_meta($post->ID, 'ptl-custom-icon', true));
        $ptl_social_type 		= (!empty(get_post_meta($post->ID, 'ptl-social-type', true))) ? get_post_meta($post->ID, 'ptl-social-type', true) : $this->settings['ptl-social-type'];

        include POST_TIMELINE_PLUGIN_PATH . 'admin/partials/admin-fields.php';
    }

    /**
     * [ptl_gallery_field] Description
     * Get Gallery HTML
     *
     * @since    0.0.1
     */
    public function ptl_gallery_field($name, $value = '')
    {
        $html 	= '<div><ul class="ptl_gallery_mtb">';

        $hidden = [];

        if($images = get_posts([
            'post_type'      => 'attachment',
            'orderby' 	      => 'post__in', /* we have to save the order */
            'order' 		       => 'ASC',
            'post__in' 	     => explode(',', $value), /* $value is the image IDs comma separated */
            'numberposts' 	  => -1,
            'post_mime_type' => 'image'
        ])) {
            foreach($images as $image) {
                $hidden[]  = $image->ID;
                $image_src = wp_get_attachment_image_src($image->ID, [80, 80]);
                $html .= '<li data-id="' . esc_attr($image->ID) . '"><span style="background-image:url(' . esc_url( isset($image_src[0])? $image_src[0]: '' ) . ')"></span><a class="ptl_gallery_remove">&times;</a></li>';
            }
        }

        $html .= '</ul><div style="clear:both"></div></div>';
        $html .= '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr(join(',', $hidden)) . '" /><a class="button ptl_upload_gallery_button">' . __('Add Images', 'post-timeline') . '</a>';

        return $html;
    }

    /**
     * [ptl_gallery_field] Description
     * Get Gallery HTML
     *
     * @since    0.0.1
     */
    public function ptl_upload_icon($name, $value = '')
    {
        $html = '<div><ul class="ptl_icon_mtb">';
        /* array with image IDs for hidden field */
        $hidden = [];

        if($images = get_posts([
            'post_type'      => 'attachment',
            'orderby'        => 'post__in', /* we have to save the order */
            'order'          => 'ASC',
            'post__in'       => explode(',', $value), /* $value is the image IDs comma separated */
            'numberposts'    => -1,
            'post_mime_type' => 'image'
        ])) {
            foreach($images as $image) {
                $hidden[]  = $image->ID;
                $image_src = wp_get_attachment_image_src($image->ID, [80, 80]);
                $html .= '<li  data-id="' . esc_attr($image->ID) . '"><span style="background-image:url(' . (isset($image_src[0])? $image_src[0]: ''). ')"></span><a class="ptl_icon_remove">&times;</a></li>';
                
            }
        }

        $html .= '</ul><div style="clear:both"></div></div>';
        $html .= '<input type="hidden" name="' . esc_attr($name) . '" value="' . esc_attr(join(',', $hidden)) . '" /><a  class="button ptl_upload_icon_button">'.__('Upload Icon', 'post-timeline').'</a>';

        return $html;
    }

    /**
     * Save Meta Data in database
     *
     * @since    0.0.1
     */
    public function save_ptl_meta_fields($post_id)
    {
        // update fav icon
        $ptl_fav_icon = (isset($_POST['ptl-fav-icon'])) ? sanitize_text_field(wp_unslash($_POST['ptl-fav-icon'])) : '';
        update_post_meta($post_id, 'ptl-fav-icon', $ptl_fav_icon);

        // update post link
        $ptl_post_link = (isset($_POST['ptl-post-link'])) ? sanitize_text_field(wp_unslash($_POST['ptl-post-link'])) : '';
        update_post_meta($post_id, 'ptl-post-link', $ptl_post_link);

        // update video url
        $ptl_post_video = (isset($_POST['ptl-video-url'])) ? sanitize_text_field(wp_unslash($_POST['ptl-video-url'])) : '';
        update_post_meta($post_id, 'ptl-video-url', $ptl_post_video);

        // update tag line
        $ptl_post_tag_line = (isset($_POST['ptl-tag-line'])) ? sanitize_text_field(wp_unslash($_POST['ptl-tag-line'])) : '';
        update_post_meta($post_id, 'ptl-tag-line', $ptl_post_tag_line);

        // update post order
        $ptl_post_order = (isset($_POST['ptl-post-order'])) ? sanitize_text_field(wp_unslash($_POST['ptl-post-order'])) : '';
        update_post_meta($post_id, 'ptl-post-order', $ptl_post_order);

        // update post order
        $ptl_tag_line = (isset($_POST['ptl-tag-line'])) ? sanitize_text_field(wp_unslash($_POST['ptl-tag-line'])) : '';
        update_post_meta($post_id, 'ptl-tag-line', $ptl_tag_line);

        
        // update ptl gallery
        $ptl_gallery = (isset($_POST['ptl_gallery'])) ? \PostTimeline\Helper::sanitize_array($_POST['ptl_gallery']) : '';

        if($ptl_gallery) {
            $ptl_gallery = trim($ptl_gallery, ',');   
        }

        update_post_meta($post_id, 'ptl_gallery', $ptl_gallery);

        // update custom icon
        $ptl_post_custm_icon = (isset($_POST['ptl-custom-icon'])) ? sanitize_text_field(wp_unslash($_POST['ptl-custom-icon'])) : '';
        update_post_meta($post_id, 'ptl-custom-icon', $ptl_post_custm_icon);

        if(isset($_POST['ptl-post-date'])) :

            $Date = date('Y-m-d', strtotime(sanitize_text_field(wp_unslash($_POST['ptl-post-date']))));

            update_post_meta($post_id, 'ptl-post-date', $Date);
        endif;

        if(isset($_POST['ptl-post-color'])) :
            update_post_meta($post_id, 'ptl-post-color', sanitize_text_field(wp_unslash($_POST['ptl-post-color'])));
        endif;

        if(isset($_POST['ptl-icon-type'])) :
            update_post_meta($post_id, 'ptl-icon-type', sanitize_text_field(wp_unslash($_POST['ptl-icon-type'])));
        endif;

        if(isset($_POST['ptl-media-type'])) :
            update_post_meta($post_id, 'ptl-media-type', sanitize_text_field(wp_unslash($_POST['ptl-media-type'])));
        endif;

        if(isset($_POST['ptl-social-type'])) :
            update_post_meta($post_id, 'ptl-social-type', sanitize_text_field(wp_unslash($_POST['ptl-social-type'])));
        endif;

        if (isset($_POST['ptl-social-rep'])) {

            update_post_meta($post_id, 'ptl-social-rep', \PostTimeline\Helper::sanitize_array($_POST['ptl-social-rep']));
        } else {
            update_post_meta($post_id, 'ptl-social-rep', '');
        }
    }

    /**
     * [render_template Render the Admin Template]
     * @return [type] [description]
     */
    public function render_template()
    {
        $template_id   =  isset($_GET['template_id']) ? sanitize_text_field(wp_unslash($_GET['template_id'])) : '0';
        $tmpl_class    =  isset($_GET['class']) ? sanitize_text_field(wp_unslash($_GET['class'])) : null;
    }

	/**
	 * [register_assets Include the assets of the page]
	 */
	function register_assets() {

		/////////////////
		/////CSS Files //
		/////////////////
		
		wp_enqueue_style($this->plugin_name.'-bootstrap', POST_TIMELINE_URL_PATH . 'public/css/bootstrap.min.css', array(), $this->version, 'all' );			

		//	Font-awesome library
		wp_enqueue_style($this->plugin_name.'-fas',  'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), $this->version, 'all' );		

		
		//	Argon library - reduce it
		wp_enqueue_style($this->plugin_name.'-admin-lib', POST_TIMELINE_URL_PATH . 'admin/css/ptl-admin-lib.css', array(), $this->version, 'all' );

		//	Admin CSS
		wp_enqueue_style($this->plugin_name.'-custom-admin', POST_TIMELINE_URL_PATH . 'admin/css/admin-custom.css', array(), $this->version, 'all' );

		
		wp_enqueue_style($this->plugin_name.'-blocks-style.css', POST_TIMELINE_URL_PATH . 'admin/blocks/assets/blocks-style.css', array(), $this->version, 'all' );


		wp_enqueue_style($this->plugin_name.'-css', POST_TIMELINE_URL_PATH . 'admin/css/timeline_admin.css', array(), $this->version, 'all' );		


		//////////////////
		////// JS Files //
		//////////////////

		wp_register_script($this->plugin_name.'-libs', POST_TIMELINE_URL_PATH.'admin/js/libs.js', array('jquery')); 

  	//	Timeline main JS
  	wp_register_script($this->plugin_name.'-script', POST_TIMELINE_URL_PATH . 'admin/js/timeline_script.js', array('jquery'), $this->version, false );

  	//	Todo, Move it somewhere else
  	$this->enqueue_assets();
	}



	/**
	 * [enqueue_assets Display the public JS/CSS files]
	 * @return [type] [description]
	 */
	function enqueue_assets($all_scripts = true) {
			
		//	Only enqueue it on the taxonomy
		if(isset($_GET['post_type']) && $_GET['post_type'] == 'post-timeline') {
			
			wp_enqueue_script( 'jquery-ui-sortable' );

			// Enqueue the Media
			wp_enqueue_media();

			//wp_enqueue_script('wp-theme-plugin-editor');
			//wp_enqueue_style('wp-codemirror');
		
			// CodeMirror Enqueue
			$ptl_editor_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'text/css'));
			wp_localize_script('jquery', 'ptl_editor_settings', $ptl_editor_settings);
		}

		$logo = POST_TIMELINE_URL_PATH.'admin/images/logo-new.png';

		$this->localize_scripts($this->plugin_name.'-script', 'PTL_REMOTE', array('nounce' => wp_create_nonce('ptl-nounce'), 'ajax_url' => admin_url( 'admin-ajax.php' ),'logo' => $logo,'plugin_url' => POST_TIMELINE_URL_PATH));

		//	Add the script to the page
		//wp_add_inline_script( $this->plugin_name.'-script', $this->get_local_script_data(), 'before');

		wp_enqueue_script($this->plugin_name.'-libs');

		wp_enqueue_script($this->plugin_name.'-script');
	}


	/**
	 * [timeline_columns description]
	 * @param  [type] $gallery_columns [description]
	 * @return [type]                  [description]
	 */
	function timeline_columns($gallery_columns) {		

		return array(
				"cb"  				=>  '<input type="checkbox" />',
				"title"  			=>  _x('Timeline Title', 'post-timeline'),
				"icon"  			=>  __('Icon'),
				"images"  		=>  __('Timeline Image'),
				"event_date"  =>  __('Event Date'),
				"date"  			=>  _x('Published', 'post-timeline'),
				"content"  		=>  _x('Timeline Content', 'post-timeline')
			);
	}



	/**
     * [timeline_column_details Add Columns data to the timeline]
     * @param  [type] $_column_name [description]
     * @param  [type] $_post_id     [description]
     * @return [type]               [description]
     */
    public function timeline_column_details($_column_name, $_post_id)
    {
        switch($_column_name) {
            //	Column- Event Date
            case 'event_date':

                $timeline_date 	= get_post_meta($_post_id, 'ptl-post-date', true);

                if($timeline_date) {
                    $timeline_date = date_format(date_create($timeline_date), ' M - d - Y');
                }

                echo esc_html($timeline_date);

                break;

                //	Column- Images
            case 'images':

                $post_image_id = get_post_thumbnail_id(get_the_ID());

                if ($post_image_id) {
                    $thumbnail = wp_get_attachment_image_src($post_image_id, [150, 150], false);

                    if ($thumbnail) {
                        (string)$thumbnail = $thumbnail[0];
                    }
                    echo '<img src="' . esc_url($thumbnail) . '" alt="post-image" />';
                }

                break;

                //	Column- Icon
            case 'icon':

                $post_icon  = get_post_meta(get_the_ID(), 'ptl-fav-icon');
                $post_color = get_post_meta(get_the_ID(), 'ptl-post-color');

                $post_icon  = (is_array($post_icon) && isset($post_icon[0])) ? $post_icon[0] : '';
                $post_color = (is_array($post_color) && isset($post_color[0])) ? $post_color[0] : '';

                $ptl_icon_type 	= (!empty(get_post_meta(get_the_ID(), 'ptl-icon-type', true))) ? get_post_meta(get_the_ID(), 'ptl-icon-type', true) : $this->settings['ptl-icon-type'];

                if ($ptl_icon_type == 'upload-icon') {
                    $icon_meta 	= get_post_meta(get_the_ID(), 'ptl-custom-icon', true) ;
                    $icon_img 	 = wp_get_attachment_image_src($icon_meta);
                    $image_alt 	= (!empty(get_post_meta($icon_meta, '_wp_attachment_image_alt', true))) ? get_post_meta($icon_meta, '_wp_attachment_image_alt', true) : get_the_title($icon_meta);

                    if (!empty($icon_img[0])) {
                        echo '<span class="ptl-icon-image"><image class="ptl-ico ptl-post-icon" src="' . esc_url($icon_img[0]) . '"  alt="' . esc_attr($image_alt) . '" /></span>';
                    }
                } elseif ($ptl_icon_type == 'font-awesome') {
                    echo '<span class="ptl-ico" style="background:' . esc_attr($post_color) . '" ><span class="fa ' . esc_html($post_icon) . '"></span></span>';
                }
                break;

                //	Column- Order
            case 'order':

                $post_order  = get_post_meta(get_the_ID(), 'ptl-post-order');

                echo (is_array($post_order) && isset($post_order[0])) ? esc_html($post_order[0]) : esc_html($post_order);

                break;

                //	Column - content
            case 'content':

                echo esc_html(get_the_excerpt());
                break;
        }
    }

	/**
	 * Filter the required "title" field for list-item option types.
	 *
	 * @since    0.0.1
	 */
	public function filter_post_list_item_title_label( $label, $id ) {

    if ( $id == 'post-timeline-timeline-event' ) {
      $label = __( 'Post name', 'post-timeline' );
    }

    return $label;
	}


	/**
	* [ptl_tag_get_object_terms description]
	* 
	* reorder taxonomies
	* 
	* @param  [type] [description]
	* @return [type] [description]
	*/
	public function ptl_tag_get_object_terms( $terms )
	{
		$tags = array('ptl_tag');
		
		if ( is_admin() && isset( $_GET['orderby'] ) ) return $terms;
		
		foreach( $terms as $key => $term ) {
			if ( is_object( $term ) && isset( $term->taxonomy ) ) {
				$taxonomy = $term->taxonomy;
				if ( !in_array( $taxonomy, $tags ) ) return $terms;
			} else {
				return $terms;
			}
		}
		
		usort( $terms, array(&$this,'taxonomy_cmp')  );
		return $terms;
	}
	/**
	* [taxonomy_cmp description]
	* 
	* helper function to compare tag order
	* 
	* @param  [type] [description]
	* @return [type] [description]
	*/
	public function taxonomy_cmp( $a, $b )
	{
		if ( $a->tag_order ==  $b->tag_order ) return 0;
		return ( $a->tag_order < $b->tag_order ) ? -1 : 1;
	}
	/**
	* [manage_tag_columns description]
	* 
	* add ptl_drag column to ptl_tag listing
	* 
	* @param  [type] $columns [description]
	* @return [type] $columns [description]
	*/
	public function manage_tag_columns($columns)
	{

	 $columns = array('ptl_drag' => '') + $columns;

	 return $columns;
	}

	/**
	* [manage_tag_column_field description]
	* 
	* add ptl_drag column filed to ptl_tag listing
	* 
	* @param  [type] $deprecated,$column_name,$term_id [description]
	* @return [type] [description]
	*/
	public function manage_tag_column_field($deprecated,$column_name,$term_id)
	{

	 if ($column_name == 'ptl_drag') {
	   echo '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAABmJLR0QA/wD/AP+gvaeTAAAAnUlEQVRIie3WwQ3CMAyF4WdG8RywAGUaiAQ3wg0OdJqmC5Q5soo5IYGaSo3bRELyf4wUfQf7YMCaGbvg+djftf9Ji0JwBQAIPWK7vxSHf9BPCjwLTqILcKtKoxnzqRsA2q7LyCs+D7vvl826wJRLUsWxUtlyWcWy5bKKlXn6dGcIpS9LgY9tcysCT+KZqAoe4Qp0UeyCZxd8NfDvewMxnElAYEtE9AAAAABJRU5ErkJggg==" alt="ptl-drag">';
	 }

	}
	
	/**
   * [localize_scripts description]
   * @param  [type] $script_name [description]
   * @param  [type] $variable    [description]
   * @param  [type] $data        [description]
   * @return [type]              [description]
   */
  private function localize_scripts($script_name, $variable, $data) {

  	//$this->scripts_data[] = [$variable, $data]; 
  	
  	//	Since version 2.3.5
  	wp_localize_script( $script_name, $variable, $data );
  }


  /**
   * [get_local_script_data Render the scripts data]
   * @return [type] [description]
   */
  private function get_local_script_data($with_tags = false) {

  	$scripts = '';

  	foreach ($this->scripts_data as $script_data) {
  			
  		$scripts .= 'var '.$script_data[0].' = '.(($script_data[1] && !empty($script_data[1]))?json_encode($script_data[1]): "''").';';
  	}

  	//	With script tags
  	if($with_tags) {

  		$scripts = "<script type='text/javascript' id='post-timeline-script-js'>".$scripts."</script>";
  	}

  	//	Clear it
  	$this->scripts_data = [];

  	return $scripts;
  }

  

}
