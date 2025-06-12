<?php
/**
 * Class: Eac_Acf_Tags
 *
 * Description: Module de base pour construire les balises dynamques ACF
 * aux balises dynamiques ACF
 *
 * @since 1.7.5
 */

namespace EACCustomWidgets\Includes\Acf\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Eac_Acf_Tags {

	const TAG_DIR       = __DIR__ . '/tags/';
	const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';

	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'eac-acf-number'        => 'Eac_Acf_Number',
		'eac-acf-text'          => 'Eac_Acf_Text',
		'eac-acf-color'         => 'Eac_Acf_Color',
		'eac-acf-date'          => 'Eac_Acf_Date',
		'eac-acf-url'           => 'Eac_Acf_Url',
		'eac-acf-image'         => 'Eac_Acf_Image',
		'eac-acf-relational'    => 'Eac_Acf_Relational',
		'eac-acf-file'          => 'Eac_Acf_File',
		'eac-acf-group-text'    => 'Eac_Acf_Group_Text',
		'eac-acf-group-url'     => 'Eac_Acf_Group_Url',
		'eac-acf-group-image'   => 'Eac_Acf_Group_Image',
		'eac-acf-group-color'   => 'Eac_Acf_Group_Color',
		'eac-acf-group-date'    => 'Eac_Acf_Group_Date',
		'eac-acf-group-number'  => 'Eac_Acf_Group_Number',
		'eac-acf-group-file'    => 'Eac_Acf_Group_File',
		'eac-acf-gallery'       => 'Eac_Acf_Gallery',
		'eac-acf-group-gallery' => 'Eac_Acf_Group_Gallery',
		'eac-post-acf-keys'     => 'Eac_Post_Acf_Keys',
		'eac-post-acf-values'   => 'Eac_Post_Acf_Values',
	);

	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		/** Chargement de la Lib de gestion des balises ACF */
		if ( ! class_exists( \EACCustomWidgets\Includes\Acf\DynamicTags\Eac_Acf_Lib::class ) ) {
			require_once __DIR__ . '/eac-acf-lib.php';
		}
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_tags' ) );
	}

	/**
	 * Enregistre le groupe et les balises dynamiques des champs ACF
	 */
	public function register_tags( $dynamic_tags ) {
		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		$dynamic_tags->register_group( 'eac-acf-groupe', array( 'title' => esc_html__( 'EAC ACF', 'eac-components' ) ) );

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
new Eac_Acf_Tags();
