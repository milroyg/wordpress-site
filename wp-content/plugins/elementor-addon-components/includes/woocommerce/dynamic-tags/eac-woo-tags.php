<?php
/**
 * Class: Eac_Woo_Tags
 *
 * Description: Module de base qui enregistre les objets des balises dynamiques WooCommerce
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Woocommerce\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Eac_Woo_Tags {

	const TAG_DIR        = __DIR__ . '/tags/';
	const TAG_DIR_TRAITS = __DIR__ . '/tags/traits/';
	const TAG_NAMESPACE  = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'product-add-to-cart'          => 'Product_Add_To_Cart',
		'product-excerpt'              => 'Product_Excerpt',
		'product-featured-image'       => 'Product_Featured_Image',
		'product-onsale'               => 'Product_Onsale',
		'product-prices'               => 'Product_Prices',
		'product-rating'               => 'Product_Rating',
		'product-sku'                  => 'Product_Sku',
		'product-stock'                => 'Product_Stock',
		'product-terms'                => 'Product_Terms',
		'product-title'                => 'Product_Title',
		'product-url'                  => 'Product_Url',
		'product-sale'                 => 'Product_Sale',
		'product-category-image'       => 'Product_Category_Image',
		'product-category-url'         => 'Product_Category_Url',
		'product-field-keys'           => 'Product_Field_Keys',
		'product-field-values'         => 'Product_Field_Values',
		'product-best-selling-gallery' => 'Product_Best_Selling_Gallery',
		'product-category-gallery'     => 'Product_Category_Gallery',
		'product-categories-gallery'   => 'Product_Categories_Gallery',
		'product-featured-gallery'     => 'Product_Featured_Gallery',
		'product-gallery-images'       => 'Product_Gallery_Images',
		'product-recent-sales-gallery' => 'Product_Recent_Sales_Gallery',
		'product-similar-gallery'      => 'Product_Similar_Gallery',
		'product-upsell-gallery'       => 'Product_Upsell_Gallery',
	);

	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		// Charge le trait 'product id'
		require_once self::TAG_DIR_TRAITS . 'product-trait.php';

		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );

		/** Supprime les zéros à la fin des prix */
		add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
	}

	/**
	 * Enregistre le groupe et les balises dynamiques WooCommerce
	 */
	public function register_tags( $dynamic_tags ) {
		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-woo-groupe', array( 'title' => esc_html__( 'EAC WooCommerce', 'eac-components' ) ) );

		foreach ( $this->tags_list as $file => $class_name ) {
			$full_class_name = self::TAG_NAMESPACE . $class_name;
			$full_file       = self::TAG_DIR . $file . '.php';

			if ( ! file_exists( $full_file ) ) {
				continue;
			}

			// Le fichier est chargé avant de checker le nom de la class
			require_once $full_file;

			if ( class_exists( $full_class_name ) ) {
				$dynamic_tags->register( new $full_class_name() );
			}
		}
	}
}
new Eac_Woo_Tags();
