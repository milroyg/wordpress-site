<?php


namespace PostTimeline\Vendors\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module;




if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed direptly.
}


/**
 * Elementor PostTimelineAddonWidget
 *
 * Elementor widget for PostTimelineAddonWidget
 *
 * @since 1.0.0
 */
class Widget extends Widget_Base {

	// Template ID of the timeline
	public $template_id  = '5';

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'post-timeline-addon';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return __( 'Post Timeline', 'post-timeline' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-time-line';
	}


	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'post-timeline' ];
	}

	/**
	 * Retrieve the list of scripts the widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'ptla' ];
	}


	/**
	 * Check for empty values and return provided default value if required
	 */
	protected function set_default( $value, $default ){
		if( isset($value) && $value!="" ){
			return $value;
		}else{
			return $default;
		}
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function register_controls() {
		


		$terms = get_terms(array(
			'taxonomy' => 'ptl_categories',
			'hide_empty' => false,
		));

		$ptl_categories=array();
		// $ptl_categories[''] = __('All Categories','post-timeline');

		if (!empty($terms) || !is_wp_error($terms)) {
			foreach ($terms as $term) {
				$ptl_categories[$term->term_id] = $term->name ;
			}
		}

		$args=array(
		      'public' => true,
		  ); 

		  $output = 'names'; // names or objects, note names is the default
		  $operator = 'and'; // 'and' or 'or'
		  $post_types = get_post_types($args,$output,$operator);

		  $posttypes_array = array();
	      $posttypes_array[''] = __('Select Post Type','post-timeline');

		  unset( $post_types['attachment'] );
		  unset( $post_types['page'] );
		  foreach ($post_types  as $post_type ) {
		  	$post_type_name = str_replace('-', ' ', $post_type);
		  	$post_type_name = str_replace('_', ' ', $post_type_name);
		  	$post_type_name = ucfirst($post_type_name);
		      $posttypes_array[$post_type] = $post_type_name;
		  }

		 $all_taxanomies = array();
		 foreach ($posttypes_array as $key => $value) {
		 	if ($value == 'Select Post Type' ) continue;

			$taxonomies = get_object_taxonomies(  $key, 'objects' );
		 	foreach( $taxonomies as $taxonomy ){
		 	    if ($taxonomy->name == 'post_tag' || $taxonomy->name ==  'post_format') continue;

		 	    $all_taxanomies[$key][] = $taxonomy->name;
		 	} 
		 }

		$tax_cat = array();

		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Post Story Timeline Settings', 'post-timeline' ),
			]
		);	

		$this->add_control(
			'post_timeline_notice',
			[
				'label' => __( '', 'post-timeline' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<strong style="color:red">It is only a shortcode builder. Kindly update/publish the page and check the actually post timeline on front-end</strong>',
				'content_classes' => 'post_timeline_notice',
			]
		);

		$this->add_control(
			'ptl_select_posttype',
			[
				'label' => __( 'Select types', 'post-timeline' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'ptl',
				'options' => array(
					'ptl' => 'Post-Timeline',
					'cpt' => 'Custom Post Type',
				),
			]
		);

		$this->start_controls_tabs(
			'style_tabs'
		);


		$this->start_controls_tab(
			'post-setting-tab',
			[
				'label' => esc_html__( 'Timeline Settings', 'plugin-name' ),
			]
		);

		$this->add_control(
			'post-type',
			[
				'label' => __( 'Select Post types', 'post-timeline' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $posttypes_array,
				'condition'   => [
					'ptl_select_posttype'   => 'cpt',
				],
			]
		);



		$this->add_control(
			'category_ptl',
			[
				'label' => __( 'Timeline Categories', 'post-timeline' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'options' => $ptl_categories,
				'multiple' => true,
				'condition'   => [
					'ptl_select_posttype'   => 'ptl',
				],
				
			]
		);

		$this->add_control(
			'post-desc',
			[
				'label' => __( 'Select Description Type', 'post-timeline' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'excerpt',
				'options' => [
					'excerpt' => __( "Excerpt",'post-timeline' ) ,
					'full'=>__('Full Text','post-timeline' ),
				]
			]
		);


		$this->add_control(
			'taxonomy',
			[
				'label' => __( 'Enter Taxanomy', 'post-timeline' ),
				'type' => Controls_Manager::TEXT,
				'condition'   => [
					'post-type!'   => '',
					'ptl_select_posttype'   => 'cpt',
				],
				
			]
		);

		$this->add_control(
			'category_cpt',
			[
				'label' => __( 'Category IDs', 'post-timeline' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'example: 10,13,2,4', 'post-timeline' ),
				'description' => 'Show Post by Category IDs (example: 10,13,2,4)',
				'condition'   => [
					'post-type!'   => '',
					'ptl_select_posttype'   => 'cpt',
				],
				
			]
		);


		$this->add_control(
			'filter-ids',
			[
				'label' => __( 'Filter By IDs', 'post-timeline' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'example: 10,13,2,4', 'post-timeline' ),
				'description' => 'Show Post by IDs (example: 10,13,2,4)',
			]
		);

		$this->add_control(
			'exclude-ids',
			[	
				'label' => __( 'Exclude IDs', 'post-timeline' ),
				'type' => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'example: 11,12,5,7', 'post-timeline' ),
				'description' => 'Exclude Post by IDs example: 11,12,5,7',
			]
		);

		$this->add_control(
			'post-per-page',
			[
			
				'label' => __( 'Display per page', 'post-timeline' ),
				'type' => Controls_Manager::NUMBER,
				'default' => '10'
			,
			]
		);
		$this->add_control(
		 	'pagination',
		 	[
				'label' => __( 'Load More', 'post-timeline' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'button',
				'label_on' => esc_html__( 'On', 'post-timeline' ),
				'label_off' => esc_html__( 'Off', 'post-timeline' ),
				'return_value' => 'button',
				
			]
		);


		$this->end_controls_tab();


		$this->start_controls_tab(
			'post-ui-tab',
			[
				'label' => esc_html__( 'Timeline UI', 'plugin-name' ),
			]
		);

			$this->add_control(
				'layout',
				[
					'label' => __( 'Timeline Layout', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'vertical',
					'options' => [
						'vertical' => __( 'Vertical Layout', 'post-timeline' ),
						'one-side' => __( 'One Side Layout', 'post-timeline' )
					]
					
				]
			);

			$this->add_control(
				'position',
				[
					'label' => __( 'Position', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'left',
					'options' => [
						'left' => __( 'Left', 'post-timeline' ),
						'right' => __( 'Right', 'post-timeline' )
					],
					'condition'   => [
						'layout'   =>	'one-side',
					],
					
				]
			);


			$this->add_control(
				'skin-type',
				[
					'label' => __( 'Timeline Skin', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'dark',
					'options' => [
						'dark' => __( 'Dark', 'post-timeline' ),
						'light' => __( 'Light', 'post-timeline' ),
					],
					
				]
			);

			 $this->add_control(
				'template',
				[
					'label' => __( 'Timeline Template Design', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => '0',
					'options' => [
						'0' => __( "Template 0",'post-timeline' ) ,
					]
					
				]
			);

			 $this->add_control(
				'nav-type',
				[
					'label' => __( 'Select Navigation Type', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'style-0',
					'options' => [
						'0' => __( "Style 0",'post-timeline' ) ,
					]
					
				]
			);


			 $this->add_control(
			 	'nav-max',
			 	[
		 			'label' => __( 'Nav max', 'post-timeline' ),
		 			'description' => __('Navigation maximum number (example: 5)','post-timeline'),
		 			'type' => Controls_Manager::NUMBER,
		 			'default' => '5'
			 		
			 	]
			 );
			 $this->add_control(
			 	'nav-offset',
			 	[
			 		
		 			'label' => __( 'Nav Offset', 'post-timeline' ),
		 			'description' => __('Navigation offset (example: 70)','post-timeline'),
		 			'type' => Controls_Manager::NUMBER,
		 			'default' => '70',
		 			'condition'   => [
		 				'layout!'   =>	'horizontal',
		 			],
			 		
			 	]
		
			 );		 

			$this->add_control(
			 	'anim-type',
			 	[
					'label' => __( 'Select Animation', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'fadeInUp',
					'options' => [
						'backIn_left-right'     => esc_attr__('BackIn Left/Right','post-timeline'),
						'bounceIn'              => esc_attr__('BounceIn','post-timeline'),
						'bounceInUp'            => esc_attr__('BounceInUp','post-timeline'),
						'fadeIn'                => esc_attr__('FadeIn','post-timeline'),
						'fadeUpLeft-Right'      => esc_attr__('FadeUp Left/Right','post-timeline'),
						'fadeInDown'            => esc_attr__('FadeInDown','post-timeline'),
						'fadeDownLeft-Right'    => esc_attr__('FadeDown Left/Right','post-timeline'),
						'fadeIn_left-right'     => esc_attr__('FadeIn Left/Right','post-timeline'),
						'flipInY'               => esc_attr__('Flip Y','post-timeline'),
						'flipInX'               => esc_attr__('Flip X','post-timeline'),
						'flip-left-right'       => esc_attr__('Flip Left/Right','post-timeline'),
						'lightSpeed_left-right' => esc_attr__('lightSpeed Left/Right','post-timeline'),
						'rotateIn'              => esc_attr__('RotateIn','post-timeline'),
						'fadeInUp'              => esc_attr__('Slide Up','post-timeline'),
						'zoomInUp'              => esc_attr__('ZoomInUp','post-timeline'),
						'zoomInDown'            => esc_attr__('ZoomInDown','post-timeline'),
						'zoomInLeft-Right'      => esc_attr__('ZoomIn Left/Right','post-timeline'),
						'zoomOutLeft-Right'     => esc_attr__('ZoomOut Left/Right','post-timeline')
					],
					'condition'   => [
						'layout!'   =>	'horizontal',
					]
					
				]
			);

			$this->add_control(
			 	'type',
			 	[
					'label' => __( 'Select Tag Type', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'date',
					'options' => [
						'date' => __( "Date Based",'post-timeline' ) ,
						'tag' => __( "Tag Based",'post-timeline') ,
					]
					
				]
			);

			$this->add_control(
				'tag-taxonomy',
				[
					'label' => __( 'Enter Tag Taxanomy', 'post-timeline' ),
					'type' => Controls_Manager::TEXT,
					'condition'   => [
						'post-type!'   => '',
						'ptl_select_posttype'   => 'cpt',
						'type'   => 'tag',
					],
					
				]
			);

			$this->add_control(
			 	'sort',
			 	[
					'label' => __( 'Sorting Order', 'post-timeline' ),
					'type' => Controls_Manager::SELECT2,
					'default' => 'ASC',
					'options' => [
						'ASC' => __( "Ascending",'post-timeline' ) ,
						'DESC' => __( "Descending",'post-timeline') ,
					]
					
				]
			);


			$this->add_control(
			 	'bg-color',
			 	[
					'label' => __( 'Post Background Color', 'post-timeline' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#F5F5F5',
					
				]
			);

			$this->add_control(
			 	'nav-color',
			 	[
					'label' => __( 'Post Navigation Color', 'post-timeline' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#4285F4',
					
				]
			);


			$this->add_control(
			 	'targe-blank',
			 	[
					'label' => __( 'Link open in new tab', 'post-timeline' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'on',
					'label_on' => esc_html__( 'On', 'post-timeline' ),
					'label_off' => esc_html__( 'Off', 'post-timeline' ),
					'return_value' => 'on',
					
				]
			);

			$this->add_control(
			 	'html',
			 	[
					'label' => __( 'Include HTML', 'post-timeline' ),
					'type' => Controls_Manager::SWITCHER,
					'default' => 'on',
					'label_on' => esc_html__( 'On', 'post-timeline' ),
					'label_off' => esc_html__( 'Off', 'post-timeline' ),
					'return_value' => 'on',
					
				]
			);

		$this->end_controls_tab();


		
		$this->end_controls_section();

		
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render() {
		
		$settings = $this->get_settings();
		// dd($settings);
		$post_type_selection = $settings['ptl_select_posttype'];

		$shortcode_attr = array();

		// Post type if Post Type Selection is CPT/PTL
		(($post_type_selection == 'cpt') ? $shortcode_attr['post-type'] = 'post-type="'.$settings['post-type'].'"' : 'post-timeline');

		// Select Template
		((!empty($settings['template'])) ? $shortcode_attr['template'] = 'template="'.$settings['template'].'"' : $shortcode_attr['template'] = 'template="0"' );

		// Post Description
		$shortcode_attr['post-desc'] = 'post-desc="'.$settings['post-desc'].'"';

		// Set Taxonomy
		if (!empty($settings['taxonomy']))
			(($post_type_selection == 'cpt' && !empty($settings['taxonomy'])) ? $shortcode_attr['taxonomy'] = 'taxonomy="'.$settings['taxonomy'].'"' : '' );

		// Category 	
		(($post_type_selection == 'cpt' && !empty($settings['taxonomy'])) ? (!empty($settings['category_cpt']) ? $shortcode_attr['category'] = 'category="'.$settings['category_cpt'].'"' : '' ) : (!empty($settings['category_ptl']) ? $shortcode_attr['category'] = 'category="'.implode(',', $settings['category_ptl']).'"' : '' ) );

		// Filter By IDs
		((!empty($settings['filter-ids'])) ? $shortcode_attr['filter-ids'] = 'filter-ids="'.$settings['filter-ids'].'"' : '' );

		// Exclude IDs
		((!empty($settings['exclude-ids'])) ? $shortcode_attr['exclude-ids'] = 'exclude-ids="'.$settings['exclude-ids'].'"' : '' );

		// Post Per Page
		((!empty($settings['post-per-page'])) ? $shortcode_attr['post-per-page'] = 'post-per-page="'.$settings['post-per-page'].'"' : '' );

		// Pagination
		((!empty($settings['pagination'])) ? $shortcode_attr['pagination'] = 'pagination="'.$settings['pagination'].'"' : '' );

		// Timeline Layout
		((!empty($settings['layout'])) ? $shortcode_attr['layout'] = 'layout="'.$settings['layout'].'"' : '' );

		// Timeline Layout postion  
		((!empty($settings['position']) && $settings['layout'] == 'one-side') ? $shortcode_attr['position'] = 'position="'.$settings['position'].'"' : '' );

		// Skin Type
		((!empty($settings['skin-type'])) ? $shortcode_attr['skin-type'] = 'skin-type="'.$settings['skin-type'].'"' : '' );

		// Select Nav Style
		((!empty($settings['nav-type'])) ? $shortcode_attr['nav-type'] = 'nav-type="'.$settings['nav-type'].'"' : '' );

		// Nav Max
		((!empty($settings['nav-max'])) ? $shortcode_attr['nav-max'] = 'nav-max="'.$settings['nav-max'].'"' : '' );

		// Nav Offset
		((!empty($settings['nav-offset'])) ? $shortcode_attr['nav-offset'] = 'nav-offset="'.$settings['nav-offset'].'"' : '' );

		// Set Animation Type
		((!empty($settings['anim-type'])) ? $shortcode_attr['anim-type'] = 'anim-type="'.$settings['anim-type'].'"' : '' );

		// Set Sort Type
		((!empty($settings['sort'])) ? $shortcode_attr['sort'] = 'sort="'.$settings['sort'].'"' : '' );

		// Set types 	
		((!empty($settings['type'])) ? $shortcode_attr['type'] = 'type="'.$settings['type'].'"' : '' );

		// Set Tag Taxonomy
		if (!empty($settings['tag-taxonomy']))
			(($post_type_selection == 'cpt' && !empty($settings['tag-taxonomy']) && $settings['type'] == 'tag') ? $shortcode_attr['tag-taxonomy'] = 'tag-taxonomy="'.$settings['tag-taxonomy'].'"' : '' );

		// Set BG Color
		((!empty($settings['bg-color'])) ? $shortcode_attr['bg-color'] = 'bg-color="'.$settings['bg-color'].'"' : '' );

		// Set Navigation color
		((!empty($settings['nav-color'])) ? $shortcode_attr['nav-color'] = 'nav-color="'.$settings['nav-color'].'"' : '' );

		((!empty($settings['targe-blank'])) ? $shortcode_attr['targe-blank'] = 'targe-blank="'.$settings['targe-blank'].'"' : '' );

		// HTML content in post
		((!empty($settings['html'])) ? $shortcode_attr['html'] = 'html="'.$settings['html'].'"' : '' );
		$shortcode_attr = implode(' ', $shortcode_attr);
		$timeline = '[post-timeline '.$shortcode_attr.']';


		//  If is edit Mode
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
		    // do stuff for edit mode
			
			echo '<link rel="stylesheet" id="post-timeline-timeline-5-css" href="'.POST_TIMELINE_URL_PATH . 'public/css/tmpl-5.css" media="all" />';
			$html  = '<style type="text/css">';
			$html .= '.yr_list {display: none !important;}';
			$html .= '</style>';

			echo ($html);
		} 


		echo'<div class="elementor-shortcode post-timeline-free-addon">';
		echo do_shortcode($timeline);
 		echo'</div>';
	}	
}
