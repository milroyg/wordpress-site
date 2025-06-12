<?php

class BTimeline {

    public static $_instance = null;

    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }


    public function __construct() {
            add_action('init', [__CLASS__, 'init'], 0);
            add_action('plugins_loaded', [__CLASS__, 'load_dependencies']);
            add_action('admin_menu', [__CLASS__, 'add_help_pages']);
            add_action('admin_enqueue_scripts', [__CLASS__, 'admin_style']);
            add_action('wp_enqueue_scripts', [__CLASS__, 'btimeline_scripts']);
            add_shortcode('btimeline', [__CLASS__, 'bptl_shortcode']);
            add_filter('admin_footer_text', [__CLASS__, 'admin_footer']);
    }

    public static function init() {
        if (!class_exists('CSF')) {
            require_once BPTL_PLUGIN_PATH . 'inc/codestar/csf-config.php';
        }
        require_once BPTL_PLUGIN_PATH . 'inc/btimeline-options.php';
    }

    public static function load_dependencies() {
        require_once BPTL_PLUGIN_PATH . '/inc/BTimeline_Admin.php';
    }

    public static function load_textdomain() {
        load_plugin_textdomain('b-timeline', false, dirname(__FILE__) . "/../languages");
    }

    public static function btimeline_scripts() {
        wp_register_script('bptl-timeline', BPTL_PLUGIN_DIR . '/public/assets/js/timeline.min.js', ['jquery'], BPTL_VER, true);
        wp_register_script('bptl-timeline-config', BPTL_PLUGIN_DIR . '/public/assets/js/public.js', ['jquery', 'bptl-timeline'], BPTL_VER, true);
        wp_enqueue_script('bptl-timeline');
        wp_enqueue_script('bptl-timeline-config');
    
        wp_register_style('timeline-style', BPTL_PLUGIN_DIR . '/public/assets/css/timeline.min.css', NULL, 'v0.0.2', 'all');
        wp_enqueue_style('timeline-style');
    }

    public static function admin_style($hook) {
        wp_register_style('bptl-admin-style', BPTL_PLUGIN_DIR . '/public/assets/css/admin-style.css');
        wp_enqueue_style('bptl-admin-style');
    
        if ("btimeline_page_dashboard" === $hook) {
            wp_enqueue_style('bptl-admin-help', BPTL_PLUGIN_DIR . '/build/admin-help.css', [], BPTL_VER);
            wp_enqueue_script('bptl-admin-help', BPTL_PLUGIN_DIR . '/build/admin-help.js', ['react', 'react-dom'], BPTL_VER);
            wp_enqueue_script('fs', BPTL_PLUGIN_DIR . 'public/assets/js/fs.js', [], '1');
            wp_set_script_translations('bptl-admin-help', 'b-timeline', BPTL_PLUGIN_DIR . 'languages');
        }
    }

    public static function admin_footer($text) {
        if ('btimeline' === get_post_type()) {
            $url = 'https://wordpress.org/plugins/b-timeline/reviews/?filter=5#new-post';
            $text = sprintf(__('If you like <strong> B-Timeline </strong> please leave us a <a href="%s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> rating. Your Review is very important to us as it helps us to grow more. ', 'b-timeline'), $url);
        }
        return $text;
    }

    public static function bptl_shortcode($atts) {
        extract(shortcode_atts(array(
            'id' => null
        ), $atts));
        ob_start(); ?>
    
        <!-- Timeline Meta Data -->
        <?php $bptl_datas = get_post_meta($id, '_bptimeline_', true); ?>
    
        <!-- Start Parent Container -->
        <div id="btimeline-<?php echo esc_attr($id); ?>">
            <div class="timeline bp_titleline" data-timeline='<?php echo esc_attr(wp_json_encode($bptl_datas)); ?>'>
                <div class="timeline__wrap">
                    <div class="timeline__items">
    
                        <?php foreach ($bptl_datas['item_datas'] as $item_data):
    
                            $timeline_label = $item_data['date_label'] ?? 'January';
                            $timeline_desc = $item_data['item_details'] ?? 'Timeline Description';
                            $timeline_position = $item_data['item_position'] ?? '';
                            ?>
    
                            <div class="timeline__item <?php echo esc_attr($timeline_position); ?> fadeIn">
                                <div class="timeline__item__inner">
                                    <div class="timeline__content__wrap">
                                        <div class="timeline__content">
                                            <p class="title"><?php echo esc_html($timeline_label) ?> </p>
                                            <p><?php echo wp_kses_post($timeline_desc) ?> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
    
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div> <!-- End Parent Container -->
        <style>
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__content {
                background:
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
                border:
                    <?php echo esc_attr($bptl_datas['item_border_size']); ?>
                    px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                ;
    
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__content .title{
                font-size: <?php echo esc_attr($bptl_datas['item_fontSize']); ?>px;
                color: <?php echo esc_attr($bptl_datas['item_color']); ?>;
                font-style: <?php echo esc_attr($bptl_datas['item_fontStyle']); ?>;
                font-weight: <?php echo esc_attr($bptl_datas['item_fontWeight']); ?>
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__content p {
                font-size: <?php echo esc_attr($bptl_datas['label_fontSize']); ?> px;
                color: <?php echo esc_attr($bptl_datas['label_color']); ?>;
                font-style: <?php echo esc_attr($bptl_datas['label_fontStyle']); ?>;
                font-weight: <?php echo esc_attr($bptl_datas['lebel_fontWeight']); ?>
            }
    
            /* Timeline Dot */
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item::after {
                background-color: '#fff';
                border: 5px solid <?php echo esc_attr($bptl_datas['bar_dot_color']); ?>;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline--horizontal .timeline-divider,
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline:not(.timeline--horizontal)::before {
                background-color: <?php echo esc_attr($bptl_datas['bar_bg_color']); ?>;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item--left .timeline__content::before {
                border-left: 11px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item--right .timeline__content::before {
                border-right: 12px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item--left .timeline__content::after {
                border-left: 11px solid
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item--right .timeline__content::after {
                border-right: 12px solid
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item.timeline__item--top .timeline__content::before {
                border-top: 14px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                    !important;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item.timeline__item--bottom .timeline__content::before {
                border-bottom: 14px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                    !important;
                border-top: none;
            }
    
            /* Horizontal view */
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item.timeline__item--top .timeline__content::after {
                border-top: 12px solid
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline__item.timeline__item--bottom .timeline__content::after {
                border-bottom: 12px solid
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
                border-top: none;
            }
    
            /* Mobaile view */
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline--mobile .timeline__item .timeline__content::before {
                border-left: none;
                border-right: 12px solid
                    <?php echo esc_attr($bptl_datas['item_border_color']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline--mobile .timeline__item .timeline__content::after {
                border-left: none;
                border-right: 12px solid
                    <?php echo esc_attr($bptl_datas['item_bg']); ?>
                ;
            }
    
            <?php echo '#btimeline-' . esc_attr($id); ?>
            .timeline-nav-button {
                background-color: #fff;
                border: 2px solid
                    <?php echo esc_attr($bptl_datas['bar_bg_color']); ?>
                ;
            }
        </style>
    
        <?php
        return ob_get_clean();
    
    }

    public static function add_help_pages() {
        add_submenu_page(
            'edit.php?post_type=btimeline', 
            __('Dashboard', 'b-timeline'), 
            __('Demo & Help', 'b-timeline'),  
            'manage_options',  
            'dashboard',   
            [__CLASS__, 'render_dashboard']   
        );
    }

    public static function render_dashboard() {
        ?>
        <div
           id="bplAdminHelpPage"
           data-version="<?php echo esc_attr(BPTL_VER); ?>">
        </div>
        <?php
    }

    public static function activation_redirect() {
        add_option('bptl_do_activation_redirect', true);
    }

    public static function do_redirect_to_dashboard() {
        if (get_option('bptl_do_activation_redirect')) {
            delete_option('bptl_do_activation_redirect');
            if (!is_network_admin() &&!isset($_GET['activate-multi'])) {
                wp_safe_redirect(admin_url('edit.php?post_type=btimeline&page=dashboard#/dashboard'));
                exit;
            }
        }
    }



}