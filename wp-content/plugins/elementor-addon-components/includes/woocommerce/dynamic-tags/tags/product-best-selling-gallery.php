<?php
/**
 * Class: Product_Best_Selling_Gallery
 *
 * @return créer un tableau d'ID des images des meilleures ventes
 * @since 2.2.2
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

class Product_Best_Selling_Gallery extends Data_Tag {
	use \EACCustomWidgets\Includes\Woocommerce\DynamicTags\Tags\Traits\Eac_Product_Woo_Traits;

	public function get_name() {
		return 'eac-addon-woo-best-selling-gallery';
	}

	public function get_title() {
		return esc_html__( 'Galerie des meilleures ventes', 'eac-components' );
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return array( TagsModule::GALLERY_CATEGORY );
	}

	protected function register_controls() {
		$this->add_control(
			'woo_selling',
			array(
				'label'   => esc_html__( 'Nombre de produits', 'eac-components' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 1,
				'max'     => 50,
				'step'    => 1,
				'default' => 4,
			)
		);

		$this->register_product_term_control();
	}

	public function get_value( array $options = array() ) {
		$limit  = $this->get_settings( 'woo_selling' );
		$termid = $this->get_settings( 'product_category' );
		$value  = array();

		$products = wc_get_products(
			array(
				'meta_key' => 'total_sales',
				'limit'    => absint( $limit ),
				'parent'   => 0,
				'orderby'  => array(
					'meta_value_num' => 'DESC',
					'title'          => 'ASC',
				),
			)
		);

		if ( ! is_wp_error( $products ) && ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$product_id = $product->get_id();
				if ( ! empty( $termid ) && ! has_term( $termid, 'product_cat', $product_id ) ) {
					continue;
				}

				$thumb_id = $product->get_image_id();
				if ( $thumb_id ) {
					$value[] = array( 'id' => $thumb_id . '::selling::' . $product_id );
				}
			}
		}
		return $value;
	}
}
