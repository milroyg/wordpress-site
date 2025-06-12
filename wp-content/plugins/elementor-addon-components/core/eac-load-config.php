<?php
/**
 * Class: Eac_Config_Elements
 *
 * Description: Charge les listes de composants et des fonctionnalités
 * Implémente les méthodes pour gérer les éléments
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Eac_Config_Elements class */
class Eac_Config_Elements {

	/**
	 * $manage_options
	 *
	 * @var string
	 */
	private static $manage_options = 'eac_manage_options';

	/**
	 * $options_block_theme
	 *
	 * @var string
	 */
	private static $options_block_theme = 'eac_options_fse';

	/**
	 * L'option de la BDD des filtres WooCommerce
	 *
	 * @var String $options_woo_filter
	 */
	private static $options_woo_hooks = 'eac_options_woo_hooks';

	/**
	 * L'option de la BDD pour les menus de navigation du header footer
	 *
	 * @var String $options_mega_nav_menu
	 */
	private static $options_mega_nav_menu = 'eac_options_nav_menu-';

	/**
	 * L'option de la BDD pour les menus mis en cache
	 *
	 * @var String $options_mega_nav_menu_cache
	 */
	private static $options_mega_nav_menu_cache = 'eac_options_nav_menu_cache';

	/**
	 * L'option de la BDD pour le nombre d'utilisation des éléments par les pages/articles
	 *
	 * @var String $options_usage_count
	 */
	private static $options_usage_count = 'eac_options_usage_count';

	/**
	 * La structure de l'option de la BDD des filtres WooCommerce
	 *
	 * @var Array $woo_shop_args
	 */
	private static $woo_shop_args = array(
		'product-page'   => array(
			'shop'             => array(
				'url' => '',
				'id'  => 0,
			),
			'redirect_buttons' => false,
			'breadcrumb'       => false,
			'metas'            => false,
		),
		'catalog'        => array(
			'active'        => false,
			'request_quote' => false,
		),
		'redirect_pages' => false,
		'mini_cart'      => false,
	);

	/**
	 * $widgets_list
	 *
	 * @var array
	 */
	private static $widgets_list = array();

	/**
	 * $widgets_keys_active
	 *
	 * @var array
	 */
	private static $widgets_keys_active = array();

	/**
	 * $widgets_advanced_keys_active
	 *
	 * @var array
	 */
	private static $widgets_advanced_keys_active = array();

	/**
	 * $widgets_common_keys_active
	 *
	 * @var array
	 */
	private static $widgets_common_keys_active = array();

	/**
	 * $widgets_ehf_keys_active
	 *
	 * @var array
	 */
	private static $widgets_ehf_keys_active = array();

	/**
	 * $features_list
	 *
	 * @var array
	 */
	private static $features_list = array();

	/**
	 * $features_keys_active
	 *
	 * @var array
	 */
	private static $features_keys_active = array();

	/**
	 * $features_advanced_keys_active
	 *
	 * @var array
	 */
	private static $features_advanced_keys_active = array();

	/**
	 * $features_common_keys_active
	 *
	 * @var array
	 */
	private static $features_common_keys_active = array();

	/**
	 * $elements_list
	 *
	 * @var array
	 */
	private static $elements_list = array();

	/**
	 * $elements_keys
	 *
	 * @var array
	 */
	private static $elements_keys = array();

	/**
	 * $elements_keys_active
	 *
	 * @var array
	 */
	private static $elements_keys_active = array();

	/**
	 * $element_usage
	 *
	 * La liste des éléments actifs par leur name
	 *
	 * @var array
	 */
	private static $element_usage = array();

	/**
	 * $options_elements_name
	 *
	 * @var string
	 */
	private static $options_elements_name = 'eac_options_elements';

	/**
	 * $count_enabled_elements
	 *
	 * @var int
	 */
	private static $count_enabled_elements = 0;

	/**
	 * $count_disabled_elements
	 *
	 * @var int
	 */
	private static $count_disabled_elements = 0;

	/**
	 * $count_all_elements
	 *
	 * @var int
	 */
	private static $count_all_elements = 0;

	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 */
	private static $instance = null;

	/**
	 * __construct
	 *
	 * @return void
	 */
	private function __construct() {
		add_action( 'current_screen', array( $this, 'set_element_usage' ) );

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			update_option( self::$options_block_theme, 'true' );
		} else {
			update_option( self::$options_block_theme, 'false' );
		}

		// Charge les listes des widgets et des fonctionnalités
		$this->set_widgets_list();
		$this->set_features_list();

		/**
		 * Construit la liste des widgets actives par défaut
		 * TODO Conservé pour la non régression, à supprimer
		 */
		$widgets_keys = array();
		foreach ( self::$widgets_list as $key => $value ) {
			$widgets_keys[ $key ] = $value['active'];
		}

		$options_widgets_name = 'eac_options_settings';
		if ( get_option( $options_widgets_name, false ) ) {
			self::$widgets_keys_active = get_option( $options_widgets_name );
		} else {
			self::$widgets_keys_active = $widgets_keys;
		}

		/**
		 * Construit la liste des fonctionnalités actives par défaut
		 * TODO Conservé pour la non régression, à supprimer
		 */
		$features_keys = array();
		foreach ( self::$features_list as $key => $value ) {
			$features_keys[ $key ] = $value['active'];
		}

		$options_features_name = 'eac_options_features';
		if ( get_option( $options_features_name, false ) ) {
			self::$features_keys_active = get_option( $options_features_name );
		} else {
			self::$features_keys_active = $features_keys;
		}

		/**
		 * Construit la liste des éléments actifs par défaut
		 * Regroupe les deux anciennes options existantes
		 */
		self::$elements_list = array_merge( self::$widgets_list, self::$features_list );
		foreach ( self::$elements_list as $key => $value ) {
			self::$elements_keys[ $key ] = $value['active'];
		}

		// Enregistre l'option des elements si elle n'existe pas
		if ( get_option( self::$options_elements_name, false ) ) {
			$this->compare_elements_option();
		} else {
			update_option( self::$options_elements_name, array_merge( self::$widgets_keys_active, self::$features_keys_active ) );
			self::$elements_keys_active = array_merge( self::$widgets_keys_active, self::$features_keys_active );
		}

		/** Le nom des composants séparateurs des différents groupes */
		$widgets_advanced_name  = 'all-advanced';
		$widgets_common_name    = 'all-components';
		$widgets_ehf_name       = 'all-ehf';
		$features_advanced_name = 'all-features-advanced';
		$features_common_name   = 'all-features-common';

		/** Position des composants par groupe dans la liste des éléménts actifs */
		$pos_widgets_advanced  = array_search( $widgets_advanced_name, array_keys( self::$elements_keys_active ), true );
		$pos_widgets_common    = array_search( $widgets_common_name, array_keys( self::$elements_keys_active ), true );
		$pos_widgets_ehf       = array_search( $widgets_ehf_name, array_keys( self::$elements_keys_active ), true );
		$pos_features_advanced = array_search( $features_advanced_name, array_keys( self::$elements_keys_active ), true );
		$pos_features_common   = array_search( $features_common_name, array_keys( self::$elements_keys_active ), true );

		/** Variables utilisées pour répartir les composants dans leurs groupes d'onglets dans l'admin */
		self::$widgets_advanced_keys_active  = array_slice( self::$elements_keys_active, $pos_widgets_advanced, $pos_widgets_common );
		self::$widgets_common_keys_active    = array_slice( self::$elements_keys_active, $pos_widgets_common, ( $pos_widgets_ehf - $pos_widgets_common ) );
		self::$widgets_ehf_keys_active       = array_slice( self::$elements_keys_active, $pos_widgets_ehf, ( $pos_features_advanced - $pos_widgets_ehf ) );
		self::$features_advanced_keys_active = array_slice( self::$elements_keys_active, $pos_features_advanced, ( $pos_features_common - $pos_features_advanced ) );
		self::$features_common_keys_active   = array_slice( self::$elements_keys_active, $pos_features_common );

		/** Variables pour répartir les widgets et les fonctionnalités actives */
		self::$widgets_keys_active  = array_merge( self::$widgets_advanced_keys_active, self::$widgets_common_keys_active, self::$widgets_ehf_keys_active );
		self::$features_keys_active = array_merge( self::$features_advanced_keys_active, self::$features_common_keys_active );

		/** Met à jour les anciennes options pour garder la compatibilité */
		update_option( $options_widgets_name, self::$widgets_keys_active );
		update_option( $options_features_name, self::$features_keys_active );

		/** Compte les éléments */
		foreach ( self::$elements_keys_active as $key => $active ) {
			if ( 'all-' !== substr( $key, 0, 4 ) && ( self::are_widget_dependencies_enabled( $key ) || self::are_feature_dependencies_enabled( $key ) ) ) {
				self::$count_all_elements++;
				if ( $active ) {
					$name = self::get_widget_name( $key );
					if ( ! empty( $name ) && ! array_search( $name, self::$element_usage, true ) ) {
						$key = sanitize_text_field( $key );
						self::$element_usage[ $key ] = array(
							'name'  => sanitize_text_field( $name ),
							'count' => 0,
							'perma' => '',
						);
					}
					self::$count_enabled_elements++;
				} else {
					self::$count_disabled_elements++;
				}
			}
		}
	}

	/**
	 * instance
	 *
	 * @return void
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * set_element_usage
	 *
	 * Le nombre de articles / pages ou autres post types dans lesquels le widget est utilisé
	 * The number of posts / pages or other post types in which the widget is used
	 *
	 * @return void
	 */
	public function set_element_usage(): void {
		/**
		SELECT post_title p, ID p, post_type p, post_id pm FROM `wp_posts` p, `wp_postmeta` pm
		WHERE p.ID = pm.post_id
		AND p.post_type <> "revision"
		AND p.post_status = "publish"
		AND pm.meta_key = "_elementor_data"
		AND pm.meta_value LIKE '%"widgetType":"eac-addon-breadcrumbs"%';
		------------
		SELECT pm.`post_id`, p.`post_title`
		FROM `eac_postmeta` pm, `eac_posts` p
		WHERE pm.`post_id` = p.`ID`
		AND p.`post_type` <> 'revision'
		AND p.`post_status` = 'publish'
		AND pm.`meta_key` = '_elementor_controls_usage'
		AND pm.`meta_value` LIKE '%eac-addon-lecteur-rss%'
		*/
		global $wpdb;

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}
		$screen = get_current_screen();

		if ( is_object( $screen ) && 'toplevel_page_eac-components' === $screen->id && self::is_feature_active( 'widget-count' ) ) {
			$usage_options = get_option( self::$options_usage_count, false );
			if ( $usage_options ) {
				self::$element_usage = array();
				foreach ( $usage_options as $key => $values ) {
					self::$element_usage[ $key ] = array(
						'name'  => $values['name'],
						'count' => $values['count'],
						'perma' => $values['perma'],
					);
				}
			} else {
				foreach ( self::$element_usage as $key => $values ) {
					$post_ids = $wpdb->get_col(
						$wpdb->prepare(
							"SELECT pm.post_id
							FROM {$wpdb->prefix}postmeta pm, {$wpdb->prefix}posts p
							WHERE pm.post_id = p.ID
							AND pm.meta_key = \"_elementor_data\"
							AND pm.meta_value LIKE %s
							AND p.post_status = \"publish\"
							AND p.post_type <> \"revision\"",
							'%"widgetType":"' . $values['name'] . '"%'
						)
					);
					if ( is_countable( $post_ids ) && ! empty( $post_ids ) ) {
						$perma = array();
						self::$element_usage[ $key ]['count'] = absint( count( $post_ids ) );
						foreach ( $post_ids as $post_id ) {
							$perma_link = get_permalink( $post_id );
							if ( $perma_link ) {
								$perma[] = esc_url_raw( $perma_link );
							}
						}
						self::$element_usage[ $key ]['perma'] = implode( ',', $perma );
					}
				}

				if ( ! empty( self::$element_usage ) ) {
					update_option( self::$options_usage_count, self::$element_usage );
				}
			}
		}
		remove_action( 'current_screen', array( $this, 'set_element_usage' ) );
	}

	/**
	 * get_elements_option_name
	 *
	 * @return String Le libellé de l'option des éléments
	 */
	public static function get_elements_option_name() {
		return self::$options_elements_name;
	}

	/**
	 * get_elements_active
	 *
	 * @return Array La liste des éléments et leur statut
	 */
	public static function get_elements_active() {
		return self::$elements_keys_active;
	}

	/**
	 * compare_elements_option
	 *
	 * Charge les options des éléments
	 * Ajoute/Supprime les éléments en comparant les options par défaut et celles de la BDD
	 */
	private function compare_elements_option() {
		$diff_settings = array();

		/** Récupère les options dans la BDD */
		$bdd_settings = get_option( self::$options_elements_name );

		/** Ajoute les widgets */
		if ( count( self::$elements_keys ) > count( $bdd_settings ) ) {
			$diff_settings = array_merge( $bdd_settings, array_diff_key( self::$elements_keys, $bdd_settings ) );
			update_option( self::$options_elements_name, $diff_settings );
			/** Supprime les widgets */
		} elseif ( count( self::$elements_keys ) < count( $bdd_settings ) ) {
			$diff_settings = array_diff_key( $bdd_settings, array_diff_key( $bdd_settings, self::$elements_keys ) );
			update_option( self::$options_elements_name, $diff_settings );
		}

		/** Charge les options mises à jour et les conserve ordonnées avec array_merge comme dans la liste $elements_list */
		self::$elements_keys_active = array_merge( self::$elements_keys, get_option( self::$options_elements_name ) );
	}

	/**
	 * get_widgets_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_active() {
		return self::$widgets_keys_active;
	}

	/**
	 * get_widgets_advanced_active
	 *
	 * @return Array La liste des composants et leur statut
	 */
	public static function get_widgets_advanced_active() {
		return self::$widgets_advanced_keys_active;
	}

	/**
	 * get_widgets_common_active
	 *
	 * @return Array La liste des composants communs et leur statut
	 */
	public static function get_widgets_common_active() {
		return self::$widgets_common_keys_active;
	}

	/**
	 * get_widgets_ehf_active
	 *
	 * @return Array La liste des composants communs et leur statut
	 */
	public static function get_widgets_ehf_active() {
		return self::$widgets_ehf_keys_active;
	}

	/**
	 * is_widget_active
	 *
	 * Découpage de la fonction en deux parties 'active et dépendances'
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool Composant actif
	 */
	public static function is_widget_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			if ( ! self::are_widget_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen de l'élément stocké dans la table des options
			$active = self::$elements_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_widget_dependencies_enabled
	 *
	 * Check si le breadcrumb est actif avec quelques plugin SEO
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool les dépendances sont actives
	 */
	public static function are_widget_dependencies_enabled( $element ) {
		$active = true;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$elements_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$elements_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}

			/**
			 * Teste si la fonction'yoast_breadcrumb' existe
			 * Teste de l'option Rank Math
			 * Teste si les version PRO Elementor et ACF existent (acf_register_block_type)
			 */
			if ( ! empty( self::$elements_list[ $element ]['elems_exclude'] ) ) {
				foreach ( self::$elements_list[ $element ]['elems_exclude'] as $elem ) {
					if ( 'yoast' === $elem && function_exists( 'yoast_breadcrumb' ) && isset( get_option( 'wpseo_titles' )['breadcrumbs-enable'] ) && get_option( 'wpseo_titles' )['breadcrumbs-enable'] ) {
						return false;
					} elseif ( 'rankmath' === $elem && function_exists( 'rank_math_the_breadcrumbs' ) && isset( get_option( 'rank-math-options-general' )['breadcrumbs'] ) && 'on' === get_option( 'rank-math-options-general' )['breadcrumbs'] ) {
						return false;
					} elseif ( 'seopress' === $elem && function_exists( 'seopress_display_breadcrumbs' ) ) {
						return false;
					} elseif ( 'bcn' === $elem && function_exists( 'bcn_display' ) ) { // NavXT
						return false;
					} elseif ( 'ELEMENTOR_PRO_VERSION' === $elem && defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						return false;
					} elseif ( 'acf_register_block_type' === $elem && function_exists( 'acf_register_block_type' ) ) {
						return false;
					}
				}
			}
		}

		return $active;
	}

	/**
	 * get_widget_path
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le chemin absolu du fichier PHP des composants
	 */
	public static function get_widget_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_path = self::$elements_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_widget_namespace
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le NAMESPACE du composant
	 */
	public static function get_widget_namespace( $element ) {
		$full_class_name = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_class_name = self::$elements_list[ $element ]['name_space'];
			if ( ! empty( $full_class_name ) && class_exists( $full_class_name ) ) {
				return $full_class_name;
			}
		}
		return $full_class_name;
	}

	/**
	 * get_widget_name
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le nom du composant unique
	 */
	public static function get_widget_name( $element ) {
		$name = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && isset( self::$elements_list[ $element ]['name'] ) ) {
			$name = self::$elements_list[ $element ]['name'];
		}
		return $name;
	}

	/**
	 * get_widget_title
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String Le titre du composant traduit dans la locale + le nombre de publications si la fonctionnalité est active
	 */
	public static function get_widget_title( $element ): string {
		$title = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$is_elementor = ( isset( $_POST['action'] ) && 'elementor_element_manager_get_admin_app_data' === $_POST['action'] ) || \Elementor\Plugin::$instance->editor->is_edit_mode();

			if ( 'all-' === substr( $element, 0, 4 ) || empty( self::$element_usage ) || ! self::is_feature_active( 'widget-count' ) || $is_elementor ) {
				$title = sprintf( '%s', esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) );
			} else {
				$count = isset( self::$element_usage[ $element ] ) ? absint( self::$element_usage[ $element ]['count'] ) : '0';
				$perma = isset( self::$element_usage[ $element ] ) ? self::$element_usage[ $element ]['perma'] : '';
				$title = sprintf( '%s %s', '<span class="eac-elements__item-title">' . esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) . '</span>', '<span class="eac-elements__item-count" data-perma="' . $perma . '">' . $count . '</span>' );
			}
		}
		return $title;
	}

	/**
	 * get_widget_icon
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La class Elementor de l'icone du widget
	 */
	public static function get_widget_icon( $element ) {
		$icon = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$icon = self::$elements_list[ $element ]['icon'];
		}
		return $icon;
	}

	/**
	 * get_widget_keywords
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return Array La liste des mots-clés du widget
	 */
	public static function get_widget_keywords( $element ) {
		$keywords = array();

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$keywords = self::$elements_list[ $element ]['keywords'];
		}
		return $keywords;
	}

	/**
	 * get_widget_help_url
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String L'URL de l'aide en ligne du widget
	 */
	public static function get_widget_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help = self::$elements_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_widget_help_url_class
	 *
	 * @param String le composant à checker
	 *
	 * @return String La class de l'aide en ligne du widget
	 */
	public static function get_widget_help_url_class( $element ): string {
		$help_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help_class = self::$elements_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_widget_badge_class
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La class du badge du widget
	 */
	public static function get_widget_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && ! empty( self::$elements_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$elements_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * get_widget_categories
	 *
	 * @param $element (String) le composant à checker
	 *
	 * @return String La catégorie du widget
	 */
	public static function get_widget_categories( $element ) {
		$category = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$category = self::$elements_list[ $element ]['category'];
		}
		return $category;
	}

	/**
	 * set_widgets_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 * NE JAMAIS SUPPRIMER et AJOUTER UNE CLÉ POUR LA MÊME VERSION
	 */
	public function set_widgets_list() {

		self::$widgets_list = array(
			'all-advanced'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Tous les composants activés', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'acf-relationship'  => array(
				'active'         => true,
				'title'          => esc_html__( 'ACF relationship', 'eac-components' ),
				'keywords'       => array( 'ACF', 'relationship', 'grid' ),
				'icon'           => 'eicon-gallery-grid eac-icon-elements',
				'name'           => 'eac-addon-acf-relationship',
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-display-acf-relationship-posts-in-a-grid/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'acf-relationship.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Acf_Relationship_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'advanced-gallery'  => array(
				'active'         => true,
				'title'          => esc_html__( 'Galerie d&#039;images avancée', 'eac-components' ),
				'keywords'       => array( 'image', 'gallery', 'masonry', 'grid', 'metro', 'slider' ),
				'icon'           => 'eicon-gallery-justified eac-icon-elements',
				'name'           => 'eac-addon-advanced-gallery',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-advanced-image-gallery-using-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'advanced-image-gallery.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Advanced_Image_Gallery_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'author-infobox'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Boîte auteur', 'eac-components' ),
				'keywords'       => array( 'author', 'info', 'box' ),
				'icon'           => 'eicon-person eac-icon-elements',
				'name'           => 'eac-addon-author-infobox',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-an-author-info-box-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'author-infobox.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Author_Infobox_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'chart'             => array(
				'active'         => true,
				'title'          => esc_html__( 'Diagrammes', 'eac-components' ),
				'keywords'       => array( 'chart', 'json', 'bar', 'line', 'pie' ),
				'icon'           => 'eicon-dashboard eac-icon-elements',
				'name'           => 'eac-addon-chart',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'chart.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Chart_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'count-down'         => array(
				'active'         => false,
				'title'          => esc_html__( 'Compte à rebours', 'eac-components' ),
				'keywords'       => array( 'countdown', 'time', 'date' ),
				'icon'           => 'eicon-countdown eac-icon-elements',
				'name'           => 'eac-addon-count-down',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-a-countdown-timer-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'count-down.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Count_Down_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'html-sitemap'      => array(
				'active'         => true,
				'title'          => esc_html__( 'HTML sitemap', 'eac-components' ),
				'keywords'       => array( 'html', 'sitemap' ),
				'icon'           => 'eicon-sitemap eac-icon-elements',
				'name'           => 'eac-addon-html-sitemap',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-do-i-make-a-html-sitemap-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'html-sitemap.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Html_Sitemap_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'image-galerie'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Galerie d&#039;images', 'eac-components' ),
				'keywords'       => array( 'image', 'gallery', 'masonry', 'justify', 'grid', 'slider' ),
				'icon'           => 'eicon-gallery-masonry eac-icon-elements',
				'name'           => 'eac-addon-image-galerie',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-amazing-image-gallery-using-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'image-gallery.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Galerie_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'image-hotspots'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Image réactive', 'eac-components' ),
				'keywords'       => array( 'image', 'hotspot' ),
				'icon'           => 'eicon-hotspot eac-icon-elements',
				'name'           => 'eac-addon-image-hotspots',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-image-hotspots-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'image-hotspots.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Image_Hotspots_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'lottie-animations' => array(
				'active'         => true,
				'title'          => esc_html__( 'Lottie animation', 'eac-components' ),
				'keywords'       => array( 'lottie', 'animation' ),
				'icon'           => 'eicon-lottie eac-icon-elements',
				'name'           => 'eac-addon-lottie-animations',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-lottie-animation-in-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'lottie-animations.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lottie_Animations_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'modal-box'         => array(
				'active'         => true,
				'title'          => esc_html__( 'Boîte modale', 'eac-components' ),
				'keywords'       => array( 'modalbox', 'popup' ),
				'icon'           => 'eicon-lightbox eac-icon-elements',
				'name'           => 'eac-addon-modal-box',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/elementor-modal-box-doc/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'modal-box.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Modal_Box_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'news-ticker'       => array(
				'active'         => true,
				'title'          => esc_html__( 'Fil d&#039;actualité', 'eac-components' ),
				'keywords'       => array( 'news', 'ticker', 'feed' ),
				'icon'           => 'eicon-posts-ticker eac-icon-elements',
				'name'           => 'eac-addon-news-ticker',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-a-news-ticker-reader-in-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'news-ticker.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'News_Ticker_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'off-canvas'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Barre latérale', 'eac-components' ),
				'keywords'       => array( 'off-canvas', 'Menu' ),
				'icon'           => 'eicon-off-canvas eac-icon-elements',
				'name'           => 'eac-addon-off-canvas',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-off-canvas-menu-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'off-canvas.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Off_Canvas_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'open-streetmap'    => array(
				'active'         => true,
				'title'          => esc_html__( 'OpenStreetMap', 'eac-components' ),
				'keywords'       => array( 'map', 'openstreetmap' ),
				'icon'           => 'eicon-google-maps eac-icon-elements',
				'name'           => 'eac-addon-open-streetmap',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-openstreetmap-to-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'open-streetmap.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Open_Streetmap_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'pdf-viewer'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Visionneuse PDF', 'eac-components' ),
				'keywords'       => array( 'viewer', 'PDF', 'embed' ),
				'icon'           => 'eicon-document-file eac-icon-elements',
				'name'           => 'eac-addon-pdf-viewer',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-a-pdf-viewer-to-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'pdf-viewer.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Simple_PDF_Viewer_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'articles-liste'    => array(
				'active'         => true,
				'title'          => esc_html__( 'Grille d&#039;articles', 'eac-components' ),
				'keywords'       => array( 'post', 'query', 'category', 'post_tag', 'acf', 'slider' ),
				'icon'           => 'eicon-posts-grid eac-icon-elements',
				'name'           => 'eac-addon-articles-liste',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-advanced-queries-for-the-component-post-grid/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'post-grid.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Articles_Liste_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'lecteur-rss'       => array(
				'active'         => true,
				'title'          => esc_html__( 'Lecteur RSS', 'eac-components' ),
				'keywords'       => array( 'rss', 'feed' ),
				'icon'           => 'eicon-alert eac-icon-elements',
				'name'           => 'eac-addon-lecteur-rss',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-add-rss-feed-reader-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'rss-reader.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lecteur_Rss_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'table-content'     => array(
				'active'         => true,
				'title'          => esc_html__( 'Table des matières', 'eac-components' ),
				'keywords'       => array( 'table of content' ),
				'icon'           => 'eicon-table-of-contents eac-icon-elements',
				'name'           => 'eac-addon-toc',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-and-display-the-table-of-contents-of-your-posts/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'table-of-contents.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Table_Of_Contents_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'team-members'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Membres de l&#039;équipe', 'eac-components' ),
				'keywords'       => array( 'team', 'member', 'info' ),
				'icon'           => 'eicon-person eac-icon-elements',
				'name'           => 'eac-addon-team-members',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-a-team-members-page-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'team-members.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Team_Members_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'woo-product-grid'  => array(
				'active'         => false,
				'title'          => esc_html__( 'WC Grille de produits', 'eac-components' ),
				'keywords'       => array( 'product', 'query', 'filter', 'category', 'tag' ),
				'icon'           => 'eicon-products eac-icon-elements',
				'name'           => 'eac-addon-product-grid',
				'class_depends'  => array( 'WooCommerce' ),
				'func_depends'   => array(),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/woocommerce-product-grid-for-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'wc-product-grid.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'WC_Product_Grid_Widget',
				'badge'          => '',
				'category'       => array( 'eac-advanced' ),
			),
			'all-components'    => array(
				'active'         => false,
				'title'          => esc_html__( 'Tous les composants activés', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'images-comparison' => array(
				'active'         => false,
				'title'          => esc_html__( 'Comparaison d&#039;images', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-image-before-after eac-icon-elements',
				'name'           => 'eac-addon-images-comparison',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/animation-dimages/#image-comparison',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'images-comparison.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Images_Comparison_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'pinterest-rss'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Flux Pinterest', 'eac-components' ),
				'keywords'       => array( 'rss', 'feed', 'pinterest' ),
				'icon'           => 'eicon-social-icons eac-icon-elements',
				'name'           => 'eac-addon-pinterest-rss',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/rss-feed/#display-and-share-your-favorite-pinterest-feeds',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'pinterest-rss.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Pinterest_Rss_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'image-promotion'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Promotion de produit', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-price-table eac-icon-elements',
				'name'           => 'eac-addon-image-promo',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/promotion-de-produit/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'product-promotion.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Product_Promotion_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'site-thumbnail'    => array(
				'active'         => false,
				'title'          => esc_html__( 'Miniature de site', 'eac-components' ),
				'keywords'       => array( 'site', 'thumbnail' ),
				'icon'           => 'eicon-thumbnails-right eac-icon-elements',
				'name'           => 'eac-addon-site-thumbnail',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-website-thumbnail-like-a-screenshot/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'site-thumbnail.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Site_Thumbnails_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'reseaux-sociaux'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Partager un article', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => 'eicon-share eac-icon-elements',
				'name'           => 'eac-addon-reseaux-sociaux',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => EAC_WIDGETS_PATH . 'share-post.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Reseaux_Sociaux_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'syntax-highlight'  => array(
				'active'         => false,
				'title'          => esc_html__( 'Coloration syntaxique', 'eac-components' ),
				'keywords'       => array( 'syntax', 'php', 'css' ),
				'icon'           => 'eicon-code-highlight eac-icon-elements',
				'name'           => 'eac-addon-syntax-highlighter',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/syntax-highlighter/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'syntax-highlighter.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Syntax_Highlighter_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'lecteur-audio'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Flux webradio', 'eac-components' ),
				'keywords'       => array( 'webradio', 'player', 'audio' ),
				'icon'           => 'eicon-headphones eac-icon-elements',
				'name'           => 'eac-addon-lecteur-audio',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/rss-feed/#listen-to-your-favorite-web-radios-feeds',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'webradio-player.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Lecteur_Audio_Widget',
				'badge'          => '',
				'category'       => array( 'eac-basic' ),
			),
			'all-ehf'           => array(
				'active'         => false,
				'title'          => esc_html__( 'Tous les composants activés', 'eac-components' ),
				'keywords'       => array(),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'header-footer'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Entête - Pied de page', 'eac-components' ),
				'keywords'       => array( 'header', 'footer', 'builder' ),
				'icon'           => '',
				'name'           => '',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/how-to-create-custom-headers-and-footers-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'documents/manager.php',
				'name_space'     => '',
				'badge'          => '',
				'category'       => '',
			),
			'breadcrumbs'       => array(
				'active'         => false,
				'title'          => esc_html__( 'Fil d&#039;ariane', 'eac-components' ),
				'keywords'       => array( 'breadcrumb' ),
				'icon'           => 'eicon-product-breadcrumbs eac-icon-elements',
				'name'           => 'eac-addon-breadcrumbs',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'yoast', 'rankmath', 'seopress', 'bcn' ),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-a-breadcrumb-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/breadcrumb.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Breadcrumb_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'button-back'       => array(
				'active'         => false,
				'title'          => esc_html__( 'Bouton retour en haut', 'eac-components' ),
				'keywords'       => array( 'button', 'top' ),
				'icon'           => 'eicon-v-align-top eac-icon-elements',
				'name'           => 'eac-addon-button-back',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-a-button-back-to-top-in-the-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/button-back.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Button_Back_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'site-copyright'    => array(
				'active'         => false,
				'title'          => esc_html__( 'Copyright', 'eac-components' ),
				'keywords'       => array( 'copyright', 'site' ),
				'icon'           => 'eicon-shortcode eac-icon-elements',
				'name'           => 'eac-addon-site-copyright',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-custom-copyright-in-the-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/site-copyright.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Copyright_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'memory-usage'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Utilisation mémoire', 'eac-components' ),
				'keywords'       => array( 'usage', 'memory' ),
				'icon'           => 'eicon-info eac-icon-elements',
				'name'           => 'eac-addon-memory-usage',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/improve-page-loading-speed/#activate-and-use-memory-usage-component',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WIDGETS_PATH . 'memory-usage.php',
				'name_space'     => EAC_WIDGETS_NAMESPACE . 'Memory_Usage_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'page-title'        => array(
				'active'         => false,
				'title'          => esc_html__( 'Titre de la page', 'eac-components' ),
				'keywords'       => array( 'title', 'page' ),
				'icon'           => 'eicon-post-title eac-icon-elements',
				'name'           => 'eac-addon-page-title',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-page-title-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/page-title.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Page_Title_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'reader-progress'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Barre de progression de lecture', 'eac-components' ),
				'keywords'       => array( 'progress' ),
				'icon'           => 'eicon-progress-tracker eac-icon-elements',
				'name'           => 'eac-addon-reader-progress',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-a-reading-progress-bar-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/reader-progress-bar.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Reader_Progress_Bar_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'site-search'       => array(
				'active'         => false,
				'title'          => esc_html__( 'Rechercher', 'eac-components' ),
				'keywords'       => array( 'search', 'form' ),
				'icon'           => 'eicon-site-search eac-icon-elements',
				'name'           => 'eac-addon-site-search',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-search-widget-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/site-search.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Search_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'mega-menu'         => array(
				'active'         => false,
				'title'          => esc_html__( 'Navigation menu', 'eac-components' ),
				'keywords'       => array( 'navigation', 'nav', 'menu', 'mega' ),
				'icon'           => 'eicon-nav-menu eac-icon-elements',
				'name'           => 'eac-addon-mega-menu',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-navigation-menu-in-the-header-or-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/mega-menu.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Mega_Menu_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'site-logo'         => array(
				'active'         => false,
				'title'          => esc_html__( 'Logo du site', 'eac-components' ),
				'keywords'       => array( 'logo', 'site' ),
				'icon'           => 'eicon-site-logo eac-icon-elements',
				'name'           => 'eac-addon-site-logo',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-logo-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/site-logo.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Logo_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'site-tagline'      => array(
				'active'         => false,
				'title'          => esc_html__( 'Slogan du site', 'eac-components' ),
				'keywords'       => array( 'tagline', 'site' ),
				'icon'           => 'eicon-site-identity eac-icon-elements',
				'name'           => 'eac-addon-site-tagline',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-site-tagline-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/site-tagline.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Tagline_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'site-title'        => array(
				'active'         => false,
				'title'          => esc_html__( 'Titre du site', 'eac-components' ),
				'keywords'       => array( 'title', 'site' ),
				'icon'           => 'eicon-site-title eac-icon-elements',
				'name'           => 'eac-addon-site-title',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-site-title-in-the-header',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/site-title.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Site_Title_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
			'social-media'      => array(
				'active'         => false,
				'title'          => esc_html__( 'Réseaux sociaux', 'eac-components' ),
				'keywords'       => array( 'social', 'media' ),
				'icon'           => 'eicon-social-icons eac-icon-elements',
				'name'           => 'eac-addon-social-media',
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-widgets-with-header-footer-builder/#add-your-social-medias-in-the-header-or-footer',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_EHF_PATH . 'widgets/social-media.php',
				'name_space'     => EAC_EHF_WIDGETS_NAMESPACE . 'Social_Media_Widget',
				'badge'          => '',
				'category'       => array( 'eac-ehf' ),
			),
		);
	}

	/**
	 * get_features_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_active() {
		return self::$features_keys_active;
	}

	/**
	 * get_features_advanced_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_advanced_active() {
		return self::$features_advanced_keys_active;
	}

	/**
	 * get_features_common_active
	 *
	 * @return Array La liste des fonctionnalités et leur statut
	 */
	public static function get_features_common_active() {
		return self::$features_common_keys_active;
	}

	/**
	 * is_feature_active
	 *
	 * @param $element (String) la fonctionnalité à checker
	 * @return Bool Feature actif
	 */
	public static function is_feature_active( $element ) {
		$active = false;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			if ( ! self::are_feature_dependencies_enabled( $element ) ) {
				return $active;
			}

			// Le booléen du feature stocké dans la table des options
			$active = self::$elements_keys_active[ $element ];
		}

		return $active;
	}

	/**
	 * are_feature_dependencies_enabled
	 *
	 * @param $element (String) le composant à checker
	 * @return Bool les dépendances sont actives
	 */
	public static function are_feature_dependencies_enabled( $element ) {
		$active = true;

		// La clé est enregistrée dans la table des options
		if ( array_key_exists( $element, self::$elements_keys_active ) ) {

			// Check les class dépendantes
			if ( ! empty( self::$elements_list[ $element ]['class_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['class_depends'] as $class ) {
					if ( ! class_exists( $class ) ) {
						return false;
					}
				}
			}

			// Check les fonctions dépendantes
			if ( ! empty( self::$elements_list[ $element ]['func_depends'] ) ) {
				foreach ( self::$elements_list[ $element ]['func_depends'] as $func ) {
					if ( ! function_exists( $func ) ) {
						return false;
					}
				}
			}

			/** Teste si les version PRO Elementor et ACF existent (acf_register_block_type) */
			if ( ! empty( self::$elements_list[ $element ]['elems_exclude'] ) ) {
				foreach ( self::$elements_list[ $element ]['elems_exclude'] as $elem ) {
					if ( 'ELEMENTOR_PRO_VERSION' === $elem && defined( 'ELEMENTOR_PRO_VERSION' ) ) {
						return false;
					} elseif ( 'acf_register_block_type' === $elem && function_exists( 'acf_register_block_type' ) ) {
						return false;
					}
				}
			}
		}

		return $active;
	}

	/**
	 * get_feature_path
	 *
	 * @param $element (String) la fonctionnnalité à checker
	 * @return String Le chemin absolu du fichier PHP des fonctionnalités
	 */
	public static function get_feature_path( $element ) {
		$path = false;

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$full_path = self::$elements_list[ $element ]['file_path'];
			if ( ! empty( $full_path ) && file_exists( $full_path ) ) {
				return $full_path;
			}
		}
		return $path;
	}

	/**
	 * get_feature_title
	 *
	 * @param $element (String) le composant à checker
	 * @return String Le titre de la fonctionnalité traduite dans la locale
	 */
	public static function get_feature_title( $element ) {
		$title = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$title = sprintf( '%s', esc_html__( self::$elements_list[ $element ]['title'], 'eac-components' ) );
		}
		return $title;
	}

	/**
	 * get_feature_help_url
	 *
	 * @param $element (String) le composant à checker
	 * @return String L'URL de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url( $element ) {
		$help = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help = self::$elements_list[ $element ]['help_url'];
		}
		return esc_url( $help );
	}

	/**
	 * get_feature_help_url_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class de l'aide en ligne de la fonctionnalité
	 */
	public static function get_feature_help_url_class( $element ) {
		$help_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) ) {
			$help_class = self::$elements_list[ $element ]['help_url_class'];
		}
		return esc_html( $help_class );
	}

	/**
	 * get_feature_badge_class
	 *
	 * @param $element (String) le composant à checker
	 * @return String La class du badge de la fonctionnalité
	 */
	public static function get_feature_badge_class( $element ) {
		$badge_class = '';

		if ( array_key_exists( $element, self::$elements_keys_active ) && ! empty( self::$elements_list[ $element ]['badge'] ) ) {
			$badge_class = ' ' . self::$elements_list[ $element ]['badge'];
		}
		return esc_html( $badge_class );
	}

	/**
	 * set_features_list
	 *
	 * On peut ajouter/supprimer
	 * NE JAMAIS CHANGER LE NOM DES CLÉS
	 * Ne JAMAIS SUPPRIMER et AJOUTER UNE CLÉ DANS LA MÊME VERSION
	 */
	public function set_features_list() {

		self::$features_list = array(
			'all-features-advanced' => array(
				'active'         => true,
				'title'          => esc_html__( 'Tous les composants activés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-dynamic-tag'       => array(
				'active'         => true,
				'title'          => esc_html__( 'ACF balises dynamiques', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-integrate-and-use-acf-fields-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ACF_INCLUDES . 'dynamic-tags/eac-acf-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-image-gallery'     => array(
				'active'         => false,
				'title'          => esc_html__( 'ACF galerie d&#039;images', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups', 'acf_register_field_type' ),
				'elems_exclude'  => array( 'acf_register_block_type' ),
				'help_url'       => 'https://elementor-addon-components.com/add-and-publish-an-image-gallery-with-the-free-version-of-acf/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ACF_INCLUDES . 'gallery/class-eac-acf-field-gallery.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-json'              => array(
				'active'         => false,
				'title'          => 'ACF JSON',
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'elems_exclude'  => array( 'acf_register_block_type' ),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help acf-json',
				'file_path'      => EAC_ACF_INCLUDES . 'eac-acf-json.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'acf-option-page'       => array(
				'active'         => false,
				'title'          => esc_html__( 'ACF page d&#039;options', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'elems_exclude'  => array( 'acf_register_block_type' ),
				'help_url'       => 'https://elementor-addon-components.com/add-options-page-for-the-free-version-of-acf/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ACF_INCLUDES . 'eac-acf-options-page.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'alt-attribute'         => array(
				'active'         => false,
				'title'          => esc_html__( 'ALT attribut', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-external-image-for-elementor/#improve-your-seo-with-the-dynamic-tag-external-image',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-image.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-attribute'      => array(
				'active'         => true,
				'title'          => esc_html__( 'Attributs personnalisés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/add-your-custom-attributes-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'custom-attributes/eac-custom-attributes.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-css'            => array(
				'active'         => true,
				'title'          => esc_html__( 'CSS personnalisé', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/elementor-custom-css/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'custom-css/eac-custom-css.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'dynamic-tag'           => array(
				'active'         => true,
				'title'          => esc_html__( 'Balises dynamiques', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/elementor-dynamic-tags/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'dynamic-tags/eac-dynamic-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'dysplay-condition'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Conditions d&#039;affichage', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/how-to-set-display-conditions-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_CONDITION_PATH . 'module.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'element-link'          => array(
				'active'         => false,
				'title'          => esc_html__( 'Lien élément', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-link-to-a-section-column-using-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-links.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'lottie-background'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Lottie d&#039;arrière-plan', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/add-lottie-animation-in-elementor/#use-lottie-animations-in-the-background-of-an-element',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-lottie.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'kenburns-slideshow'    => array(
				'active'         => false,
				'title'          => esc_html__( 'Effet Ken Burns', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/animation-dimages/#ken-burns-slideshow-for-background-images',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-kenburns.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'motion-effects'        => array(
				'active'         => false,
				'title'          => esc_html__( 'Effets de mouvement', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/create-animation-effects-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-effect.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'background-images'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Multiples images de fond', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/add-multiple-background-images-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-background-images.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'element-sticky'        => array(
				'active'         => true,
				'title'          => esc_html__( 'Sticky élément', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://elementor-addon-components.com/use-sticky-scrolling-effect-with-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_ELEMENTOR_INCLUDES . 'injection/eac-injection-sticky.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'woo-dynamic-tag'       => array(
				'active'         => false,
				'title'          => esc_html__( 'WC balises dynamiques', 'eac-components' ),
				'class_depends'  => array( 'WooCommerce' ),
				'func_depends'   => array(),
				'elems_exclude'  => array(),
				'help_url'       => 'https://elementor-addon-components.com/dynamic-woocommerce-tags-for-elementor/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_WC_INCLUDES . 'dynamic-tags/eac-woo-tags.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'all-features-common'   => array(
				'active'         => false,
				'title'          => esc_html__( 'Tous les composants activés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '',
				'help_url_class' => '',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'custom-nav-menu'       => array(
				'active'         => false,
				'title'          => esc_html__( 'Personnaliser le menu de navigation', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/customize-wordpress-navigation-menus/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => EAC_PLUGIN_PATH . 'admin/settings/eac-nav-menu.php',
				'name_space'     => '',
				'badge'          => '',
			),
			'grant-option-page'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Accès page d&#039;options', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array( 'acf_get_field_groups' ),
				'elems_exclude'  => array( 'acf_register_block_type' ),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help grant-option-page',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'unfiltered-medias'     => array(
				'active'         => false,
				'title'          => esc_html__( 'Téléverser les fichiers non filtrés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help grant-medias-upload',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'extend-fields-medias'  => array(
				'active'         => false,
				'title'          => esc_html__( 'Médias champs personnalisés', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'help_url'       => 'https://elementor-addon-components.com/create-advanced-image-gallery-using-elementor/#activate-a-link-and-add-keywords-to-the-filter',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'preload-page'  => array(
				'active'         => false,
				'title'          => esc_html__( 'Préchargement des pages', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array( 'ELEMENTOR_PRO_VERSION' ),
				'help_url'       => 'https://instant.page/',
				'help_url_class' => 'eac-admin-help',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
			'widget-count'  => array(
				'active'         => false,
				'title'          => esc_html__( 'Utilisation des éléments', 'eac-components' ),
				'class_depends'  => array(),
				'func_depends'   => array(),
				'elems_exclude'  => array(),
				'help_url'       => '#',
				'help_url_class' => 'eac-admin-help grant-elements-usage',
				'file_path'      => '',
				'name_space'     => '',
				'badge'          => '',
			),
		);
	}

	/**
	 * get_manage_options_name
	 *
	 * @return le nom de la capacité 'eac_manage_options'
	 */
	public static function get_manage_options_name() {
		return self::$manage_options;
	}

	/**
	 * get_mega_nav_menu_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_mega_nav_menu_option_name() {
		return self::$options_mega_nav_menu;
	}

	/**
	 * get_mega_nav_menu_cache_name
	 *
	 * @return String L'option des menus mis en cache
	 */
	public static function get_mega_nav_menu_cache_name() {
		return self::$options_mega_nav_menu_cache;
	}

	/**
	 * get_woo_hooks_option_name
	 *
	 * @return String Le libellé de l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_name() {
		return self::$options_woo_hooks;
	}

	/**
	 * get_woo_hooks_option_args
	 *
	 * @return Array La structure l'option des hooks WooCommerce
	 */
	public static function get_woo_hooks_option_args() {
		return self::$woo_shop_args;
	}

	/**
	 * Test du type de thème FSE ou non
	 */
	public static function is_a_block_theme() {
		return 'true' === get_option( self::$options_block_theme ) ? true : false;
	}

	/** statistiques des éléments actifs ou non */
	public static function get_count_all_elements() {
		return absint( self::$count_all_elements );
	}

	public static function get_count_enabled_elements() {
		return absint( self::$count_enabled_elements );
	}

	public static function get_count_disabled_elements() {
		return absint( self::$count_disabled_elements );
	}

	/** L'option de sauvegarde de l'usage des éléments */
	public static function get_usage_count_option_name() {
		return self::$options_usage_count;
	}

} Eac_Config_Elements::instance();
