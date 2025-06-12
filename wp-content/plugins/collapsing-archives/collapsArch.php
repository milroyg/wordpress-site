<?php
/*
Plugin Name: Collapsing Archives
Plugin URI: http://robfelty.com/plugins/collapsing-archives
Description: Allows users to expand and collapse archive links like Blogger. <a href='http://wordpress.org/extend/plugins/collapsing-archives/other_notes'>Manual</a> | <a href='http://wordpress.org/extend/plugins/collapsing-archives/faq'>FAQ</a>
Author: Robert Felty
Version: 3.0.6
Author URI: http://robfelty.com

Copyright 2007-2023 Robert Felty

This file is part of Collapsing Archives

    Collapsing Archives is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    Collapsing Archives is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Collapsing Archives; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
$url = get_option('siteurl');
global $collapsArchVersion;
$collapsArchVersion = '3.0';

// LOCALIZATION
function collapsArch_load_domain() {
	load_plugin_textdomain( 'collapsArch', WP_PLUGIN_DIR."/".basename(dirname(__FILE__)), basename(dirname(__FILE__)) );
}
add_action('init', 'collapsArch_load_domain');


/****************/
if (!is_admin()) {
  add_action( 'wp_head', array('collapsArch','get_head'));
} else {
  // call upgrade function if current version is lower than actual version
  $dbversion = get_option('collapsArchVersion');
  if (!$dbversion || $collapsArchVersion != $dbversion)
    collapsArch::init();
}
register_activation_hook(__FILE__, array('collapsArch','init'));

class collapsArch {
	public static function init() {
    global $collapsArchVersion;
    $dbversion = get_option('collapsArchVersion');
    if ($collapsArchVersion != $dbversion) {
      update_option( 'collapsArchVersion', $collapsArchVersion);
    }
    if (!get_option('collapsArchVersion')) {
      add_option( 'collapsArchVersion', $collapsArchVersion);
		}

	}

	public static function get_head() {
    echo "<style type='text/css'>";
    echo collapsArch::set_styles();
    echo "</style>\n";
	}
  public static function set_styles() {
    $widget_options = get_option('widget_collapsarch');
    include('collapsArchStyles.php');
    $css = '';
    $oldStyle=true;
    foreach ($widget_options as $key=>$value) {
      $id = "widget-collapsarch-$key-top";
      if (isset($value['style'])) {
        $oldStyle=false;
        $style = $defaultStyles[$value['style']];
        $css .= str_replace('{ID}', '#' . $id, $style);
      }
    }
    if ($oldStyle)
      $css=stripslashes(get_option('collapsArchStyle'));
    return($css);
  }
  public static function phpArrayToJS($array, $name, $options) {
    /* generates javscript code to create an array from a php array */
	$js = "try { $name" .
        "['catTest'] = 'test'; } catch (err) { $name = new Object(); }\n";
    if (!$options['expandYears'] && $options['expandMonths']) {
		$js .= '//EXPANDING';
      $lastYear = -1;
      foreach ($array as $key => $value){
        $parts = explode('-', $key);
        $label = $parts[0];
        $year = $parts[1];
        $moreparts = explode(':', $key);
        $widget = $moreparts[1];
        if ($year != $lastYear) {
          if ($lastYear>0)
            $js .=  "';\n";
          $js .= $name . "['$label-$year:$widget'] = '" .
              addslashes(str_replace("\n", '', $value));

          $lastYear=$year;
        } else {
          $js .= addslashes(str_replace("\n", '', $value));
        }
      }
      $js .=  "';\n";
    } else {
      foreach ($array as $key => $value){
        $js .= $name . "['$key'] = '" .
            addslashes(str_replace("\n", '', $value)) . "';\n";
      }
    }
	return $js;
  }
	public static function render_callback( $attributes ) {
		include('collapsArchStyles.php');
		$html = '';
		$block_id = sanitize_key( $attributes['blockId'] );
		$wrapper_attributes = get_block_wrapper_attributes();
		$instance = $attributes;
		$instance['number'] = $block_id;
		if ( ! $instance['widgetTitle'] ) {
				$title = 'Archives';
				$instance['widgetTitle'] = $title;
		} else {
				$title = sanitize_title( $instance['widgetTitle'] );
		}
		$html .= "<h2 class='widget-title'>$title</h2>";
		if ( ! empty( $attributes[ 'style' ] ) ) {
			if ( isset( $defaultStyles[ $attributes['style'] ] ) ) {
				$html .= '<style type="text/css">';
				$style = $defaultStyles[ $attributes['style'] ];
				$style = str_replace('{ID}', 'ul#widget-collapsArch-' . esc_attr( $block_id ) . '-top', $style);
				$html .= "$style</style>";
			}
		}
		$html .= "<ul id='widget-collapsArch-" . esc_attr( $block_id ) . "-top'>";
		$html .= collapsArch($instance, $_COOKIE, false, true );
		$html .= "</ul>";
		return sprintf(
				'<div %1$s>%2$s</div>',
				$wrapper_attributes,
				$html
		);
	}
}
include_once( 'collapsArchList.php' );

function collapsArch($args='', $cookies=null, $print=true, $callback=false) {
	global $collapsArchItems;
	$defaults = array(
		'title'              => __('Archives', 'collapsArch'),
		'noTitle'            => '',
		'inExcludeCat'       => 'exclude',
		'inExcludeCats'      => '',
		'inExcludeYear'      => 'exclude',
		'inExcludeYears'     => '',
		'showPages'          => false,
		'sort'               => 'DESC',
		'linkToArch'         => true,
		'showYearCount'      => true,
		'expandCurrentYear'  => true,
		'expandMonths'       => true,
		'expandYears'        => true,
		'expandCurrentMonth' => true,
		'showMonthCount'     => true,
		'showPostTitle'      => true,
		'expand'             => '0',
		'showPostDate'       => false,
		'debug'              => '0',
		'postDateFormat'     => 'm/d',
		'postDateAppend'     => 'after',
		'accordion'          => 0,
		'useCookies'         => true,
		'post_type'          => 'post',
		'taxoncmy'           => 'category',
		'postTitleLength'    => '',
	);
  $options=wp_parse_args($args, $defaults);
  $html = '';
  if (!is_admin()) {
    if (!$options['number'] || $options['number']=='')
      $options['number']=1;
   $archives = list_archives($options);
    extract($options);
    include('symbols.php');
    $archives .= "<li style='display:none'><script type=\"text/javascript\">\n";
    $archives .= "// <![CDATA[\n";
      $archives .= '/* These variables are part of the Collapsing Archives Plugin
   * version: 3.0.6
   * revision: $Id: collapsArch.php 3131141 2024-08-05 15:09:23Z robfelty $
   * Copyright 2008 Robert Felty (robfelty.com)
           */' ."\n";

	// now we create an array indexed by the id of the ul for posts
	$archives .= collapsArch::phpArrayToJS($collapsArchItems, 'collapsItems', $options);
	$archives .= file_get_contents( dirname( __FILE__ ) . '/collapsFunctions.js' );
	$archives .= "widgetRoot = document.querySelector( '#widget-collapsArch-$number-top' );";
	$archives .= "addExpandCollapseArch(widgetRoot, '$expandSym', '$collapseSym', $accordion )";
	$archives .= "// ]]>\n</script></li>\n";
	if ( $print ) {
		print $archives;
	} elseif ($callback) {
		return $archives;
	}
  }
}
function create_block_collapsArch_block_init() {
		register_block_type(
				__DIR__ . '/build',
				[ 'render_callback' => [ collapsArch::class, 'render_callback' ] ]
		);
}
add_action( 'init', 'create_block_collapsArch_block_init' );
?>
