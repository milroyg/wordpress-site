<?php
/**
 * Class: Eac_Dynamic_Tags
 *
 * Description: Enregistre les Balises Dynamiques (Dynamic Tags)
 * Met à disposition un ensemble de méthodes pour valoriser les options des listes de Tag
 * Ref: https://gist.github.com/iqbalrony/7ee129379965082fb6c62cf5db372752
 *
 * @since 1.6.0
 */

namespace EACCustomWidgets\Includes\Elementor\DynamicTags;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Dynamic_Tags {

	const TAG_DIR        = __DIR__ . '/tags/';
	const TAG_DIR_TRAITS = __DIR__ . '/tags/traits/';
	const TAG_NAMESPACE  = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'url-post'                 => 'Url_Post',
		'url-cpt'                  => 'Url_Cpt',
		'url-page'                 => 'Url_Page',
		'url-chart'                => 'Url_Chart',
		'url-all'                  => 'Url_All',
		'featured-image-url'       => 'Featured_Image_Url',
		'author-website-url'       => 'Author_Website_Url',
		'url-external-image'       => 'Url_External_Image',
		'post-by-user'             => 'Post_By_User',
		'post-custom-field-keys'   => 'Post_Custom_Field_Keys',
		'post-custom-field-values' => 'Post_Custom_Field_Values',
		'post-elementor-tmpl'      => 'Post_Elementor_Tmpl',
		'post-title'               => 'Post_Title',
		'post-excerpt'             => 'Post_Excerpt',
		'post-gallery'             => 'Post_Gallery',
		'featured-image'           => 'Featured_Image',
		'user-info'                => 'User_Info',
		'page-title'               => 'Page_Title',
		'site-email'               => 'Site_Email',
		'site-url'                 => 'Site_URL',
		'site-server'              => 'Site_Server',
		'site-title'               => 'Site_Title',
		'site-tagline'             => 'Site_Tagline',
		'site-logo'                => 'Site_Logo',
		'site-stats'               => 'Site_Stats',
		'author-info'              => 'Author_Info',
		'author-name'              => 'Author_Name',
		'author-picture'           => 'Author_Picture',
		'author-social-media'      => 'Author_Social_Media',
		'featured-image-data'      => 'Featured_Image_Data',
		'user-picture'             => 'User_Picture',
		'cookies-tag'              => 'Cookies_Tag',
		'shortcode-tag'            => 'Shortcode_Tag',
		'lightbox-tag'             => 'Lightbox_Tag',
	);

	/** Constructeur de la class */
	public function __construct() {
		// Charge le trait 'page/post'
		// require_once self::TAG_DIR_TRAITS . 'page-post-trait.php';

		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
	}

	/** Enregistre les groupes et les balises dynamiques (Dynamic Tags) */
	public function register_tags( $dynamic_tags ) {
		// Enregistre les nouveaux groupes avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-action', array( 'title' => esc_html__( 'EAC Actions', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-author-groupe', array( 'title' => esc_html__( 'EAC Auteur', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-post', array( 'title' => esc_html__( 'EAC Article', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-site-groupe', array( 'title' => esc_html__( 'EAC Site', 'eac-components' ) ) );
		$dynamic_tags->register_group( 'eac-url', array( 'title' => esc_html__( 'EAC URLs', 'eac-components' ) ) );

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
new Eac_Dynamic_Tags();
