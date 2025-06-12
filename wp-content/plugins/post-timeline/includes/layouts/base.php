<?php

namespace PostTimeline\Layouts;

use PostTimeline\Helper;

/**
 * The file that defines the core plugin class
 *
 * @since      5.5
 *
 * @package    Post PostTimeline\Layout
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
class Base
{
    //	Overriding attributes
    public $atts;

    //	All the active settings
    public $settings;

    //	All posts
    public $all_posts;

    //	Tag List
    public $tag_list;

    //	The Query
    public $the_query;

    // Query Arguments
    public $query_args;

    //	Is a horizontal timeline?
    public $horizontal;

    // Placeholder image for lazyLoad
    public $placeholder_img;

    //	All the styles in one tag
    public $style_css = '';

    //	to change the attributes
    public $attr_updates = [];

    /**
     * @since    0.0.1
     * @access   protected
     * @var      string    $default_settings
     */
    protected $default_settings;

    public $plugin_name =  POST_TIMELINE_SLUG;

    public function __construct($_atts)
    {
        $this->atts 	          = $_atts;
        $this->placeholder_img = POST_TIMELINE_URL_PATH . 'public/img/thumbnail.jpg';

        //	Get all data
        $all_posts = $this->query_all_posts();

        //	Arrange Posts by the tag/date array
        $this->categorize_posts($all_posts);
    }

    /**
     * [load_fonts Load the fonts]
     * @return [type] [description]
     */
    public function load_fonts()
    {
        if(isset($this->settings['ptl-fonts-title']) && isset($this->settings['ptl-fonts-title'])) {
            $font           = $this->settings['ptl-fonts-title'];
            $font_name      = ($font['font']['family-title']);
            $font_name_full = '';

            $ptl_google_fonts = file_get_contents(POST_TIMELINE_PLUGIN_PATH . '/includes/gfonts.json');
            $ptl_google_fonts = json_decode($ptl_google_fonts, true);

            if($ptl_google_fonts && isset($ptl_google_fonts[$font_name])) {
                $font_name      = $ptl_google_fonts[$font_name]['family'];
                $font_name_full = $font_name;
                $font_name      = urlencode($font_name);
            }

            $font_vars = '';
            if(isset($font['font']['variants-title'])) {
                $font_vars = implode(',', $font['font']['variants-title']);
            }
            if($font_vars) {
                $font_vars = ':' . $font_vars;
            }

            $font_subsets = '';
            if(isset($font['font']['subsets-title'])) {
                $font_subsets = implode(',', $font['font']['subsets-title']);
            }
            if($font_subsets) {
                $font_subsets = ':' . $font_subsets;
            }

            if($font_name) {
                $font_url = 'https://fonts.googleapis.com/css?family=' . $font_name . $font_vars . $font_subsets;
                wp_enqueue_style('post-timeline' . '-fonts', $font_url, [], '', '');
            }
        }
    }

    /**
     * [_query_clause_tag_order Set the tag order clause]
     * @return [type]          [description]
     */
    private function _query_clause_tag_order()
    {
        add_filter('posts_clauses', function ($clauses, $query) {
            if (
                isset($query->query_vars['post_type']) &&
                $query->query_vars['post_type'] === 'post-timeline' &&
                isset($query->query_vars['orderby']) &&
                $query->query_vars['orderby'] === 'ptl_tag_order'
            ) {
                global $wpdb;

                // Determine the order (default to ASC if not set)
                $order = strtoupper($query->get('order')) === 'DESC' ? 'DESC' : 'ASC';

                // Add custom JOIN clauses
                $clauses['join'] .= "
                    INNER JOIN {$wpdb->term_relationships} AS tr ON {$wpdb->posts}.ID = tr.object_id
                    INNER JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    INNER JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
                ";

                // Modify the GROUP BY clause to ensure proper ordering
                $clauses['groupby'] = "{$wpdb->posts}.ID";

                // Modify the ORDER BY clause to include the tag_order column
                $clauses['orderby'] = "MAX(t.tag_order) $order";
            }

            return $clauses;
        }, 10, 2);
    }

    /**
     * [query_all_posts compile all the data]
     * @return [type] [description]
     */
    public function query_all_posts()
    {
        //  All the attributes to override defaults
        $atts = $this->atts;

        //  Settings from the DB
        $ptl_settings = get_option('post_timeline_global_settings');

        //  Temporary off this feature
        if (isset($ptl_settings['ptl-lazy-load'])) {
            $ptl_settings['ptl-lazy-load'] = 'off';
        }

        // Change Pricing if option is enable
        $this->default_settings = \PostTimeline\Helper::get_default_settings();

        $ptl_settings = (!empty($ptl_settings) ? array_merge($this->default_settings, $ptl_settings) : $this->default_settings);

        $ptl_settings = (!empty($atts) ? array_merge($ptl_settings, $atts) : $ptl_settings);

        //  merge with the attributes
        $ptl_settings = shortcode_atts($ptl_settings, $atts);

        if ($ptl_settings['ptl-anim-speed'] > 2) {
            $ptl_settings['ptl-anim-speed'] = 1;
        }

        //add the missing attributes into settings
        $ptl_settings = array_merge($ptl_settings, $atts);

        $per_page = $ptl_settings['ptl-post-per-page'];

        //For Class
        $ptl_settings['class'] = isset($atts['class']) ? $atts['class'] : null;

        //Restrict category
        $taxonomy     = isset($atts['ptl-taxonomy']) ? $atts['ptl-taxonomy'] : null;
        $category_ids = isset($atts['ptl-category']) ? $atts['ptl-category'] : null;

        //0 for Load All
        if($ptl_settings['ptl-post-load'] == 0) {
            $per_page = -1;
        }

        $sort_order = ($ptl_settings['ptl-sort'] == '1' || $ptl_settings['ptl-sort'] == 'ASC') ? 'ASC' : 'DESC';

        //Get the title Post
        $top_post       = (isset($atts['header']) && $atts['header']) ? get_post($atts['header']) : null;
        $current_page   = get_query_var('paged');
        $current_page   = max(1, $current_page);
        $offset_start   = 1;
        $offset         = ($current_page - 1) * (int)$per_page + $offset_start;
        $paged          = (get_query_var('paged')) ? absint(get_query_var('paged')) : 1;

        $query_args =  [
            'post_type'         => $ptl_settings['ptl-post-type'],
            'post_status'       => 'publish',
            'posts_per_page'    => $per_page,
            'paged'             => $paged,
            'page'              => $paged,
            'order'             => $sort_order,
        ];

        if (isset($ptl_settings['ptl-sort-by'])) {
            if ($ptl_settings['ptl-sort-by'] == 'meta') {
                $query_args['meta_key'] = isset($ptl_settings['ptl-sort-meta']) ? $ptl_settings['ptl-sort-meta'] : '';
                $query_args['orderby']  = isset($ptl_settings['ptl-sort-meta-num']) ? 'meta_value_num' : 'meta_value';
            } elseif ($ptl_settings['ptl-sort-by'] == 'ptl-post-date') {
                $query_args['meta_key'] = 'ptl-post-date';
                $query_args['orderby']  = 'meta_value';
            } else {
                $query_args['orderby'] = $ptl_settings['ptl-sort-by'];
            }
        }

        // Remove Header Post
        if($top_post) {
            $query_args['post__not_in'] = [$top_post->ID];
            $ptl_settings['top_id']     = $top_post->ID;
        }

        // Include Post ids
        if((isset($ptl_settings['ptl-filter-ids']))) {
            $inc_ids = explode(',', $ptl_settings['ptl-filter-ids']);

            $query_args['post__in'] = $inc_ids;
        }

        // Exclude Post ids
        if((isset($ptl_settings['ptl-exclude-ids']))) {
            $exc_ids = explode(',', $ptl_settings['ptl-exclude-ids']);

            $query_args['post__not_in'] = $exc_ids;
        }

        // Filter by Category
        if($category_ids) {
            $category_ids = explode(',', $category_ids);

            if ($ptl_settings['ptl-post-type'] == 'post-timeline') {
                $taxonomy = (isset($atts['ptl-all-categories'])) ? 'category' : 'ptl_categories';
            }

            $query_args['tax_query'] = [
                [
                    'taxonomy'         => $taxonomy,
                    'terms'            => $category_ids,
                    'field'            => 'id',
                    'include_children' => true,
                    'operator'         => 'IN'
                ]
            ];
        }

        //  Sort By TAG
        if($ptl_settings['ptl-type'] == 'tag') {
            // Add the tag order clause
            $this->_query_clause_tag_order();

            $cat_tax_query = (isset($query_args['tax_query']) ? $query_args['tax_query'] : '');

            unset($query_args['orderby'], $query_args['meta_key'], $query_args['order']);

            if ($ptl_settings['ptl-term-csort'] == 'on') {
                $query_args['orderby'] = 'ptl_term_order';
            } elseif($ptl_settings['ptl-post-csort'] == 'on') {
                $query_args['meta_query'] = [
                    'meta_query' => [
                        'post_order_clause' => [
                            'key'     => 'ptl-post-order',
                            'compare' => 'EXIST',
                        ],
                    ],
                ];

                $query_args['orderby'] = 'post_order_clause';
            } else {
                $query_args['orderby'] = 'ptl_tag_order';
            }

            $query_args['order'] = $sort_order;

            if ($ptl_settings['ptl-post-type'] == 'post-timeline') {
                $query_args['tax_query'] = [
                    [
                        'taxonomy' => 'ptl_tag',
                        'field'    => 'slug',
                        'operator' => 'EXISTS',
                    ]
                ];

                if ($category_ids) {
                    $category_clause    = [];

                    $category_clause['relation'] =  'AND';

                    $query_args['tax_query'] = array_merge($cat_tax_query, $query_args['tax_query']) ;
                    $query_args['tax_query'] = array_merge($category_clause, $query_args['tax_query']) ;
                }
            }
        }
        // Sort by Date
        elseif($ptl_settings['ptl-type'] == 'date' && $ptl_settings['ptl-post-type'] == 'post-timeline') {
            $query_args['meta_query'] = [
                'meta_query' => [
                    'relation'         => 'AND',
                    'post_date_clause' => [
                        'key'       => 'ptl-post-date',
                        'value'     => '',
                        'compare'   => '!=',
                    ],
                    'post_order_clause' => [
                        'key'     => 'ptl-post-order',
                        'compare' => 'EXIST',
                    ],
                ],
            ];

            $query_args['orderby'] = [
                'post_date_clause'  => $sort_order,
                'post_order_clause' => $sort_order,
            ];
        }
        //  Unset things if not post-timeline
        if($ptl_settings['ptl-post-type'] != 'post-timeline') {
            // $query_args['orderby']   =  'publish_date';
            $query_args['order']    =  $sort_order;
            // unset($query_args['meta_key']);
            unset($query_args['meta_query']);
        }
        //  Unset things if not post-timeline
        if($ptl_settings['ptl-post-type'] == 'post' && $ptl_settings['ptl-type'] == 'tag') {
            $cat_tax_query = (isset($query_args['tax_query']) ? $query_args['tax_query'] : '');

            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'operator' => 'EXISTS',
                ]
            ];

            if ($category_ids) {
                $category_clause    = [];

                $category_clause['relation'] =  'AND';

                $query_args['tax_query'] = array_merge($cat_tax_query, $query_args['tax_query']) ;
                $query_args['tax_query'] = array_merge($category_clause, $query_args['tax_query']) ;
            }
        }
        //  Unset things if not post-timeline
        if($ptl_settings['ptl-post-type'] != 'post-timeline' && $ptl_settings['ptl-type'] == 'tag') {
            $cat_tax_query = (isset($query_args['tax_query']) ? $query_args['tax_query'] : '');
            $tag           = (isset($ptl_settings['ptl-tag-taxonomy'])) ? $ptl_settings['ptl-tag-taxonomy'] : $ptl_settings['ptl-post-type'] . '_tag';

            $query_args['tax_query'] = [
                [
                    'taxonomy' => $tag,
                    'field'    => 'slug',
                    'operator' => 'EXISTS',
                ]
            ];

            if ($category_ids) {
                $category_clause    = [];

                $category_clause['relation'] =  'AND';

                $query_args['tax_query'] = array_merge($cat_tax_query, $query_args['tax_query']) ;
                $query_args['tax_query'] = array_merge($category_clause, $query_args['tax_query']) ;
            }
        }

        //  All the posts
        $posts = null;

        $the_query = new \WP_Query($query_args);
        $posts     = $the_query->posts;

        //  All Settings
        $this->settings     = $ptl_settings;

        $this->the_query    = $the_query;

        $this->query_args   = $query_args;

        return $posts;
    }

    /**
     * [categorize_posts Categorize the Posts by the Tag or Date Array]
     * @return [type] [description]
     */
    public function categorize_posts($all_posts)
    {
        //  $all settings
        $all_configs = $this->settings;

        //  is Date?
        $by_date = ($all_configs['ptl-type'] == 'date') ? true : false;

        $tags_list     = [];
        $posts_by_tags = [];

        //  Loop over posts
        foreach ($all_posts as $_post) {
            $post_id = $_post->ID;

            //  Get the Custom Meta
            $_post->custom          = get_post_custom($post_id);

            //  Users can manage content via it!
            $_post                  = apply_filters('ptl_filter_timeline_post', $_post);

            $post_desc              = (isset($all_configs['ptl-post-desc'])) ? $all_configs['ptl-post-desc'] : 'full';

            $_post->post_content    = ($post_desc == 'excerpt') ? $_post->post_excerpt : $_post->post_content;

            // HTML will not have the ... break!
            if (((isset($all_configs['ptl-html'])) && ($all_configs['ptl-html'] == 1 || $all_configs['ptl-html'] == 'on')) && ((isset($all_configs['ptl-post-desc'])) && $all_configs['ptl-post-desc'] == 'full')) {
                // set post length empty
                $this->settings['ptl-post-length'] = '';
            }

            if (!empty($this->settings['ptl-post-length']) || !isset($all_configs['ptl-html'])) {
                $all_configs['ptl-post-length'] = (empty($all_configs['ptl-post-length'])) ? 200 : $all_configs['ptl-post-length'];
                $_post->post_content            = Helper::strip_html($_post->post_content, $all_configs['ptl-post-length']);
            }

            if (!empty($_post->post_content) && !$this->isHTML($_post->post_content)) {
                $_post->post_content = '<p>' . implode("</p>\n\n<p>", preg_split('/\n(?:\s*\n)+/', $_post->post_content)) . '</p>';
            }

            //  post date
            $tl_date = (isset($_post->custom['ptl-post-date'][0]) && $_post->custom['ptl-post-date'][0]) ? $_post->custom['ptl-post-date'][0] : $_post->post_date;

            //  Full date
            $_post->event_date = date_i18n($this->settings['ptl-date-format'], strtotime($tl_date));

            $sort_meta = isset($this->settings['ptl-sort-meta']) ? $this->settings['ptl-sort-meta'] : null;

            //  When timeline is by date
            if($all_configs['ptl-type'] == 'date') {
                //  post date
                $tl_date = (isset($_post->custom['ptl-post-date'][0]) && $_post->custom['ptl-post-date'][0]) ? $_post->custom['ptl-post-date'][0] : $_post->post_date;

                //  Get the year as tag
                $_post->tag  = $this->get_date_year($tl_date);
            } elseif($all_configs['ptl-type'] == 'none') {
                //  Get the year as tag
                $_post->tag  = 'none';
            }
            //  When the timeline is by tag
            else {
                //  Get the tag
                $_post->tag  = $this->get_post_tag($post_id);
            }

            // Set post Icon  type
            if(empty($_post->custom['ptl-icon-type'][0])) {
                $_post->custom['ptl-icon-type'][0]   = $this->settings['ptl-icon-type'];
                $_post->custom['ptl-custom-icon'][0] = $this->settings['ptl-default-custom-icon'];
            }

            // Set post descriptiop color
            if(empty($_post->custom['ptl-post-desc-color'][0])) {
                $_post->custom['ptl-post-desc-color'][0] = $this->settings['ptl-post-desc-color'];
            }

            // Set post Text color
            if(!isset($_post->custom['ptl-post-text-color'][0])) {
                $_post->custom['ptl-post-text-color'][0] = $this->settings['ptl-post-head-color'];
            }

            // Set post BG color
            if(!isset($_post->custom['ptl-post-color'][0])) {
                $_post->custom['ptl-post-color'][0] = $this->settings['ptl-post-bg-color'];
            }

            // Set post link url
            $post_url = get_permalink($post_id);
            if(empty($_post->custom['ptl-post-link'][0])) {
                $_post->custom['ptl-post-link'][0] = $post_url;
            }

            // Set post thumbnail
            $post_thumnail = get_post_thumbnail_id($post_id);
            if(empty($_post->custom['_thumbnail_id'][0])) {
                $_post->custom['_thumbnail_id'][0] = $post_thumnail;
            }

            // Set post BG color
            if(empty($_post->custom['ptl-media-type'][0])) {
                $_post->custom['ptl-media-type'][0] = 'image';
            }

            // Set post icon
            if(empty($_post->custom['ptl-fav-icon'][0])) {
                $_post->custom['ptl-fav-icon'][0] = $this->settings['ptl-fav-icon'];
            }

            //  Create the Node if not exist
            if(empty($tag_list[$_post->tag])) {
                $tag_list[$_post->tag]          = $_post->tag;
                $posts_by_tags[$_post->tag]     = [];
            }

            $posts_by_tags[$_post->tag][] = $_post;
        }
        // END OF LOOP

        //  the Tag list
        $this->tag_list             = (isset($tag_list) ? $tag_list : '');

        //  The Post list
        $this->all_posts            = $posts_by_tags;
    }

    /**
     * [isHTML check if content has HTML]
     * @return [type] [description]
     */
    public function isHTML($string)
    {
        return $string != strip_tags($string) ? true : false;
    }

    /**[add_tag]
     * @return [type] [description]
     * [add_tag to render the post tag
     */
    public function add_tag($tag, $color)
    {
        $hash = $tag;
        $html = '';
        if ($this->settings['ptl-type'] == 'tag') {
            $hash 		= strtolower($tag);
            $hash 		= str_replace(' ', '-', $hash);
        }

        // <!-- Year Row Start -->
        $html .= '<div  class="ptl-row">';
        $html .= '<div class="pol-md-12">';

        $html .= '<div class="panim ptl-callb" ptl-anim-tag="' . $hash . '" id="' . $hash . '" data-tag="' . $hash . '"></div>';
        if ($this->settings['ptl-type'] != 'none') {
            $html .= '<div  class="ptl-sec-year" style="opacity:0;" data-id="' . $hash . '">' . $tag . '</div>';
        }
        $html .= '</div>';
        $html .= '</div>';
        // <!-- Year Row End -->

        return $html;
    }

    /**
     * [render_template The common render template method]
     * @return [type] [description]
     */
    public function render_template()
    {
        $all_posts = $this->all_posts;

        $cont_bg        = '';
        $nav_clr 			    = $this->settings['ptl-nav-color'];
        $tagline_clr 	  = $this->settings['ptl-tagline-color'];
        $radius 	       = (!empty($this->settings['ptl-post-radius'])) ? '--ptl-border-radius: ' . $this->settings['ptl-post-radius'] . 'px;' : '';
        $size_content 	 = (!empty($this->settings['ptl-size-content'])) ? '--ptl-post-ctn-size: ' . $this->settings['ptl-size-content'] . 'px;' : '';
        $letter_spacing = (!empty($this->settings['ptl-letter-spacing'])) ? '--ptl-post-ltr-spacing: ' . $this->settings['ptl-letter-spacing'] . 'px;' : '';
        $cstm_css_class = (!empty($this->settings['ptl-css-class']) ? ' ' . $this->settings['ptl-css-class'] : '');
        $html 			       = '';

        // Load Fonts
        $html .= $this->load_fonts();

        if(isset($this->settings['ptl-fonts-title']['font']['family-title'])):
            $html = '<style type="text/css">';
            $html .= '.ptl-cont *:not(.fa):not(.glyphicon):not(i){font-family:' . $this->settings['ptl-fonts-title']['font']['family-title'] . ' !important}';
            $html .= '</style>';
        endif;

        $html .= '<style type="text/css">';

        if(!is_admin()) {
            $html .= 'body .ptl-cont .ptl-content-loaded{';
            $html .= 'opacity:0 !important;';
            $html .= '}';
        }
        $html .= '#ptl-' . $this->settings['unique_id'] . ' {';
        $html .= '--ptl-primary:' . $tagline_clr . ';';
        $html .= '--ptl-nav:' . $nav_clr . ';';
        $html .= '--ptl-line:' . $tagline_clr . ';';
        $html .= $radius;
        $html .= $size_content;
        $html .= $letter_spacing;
        $html .= '}';
        $html .= '</style>';

        if ($this->settings['ptl-bg-status'] == 'on') {
            $cont_bg = 'style="background:' . $this->settings['ptl-bg-color'] . '"';
        }

        $template = $this->settings['template'];

        $class_tmpl       = $template;
        $responsive_class = '';

        //  FoR horizontal Template
        if ((isset($this->settings['ptl-layout']))) {
            if ($this->settings['ptl-layout'] == 'horizontal') {
                $class_tmpl = $template . '-h';
            }
            // For One Side Template
            elseif ($this->settings['ptl-layout'] == 'one-side') {
                $class_tmpl = str_replace('-one', '', $template);
            } elseif ($this->settings['ptl-layout'] == 'vertical') {
                $responsive_class = 'ptl-both-side';
            }
        }

        $layout_class    = (isset($this->settings['ptl-layout']) && $this->settings['ptl-layout'] == 'one-side') ? 'ptl-one-side-' . $class_tmpl : '';
        $layout_position = (isset($this->settings['ptl-layout']) && $this->settings['ptl-layout'] == 'one-side' && isset($this->settings['ptl-position'])) ? 'ptl-one-side-' . $this->settings['ptl-position'] : '';

        $html .= '<div id="ptl-' . $this->settings['unique_id'] . '" data-id="' . $this->settings['unique_id'] . '" class="ptl-cont ptl-tmpl-' . $class_tmpl . ' ' . $layout_class . ' ' . $this->settings['ptl-skin-type'] . ' ' . $layout_position . ' ' . $responsive_class . $cstm_css_class . '" ' . $cont_bg . '>';

        //	Add the global loader
        $loader = $this->settings['ptl-loader'];

        if($loader && !is_admin()) {
            $html .= '<div class="ptl-loader-overlay"><span class="ptl-preloder ' . $loader . '"></span></div>';
        }

        //	Main opening tag of the timeline
        $html .= $this->template_upper_section();

        $step                = 1;
        $last_post_postition = '';
        foreach ($all_posts as $tag => $post_list) {
            //	Get the color
            $color = $this->get_post_color($post_list[0]);

            $hash  = $tag;

            if ($this->settings['ptl-type'] == 'tag') {
                $hash 		= strtolower($tag);
                $hash 		= str_replace(' ', '-', $hash);
            }

            $html .= '<span id="ptl-tag-' . $hash . '" class="ptl-ach-tag" data-scroll-id="ptl-tag-' . $hash . '" tabindex="-1" style="outline: none;"></span>';

            if ($this->settings['template'] != '4' && $this->settings['template'] != '6') {
                //	Add a tag posts separator
                $html .= $this->add_tag($tag, $color);
            }

            //	Loop over the list of Posts
            foreach ($post_list as $post_index => $post) {
                if (count($post_list) == 1) {
                    $post->section = 1;
                } elseif ($post_index == 0) {
                    $post->section = 'start';
                } elseif ($post_index == count($post_list) - 1) {
                    $post->section = 'end';
                } else {
                    $post->section = '';
                }

                if ($this->settings['template'] == '4' || $this->settings['template'] == '6') {
                    $post->step = str_pad($step, 2, '0', STR_PAD_LEFT);
                }

                $is_even = ($post_index % 2 == 0) ? true : false;

                if ($this->settings['template'] != '4' || $this->settings['template'] != '6') {
                    if (count($post_list) == 1) {
                        $is_even = ($last_post_postition == false) ? true : false;
                    }
                }

                $html .= $this->render_post($post, $is_even, count($post_list));

                $last_post_postition = $is_even;
            }
            $step++;
        }

        //	Add a tag posts separator closing div
        $html .= $this->template_lower_section();

        $html .= '</div>';

        return $html;
    }

    /**
     * [render_ajax_template The common render template method]
     * @return [type] [description]
     */
    public function render_ajax_template($step)
    {
        $all_posts = $this->all_posts;

        $section = '';

        $old_tag             = '';
        $step                = $step + 1;
        $last_post_postition = '';
        foreach ($all_posts as $tag => $post_list) {
            //	Get the color
            $color = $this->get_post_color($post_list[0]);

            $hash = $tag;

            if ($this->settings['ptl-anim-speed'] > 2) {
                $this->settings['ptl-anim-speed'] = 1;
            }

            if ($this->settings['ptl-type'] == 'tag') {
                $hash 		= strtolower($tag);
                $hash 		= str_replace(' ', '-', $hash);
            }
            // echo $old_tag ;
            if ($old_tag != $tag) {
                $section .= '<span id="ptl-tag-' . $hash . '" class="ptl-ach-tag" data-scroll-id="ptl-tag-' . $hash . '" tabindex="-1" style="outline: none;"></span>';

                if ($this->settings['template'] != '4' && $this->settings['template'] != '6') {
                    //	Add a tag posts separator
                    $section .= $this->add_tag($tag, $color);

                    $old_tag = $tag;
                }
            }

            //	Loop over the list of Posts
            foreach ($post_list as $post_index => $post) {
                if (count($post_list) == 1) {
                    $post->section = 1;
                } elseif ($post_index == 0) {
                    $post->section = 'start';
                } elseif ($post_index == count($post_list) - 1) {
                    $post->section = 'end';
                } else {
                    $post->section = '';
                }

                if ($this->settings['template'] == '4' || $this->settings['template'] == '6') {
                    $post->step = str_pad($step, 2, '0', STR_PAD_LEFT);
                }

                $is_even = ($post_index % 2 == 0) ? true : false;

                if ($this->settings['template'] != '4' || $this->settings['template'] != '6') {
                    if (count($post_list) == 1) {
                        $is_even = ($last_post_postition == false) ? true : false;
                    }
                }

                $section .= $this->render_post($post, $is_even, count($post_list));
                $last_post_postition = $is_even;
            }
            $step++;
        }

        if (!empty($this->style_css)) {
            $section .= '<style type="text/css">' . $this->style_css . '</style>';
        }

        return $section;
    }

    /**
     * [ajax_call_posts]
     * @return [type] [description]
     */
    public function ajax_call_posts($paged, $step)
    {
        $current_page              = $paged;
        $this->query_args['paged'] = $paged;
        $per_page                  = $this->query_args['posts_per_page'];

        $offset_start = 1 ;
        $offset       = ($current_page - 1) * $per_page + $offset_start;
        $post_data    = [];
        $the_query    = new \WP_Query($this->query_args);

        $posts 	   = $the_query->posts;
        $this->categorize_posts($posts);
        return $this->render_ajax_template($step);
    }

    /**
     * [load_more description]
     * @param  [type] $wp_query [description]
     * @return [type]           [description]
     */
    private function load_more($wp_query)
    {
        if($wp_query->max_num_pages <= 1) {
            return '';
        }

        $html = '';

        $html  = '<div class="plt-load-more" data-steps="' . $wp_query->post_count . '">';
        if ($this->settings['ptl-pagination'] == 'button') {
            $html .= '  <button class="ptl-more-btn' . ($wp_query->max_num_pages == 1 ? ' disabled' : '') . '" data-loading-text="" data-completed-text="' . __('Load More', 'post-timeline') . '" data-href="" data-page="' . (get_query_var('paged') ? get_query_var('paged') : '1') . '" data-max-pages="' . $wp_query->max_num_pages . '">' . __('Load More', 'post-timeline');
            $html .= '  </button>' . "\n";
        }

        $html .= '<div class="ptl-no-more-posts"><span></span></div>' . "\n";
        $html .= '</div>' . "\n";

        return $html;
    }

    /**
     * [template_upper_section Renders Upper section of template]
     * @return [type] [description]
     */
    public function template_upper_section()
    {
        $all_posts = $this->all_posts;

        $html = '<div class="ptl-container">';
        $html .= '<div class="ptl-row">';
        $html .= '<div class="pol-lg-11 pol-md-12">';
        $html .= '<div class="ptl-tmpl-main my-5">';

        if ($this->settings['ptl-nav-status'] == 'on' && $this->settings['ptl-type'] != 'none') {
            $nav_style = (isset($this->atts['ptl-nav-type']) ? $this->atts['ptl-nav-type'] : $this->atts['template']);

            $html .= '<div class="ptl-nav-wrapper ptl-content-loaded ptl-nav-' . $nav_style . '">';
            // Render Post avigation
            $html .= $this->render_navigation();
            $html .= '</div>';
        }

        $html .= '<div class="ptl-tmpl-main-inner">';

        if ($this->settings['ptl-type'] == 'tag' && count($all_posts) <= 0) {
            $html .= '<div class="ptl-no-post"><span>' . esc_attr__('No tags are assigned to posts, please assign relevant tags to posts.', 'post-timeline') . '</span></div>';
        } else {
            $html .= '<span class="ptl-center-line ' . $this->settings['ptl-line-style'] . '" style="opacity:0;"></span>';
        }

        return $html;
    }

    /**
     * [template_lower_section Renders lower section of template]
     * @return [type] [description]
     */
    public function template_lower_section()
    {
        $html = '</div>';
        if (isset($this->settings['ptl-pagination']) && $this->settings['ptl-pagination'] != 'off') {
            // LoadMore Button
            $html .= $this->load_more($this->the_query);
        }

        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        //	Add the style CSS
        $html .= '<style type="text/css">' . $this->style_css . '</style>';

        //  open stream
        ob_start();
        // include Video Modal
        include POST_TIMELINE_PLUGIN_PATH . 'public/partials/modal-popup.php';
        $html .= ob_get_contents();

        //  clean it
        ob_end_clean();

        if ($this->settings['ptl-slides-setting'] == 'popup') {
            // Popup Gallery
            $html .= '<div class="popout fade ptl-gallery-popup" tabindex="-1" aria-labelledby="post-timeline-gallery-popout" aria-hidden="true">';
            $html .= '<div class="popout-dialog popout-lg">';
            $html .= '<div class="popout-content">';
            $html .= '<div class="ptl-popup-carousel"></div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    /**[post_thumbnail]
     * @return [type] [description]
     * [post_thumbnail to render the post Image/Video
     */
    public function render_post_thumbnail($post)
    {
        $post_media_type 	= (isset($post->custom['ptl-media-type'][0])) ? $post->custom['ptl-media-type'][0] : 'image';
        $html 				= '';
		
        $post_thumbnail = (isset($post->custom['_thumbnail_id'][0])) ? $post->custom['_thumbnail_id'][0] : get_post_thumbnail_id($post->ID);

        $img 		     = wp_get_attachment_image_src($post_thumbnail, 'full');
        $image_alt  	 = 'placeholder image';
        if ($img) {
            $img       = $img[0];
            $image_alt = (!empty(get_post_meta($post_thumbnail, '_wp_attachment_image_alt', true))) ? get_post_meta($post_thumbnail, '_wp_attachment_image_alt', true) : get_the_title($post_thumbnail);
        } else {
            $img = '';
        }

        if ($post_media_type == 'gallery' && isset($post->custom['ptl_gallery'][0])) {
            $gallery = $post->custom['ptl_gallery'][0];
			
            $gallery = explode(',', $gallery);
            $gallery = array_filter($gallery);
			
			
            if (!empty($gallery)) {
                if ($this->settings['ptl-slides-setting'] == 'carousel') {
                    $html = '<div class="owl-carousel owl-theme ptl-media-gallery-' . $post->ID . '  ptl-media-post-gallery">';

                    if ($img) {
                        $img_src = 'src="' . $img . '"';

                        if ($this->settings['ptl-lazy-load'] == 'on') {
                            $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $img . '"';
                        }

                        $html .= '<div><img ' . $img_src . '  alt="' . $image_alt . '"/></div>';
                    }

                    foreach ($gallery as $image) {
                        if ($image) {
                            $url = wp_get_attachment_image_src($image, 'full')[0];

                            $img_src   = 'src="' . $url . '"';
                            $image_alt = (!empty(get_post_meta($image, '_wp_attachment_image_alt', true))) ? get_post_meta($image, '_wp_attachment_image_alt', true) : get_the_title($image);

                            if ($this->settings['ptl-lazy-load'] == 'on') {
                                $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $url . '"';
                            }

                            $html .= '<div><img ' . $img_src . ' alt="' . $image_alt . '" /></div>';
                        }
                    }
                    $html .= '</div>';
                } 
				elseif ($this->settings['ptl-slides-setting'] == 'popup') {
                    $img_src = 'src="' . $img . '"';

                    if ($this->settings['ptl-lazy-load'] == 'on') {
                        $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $img . '"';
                    }

                    $html = '<img data-toggle="popout" class="ptl-gallery-popup-btn" data-post="' . $post->ID . '" ' . $img_src . ' alt="' . $image_alt . '" />';
                }
            } 
			else {
                if ($img) {
                    $img_src = 'src="' . $img . '"';

                    if ($this->settings['ptl-lazy-load'] == 'on') {
                        $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $img . '"';
                    }

                    $html .= '<img ' . $img_src . ' alt="' . $image_alt . '" />';
                }
            }
        }
		elseif ($post_media_type == 'video' && isset($post->custom['ptl-video-url'][0])) {
            $video_url   = (isset($post->custom['ptl-video-url'][0])) ? $post->custom['ptl-video-url'][0] : '';

            if ($img) {
                $img_src = 'src="' . $img . '"';

                if ($this->settings['ptl-lazy-load'] == 'on') {
                    $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $img . '"';
                }

                $html .= '<img ' . $img_src . ' alt="' . $image_alt . '"  />';
            }

            if (!empty($video_url)) {
                $html .= '<span class="ptl-video-btn" data-src="' . $video_url . '" ><i class="fa-solid fa-play"></i></span>';
            }
        } else {
            if ($img) {
                $img_src = 'src="' . $img . '"';

                if ($this->settings['ptl-lazy-load'] == 'on') {
                    $img_src = 'src="' . $this->placeholder_img . '" data-src="' . $img . '"';
                }

                $html .= '<img ' . $img_src . ' alt="' . $image_alt . '"  />';
            }
        }

        return $html;
    }

    /**[render_navigation]
     * @return [type] [description]
     * [render_navigation to render the post
     */
    public function render_navigation()
    {
        $html = '';
        $html .= '<div class="yr_list">';
        $html .= '<div class="btn-top ptl-btn temp"><a><i class="fa-solid fa-chevron-up"></i></a></div>';
        $html .= '<div class="yr_list-view">';
        if (!empty($this->tag_list)) {
            $html .= '<ul class="list-unstyled yr_list-inner menu-timeline">';
            foreach ($this->tag_list as $key => $list) {
                $hash = strtolower($list);
                $hash = str_replace(' ', '-', $hash);

                $html .= '<li data-tag="' . $hash . '"><a  data-scroll data-options="' . '"{ "easing": "Quart" }"' . '" data-href="#ptl-tag-' . $hash . '"><span>' . $list . '</span></a></li>';
            }
            $html .= '</ul>';
        }
        $html .= '</div>';
        $html .= '<div class="btn-bottom ptl-btn temp"><a><i class="fa-solid fa-chevron-down"></i></a></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * [social_icons description]
     * @param  [type] $post_id [description]
     * @return [type]          [description]
     */
    public function social_icons($post_id)
    {
        $html = '';

        $ptl_social_type = (!empty(get_post_meta($post_id, 'ptl-social-type', true))) ? get_post_meta($post_id, 'ptl-social-type', true) : $this->settings['ptl-social-type'];
        $html            = '<ul class="ptl-social-icon">';
        if ($ptl_social_type == 'social-rep') {
            $ptl_social_rep = get_post_meta($post_id, 'ptl-social-rep', true);
            if($ptl_social_rep) {
                foreach($ptl_social_rep as $item_key => $item_value) {
                    $html .= '<li><a href="' . $item_value['url'] . '" class="button"><i class="fa-brands fa-' . $item_value['icon'] . '"></i></a></li>';
                    ?>

  				        <?php
                }
            }
        } else {
            $repeat_counter = 0;
            foreach ($this->settings['social-shares'] as $key => $value) {
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
        $html .= '</ul>';

        return $html;
    }

    /**[ajax_render_navigation]
     * @return [type] [description]
     * [ajax_render_navigation to render the post
     */
    public function ajax_render_navigation()
    {
        $tags = [];

        if ($this->tag_list) {
            foreach ($this->tag_list as $key => $list) {
                $hash = strtolower($list);
                $hash = str_replace(' ', '-', $hash);

                $tags[] = [$hash, $list];
            }
        }

        return $tags;
    }

    /**
     * [tag_name to render the year node]
     * @return [type] [description]
     */
    protected function tag_name()
    {
    }

    /**
     * [render_line description]
     * @param  [type] $line_type [description]
     * @return [type]            [description]
     */
    protected function render_line()
    {
    }

    /**
     * [get_year_list Get the years for the Date based]
     * @param  [type] $timeline_posts [description]
     * @return [type]                 [description]
     */
    public function get_year_list($timeline_posts)
    {
        $tag_list = [];
        foreach($timeline_posts as $child) {
            $date = $child->event_date;

            if($date) {
                $date_comp        = explode('-', $date);
                $child->date_comp = $date_comp;

                //capture the year
                if(count($date_comp) >= 1) {
                    if(!in_array($date_comp[0], $tag_list)) {
                        $tag_list[] = $date_comp[0];
                    }
                }
            }
        }

        //make it key value pair
        $temp_list = [];

        foreach($tag_list as $y) {
            $temp_list[$y] = $y;
        }

        return $temp_list;
    }

    /**
     * [get_tag_list Get the Tags for the Tag based]
     * @param  [type] $timeline_posts [description]
     * @return [type]                 [description]
     */
    public function get_tag_list($timeline_posts)
    {
        $tag_list = [];

        foreach($timeline_posts as $child) {
            $term_id = [];
            $tag 		  = [0];

            // $tag = $child->custom['ptl-post-tag'];
            $gettermid = wp_get_post_terms($child->ID, 'post_tag');

            if ($gettermid) {
                $term_id[] = $gettermid[0]->term_id;
                $tag 			   = $term_id;
            }

            // Missing Tags
            if(!$tag[0]) {
                $tag = [0];
            }

            if($tag) {
                $child->date_comp = $tag;

                //capture the Tag
                if(!in_array($tag[0], $tag_list)) {
                    $tag_list[] = $tag[0];
                }
            }
        }

        //make it key value pair
        $temp_list = [];

        foreach($tag_list as $l) {
            $term = null;

            //Empty Terms
            if($l == 0) {
                $term          = new \stdclass();
                $term->term_id = 0;
                $term->name    = 'None';
            } else {
                $term = get_term($l);

                //Term not found
                if(!$term) {
                    $term          = new \stdclass();
                    $term->term_id = 0;
                    $term->name    = 'None';
                }
            }

            if($term) {
                $temp_list[$term->term_id] = $term->name;
            }
        }

        return $temp_list;
    }

    /**
     * [cmp_year description]
     * @param  [type] $a [description]
     * @param  [type] $b [description]
     * @return [type]    [description]
     */
    public function cmp_year($a, $b)
    {
        return strcmp($a->event_date, $b->event_date);
    }

    /**
     * [get_date_year get the year]
     * @param  [type] $date_str [description]
     * @return [type]           [description]
     */
    private function get_date_year($date_str)
    {
        $date_split = explode('-', $date_str);

        return (string)$date_split[0];
    }

    /**
     * [get_post_color get the color of the post]
     * @param  [type]  $post       [description]
     * @param  boolean $background [description]
     * @return [type]              [description]
     */
    protected function get_post_color($post, $background = false)
    {
        return (isset($post->custom['ptl-post-color'][0])) ? $post->custom['ptl-post-color'][0] : $this->settings['ptl-post-bg-color'];
    }

    /**
     * [get_post_tag get the tag of the post]
     * @param  [type] $post_id [description]
     * @return [type]          [description]
     */
    private function get_post_tag($post_id)
    {
        $post_term = '';

        $taxonomy_names = wp_get_object_terms($post_id, 'ptl_tag', ['fields' => 'names']);

        if (isset($this->settings['ptl-post-type']) && $this->settings['ptl-post-type'] == 'post') {
            $taxonomy_names = wp_get_object_terms($post_id, 'post_tag', ['fields' => 'names']);
        }

        if(($this->settings['ptl-post-type'] != 'post' && $this->settings['ptl-post-type'] != 'post-timeline') && $this->settings['ptl-type'] == 'tag') {
            $tag = (isset($this->settings['ptl-tag-taxonomy']) ? $this->settings['ptl-tag-taxonomy'] : $this->settings['ptl-post-type'] . '_tag');

            if ($tag) {
                $taxonomy_names = wp_get_object_terms($post_id, $tag, ['fields' => 'names']);
            }
        }

        if(!empty($taxonomy_names)) :
            $post_term = $taxonomy_names[0];
        endif;

        return $post_term;
    }
}
