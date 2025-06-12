<?php

class BTimelineAdmin {

    public function __construct() {
        add_action('init', [__CLASS__,  'register_post_type']);
        if (is_admin()) {
            add_filter('post_row_actions', [__CLASS__, 'remove_row_actions'], 10, 2);
        }
        add_filter('post_updated_messages', [__CLASS__, 'updated_messages']);
        add_action('admin_head-post.php', [__CLASS__, 'hide_publishing_actions']);
        add_action('admin_head-post-new.php', [__CLASS__, 'hide_publishing_actions']);
        add_filter('gettext', [__CLASS__, 'change_publish_button'], 10, 2);
        add_filter('manage_btimeline_posts_columns', [__CLASS__, 'columns_head_only'], 10);
        add_action('manage_btimeline_posts_custom_column', [__CLASS__, 'columns_content_only'], 10, 2);
        add_action('edit_form_after_title', [__CLASS__, 'shortcode_area']);
    }

    public static function register_post_type() {
        $labels = array(
            'name' => __('B-Timeline', 'b-timeline'),
            'menu_name' => __('B-Timeline', 'b-timeline'),
            'name_admin_bar' => __('B-Timeline', 'b-timeline'),
            'add_new' => __('Add New', 'b-timeline'),
            'add_new_item' => __('Add New ', 'b-timeline'),
            'new_item' => __('New Timeline ', 'b-timeline'),
            'edit_item' => __('Edit Timeline ', 'b-timeline'),
            'view_item' => __('View Timeline ', 'b-timeline'),
            'all_items' => __('All Timeline', 'b-timeline'),
            'not_found' => __('Sorry, we couldn\'t find the Feed you are looking for.')
        );
        $args = array(
            'labels' => $labels,
            'description' => __('B Timeline Options.', 'b-timeline'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => BPTL_PLUGIN_DIR . '/public/assets/images/timeline.png',
            'query_var' => true,
            'rewrite' => array('slug' => 'b-timeline'),
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 20,
            'supports' => array('title'),
        );
        register_post_type('btimeline', $args);
    }

    public static function remove_row_actions($idtions) {
        global $post;
        if ($post->post_type == 'btimeline') {
            unset($idtions['view']);
            unset($idtions['inline hide-if-no-js']);
        }
        return $idtions;
    }

    public static function hide_publishing_actions() {
        $my_post_type = 'btimeline';
        global $post;
        if ($post->post_type == $my_post_type) {
            echo '
                <style type="text/css">
                    #misc-publishing-actions,
                    #minor-publishing-actions{
                        display:none;
                    }
                </style>
            ';
        }
    }

    public static function updated_messages($messages) {
        $messages['btimeline'][1] = __('Timeline Item updated ', 'btimeline');
        return $messages;
    } 

    public static function change_publish_button($translation, $text) {
        if ('btimeline' == get_post_type())
            if ($text == 'Publish')
                return 'Save';

        return $translation;
    }

    public static function shortcode_area() {
        global $post;
        if ($post->post_type == 'btimeline'): ?>
            <div class="bptl_shortcode">
                <div class="shortcode-heading">
                    <div class="icon"><span class="dashicons dashicons-shortcode"></span> <?php _e("B-Timeline", "b-timeline") ?>
                    </div>
                    <div class="text"> <a href="https://bplugins.com/support/" target="_blank"><?php _e("Supports", "pdfp") ?></a>
                    </div>
                </div>
                <div class="shortcode-left">
                    <h3><?php _e("Shortcode", "pdfp") ?></h3>
                    <p><?php _e("Copy and paste this shortcode into your posts, pages and widget:", "b-timeline") ?></p>
                    <div class="shortcode" selectable>[btimeline id="<?php echo esc_attr($post->ID); ?>"]</div>
                </div>
                <div class="shortcode-right">
                    <h3><?php _e("Template Include", "pdfp") ?></h3>
                    <p><?php _e("Copy and paste the PHP code into your template file:", "b-timeline"); ?></p>
                    <div class="shortcode">&lt;?php echo do_shortcode('[btimeline id="<?php echo esc_html($post->ID); ?>"]');
                        ?&gt;</div>
                </div>
            </div>

        <?php endif;
    }

    public static function columns_head_only($defaults) {
        unset($defaults['date']);
        $defaults['directors_name'] = 'ShortCode';
        $defaults['date'] = 'Date';
        return $defaults;
    }

    public static function columns_content_only($column_name, $post_ID) {
        if ($column_name == 'directors_name') {
            echo '<div class="bptl_front_shortcode"><input onfocus="this.select();" style="text-align: center; border: none; outline: none; background-color: #1e8cbe; color: #fff; padding: 4px 10px; border-radius: 3px;" value="[btimeline  id=' . "'" . esc_attr($post_ID) . "'" . ']" ></div>';
        }
    }

}

new BTimelineAdmin();