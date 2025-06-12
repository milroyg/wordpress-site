<?php
/**
 * Add custom block category to default categories.
 *
 * https://wordpress.org/gutenberg/handbook/designers-developers/developers/filters/block-filters/#managing-block-categories
 */
function ptl_blocks_starter_block_categories( $categories, $post ) {
	/*
	if ( $post->post_type !== 'post' ) {
		return $categories;
	}
	*/
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'ptl-blocks',
				'title' => esc_html__( 'PTL Blocks', 'post-timeline' ),
			),
		)
	);
}
add_filter( 'block_categories_all', 'ptl_blocks_starter_block_categories', 10, 2 );


/**
 * Enqueue block assets: Backend.
 *
 * https://github.com/WordPress/gutenberg/blob/master/docs/designers-developers/developers/tutorials/javascript/js-build-setup.md#dependency-management
 */
function ptl_blocks_starter_enqueue_block_editor_assets() {
	$blocks_dir = basename( __DIR__ ) . '/build/';

	$blocks_asset_file = include POST_TIMELINE_PLUGIN_PATH. 'admin/'.$blocks_dir . 'index.asset.php' ; // Plugin path: plugin_dir_path( dirname( __FILE__ ) ) . $blocks_dir . '/index.asset.php';

	// Replace "wp-blockEditor" with "wp-block-editor".
	$blocks_asset_file['dependencies'] = array_replace(
		$blocks_asset_file['dependencies'],
		array_fill_keys(
			array_keys( $blocks_asset_file['dependencies'], 'wp-blockEditor' ),
			'wp-block-editor'
		)
	);

	wp_enqueue_script(
		'ptl-blocks-starter-blocks',
		POST_TIMELINE_URL_PATH.'admin/' .$blocks_dir . 'index.js' , // Plugin path: plugin_dir_url( dirname( __FILE__ ) ) . $blocks_dir . '/index.js',
		$blocks_asset_file['dependencies'],
		$blocks_asset_file['version']
	);

	wp_localize_script(
	    'ptl-blocks-starter-blocks',
	    'PTL_BLOCK',
	    [
	        'ptl_path'       => POST_TIMELINE_URL_PATH
	    ]
	);
}
add_action( 'enqueue_block_editor_assets', 'ptl_blocks_starter_enqueue_block_editor_assets' );
