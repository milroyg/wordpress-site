<?php

namespace PostTimeline;

/**
 * Helper Class for the PostTimeline
 */
class Helper
{
    /**
     * [$all_settings All the settings]
     * @var [type]
     */
    private static $all_settings;

    /**
     * [get_default_settings description]
     * @param  [type]
     * @return [type] $default_Settings [description]
     */
    public static function get_default_settings()
    {
        $default_Settings =
        [
            'ptl-bg-status'             => 'off',
            'ptl-images'                => 'on',
            'ptl-post-icon'             => 'on',
            'ptl-nav-status'            => 'on',
            'ptl-anim-status'           => 'off',
            'ptl-scroll-anim'           => 'off',
            'ptl-social-share'          => 'off',
            'ptl-post-type'             => 'post-timeline',
            'ptl-post-type'             => 'post-timeline',
            'ptl-post-link-type'        => 'readmore',
            'ptl-anim-type'             => 'fadeIn',
            'ptl-post-load'             => '1',
            'ptl-post-desc-color'       => '#FFFFFF',
            'ptl-post-head-color'       => '#E11619',
            'ptl-anim-speed'            => '0.8',
            'ptl-icon-type'             => 'font-awesome',
            'ptl-fav-icon'              => 'fa-adjust',
            'ptl-default-custom-icon'   => '130',
            'ptl-sort'                  => 'ASC',
            'ptl-date-format'           => 'M',
            'ptl-nav-date-format'       => 'Y',
            'ptl-type'                  => 'date',
            'ptl-filter-by'             => 'none',
            'ptl-post-length'           => '350',
            'ptl-post-per-page'         => 10,
            'ptl-bg-color'              => '#ffffff',
            'ptl-bg-color-text'         => '#bfbfbf',
            'ptl-post-bg-color'         => '#dd3332',
            'ptl-post-bg-color-text'    => '#dd3332',
            'ptl-nav-color'             => '#dd3332',
            'ptl-nav-color-text'        => '#dd3332',
            'ptl-slides-setting'        => 'carousel',
            'ptl-nav-type'              => '1',
            'ptl-slug'                  => '',
            'ptl-custom-css'            => '',
            'ptl-loader'                => 'ptl-spinner-glow',
            'ptl-skin-type'             => 'ptl-light',
            'ptl-content-hide'          => 'off',
            'ptl-line-style'            => 'solid',
            'ptl-tagline-color'         => '#E11619',
            'ptl-tagline-color-text'    => '#E11619',
            'ptl-post-radius'           => '',
            'ptl-pagination'            => 'button',
            'ptl-lazy-load'             => 'off',
            'ptl-size-content'          => '',
            'ptl-term-csort'            => 'off',
            'ptl-post-csort'            => 'off',
            'ptl-css-class'             => '',
            'ptl-easing'                => 'easeInQuad',
            'ptl-single-temp'           => 'none',
            'ptl-social-type'           => 'social-fix',
            'ptl-layout'                => 'vertical',
            'ptl-target-blank'          => 'on',
            'ptl-sort-by'               => 'title',
            'social-shares'             => ['ptl_facebook' => 'on', 'ptl_twitter' => 'on', 'ptl_linkedin' => 'on', 'ptl_pinterest' => 'on']

        ];

        return $default_Settings;
    }


    /**
     * Sanitize the array
     */
    public static function sanitize_array($array)
    {
        
        if(is_array($array)) {

            foreach ($array as $key => &$value) {
                if (is_array($value)) {
                    $value = self::sanitize_array($value);
                } else {
                    $value = sanitize_text_field($value);
                }
            }
        }
        
        return $array;
    }

    /**
       * [get_single_template Get the list of the single template files]
       * @return [type] [description]
       */
    public static function get_single_template()
    {
        $files_arr = ['single.php', 'page.php', 'blog.php', 'single-*', 'index.php'];
        $fileList  = [];

        foreach ($files_arr as $key => $value) {
            $list = glob(get_template_directory() . '/' . $value);

            if (isset($list[0])) {
                $filename            = basename($list[0]);
                $filename            = str_replace('.php', '', $filename);
                $fileList[$filename] = $list[0];
            }
        }

        return $fileList;
    }

    /**
     * [get_all_setting Get all the settings in key/pair values]
     * @return [type] [description]
     */
    public static function get_all_setting()
    {
        //  Return the fetched version
        if (self::$all_setting) {
            return self::$all_setting;
        }

        $all_results = get_option('post_timeline_global_settings');

        $all_settings = [];

        foreach ($all_results as $config) {
            $all_settings[$config->key] = $config->value;
        }
        //  Make Backup
        self::$all_setting = $all_settings;

        return $all_settings;
    }

    /**
     * [get_default_settings description]
     * @param  [type]
     * @return [type] $default_Settings [description]
     */
    public static function get_setting($opt_key)
    {
        return $opt_val;
    }

    /**
     * [strip_html Get rid of th HTML and provide ... ending]
     * @param  [type]  $text   [description]
     * @param  integer $length [description]
     * @param  string  $ending [description]
     * @return [type]          [description]
     */
    public static function strip_html($text, $length = 100, $ending = '...')
    {
        $text  = strip_tags($text);

        $total_length = strlen($text);

        if ($total_length <= $length) {
            return $text;
        }

        return substr($text, 0, $length - strlen($ending)) . $ending;
    }

    /**
     * [truncate Truncate the HTML]
     * @param  [type]  $text         [description]
     * @param  integer $length       [description]
     * @param  string  $ending       [description]
     * @param  boolean $exact        [description]
     * @param  boolean $considerHtml [description]
     * @return [type]                [description]
     */
    public static function truncate($text, $length = 100, $ending = '...', $exact = false, $considerHtml = true)
    {
        if ($considerHtml) {
            //  Remove the shortcode
            $text = strip_shortcodes($text);

            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            // splits all html-tags to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($ending);
            $open_tags    = [];
            $truncate     = '';
            foreach ($lines as $line_matchings) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matchings[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                        // do nothing
                        // if tag is a closing tag
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                        // delete tag from $open_tags list
                        $pos = array_search($tag_matchings[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                        // if tag is an opening tag
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                        // add tag to the beginning of $open_tags list
                        array_unshift($open_tags, strtolower($tag_matchings[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matchings[1];
                }
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
                if ($total_length + $content_length > $length) {
                    // the number of characters which are left
                    $left            = $length - $total_length;
                    $entities_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matchings[2], 0, $left + $entities_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matchings[2];
                    $total_length += $content_length;
                }
                // if the maximum length is reached, get off the loop
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }

        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        // add the defined ending to the text
        $truncate .= $ending;
        if ($considerHtml) {
            // close all unclosed html-tags
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    /**
     * [ptl_pagination description]
     * @param  string $numpages  [description]
     * @param  string $pagerange [description]
     * @param  string $paged     [description]
     * @return [type]            [description]
     */
    public static function ptl_pagination($numpages = '', $pagerange = '', $paged='')
    {
        if (empty($pagerange)) {
            $pagerange = 2;
        }

        /**
         * This first part of our function is a fallback
         * for custom pagination inside a regular loop that
         * uses the global $paged and global $wp_query variables.
         *
         * It's good because we can now override default pagination
         * in our theme, and use this function in default quries
         * and custom queries.
         */
        global $paged;
        if (empty($paged)) {
            $paged = 1;
        }
        if ($numpages == '') {
            global $wp_query;
            $numpages = $wp_query->max_num_pages;
            if (!$numpages) {
                $numpages = 1;
            }
        }

        /**
         * We construct the pagination arguments to enter into our paginate_links
         * function.
         */
        $pagination_args = [
            'base'            => get_pagenum_link(1) . '%_%',
            'format'          => 'page/%#%',
            'total'           => $numpages,
            'current'         => $paged,
            'show_all'        => false,
            'end_size'        => 5,
            'mid_size'        => $pagerange,
            'prev_next'       => true,
            'prev_text'       => __('&laquo;'),
            'next_text'       => __('&raquo;'),
            'type'            => 'plain',
            'add_args'        => false,
            'add_fragment'    => ''
        ];

        $paginate_links = paginate_links($pagination_args);

        $str_pagin = '';
        if ($paginate_links) {
            $str_pagin .= "<nav class='custom-pagination'>";
            $str_pagin .= "<span class='page-numbers hide page-num'>Page " . $paged . ' of ' . $numpages . '</span> ';
            $str_pagin .= $paginate_links;
            $str_pagin .= '</nav>';
        }

        return $str_pagin;
    }

    /**
     * [ptl_get_image description]
     * @param  [type] $_post    [description]
     * @param  [type] $is_admin [description]
     * @return [type]           [description]
     */
    public static function ptl_get_image($_post, $is_admin)
    {
        $c_image = '';

        //if admin image
        if ($is_admin) {
            return POST_TIMELINE_URL_PATH . 'public/img/sample/' . $_post->p_img;
        }

        if (isset($_post->custom['_thumbnail_id'][0])) {
            $c_image = wp_get_attachment_image_src($_post->custom['_thumbnail_id'][0], 'large');
            $c_image = isset($c_image['0']) ? $c_image['0'] : null;
        } else {
            $c_image = POST_TIMELINE_URL_PATH . 'public/img/dummy.jpg';
        }

        return $c_image;
    }
}
