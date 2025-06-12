<?php

namespace PostTimeline\Admin;

/**
 * The file that defines the users feedback class
 *
 * @since      6.0.1
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     AgileLogix <support@agilelogix.com>
 */

class Request
{
    /**
     * The settings of this plugin.
     *
     * @since    0.0.1
     * @access   private
     * @var      string    $settings    The settings of this plugin.
     */
    private $settings;

    /**
     * Initialize the class and set its properties.
     *
     */
    public function __construct()
    {
        // Change Pricing if option is enable
        $this->default_settings = \PostTimeline\Helper::get_default_settings();

        $options        = get_option('post_timeline_global_settings');
        $this->settings = (!empty($options) ? array_merge($this->default_settings, $options) : $this->default_settings);
    }

    /**
     * [save_setting] Save the settings for the post timeline
     * save settings for Post-timeline
     *
     * @since    6.0.1
     */
    public function save_setting()
    {
        $response          = new \stdclass();
        $response->success = false;

        $data = isset($_POST['formData']) ? \PostTimeline\Helper::sanitize_array($_POST['formData']) : [];

        if (isset($data['ptl-lazy-load'])) {
            $data['ptl-lazy-load'] = 'off';
        }

        if ($data['ptl-size-content'] && $data['ptl-size-content'] < 0) {
            $response->msg      = __("Content font size should'nt be in negative.", 'post-timeline');
            $response->error    = true;
            $response->success  = false;

            echo json_encode($response);
            die;
        }

        if ($data['ptl-anim-speed'] && ($data['ptl-anim-speed'] < 0.1 || $data['ptl-anim-speed'] > 2)) {
            $response->msg      = __("animation speed max: '2' and min: '0.1'", 'post-timeline');
            $response->error    = true;
            $response->success  = false;

            echo json_encode($response);
            die;
        }

        $updated = update_option('post_timeline_global_settings', $data);

        $response->msg     = __('Setting has been updated successfully.', 'post-timeline');
        $response->success = true;

        echo json_encode($response);
        die;
    }

    /**
     * [get_fonts_options] Descriptions
     * get font and options (admin panel)
     *
     * @since    0.0.1
     */
    public function get_fonts_options()
    {
        $response           = new \stdclass();
        $response->success  = false;

        $options            = $this->settings;
        $font_title         = $options['ptl-fonts-title'];

        $response->font_title         = isset($font_title['font']['family-title']) ? $font_title['font']['family-title'] : null;
        $response->subsets_title      = isset($font_title['font']['subsets-title']) ? $font_title['font']['subsets-title'] : null;
        $response->variants_title     = isset($font_title['font']['variants-title']) ? $font_title['font']['variants-title'] : null;

        $font_content                 = isset($options['ptl-fonts-content']) ? $options['ptl-fonts-content'] : null;
        $response->font_content       = isset($font_content['font']['family-content']) ? $font_content['font']['family-content'] : null;
        $response->subsets_content    = isset($font_content['font']['subsets-content']) ? $font_content['font']['subsets-content'] : null;
        $response->variants_content   = isset($font_content['font']['variants-content']) ? $font_content['font']['variants-content'] : null;

        $response->success  = true;

        echo json_encode($response);

        die;
    }

    /**
     * [select_modal_posttype] Descriptions
     * save settings for Post-timeline Pro
     *
     */
    public function select_modal_posttype()
    {
        $response          = new \stdclass();
        $response->success = false;

        $type = isset($_POST['type']) ? sanitize_text_field(wp_unslash($_POST['type'])) : null;

        if ($type == 'ptl') {
            $terms = get_terms(['taxonomy' => 'ptl_categories', 'hide_empty' => false]);
            //  open stream
            ob_start();

            // include simple products HTML
            include POST_TIMELINE_PLUGIN_PATH . 'admin/partials/shortcode-ptl.php';
            $modal_html = ob_get_contents();

            //  clean it
            ob_end_clean();
        } else {
            $args = ['public' => true];

            $output     = 'names'; // names or objects, note names is the default
            $operator   = 'and'; // 'and' or 'or'
            $post_types = get_post_types($args, $output, $operator);

            $posttypes_array = [];
            unset($post_types['attachment'] , $post_types['page']);

            foreach ($post_types as $post_type) {
                $posttypes_array[] = $post_type;
            }

            $terms = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);

            //  open stream
            ob_start();

            // include simple products HTML
            include POST_TIMELINE_PLUGIN_PATH . 'admin/partials/shortcode-cpt.php';
            $modal_html = ob_get_contents();

            //  clean it
            ob_end_clean();
        }

        $response->modal_html = $modal_html;
        $response->success    = true;

        echo json_encode($response);
        die;
    }

    /**
     * [select_layout] Description
     *
     */
    public function select_layout()
    {
        $response          = new \stdclass();
        $response->success = false;

        $layout = isset($_POST['layout']) ? sanitize_text_field(wp_unslash($_POST['layout'])) : '';

        $tpl_position = '';

        $vertical =  [
            ['id' => '0',   'template_name' => 'Template 0', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-0.png'],
            ['id' => '1',   'template_name' => 'Template 1', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1.png'],
            ['id' => '2',   'template_name' => 'Template 2', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2.png'],
            ['id' => '3',   'template_name' => 'Template 3', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-3.png'],
            ['id' => '4',   'template_name' => 'Template 4', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-4.png'],
            ['id' => '5',   'template_name' => 'Template 5', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5.png'],
            ['id' => '6',   'template_name' => 'Template 6', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6.png'],

        ];
        $one_side =  [
            ['id' => '0',   'template_name' => 'Template 0', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-0.png'],
            ['id' => '1',   'template_name' => 'Template 1', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-1.png'],
            ['id' => '2',   'template_name' => 'Template 2', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-2.png'],
            ['id' => '3',   'template_name' => 'Template 3', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-3.png'],
            ['id' => '4',   'template_name' => 'Template 4', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-4.png'],
            ['id' => '5',   'template_name' => 'Template 5', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-5.png'],
            ['id' => '6',   'template_name' => 'Template 6', 'class'=> 'one-side-right', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/right-template-6.png'],
        ];

        $horizontal = [

            ['id' => '0', 'template_name' => 'Template 0', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-0h.png'],
            ['id' => '1', 'template_name' => 'Template 1', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-1h.png'],
            ['id' => '2', 'template_name' => 'Template 2', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-2h.png'],
            ['id' => '3', 'template_name' => 'Template 3', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-3h.png'],
            ['id' => '4', 'template_name' => 'Template 4', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-4h.png'],
            ['id' => '5', 'template_name' => 'Template 5', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-5h.png'],
            ['id' => '6', 'template_name' => 'Template 6', 'image' => POST_TIMELINE_URL_PATH . 'admin/images/banners/template-6h.png'],

        ];

        $options    = [];
        $templates  = [];

        if ($layout == 'vertical') {
            $templates = $vertical;
        } elseif ($layout == 'horizontal') {
            $templates = $horizontal;
        } elseif ($layout == 'one-side') {
            $templates    = $one_side;
            $tpl_position = '<div class="btn-group btn-group-toggle" data-toggle="ptl_buttons"><label class="btn btn-warning active"><input type="radio" name="position" id="ptl-post-left" value="left" autocomplete="off" checked> Left</label><label class="btn btn-warning"><input type="radio" name="position" value="right" id="ptl-post-right" autocomplete="off"> Right</label></div>';
        }

        foreach ($templates as $key => $template) {
            $options[] = '<option src="' . $template['image'] . '" value="' . $template['id'] . '">' . $template['template_name'] . '</option>';
        }

        $response->options  = $options;
        $response->position = $tpl_position;
        $response->success  = true;

        echo json_encode($response);
        die();
    }

    /**
     * [generate_shorcode]
     *
     * Remove it, make it with JS
     *
     */
    public function generate_shorcode()
    {
        $response          = new \stdclass();
        $response->success = false;

        $data = isset($_POST['formData']) ? array_map('sanitize_text_field', wp_unslash($_POST['formData'])) : [];

        $attrs      = [];
        $shortcode  = 'post-timeline ';

        !isset($data['nav-status']) ? $data['nav-status'] = 'off' : $data;

        foreach ($data as $key => $value) {
            // skip empty value
            if ($value == '') {
                continue;
            }

            // skip ptl_post_type
            if ($key == 'ptl_select_posttype') {
                continue;
            }

            // skip ptl-layout
            if ($key == 'ptl-layout') {
                continue;
            }

            if ($key == 'post-types' || $key == 'category') {
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
            }

            $attrs[] = $key . '="' . $value . '"';
        }

        $attrs = implode(' ', $attrs);

        $response->shortcode  = '[' . $shortcode . $attrs . ']';

        $response->success    = true;

        echo json_encode($response);
        die;
    }

    /**
     * [selected_post_types] Description
     *
     */
    public function selected_post_types()
    {
        $response          = new \stdclass();
        $response->success = false;

        $post_type = isset($_POST['post_types']) ? sanitize_text_field(wp_unslash($_POST['post_types'])) : '';

        $tax = [];

        $taxonomies = get_object_taxonomies($post_type, 'objects');

        $tax[] = '<option value="">' . __('Select Taxonomy', 'post-timeline') . '</option>';

        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy->name == 'post_tag' || $taxonomy->name == 'post_format') {
                continue;
            }

            $tax[] = '<option value="' . $taxonomy->name . '">' . $taxonomy->name . '</option>';
        }

        $response->taxonomies = $tax;
        $response->success    = true;

        echo json_encode($response);
        die();
    }

    /**
     * [selected_taxonomy] Description
     *
     */
    public function selected_taxonomy()
    {
        $response          = new \stdclass();
        $response->success = false;

        $taxonomy   = isset($_POST['taxonomy']) ? sanitize_text_field(wp_unslash($_POST['taxonomy'])) : '';
        $categories = [];

        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);

        if (!empty($taxonomy)) {
            foreach ($terms as $term) {
                if ($term->slug == 'uncategorized') {
                    continue;
                }

                $categories[]  = '<option value="' . $term->term_id . '">' . $term->name . '</option>' ;
            }
        }

        $response->categories = $categories;
        $response->success    = true;

        echo json_encode($response);
        die();
    }

    /**
    * [update_menu_order_tags description]
    *
    * update ptl tag menu order
    *
    * @param  [type] [description]
    * @return [type] [description]
    */
    public function update_menu_order_tags()
    {
        global $wpdb;

        parse_str(sanitize_text_field(wp_unslash($_POST['order'])), $data);

        if (!is_array($data)) {
            return false;
        }

        $id_arr = [];
        foreach ($data as $key => $values) {
            foreach ($values as $position => $id) {
                $id_arr[] = $id;
            }
        }

        $menu_order_arr = [];
        foreach ($id_arr as $key => $id) {
            $row = $wpdb->get_row("SELECT tag_order FROM $wpdb->terms WHERE term_id = " . intval($id));
            if (is_object($row) && !empty($row)) {
                $menu_order_arr[] = $this->term_order_inc($menu_order_arr, $row->tag_order);
            }
        }
        sort($menu_order_arr);

        foreach ($data as $key => $values) {
            foreach ($values as $position => $id) {
                if (array_key_exists($position, $menu_order_arr)) {
                    $tag_order = $menu_order_arr[$position];

                    $wpdb->update($wpdb->terms, ['tag_order' => $tag_order], ['term_id' => intval($id)]);
                    $wpdb->update($wpdb->term_relationships, ['term_order' => $tag_order], ['term_taxonomy_id' => intval($id)]);
                }
            }
        }

        echo json_encode(['msg' => __('Tags order updated successfully.', 'post-timeline'), 'success' => true]);
        exit;
    }

    /**
    * [term_order_inc description]
    *
    * helper function to increament in tag order
    *
    * @param  [type] [description]
    * @return [type] [description]
    */
    public function term_order_inc($arr=[], $order=0)
    {
        if (in_array($order, $arr)) {
            $order++;
            $order = $this->term_order_inc($arr, $order);
        }

        return $order;
    }
}
