<?php

namespace PostTimeline\Layouts;

use PostTimeline\Layouts\Base;
use PostTimeline\components\Like;

/**
 * The file that defines the layout class
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


class Layout_5 extends Base  {


	/**[render_post]
	 * @return [type] [description]
	 * [render_post to render the post
	 */
	public function render_post($post, $is_even,$count = '') {

		$post_desc 				 = $post->post_content;
		$media_class  		 = ($post->custom['ptl-media-type'][0] == 'video') ? 'ptl-video-post' : '';

		// is Left or Right?
		$arrow_class 			  = ($is_even) ? 'right': 'left';
		$box_class 				  = ($is_even) ? 'left': 'right';
    $anim_class         = ($is_even) ? 'Left': 'Right';
    $offset_class       = ($is_even) ? 0 : 2;

		$section 				    = ($is_even) ? 'show': 'hide';
		$html = '';
		$post_icon_status 	= $this->settings['ptl-post-icon'];
		$animation_type     = ($this->settings['ptl-anim-status'] == 'on' ) ? $this->settings['ptl-anim-type'] : '';
    $animation_status   = ($this->settings['ptl-anim-status'] == 'on' ) ? 'panim' : '';
		$content_hide 	 		= $this->settings['ptl-content-hide'];
		$target_blank			  = (isset($this->atts['ptl-targe-blank']) && $this->atts['ptl-targe-blank'] == 'on') ? 'target="__blank"' : '';
    
    // $arrow_animation = ($animation_type == 'backIn_left-right') ? 'fadeIn' : $animation_type ;
    
    $twoSideAnimation = array(
      'fadeIn_left-right' => 'fadeIn',
      'lightSpeed_left-right' => 'lightSpeedIn',
      'backIn_left-right' => 'backIn',
      'fadeUpLeft-Right' => 'fadeUp',
      'flip-left-right' => 'flip-',
      'fadeDownLeft-Right' => 'fadeDown',
      'zoomInLeft-Right' => 'zoomIn',
      'zoomOutLeft-Right' => 'zoomOut',
    );
    
    if (array_key_exists($animation_type, $twoSideAnimation)) {
      $animation_type = $twoSideAnimation[$animation_type].$anim_class;
    }

    $animation_type = 'animate__animated  animate__'.$animation_type; //Final animation class

    $arrow_animation = 'animate__animated  animate__fadeIn'; //Final animation class
      
    $animation = (!$this->horizontal) ? $animation_status.' '.$animation_type : '';
    $arrow_animation = (!$this->horizontal) ? $animation_status.' '.$arrow_animation : '';

		//  Set the post color
		$this->style_css .= $this->color_style_tag($post);
		
		$arrow = '<svg viewBox="0 0 74 23" fill="none" xmlns="http://www.w3.org/2000/svg">
              <circle cx="62.5" cy="11.5" r="9" fill="white" stroke-width="5"/>
              <rect x="7.20001" y="10" width="44.4" height="3.6"/>
              <path d="M9.6 18.2V5L0 11.9882L9.6 18.2Z"/>
              </svg>';

		if ($post->section == 'start' || $post->section == 1) {
			$html .= '<section class="ptl-row ptl-sec-row no-gutters ptl-content-loaded">';
		}

		if ($arrow_class == 'left') {
			$arrow = '<svg viewBox="0 0 74 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle r="9" transform="matrix(-1 0 0 1 11.5 11.5)" fill="white" stroke-width="5"/>
                <rect width="44.4" height="3.6" transform="matrix(-1 0 0 1 66.8 10)"/>
                <path d="M64.4 18.2V5L74 11.9882L64.4 18.2Z"/>
                </svg>';
		}

    
    $hash = $post->tag;

    $hash     = strtolower($post->tag);
    $hash     = str_replace(' ', '-', $hash);


    if($box_class == 'right' && $count == 1){
      $html .= '<div id="ptl-post-empty-'. esc_attr($post->ID).'" class="timeline-box pol-md-6 '.$offset_class.' '.$media_class .' '.$box_class.' ptl-content-loaded" data-tag="'.$hash.'" >';
      $html .= '</div>';
    }

		$html .= '<div id="ptl-post-'. esc_attr($post->ID).'" class="timeline-box pol-md-6 '.$offset_class.' '.$media_class .' '.$box_class.' ptl-content-loaded" data-tag="'.$hash.'" >';
      $html .= '<article  class="ptl-tmpl-box '.$box_class.'-box">';
         $html .= '<div class="ptl-arrow-box '.$arrow_animation.'" data-panim-duration="'. $this->settings["ptl-anim-speed"].'s">';
            $html .= '<div class="ptl-inner-box">';
              $html .= '<div class="ptl-arrow"></div>';
              $html .= '<div class="ptl-line"></div>';
              $html .= '<div class="ptl-icon">';
                if ($post_icon_status == 'on'){
                  if (isset($post->custom['ptl-icon-type'][0]) && $post->custom['ptl-icon-type'][0] == 'upload-icon') {
                    if (!empty($post->custom['ptl-custom-icon'][0])) {
                      $icon_img = wp_get_attachment_image_src($post->custom['ptl-custom-icon'][0]);
                      $image_alt = (!empty(get_post_meta($post->custom['ptl-custom-icon'][0], '_wp_attachment_image_alt', TRUE))) ? get_post_meta($post->custom['ptl-custom-icon'][0], '_wp_attachment_image_alt', TRUE) : get_the_title($post->custom['ptl-custom-icon'][0]);
                      $html .= '<image class="ptl-icon-image ptl-post-icon" src="'.$icon_img[0].'" alt="'.$image_alt.'" />';
                    }
                  }else{
                    if (!empty($post->custom['ptl-fav-icon'][0])) 
                      $html .= '<i class="fa '.$post->custom['ptl-fav-icon'][0].'" aria-hidden="true"></i>';
                  }
                }

              $html .= '</div>';
            $html .= '</div>';
         $html .= '</div>';
         $html .= '<div class="ptl-tmpl-box-inner '.$animation.'" data-panim-duration="'. $this->settings["ptl-anim-speed"].'s">';
            if ($this->settings["ptl-post-link-type"] == 'title') {
              $html .= '<div class="ptl-top-title"><h2><a href="'. $post->custom['ptl-post-link'][0].'" '.$target_blank.'>'.$post->post_title.'</a></h2></div>';
            }else{
              $html .= '<div class="ptl-top-title"><h2>'. $post->post_title.'</h2></div>';
            }
            if ($this->settings['ptl-images'] == 'on') {
               if (!empty($this->render_post_thumbnail($post))) {
                 $html .= '<div class="ptl-tmpl-img-box">';
                     $html .=  $this->render_post_thumbnail($post);
                 $html .= '</div>';
               }

             }
            if ($content_hide != 'on') {
                $html .= '<div class="ptl-ctn-box">';
                    if (!empty($post_desc)) {
                      $html .= '<div class="ptl-short-desc">'.$post_desc.'</div>';
                    }

                    if (!empty($post->event_date)) {
                      $html .= '<span class="ptl-date">'.$post->event_date.'</span>';
                    }
                    
                    $html .= '<div class="ptl-tmpl-footer">';
                      $html .=  $this->social_icons($post->ID);
                      if ($this->settings["ptl-post-link-type"] == 'readmore') {
                        $html .=  '<a href="'.$post->custom['ptl-post-link'][0].'" class="ptl-tmpl-btn" '.$target_blank.'>'. __('Read More','post-timeline').'</a>';
                      }
                    $html .= '</div>';
                $html .= '</div>';
                
            }
        $html .= '</div>';
      $html .= '</article>';    
    $html .= '</div>';
		
    if($box_class == 'left' && $count == 1){
      $html .= '<div id="ptl-post-empty-'. esc_attr($post->ID).'" class="timeline-box pol-md-6 '.$offset_class.' '.$media_class .' '.$box_class.' ptl-content-loaded" data-tag="'.$hash.'" >';
      $html .= '</div>';
    }

		if($post->section == 'end' || $post->section == 1) {
			$html .=  '</section>';
		}

		return $html;
	}


  /**
   * [color_style_tag Generate the style tag for the post]
   * @param  [type] $post [description]
   * @param  string $skin [description]
   * @return [type]       [description]
   */
public function color_style_tag($post, $skin = 'dark') {
    
    //  Color for the post
    $primary_color = $this->get_post_color($post);
    $html  = '';
    $white = '#FFFFFF';
    $html .= "#ptl-post-{$post->ID} {
      --ptl-primary: {$primary_color};";
    if($this->settings['ptl-skin-type'] == 'ptl-light') {
      $html .= "--ptl-date-color: {$white};
      --ptl-post-background: {$white};
      --ptl-btn-color: {$white};
      --ptl-date-background: {$primary_color};
      --ptl-btn-background: {$primary_color};
      --ptl-footer-background:{$primary_color};
      --ptl-title-border-color: {$primary_color};
      --ptl-action-icon-color: {$white};

      --ptl-facebook-color: {$white};
      --ptl-facebook-background:#385398;
      --ptl-pinterest-color: {$white};
      --ptl-pinterest-background:#cc2129;
      --ptl-linkedin-color: {$white};
      --ptl-linkedin-background:#117bb7;
      --ptl-twitter-color: {$white};
      --ptl-twitter-background:#28a9e2;
      --ptl-instagram-color: {$white};
      --ptl-instagram-background:#f55376;
      --ptl-youtube-color: {$white};
      --ptl-youtube-background:#c32719;
      --ptl-whatsapp-color: {$white};
      --ptl-whatsapp-background:#30cc67;
      --ptl-google-plus-color: {$white};
      --ptl-google-plus-background:#e04a39;
      --ptl-envelope-color: {$white};
      --ptl-envelope-background:#cb0001;";
    }
    else {
      $html .= "--ptl-date-color: {$primary_color};
      --ptl-post-background: {$primary_color};
      --ptl-btn-color: {$primary_color};
      --ptl-date-background: {$white};
      --ptl-btn-background: {$white};
      --ptl-footer-background:{$white};
      --ptl-title-border-color: {$white};
      --ptl-action-icon-color: {$primary_color};

      --ptl-facebook-color:#385398;
      --ptl-facebook-background: {$white};
      --ptl-pinterest-color:#cc2129;
      --ptl-pinterest-background: {$white};
      --ptl-linkedin-color:#117bb7;
      --ptl-linkedin-background: {$white};
      --ptl-twitter-color:#28a9e2;
      --ptl-twitter-background: {$white};
      --ptl-instagram-color:#f55376;
      --ptl-instagram-background: {$white};
      --ptl-youtube-color:#c32719;
      --ptl-youtube-background: {$white};
      --ptl-whatsapp-color:#30cc67;
      --ptl-whatsapp-background: {$white};
      --ptl-google-plus-color:#e04a39;
      --ptl-google-plus-background: {$white};
      --ptl-envelope-color:#cb0001;
      --ptl-envelope-background: {$white};";
    }
    $html .= '}';
    return $html;
  }
}

