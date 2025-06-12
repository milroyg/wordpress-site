<?php

namespace PPV\Model;

use PPV\Model\AnalogSystem;

class Shortcode
{
    protected static $_instance = null;

    /**
     * construct function
     */
    public function __construct()
    {
        add_shortcode('doc', [$this, 'doc']);
    }

    /**
     * Create instance function
     */
    public static function instance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function doc($atts)
    {
        $post_type = get_post_type($atts['id']);

        if ($post_type != 'ppt_viewer') {
            return false;
        }

        $post_id = $atts['id'];
        $post = get_post($post_id);
        if (post_password_required($post)) {
            return get_the_password_form($post);
        }

        switch ($post->post_status) {
            case 'publish':
                return AnalogSystem::html($atts['id']);

            case 'private':
                if (current_user_can('read_private_posts')) {
                    return AnalogSystem::html($atts['id']);
                }
                return '';

            case 'draft':
            case 'pending':
            case 'future':
                if (current_user_can('edit_post', $post_id)) {
                    return AnalogSystem::html($atts['id']);
                }
                return '';

            default:
                return '';
        }
    }
}
Shortcode::instance();
