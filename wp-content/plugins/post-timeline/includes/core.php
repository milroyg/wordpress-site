<?php

namespace PostTimeline;

use PostTimeline\components\RadioWalker;

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
class Core
{
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
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    0.0.1
     */
    public function __construct()
    {
        $this->plugin_name = 'post-timeline';
        $this->version     = POST_TIMELINE_VERSION;
    }

    /*
    *[add_shortcode_button]
    *Create add shortcode button on admin page
    *
    */
    public function add_shortcode_button()
    {
        global $post;

        if ($post) {
            if ($post->post_type == 'page') {
                echo '<a  id="ptl-shortcode-insert" data-toggle="ptl_modal" data-target="#insert-ptl-shortcode" class="button">Add Post-timeline Shortcode</a>';
            }
        }
    }

    /*
    *[shortcode_gen_popup]
    * shortcode Popup HTML
    *
    */
    public function shortcode_gen_popup()
    {
        global $post, $pagenow;

        $ptl_templates =  [
            ['id' => '0',   'template_name' => 'Template 0', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-0.png'],
            ['id' => '1',   'template_name' => 'Template 1', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1.png'],
            ['id' => '2',   'template_name' => 'Template 2', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2.png'],
            ['id' => '3',   'template_name' => 'Template 3', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-3.png'],
            ['id' => '4',   'template_name' => 'Template 4', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-4.png'],
            ['id' => '5',   'template_name' => 'Template 5', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5.png'],
            ['id' => '6',   'template_name' => 'Template 6', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6.png'],
        ];

        $ptl_navs =  [
            ['id' => '0',   'navigation_name' => 'Style 0', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/layout/nav-0.png'],
            ['id' => '1',   'navigation_name' => 'Style 1', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/layout/nav-1.png'],
            ['id' => '2',   'navigation_name' => 'Style 2', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/layout/nav-2.png'],
            ['id' => '3',   'navigation_name' => 'Style 3', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/layout/nav-3.png'],
        ];

        $def_skin = (isset(get_option('post_timeline_global_settings')['ptl-skin-type']) ? get_option('post_timeline_global_settings')['ptl-skin-type'] : '');

        $screen = get_current_screen();

        //	Only for Post and Post Edit
        if (in_array($pagenow, ['post.php', 'post-new.php']) || $screen->base == 'post-timeline_page_ptl-dashboard') {
            $btn_id = 'ptl-add-shortcode';

            if ($screen->base == 'post-timeline_page_ptl-dashboard') {
                $btn_id =  'ptl-add-dashboard-shortcode';
            }

            // Include Add Shortcode admin Popup HTML
            include POST_TIMELINE_PLUGIN_PATH . 'admin/partials/shortcode-popup-html.php';
        }
    }

    /*
    *[change_post_slug]
    * shortcode Popup HTML
    *
    */
    public function change_post_slug($args, $post_type)
    {
        $slug = (isset(get_option('post_timeline_global_settings')['ptl-slug']) ? get_option('post_timeline_global_settings')['ptl-slug'] : '');

        if (!empty($slug)) {
            if ('post-timeline' === $post_type) {
                $args['rewrite']['slug'] = $slug;
            }
        }

        return $args;
    }

    /**
     * [remove_default_custom_fields_meta_box description]
     *
     * Remove default custom fields meta box from post-timeline post-type
     *
     * @param  [type] $post_type, $context, $post [description]
     * @return [type]                [description]
     */
    public function remove_default_custom_fields_meta_box($post_type, $context, $post)
    {
        if ($post_type == 'post-timeline') {
            remove_meta_box('postcustom', $post_type, $context);
        }
    }

    /**
     * [ptl_custom_filter_admin description]
     *
     * Add PTL Category Filter Select Column in admin page
     *
     * @param  [type] [description]
     * @return [type] [description]
     */
    public function ptl_custom_filter_admin()
    {
        global $post_type,$wpdb;

        if ($post_type == 'post-timeline') {
            $terms_cat = get_terms([
                'taxonomy'   => 'ptl_categories',
                'hide_empty' => false,
            ]);

            $selected_cat = (isset($_GET['ptl_cat_filter'])) ? sanitize_text_field(wp_unslash($_GET['ptl_cat_filter'])) : ((isset($_GET['ptl_categories'])) ? sanitize_text_field(wp_unslash($_GET['ptl_categories'])) : '');
            echo '<select name="ptl_cat_filter">';
            echo '<option value="">' . esc_attr__('All Categories', 'post-timeline') . '</option>';

            if ($terms_cat) {
                //Loop through each unique value in ptl_cat_filter
                foreach ($terms_cat as $key => $term) {
                    $selected = '';
                    if (isset($selected_cat) && $selected_cat == $term->slug) {
                        $selected = 'selected';
                    }

                    echo '<option value="' . esc_attr($term->slug) . '" ' . esc_attr($selected) . '>' . esc_attr($term->name) . '</option>';
                }
            }

            echo '</select>';

            $terms_tag = get_terms([
                'taxonomy'   => 'ptl_tag',
                'hide_empty' => false,
            ]);

            $selected_tag = (isset($_GET['ptl_tag_filter'])) ? sanitize_text_field(wp_unslash($_GET['ptl_tag_filter'])) : ((isset($_GET['ptl_tag'])) ? sanitize_text_field(wp_unslash($_GET['ptl_tag'])) : '');

            echo '<select name="ptl_tag_filter">';
            echo '<option value="">' . esc_attr__('All Tags', 'post-timeline') . '</option>';

            if ($terms_tag) {
                //Loop through each unique value in ptl_tag_filter
                foreach ($terms_tag as $key => $term) {
                    $selected = '';
                    if (isset($selected_tag) && $selected_tag == $term->slug) {
                        $selected = 'selected';
                    }

                    echo '<option value="' . esc_attr($term->slug) . '" ' . esc_attr($selected) . '>' . esc_attr($term->name) . '</option>';
                }
            }
            echo '</select>';

            // please use unique name
            $date  = (isset($_GET['ptl_event']) && !empty($_GET['ptl_event'])) ? sanitize_text_field(wp_unslash($_GET['ptl_event'])) : '';
            $cross = (empty($date)) ? 'style="display: none"' : 'style="display: block"';
            
            echo '<div class="input-group post_field date ptl-filter-date-picker ptl-cont" id="ptl-filter-date-picker">
                    <input type="text" class="form-control ptl-event-date-picker" name="ptl_event" id="ptl_event" value="' . esc_attr($date) . '"/>
                    <button class="ptl-date-clear" ' . esc_attr($cross) . ' type="button"><svg width="10" height="10" viewBox="0 0 12 12" xmlns="https://www.w3.org/2000/svg"><path d="M.566 1.698L0 1.13 1.132 0l.565.566L6 4.868 10.302.566 10.868 0 12 1.132l-.566.565L7.132 6l4.302 4.3.566.568L10.868 12l-.565-.566L6 7.132l-4.3 4.302L1.13 12 0 10.868l.566-.565L4.868 6 .566 1.698z"></path></svg></button>
                    <span class="input-group-addon input-group-append ptl-date-icon">
                      <button class="btn btn-outline-primary" type="button" id="button-addon2"> <span class="fa fa-calendar"></span></button>
                    </span>
                </div>';
        }
    }

    /**
     * [ptl_category_filter_list]
     *
     * Query PTL Category Filter Select Column in admin page
     *
     */
    public function ptl_custom_filter_list($admin_query)
    {
        global $pagenow,$wpdb;

        $post_type = (isset($_GET['post_type'])) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : 'post-timeline';

        if ($post_type == 'post-timeline' && $pagenow == 'edit.php') {
            if (is_admin() && $admin_query->is_main_query() && (isset($_GET['ptl_cat_filter']) && !empty($_GET['ptl_cat_filter']))) {
                $taxquery = [
                    [
                        'taxonomy' => 'ptl_categories',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field(wp_unslash($_GET['ptl_cat_filter'])),
                        'operator' => 'IN'
                    ]
                ];

                $admin_query->set('tax_query', $taxquery);
            }
            if (is_admin() && $admin_query->is_main_query() && (isset($_GET['ptl_tag_filter']) && !empty($_GET['ptl_tag_filter']))) {
                $cat_tax_query = (isset($admin_query->query_vars['tax_query']) ? $admin_query->query_vars['tax_query'] : '');

                $taxquery = [
                    [
                        'taxonomy' => 'ptl_tag',
                        'field'    => 'slug',
                        'terms'    => sanitize_text_field(wp_unslash($_GET['ptl_tag_filter'])),
                        'operator' => 'IN'
                    ]
                ];

                if (isset($_GET['ptl_cat_filter']) && !empty($_GET['ptl_cat_filter'])) {
                    $category_clause 	= [];

                    $category_clause['relation'] =  'AND';

                    $taxquery = array_merge($cat_tax_query, $taxquery) ;
                    $taxquery = array_merge($category_clause, $taxquery) ;
                }

                $admin_query->set('tax_query', $taxquery);
            }

            if (is_admin() && $admin_query->is_main_query() && (isset($_GET['ptl_event']) && !empty($_GET['ptl_event']))) {
                $Date = sanitize_text_field(wp_unslash($_GET['ptl_event']));
                $Date = explode('/', $Date);
                $Date = $Date[1] . '-' . $Date[0];

                $meta_query = [
                    [
                        'key'     => 'ptl-post-date',
                        'value'   => $Date,
                        'compare' => 'LIKE'
                    ]
                ];

                $admin_query->set('meta_query', $meta_query);
            }
        }

        return $admin_query;
    }

    /**
     * [ptl_taglist_radio description]
     *
     * Convert Ptl_tag From checkbox to Radio buttons
     *
     * @param  [type] $args [description]
     * @return [type] $args [description]
     */
    public function ptl_taglist_radio($args)
    {
        if (!empty($args['taxonomy']) && $args['taxonomy'] === 'ptl_tag' /* <== Change to your required taxonomy */) {
            if (empty($args['walker']) || is_a($args['walker'], 'Walker')) { // Don't override 3rd party walkers.
                $args['walker'] = new RadioWalker();
            }
        }

        return $args;
    }

    /**
     * [ptl_tag_orderby description]
     *
     * Add Custom Sort From Taxonomy
     *
     * @param  [type] $orderby , $wp_query [description]
     * @return [type] $orderby [description]
     */
    public function ptl_tag_orderby($orderby, $wp_query)
    {
        global $wpdb;

        if (isset($wp_query->query['orderby']) && 'ptl_tax_tag' == $wp_query->query['orderby']) {
            $orderby = "(
				SELECT GROUP_CONCAT(name ORDER BY name ASC)
				FROM $wpdb->term_relationships
				INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
				INNER JOIN $wpdb->terms USING (term_id)
				WHERE $wpdb->posts.ID = object_id
				AND taxonomy = 'ptl_tag'
				GROUP BY object_id
			) ";
            $orderby .= ('ASC' == strtoupper($wp_query->get('order'))) ? 'ASC' : 'DESC';
        }

        if (isset($wp_query->query['orderby']) && 'ptl_term_order' == $wp_query->query['orderby']) {
            $orderby = "(
				SELECT GROUP_CONCAT(tr.term_order ORDER BY tr.term_order ASC)
				FROM $wpdb->term_relationships AS tr
				INNER JOIN $wpdb->term_taxonomy USING (term_taxonomy_id)
				INNER JOIN $wpdb->terms USING (term_id)
				WHERE $wpdb->posts.ID = object_id
				AND taxonomy = 'ptl_tag'
				GROUP BY object_id
			) ";
            $orderby .= ('ASC' == strtoupper($wp_query->get('order'))) ? 'ASC' : 'DESC';
        }
        return $orderby;
    }
}
