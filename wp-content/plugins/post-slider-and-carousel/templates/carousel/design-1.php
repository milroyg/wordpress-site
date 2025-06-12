<?php
/**
 * Carousel Template 1
 * 
 * @package Post Slider and Carousel
 * @since 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

// Post Meta Data
$meta_data = array(
				'author'	=> $atts['show_author'],
				'post_date'	=> $atts['show_date'],
				'comments'	=> $atts['show_comments']
			);
?>
<div class="psacp-post-slide <?php echo esc_attr( $atts['wrp_cls'] ); ?>">
	<div class="psacp-post-carousel-content">
		<div class="psacp-post-img-bg" style="<?php echo esc_attr( $atts['image_style'] ); ?>">
			<a class="psacp-post-linkoverlay" href="<?php echo esc_url( $atts['post_link'] ); ?>"></a>
			<div class="psacp-post-overlay">
				<?php if( $atts['show_category'] && $atts['cate_name'] ) { ?>
					<div class="psacp-post-cats"><?php echo wp_kses_post( $atts['cate_name'] ); ?></div>
				<?php }

				if( $atts['format'] == 'video' ) { echo psac_post_format_html( $atts['format'] ); } // WPCS: XSS ok. ?>
				<h2 class="psacp-post-title">
					<a href="<?php echo esc_url( $atts['post_link'] ); ?>"><?php the_title(); ?></a>
				</h2>

				<?php if( $atts['show_date'] || $atts['show_author'] || $atts['show_comments'] ) { ?>
				<div class="psacp-post-meta psacp-post-meta-up">
					<?php echo psac_post_meta_data( $meta_data ); // WPCS: XSS ok. ?>
				</div>
				<?php } ?>
			</div>
		</div>

		<?php if( $atts['show_content'] ) { ?>
		<div class="psacp-post-content">
			<div class="psacp-post-desc"><?php echo psac_get_post_excerpt( $post->ID, get_the_content(), $atts['content_words_limit'] ); // WPCS: XSS ok. ?></div>
			<?php if( $atts['show_read_more'] ) { ?>
			<a href="<?php echo esc_url( $atts['post_link'] ); ?>" class="psacp-rdmr-btn"><?php echo wp_kses_post( $atts['read_more_text'] ); ?></a>
			<?php } ?>
		</div>
		<?php }

		if( $atts['show_tags'] && $atts['tags'] ) { ?>
		<div class="psacp-post-meta psacp-post-meta-down"><?php echo wp_kses_post( $atts['tags'] ); ?></div>
		<?php } ?>
	</div>
</div>