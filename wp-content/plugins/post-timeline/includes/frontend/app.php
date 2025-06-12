<?php

namespace PostTimeline\Frontend;

use PostTimeline\Helper;

/**
 * The public-facing functionality of the plugin.
 *
 * @package    PostTimeline
 * @subpackage PostTimeline/public
 * @author     agilelogix <support@agilelogix.com>
 */
class App
{
    /**
     * The ID of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    public $attr;
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
     * The defaults of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string    $defaults    The defaults of this plugin.
     */
    protected $defaults;

    /**
     * @since    0.0.1
     * @access   private
     * @var      string    $temp_id
     */
    protected $temp_id;

    /**
     * @since    0.0.1
     * @access   private
     * @var      string    $scripts_data
     */
    protected $scripts_data;

    /**
     * Initialize the class and set its properties.
     *
     * @since    0.0.1
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = function_exists('wp_get_environment_type') && wp_get_environment_type() == 'development' ? time() : $version;

        $this->defaults = Helper::get_default_settings();

        $options        = get_option('post_timeline_global_settings');
        $this->settings = (!empty($options) ? array_merge($this->defaults, $options) : $this->defaults);

        //  Temporary off this feature
        if (isset($this->settings['ptl-lazy-load'])) {
            $this->settings['ptl-lazy-load'] = 'off';
        }
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function register_styles()
    {
        wp_register_style($this->plugin_name . '-bootstrap', POST_TIMELINE_URL_PATH . 'public/css/bootstrap.min.css', [], $this->version, 'all', true);
        wp_register_style($this->plugin_name . '-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', [], $this->version, 'all');
        wp_register_style($this->plugin_name . '-owl-carousel', POST_TIMELINE_URL_PATH . 'public/css/owl.carousel.min.css', [], $this->version, 'all');
        wp_register_style($this->plugin_name . '-slick', POST_TIMELINE_URL_PATH . 'public/css/slider/slick.css', [], $this->version, 'all');
        wp_register_style($this->plugin_name . '-animate', POST_TIMELINE_URL_PATH . 'public/css/animate.css', [], $this->version, 'all');

        wp_register_style($this->plugin_name . '-single-page', POST_TIMELINE_URL_PATH . 'public/css/ptl-single-page.css', [], $this->version, 'all');

        // Gutenberg Blocks Css
        wp_enqueue_style($this->plugin_name . '-blocks-style', POST_TIMELINE_URL_PATH . 'admin/blocks/assets/blocks-style.css', [], $this->version, 'all');

        // Bootstrap css load everywhere in frontend
        wp_enqueue_style($this->plugin_name . '-bootstrap');
    }

    /**
     * [enqueue_styles Enqueue the script when called]
     * @return [type] [description]
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name . '-animate');
        wp_enqueue_style($this->plugin_name . '-owl-carousel');
        wp_enqueue_style($this->plugin_name . '-slick');
        wp_enqueue_style($this->plugin_name . '-fontawesome');
        wp_enqueue_style($this->plugin_name . '-single-page');
    }

    /**
     * [single_temp_style Enqueue the style when called]
     * @return [type] [description]
     */
    public function single_temp_style()
    {
        $type = get_post_type(get_the_ID());
        if ($type == 'post-timeline') {
            wp_enqueue_style($this->plugin_name . '-single-page');
        }
    }

    /**
     * [register_scripts description]
     * @return [type] [description]
     */
    public function register_scripts()
    {
        $cache  = true;
        $bottom = true;

        wp_register_script($this->plugin_name . '-owl-carousel', POST_TIMELINE_URL_PATH . 'public/js/owl.carousel.min.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-sharer', POST_TIMELINE_URL_PATH . 'public/js/sharer.min.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-anim', POST_TIMELINE_URL_PATH . 'public/js/ptl-anim.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-scroll', POST_TIMELINE_URL_PATH . 'public/js/smooth-scroll.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-slick', POST_TIMELINE_URL_PATH . 'public/js/slick.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-bootstrap', POST_TIMELINE_URL_PATH . 'public/js/bootstrap.js', ['jquery'], $this->version, $bottom);
        wp_register_script($this->plugin_name . '-public-script', POST_TIMELINE_URL_PATH . 'public/js/post-timeline.js', ['jquery'], $this->version, $bottom);

        // Disabled since 2.3.11
        // include bootstrap in single post-timeline page
        if (is_singular('post-timeline')) {
            //wp_enqueue_script($this->plugin_name . '-bootstrap');
        }
    }

    /**
     * [enqueue_scripts Enqueue the script when called]
     * @return [type] [description]
     */
    public function enqueue_scripts()
    {
        //	Register the scripts
        $this->register_scripts();

        wp_enqueue_script($this->plugin_name . '-owl-carousel');
        wp_enqueue_script($this->plugin_name . '-sharer');
        wp_enqueue_script($this->plugin_name . '-anim');
        wp_enqueue_script($this->plugin_name . '-scroll');
        wp_enqueue_script($this->plugin_name . '-slick');
        //wp_enqueue_script($this->plugin_name . '-bootstrap');
        wp_enqueue_script($this->plugin_name . '-public-script');
    }

    /**
     * Register individual shortcode [post_timeline]
     *
     * @since    0.0.1
     */
    public function add_shortcodes()
    {
        add_shortcode('post-timeline', [$this, 'timeline_shortcode']);

        add_shortcode('post-timeline-social-icons', [$this, 'temp_social_icons']);
    }

    /**
     * Callback function for individual timeline shortcode [post-timeline]
     *
     * @since    0.0.1
     */
    public function timeline_shortcode($attrs)
    {
        $atts = [];

        if (!empty($attrs)) {
            foreach ($attrs as $attr_key => $attr_value) {
                $rpl_attr        = str_replace('ptl-', '', $attr_key);
                $atts[$rpl_attr] = esc_attr__($attr_value);

                if (isset($atts['category_id'])) {
                    $cat_attr        = str_replace('_id', '', $rpl_attr);
                    $atts[$cat_attr] = esc_attr__($attr_value);
                    unset($atts['category_id']);
                }
            }
        }

        // Override attributes using "post_timeline_attribute" Hook.
        do_action_ref_array('post_timeline_attribute', [&$atts, $this]);

        $attrs = [];
        if (empty($atts) && !is_array($atts)) {
            $atts = [];
        }

        $atts['template'] = (isset($atts['template']) && $atts['template'] !== null) ? $atts['template'] : '0';


		$atts['template'] = '5';
		$atts['nav-type'] = '0';

        if(isset($atts['layout']) && $atts['layout'] == 'horizontal'){
            $atts['layout'] = 'vertical';
        }

        foreach ($atts as $key => $value) {
            $non_ptl = ['template', 'nav-max', 'nav-offset'];
            if (in_array($key, $non_ptl)) {
                $key         = str_replace('-', '_', $key);
                $attrs[$key] = $value;
            } else {
                if ($key == 'skin-type') {
                    $value = 'ptl-' . $value;
                }

                $attrs['ptl-' . $key] = $value;
            }
        }

        $this->attr 		= $attrs;

        $template 			    = $atts['template'];
        $this->temp_id  	= $template;

        $css_tmpl 			=  $template;

        //  FoR horizontal template
        if ((isset($attrs['ptl-layout'])) && $attrs['ptl-layout'] == 'horizontal') {
            $css_tmpl = $template . '-h';
            $template = $template . '-h';
        }

        //	Enqueue the CSS file
        wp_enqueue_style($this->plugin_name . '-timeline-' . $css_tmpl, POST_TIMELINE_URL_PATH . 'public/css/tmpl-' . $css_tmpl . '.css', [], $this->version, 'all');
        $this->enqueue_styles();

        $tmpl_class = $this->get_timeline_class_name($template);
        $uniqid 	   = uniqid();

        //	When we have a class
        if ($tmpl_class) {
            $this->enqueue_scripts();

            $this->settings = (!empty($attrs) ? array_merge($this->settings, $attrs) : $this->settings);

            $attrs = (!empty($attrs) ? array_merge($attrs, ['unique_id' => $uniqid]) : $attrs);

            //	Class instance
            $tpl_instance 	= new $tmpl_class($attrs);

            //	Update the attributes
            if (!empty($tpl_instance->attr_updates)) {
                $this->settings = array_merge($this->settings, $tpl_instance->attr_updates);
            }

            $this->settings = (!empty($attrs) ? array_merge($this->settings, $attrs) : $this->settings);

            $langs = [
                'no_more_posts' => esc_attr__('No more Posts available', 'post-timeline'),
                'read_more' 		  => esc_attr__('Read More', 'post-timeline'),
                'read_less' 		  => esc_attr__('Read Less', 'post-timeline')
            ];

            $this->localize_scripts($this->plugin_name . '-public-script', 'PTL_REMOTE', ['LANG' 	=> $langs, 'ajax_url' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('load_more_posts'), 'loader_style' => $this->settings['ptl-loader']]);

            $this->localize_scripts($this->plugin_name . '-public-script', 'ptl_config_' . $uniqid, $this->settings);

            //	Render the timeline
            $html_code = $tpl_instance->render_template(); //.$this->get_local_script_data(true);

            return $html_code;
        }
    }

    /*Load By Ajax*/
    public function timeline_ajax_load_posts()
    {
        check_ajax_referer('load_more_posts', 'security');
        $paged     	= (isset($_POST['page'])) ? sanitize_text_field(wp_unslash($_POST['page'])) : 1;
        $step     	 = (isset($_POST['step'])) ? sanitize_text_field(wp_unslash($_POST['step'])) : 1;
        $temp      	= (isset($_POST['config']['template'])) ? sanitize_text_field(wp_unslash($_POST['config']['template'])) : 0;

        $config    		 = isset($_POST['config']) ? array_map('sanitize_text_field', wp_unslash($_POST['config'])) : [];
        $per_page    	= $config['ptl-post-per-page'];

        $response          = new \stdclass();
        $response->success = false;

        $tmpl_class = '\PostTimeline\Layout_' . $temp;
        $post_data 	= [];

        //	Class instance
        $tmpl_class 		= $this->get_timeline_class_name($temp);

        //	When we have a class
        if ($tmpl_class) {
            //	Class instance
            $tpl_instance 	= new $tmpl_class($config);

            //	Render the timeline
            $response->template 		 = $tpl_instance->ajax_call_posts($paged, $step);
            $response->navigation 	= $tpl_instance->ajax_render_navigation();
            $response->step 				   = $step + $per_page;
            $response->success 			 = true;
        }

        echo wp_json_encode($response);
        die;
    }

    /**
     * [get_the_url description]
     * @return [type] [description]
     */
    private function get_the_url()
    {
        $host        = isset($_SERVER['HTTP_HOST']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_HOST'])) : '';
        $request_uri = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
        return (is_ssl() ? 'https://' : 'http://') . $host . $request_uri;
    }

    /**
     * [popup_gallery]
     * @param  [type] [description]
     * @return [type] [description]
     */
    public function popup_gallery()
    {
        $response          = new \stdclass();
        $response->success = false;
        $html              = '';
        $post_id           = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
        $post_thumbnail    = get_post_thumbnail_id($post_id);
        //	Get the Custom Meta
        $post_meta 	     = get_post_custom($post_id);
        $img 		          = wp_get_attachment_image_src($post_thumbnail, 'full');
        $placeholder_img = POST_TIMELINE_URL_PATH . 'public/img/thumbnail.jpg';
        $image_alt       = 'placeholder image';
        if ($img) {
            $img       = $img[0];
            $image_alt = (!empty(get_post_meta($post_thumbnail, '_wp_attachment_image_alt', true))) ? get_post_meta($post_thumbnail, '_wp_attachment_image_alt', true) : get_the_title($post_thumbnail);
        } else {
            $img = '';
        }
        if (isset($post_meta['ptl_gallery'][0])) {
            $gallery = $post_meta['ptl_gallery'][0];
            $gallery = explode(',', $gallery);
            $gallery = array_filter($gallery);
            if (!empty($gallery)) {
                $html = '<div class="owl-carousel owl-theme ptl-media-gallery-' . $post_id . '  ptl-media-post-gallery-popup">';

                if ($img) {
                    $img_src = 'src="' . $img . '"';

                    if ($this->settings['ptl-lazy-load'] == 'on') {
                        $img_src = 'src="' . $placeholder_img . '" data-src="' . $img . '"';
                    }

                    $html .= '<div><img ' . $img_src . ' alt="' . $image_alt . '"/></div>';
                }

                foreach ($gallery as $image) {
                    if ($image) {
                        $url       = wp_get_attachment_image_src($image, 'full')[0];
                        $image_alt = (!empty(get_post_meta($image, '_wp_attachment_image_alt', true))) ? get_post_meta($image, '_wp_attachment_image_alt', true) : get_the_title($image);

                        $img_src = 'src="' . $url . '"';

                        if ($this->settings['ptl-lazy-load'] == 'on') {
                            $img_src = 'src="' . $placeholder_img . '" data-src="' . $url . '"';
                        }

                        $html .= '<div><img ' . $img_src . '  alt="' . $image_alt . '" /></div>';
                    }
                }

                $html .= '</div>';
            } else {
                if ($img) {
                    $img_src = 'src="' . $img . '"';

                    if ($this->settings['ptl-lazy-load'] == 'on') {
                        $img_src = 'src="' . $placeholder_img . '" data-src="' . $img . '"';
                    }

                    $html .= '<img ' . $img_src . '  alt="' . $image_alt . '" />';
                }
            }
            $response->gallery = $html;
            $response->success = true;
        }

        echo json_encode($response);
        die;
    }

    /**
     * Include in loop so they can be displayed among regular posts.
     *
     * @since    0.0.1
     */
    public function add_timelines_to_loop($query)
    {
        global $pagenow;

        if ($pagenow == 'edit.php') {
            return;
        }

        // Querying specific page (not set as home/posts page) or attachment
        if (!$query->is_home()) {
            if ($query->is_page() || $query->is_attachment()) {
                return;
            }
        }

        // Querying a specific taxonomy
        if (!is_null($query->tax_query)) {
            $tax_queries         = $query->tax_query->queries;
            $timeline_taxonomies = get_object_taxonomies('post-timeline');

            if (is_array($tax_queries)) {
                foreach ($tax_queries as $tax_query) {
                    if (isset($tax_query['taxonomy']) && $tax_query['taxonomy'] !== '' && !in_array($tax_query['taxonomy'], $timeline_taxonomies)) {
                        return;
                    }
                }
            }
        }

        $post_type = $query->get('post_type');

        if ($post_type == '' || $post_type == 'post') {
            $post_type = ['post', 'post-timeline'];
        } elseif (is_array($post_type)) {
            if (in_array('post', $post_type) && !in_array('post-timeline', $post_type)) {
                $post_type[] = 'post-timeline';
            }
        }

        $post_type = apply_filters('post_timeline_query_posts', $post_type, $query);

        $query->set('post_type', $post_type);

        return;
    }

    /**
     * Adds custom CSS from global settings page to site header.
     *
     * @since    0.0.1
     */
    public function output_header_css()
    {
        if (isset($this->settings['ptl-custom-css']) && $this->settings['ptl-custom-css']) {
            $css = '<style type="text/css">' . wp_kses_post($this->settings['ptl-custom-css']) . '</style>';
            echo $css;
        }
    }

    /**
     * [get_timeline_class_name Include that timeline by the code]
     * @param  [type] $timeline_code [description]
     * @return [type]                [description]
     */
    private function get_timeline_class_name($timeline_code)
    {
        //	Validate what timelines we have
        if (in_array($timeline_code, ['0', '1', '2', '3', '4', '5', '6', '0-h', '1-h', '2-h', '3-h', '4-h', '5-h', '6-h'])) {
            if (file_exists(POST_TIMELINE_PLUGIN_PATH . '/includes/layouts/layout-' . $timeline_code . '.php')) {
                // For horizontal timelines include the base file as well
                if (strpos($timeline_code, '-h') != -1) {
                    $base_timeline_code = str_replace('-h', '', $timeline_code);
                }

                $tmpl_class 		= '\PostTimeline\Layouts\Layout_' . str_replace('-', '_', strtoupper($timeline_code));

                //	Add a filter to override the class
                $tmpl_class 		= apply_filters('ptl_layout_override', $tmpl_class);

                //	We have a timeline?
                if (class_exists($tmpl_class)) {
                    return $tmpl_class;
                }
            }
        }

        //	Todo, show a notice that timeline doesn't exist

        return null;
    }

    /**
       * [localize_scripts description]
       * @param  [type] $script_name [description]
       * @param  [type] $variable    [description]
       * @param  [type] $data        [description]
       * @return [type]              [description]
       */
    private function localize_scripts($script_name, $variable, $data)
    {
        //$this->scripts_data[] = [$variable, $data];

        //	Since version 6.2.5
        wp_localize_script($script_name, $variable, $data);
    }

    /**
     * [get_local_script_data Render the scripts data]
     * @return [type] [description]
     */
    private function get_local_script_data($with_tags = false)
    {
        $scripts = '';

        foreach ($this->scripts_data as $script_data) {
            $scripts .= 'var ' . $script_data[0] . ' = ' . (($script_data[1] && !empty($script_data[1])) ? json_encode($script_data[1]) : "''") . ';';
        }

        //	With script tags
        if ($with_tags) {
            $scripts = "<script type='text/javascript' id='post-timeline-script-js'>" . $scripts . '</script>';
        }

        //	Clear it
        $this->scripts_data = [];

        return $scripts;
    }

    /**
     * [temp_social_icons description]
     * @param  [type] $post_id [description]
     * @return [type]          [description]
     */
    public function temp_social_icons()
    {
        $html    = '';
        $post_id = get_the_ID();

        $ptl_social_type = (!empty(get_post_meta($post_id, 'ptl-social-type', true))) ? get_post_meta($post_id, 'ptl-social-type', true) : '';
        $html            = '<div class="ptl-post-page"><ul class="ptl-social-icon">';
        if ($ptl_social_type == 'social-rep') {
            $ptl_social_rep = get_post_meta($post_id, 'ptl-social-rep', true);
            if ($ptl_social_rep) {
                foreach ($ptl_social_rep as $item_key => $item_value) {
                    $html .= '<li><a href="' . $item_value['url'] . '" class="button"><i class="fa-brands fa-' . $item_value['icon'] . '"></i></a></li>';
                    ?>

  				        <?php
                }
            }
        } else {
            $repeat_counter = 0;
            $ptl_global_settings = get_option('post_timeline_global_settings');
            if(isset($ptl_global_settings['ptl_social_shares'])) {

                $ptl_social_shares = $ptl_global_settings['ptl_social_shares'];

                foreach ($ptl_social_shares as $key => $value) {
                    $key = str_replace('ptl_', '', $key);
    
                    $links = [
                        ['facebook' => '//www.facebook.com/sharer/sharer.php?u=', 'icon'=>'facebook-f'],
                        ['twitter'   => '//twitter.com/intent/tweet?text=PostTimeLine&amp;url=', 'icon'=>'twitter'],
                        ['linkedin'  => '//www.linkedin.com/', 'icon'=>'linkedin-in'],
                        ['pinterest' => '//plus.google.com/share?url=', 'icon'=>'pinterest'],
    
                    ];
    
                    if ($value == 'on') {
                        $html .= '<li><a href="javascript:void(0);" class="button" data-sharer="' . $key . '" data-title="' . get_the_title($post_id) . '" data-url="' . get_permalink($post_id) . '"><i class="fa-brands fa-' . $links[$repeat_counter]['icon'] . '"></i></a></li>';
                    }
    
                    $repeat_counter++;
                }
            }
        }
        $html .= '</ul></div>';

        return $html;
    }
}
