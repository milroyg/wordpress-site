<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.7.1
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
require get_template_directory() . '/inc/page-config.php';
?>
<div class="breadcroumb-boxs">
      <div class="container">
            <div class="breadcroumb-box">
                  <div class="brea-title">
                       <?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
						<h2 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h3>
						<?php endif; ?>
						 <div class="brea-title">
	                        <?php woocommerce_breadcrumb(); ?> 
	                    </div>
                  </div>
            </div>
      </div>
</div>
<?php 
/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' ); 
if( is_active_sidebar('tuchno-shop')){
	$xevso_main_col = 'col-lg-8 col-md-12 col-sm-12 col-12';
}else{
	$xevso_main_col = 'col';
}
?>
<div class="row">
	<div class="<?php echo esc_attr($xevso_main_col); ?>">
		<?php
		if ( woocommerce_product_loop() ) {

			/**
			 * Hook: woocommerce_before_shop_loop.
			 *
			 * @hooked woocommerce_output_all_notices - 10
			 * @hooked woocommerce_result_count - 20
			 * @hooked woocommerce_catalog_ordering - 30
			 */
			do_action( 'woocommerce_before_shop_loop' );

			woocommerce_product_loop_start();

			if ( wc_get_loop_prop( 'total' ) ) {
				while ( have_posts() ) {
					the_post();

					/**
					 * Hook: woocommerce_shop_loop.
					 */
					do_action( 'woocommerce_shop_loop' );

					wc_get_template_part( 'content', 'product' );
				}
			}

			woocommerce_product_loop_end();

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
		} else {
			/**
			 * Hook: woocommerce_no_products_found.
			 *
			 * @hooked wc_no_products_found - 10
			 */
			do_action( 'woocommerce_no_products_found' );
		}
		?>
	</div>
	<?php if( is_active_sidebar('tuchno-shop')) : ?>
	<div class="col-lg-4 col-md-12 col-sm-12 col-12">
		<div class="sidebar">
			<?php dynamic_sidebar('tuchno-shop'); ?>
		</div>
	</div>
	<?php endif; ?>
</div>

<?php 
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' ); 

get_footer( 'shop' ); ?>
