<?php

namespace PostTimeline\components;

use PostTimeline\components\LazyLoad;
/**
 * The LazyLoad Posts Class.
 *
 * @since      1.0.0
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     agilelogix <support@agilelogix.com>
 */
class LazyLoad {

	/**
	 * Initialise filters and actions.
	 */
	public static function initialize() {
		

	}


	public function add_image_placeholders( $content ) {
    
    // This is a pretty simple regex, but it works.
    //
    // 1. Find <img> tags
    // 2. Exclude tags, placed inside <noscript>.
    $content = preg_replace_callback( '#(?<!noscript\>)<(img)([^>]+?)(>(.*?)</\\1>|[\/]?>)#si', 'Visual_Portfolio_Images::process_image', $content );

    return $content;
  }
}
