<?php

namespace PostTimeline;

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      0.0.1
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     agilelogix <support@agilelogix.com>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    0.0.1
	 */
	public static function activate() {

		global $wpdb;
		$charset_collate = 'utf8';
		$prefix 	 	 = $wpdb->prefix."";

		/*
		$wpdb->query("UPDATE `{$prefix}posts` SET post_type   = 'post-timeline' WHERE post_type = 'agile-timeline'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-post-date' WHERE meta_key = 'agile-timeline-post-date'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-date-format' WHERE meta_key = 'agile-timeline-date-format'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-post-color' WHERE meta_key = 'agile-timeline-post-color'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-tag-line' WHERE meta_key = 'agile-timeline-image-overlay'");

		$wpdb->query("UPDATE `{$prefix}posts` SET post_type = 'post-timeline' WHERE post_type = 'agile-timeline'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-post-date' WHERE meta_key = 'agile-timeline-post-date'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-date-format' WHERE meta_key = 'agile-timeline-date-format'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-post-color' WHERE meta_key = 'agile-timeline-post-color'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'post-timeline-image-overlay' WHERE meta_key = 'agile-timeline-image-overlay'");
		*/

		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-post-date' WHERE meta_key = 'post-timeline-post-date'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-date-format' WHERE meta_key = 'post-timeline-date-format'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-post-color' WHERE meta_key = 'post-timeline-post-color'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-tag-line' WHERE meta_key = 'post-timeline-image-overlay'");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_key = 'ptl-img-txt-pos' WHERE meta_key = 'post-timeline-txt-pos'");

		$wpdb->query("UPDATE `{$prefix}postmeta` SET meta_value = '#FF7473' WHERE meta_key = 'ptl-post-color' AND LENGTH(meta_value) = 1");
		$wpdb->query("UPDATE `{$prefix}postmeta` SET `meta_value` = REPLACE(`meta_value`, '/', '-') WHERE meta_key = 'ptl-post-date'");

		$result = $wpdb->query( "DESCRIBE $wpdb->terms `tag_order`" );

		if ( !$result ) {
			$query = "ALTER TABLE $wpdb->terms ADD `tag_order` INT( 4 ) NULL DEFAULT '0'";
			$result = $wpdb->query( $query );
		}
	}

}
