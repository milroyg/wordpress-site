<?php

namespace PostTimeline\components;

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
class Like {


	/**
	 * [getCount description]
	 * @param  [type] $post_id [description]
	 * @return [type]          [description]
	 */
	public static function getCount($post_id) {

		$count = get_post_meta($post_id, 'ptl-like',true);
		$count = (!empty($count) ? $count : 0 );

		return $count;
	}


	public static function addLike($post_id) {
		
		$html = '<a href="javascript:void(0);" id="post-'.$post_id.'" class="ptl-post-like post-like-'.$post_id.'"><i class="fa-solid fa-heart heart"></i> <span class="ptl-like-count"><?php echo $like_count; ?></span></a>';

		return $html;
	}



	/**[save_post_like]
	 * @return [type] [description]
	 * [save_post_like Save Post Like with Ajax
	 */
	public function save_post_like() {
		global $wpdb;

		$response 	= new \stdClass;
		$response->success 	= false;

		$post_id 		= isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
		$ip_address   	= isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';

		//To avoid multiple creations
		$count = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS c FROM `".PTL_PREFIX."post_view` WHERE (created_on > NOW() - INTERVAL 15 MINUTE) AND post_id = %d",
			$post_id
		));

		if($count[0]->c < 1) {

			$prev_val = get_post_meta($post_id, 'ptl-like', true);

			$curr_val = $prev_val+1; 
			$updated = update_post_meta($post_id, 'ptl-like',$curr_val);

			$wpdb->query( $wpdb->prepare( "INSERT INTO ".PTL_PREFIX."post_view (post_id, ip_address ) VALUES ( %d, %s )",
				$post_id,$ip_address));

			if ($updated){
				$response->count = $curr_val; 
				$response->success = true;
				$response->msg = __('Post Liked','post-timeline');
			}
		}else{
			$response->msg = __('You already have liked this post','post-timeline');
		}

		echo json_encode($response);die;
	}




}
