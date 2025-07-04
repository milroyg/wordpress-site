<?php
/**
 * Class: WC_Product_Grid_Widget
 * Title: Grille de produits
 * Slug: eac-addon-product-grid
 *
 * Description: Affiche les produits créés avec woocommerce
 * dans différents modes, masonry, grille ou slider avec différents filtres
 *
 * @since 1.9.8
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Core\Eac_Config_Elements;
use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Helpers_Util;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;
use Elementor\Utils;

class WC_Product_Grid_Widget extends Widget_Base {
	/** Les Traits */
	use \EACCustomWidgets\Includes\Widgets\Traits\Slider_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Badge_New_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Badge_Sale_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Badge_Stock_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Button_Add_To_Cart_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Button_Read_More_Trait;
	use \EACCustomWidgets\Includes\Widgets\Traits\Shared_Icon_Svg_Trait;

	/** Constructeur de la class WC_Product_Grid_Widget */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_script( 'swiper-bundle', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.4.1/swiper-bundle.min.js', array( 'jquery' ), '9.4.1', true );
		wp_register_script( 'isotope', EAC_Plugin::instance()->get_script_url( 'assets/js/isotope/isotope.pkgd' ), array( 'jquery' ), '3.0.6', true );
		wp_register_script( 'eac-fit-rows', EAC_Plugin::instance()->get_script_url( 'assets/js/isotope/fit-rows' ), array( 'isotope' ), '2.1.1', true );
		wp_register_script( 'eac-imagesloaded', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/4.1.4/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
		wp_register_script( 'eac-infinite-scroll', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-infinitescroll/3.0.5/infinite-scroll.pkgd.min.js', array( 'jquery' ), '3.0.5', true );

		/** C'est un thème basé sur les blocks */
		if ( Eac_Config_Elements::is_a_block_theme() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'force_load_script_for_fse' ), 9999 );
		} else {
			wp_register_script( 'eac-post-grid', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-post-grid' ), array( 'jquery', 'elementor-frontend', 'swiper-bundle', 'isotope', 'eac-fit-rows', 'eac-imagesloaded', 'eac-infinite-scroll' ), '1.9.8', true );
		}

		wp_register_style( 'swiper-bundle', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/9.4.1/swiper-bundle.min.css', array(), '9.4.1' );
		wp_register_style( 'eac-swiper', EAC_Plugin::instance()->get_style_url( 'assets/css/eac-swiper' ), array(), EAC_PLUGIN_VERSION ); // 1.9.7
		wp_register_style( 'eac-post-grid', EAC_Plugin::instance()->get_style_url( 'assets/css/post-grid' ), array( 'eac-swiper' ), EAC_PLUGIN_VERSION );
		wp_register_style( 'eac-product-grid', EAC_Plugin::instance()->get_style_url( 'assets/css/wc-product' ), array( 'eac-post-grid' ), '1.9.8' );

		// Supprime les callbacks du filtre de la liste 'orderby'
		remove_all_filters( 'eac/tools/post_orderby' );

		// Filtre la liste 'orderby'
		add_filter(
			'eac/tools/post_orderby',
			function( $exclude_orderby ) {
				unset( $exclude_orderby['comment_count'] );
				return $exclude_orderby;
			},
			10
		);
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'woo-product-grid';

	/**
	 * Retrieve widget name.
	 *
	 * @access public
	 *
	 * @return widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name( $this->slug );
	}

	/**
	 * Retrieve widget title.
	 *
	 * @access public
	 *
	 * @return widget title.
	 */
	public function get_title() {
		return Eac_Config_Elements::get_widget_title( $this->slug );
	}

	/**
	 * Retrieve widget icon.
	 *
	 * @access public
	 *
	 * @return widget icon.
	 */
	public function get_icon() {
		return Eac_Config_Elements::get_widget_icon( $this->slug );
	}

	/**
	 * Affecte le composant à la catégorie définie dans plugin.php
	 *
	 * @access public
	 *
	 * @return widget category.
	 */
	public function get_categories() {
		return Eac_Config_Elements::get_widget_categories( $this->slug );
	}

	/**
	 * Load dependent libraries
	 *
	 * @access public
	 *
	 * @return libraries list.
	 */
	public function get_script_depends(): array {
		if ( Eac_Config_Elements::is_a_block_theme() ) {
			return array( 'swiper-bundle', 'isotope', 'eac-fit-rows', 'eac-imagesloaded', 'eac-infinite-scroll' );
		} else {
			return array( 'swiper-bundle', 'isotope', 'eac-fit-rows', 'eac-imagesloaded', 'eac-infinite-scroll', 'eac-post-grid' );
		}
	}

	/**
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 *
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'swiper-bundle', 'eac-swiper', 'eac-post-grid', 'eac-product-grid' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords( $this->slug );
	}

	/**
	 * Get help widget get_custom_help_url.
	 *
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
		return Eac_Config_Elements::get_widget_help_url( $this->slug );
	}

	/**
	 * has_widget_inner_wrapper
	 *
	 * @return bool
	 */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls(): void {
		/**
		 * Récupère l'otion pour affecter la valeur par défaut du champ caché
		 * Double check de l'option pour non régression de la nouvelle structure
		 */
		$catalog = 'false';
		$options = get_option( Eac_Config_Elements::get_woo_hooks_option_name() );
		if ( $options && isset( $options['catalog']['active'] ) ) {
			$catalog = true === $options['catalog']['active'] ? 'true' : 'false';
		} elseif ( $options && isset( $options['catalog'] ) ) {
			$catalog = true === $options['catalog'] ? 'true' : 'false';
		}

		$start = is_rtl() ? 'right' : 'left';
		$end   = is_rtl() ? 'left' : 'right';
		// Récupère tous les breakpoints actifs
		$active_breakpoints     = Plugin::$instance->breakpoints->get_active_breakpoints();
		$has_active_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();

		$this->start_controls_section(
			'al_post_filter',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			/**
			 * Champ caché pour déterminer si la page est un catalogue
			 * La valeur n'est pas enregistrée dans la BDD
			 */
			$this->add_control(
				'shop_is_a_catalog',
				array(
					'label'        => 'Catalogue hidden',
					'type'         => Controls_Manager::HIDDEN,
					'default'      => $catalog,
					'save_default' => false,
				)
			);

			$this->add_control(
				'al_post_filter_heading',
				array(
					'label'     => esc_html__( 'Filtre de requête', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->start_controls_tabs( 'al_article_tabs' );

				$this->start_controls_tab(
					'al_article_post_tab',
					array(
						'label' => esc_html__( 'Produits', 'eac-components' ),
					)
				);

					$this->add_control(
						'al_article_type',
						array(
							'label'       => esc_html__( 'Type de produit', 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'label_block' => true,
							'options'     => Eac_Tools_Util::get_product_post_types(),
							'default'     => 'product',
						)
					);

					$this->add_control(
						'al_article_taxonomy',
						array(
							'label'       => esc_html__( 'Sélectionner la taxonomie', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'label_block' => true,
							'options'     => Eac_Tools_Util::get_product_taxonomies(),
							'default'     => array( 'product_cat' ),
							'multiple'    => true,
						)
					);

					$this->add_control(
						'al_article_term',
						array(
							'label'       => esc_html__( 'Sélectionner les étiquettes', 'eac-components' ),
							'type'        => Controls_Manager::SELECT2,
							'label_block' => true,
							'options'     => Eac_Tools_Util::get_product_terms(),
							'multiple'    => true,
						)
					);

					$this->add_control(
						'al_article_orderby',
						array(
							'label'     => esc_html__( 'Triés par', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => Eac_Tools_Util::get_post_orderby(),
							'default'   => 'title',
						)
					);

					$this->add_control(
						'al_article_order',
						array(
							'label'   => esc_html__( 'Affichage', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'options' => array(
								'asc'  => esc_html__( 'Ascendant', 'eac-components' ),
								'desc' => esc_html__( 'Descendant', 'eac-components' ),
							),
							'default' => 'asc',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_article_query_tab',
					array(
						'label' => esc_html__( 'Requêtes', 'eac-components' ),
					)
				);

					$this->add_control(
						'al_content_user',
						array(
							'label'       => esc_html__( 'Selection des auteurs', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'Article/Auteurs'", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'ai'          => array( 'active' => false ),
							'label_block' => true,
						)
					);

					$repeater = new Repeater();

					$repeater->add_control(
						'al_content_metadata_title',
						array(
							'label'   => esc_html__( 'Titre', 'eac-components' ),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => array( 'active' => true ),
							'ai'      => array( 'active' => false ),
						)
					);

					$repeater->add_control(
						'al_content_metadata_keys',
						array(
							'label'       => esc_html__( 'Sélectionner une clé', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'WooCommerce|Clés des champs' ou entrer la clé dans le champ (sensible à la casse).", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'ai'          => array( 'active' => false ),
							'label_block' => true,
						)
					);

					$repeater->add_control(
						'al_content_metadata_type',
						array(
							'label'       => esc_html__( 'Type des données', 'eac-components' ),
							'description' => esc_html__( "Utiliser le type 'TIMESTAMP' pour comparer des dates", 'eac-components' ),
							'type'        => Controls_Manager::SELECT,
							'options'     => array(
								'CHAR'          => esc_html__( 'Caractère', 'eac-components' ),
								'NUMERIC'       => esc_html__( 'Numérique', 'eac-components' ),
								'DECIMAL(10,2)' => esc_html__( 'Décimal', 'eac-components' ),
								'TIMESTAMP'     => esc_html__( 'TimeStamp', 'eac-components' ),
							),
							'default'     => 'CHAR',
						)
					);

					$repeater->add_control(
						'al_content_metadata_compare',
						array(
							'label'   => esc_html__( 'Opérateur de comparaison', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'options' => Eac_Tools_Util::get_operateurs_comparaison(),
							'default' => 'IN',
						)
					);

					$repeater->add_control(
						'al_content_metadata_values',
						array(
							'label'       => esc_html__( 'Sélection des valeurs', 'eac-components' ),
							'description' => esc_html__( "Balises dynamiques 'WooCommerce|Valeurs des champs' ou entrer les valeurs dans le champ (insensible à la casse) et utiliser le pipe '|' comme séparateur.", 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array(
								'active'     => true,
								'categories' => array(
									TagsModule::POST_META_CATEGORY,
								),
							),
							'ai'          => array( 'active' => false ),
							'label_block' => true,
						)
					);

					$this->add_control(
						'al_content_metadata_list',
						array(
							'label'       => esc_html__( 'Requêtes', 'eac-components' ),
							'type'        => Controls_Manager::REPEATER,
							'fields'      => $repeater->get_controls(),
							'default'     => array(
								array(
									'al_content_metadata_title' => esc_html__( 'Requête #1', 'eac-components' ),
								),
							),
							'title_field' => '{{{ al_content_metadata_title }}}',
							'button_text' => esc_html__( 'Ajouter une requête', 'eac-components' ),
						)
					);

					$this->add_control(
						'al_content_metadata_keys_relation',
						array(
							'label'        => esc_html__( 'Relation entre les requêtes', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => 'AND',
							'label_off'    => 'OR',
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_display_content_args',
						array(
							'label'        => esc_html__( 'Afficher la requête', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'al_post_settings_heading',
				array(
					'label'     => esc_html__( 'Produits', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_layout_type!' => 'slider' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_article_nombre',
				array(
					'label'       => esc_html__( 'Nombre de produits', 'eac-components' ),
					'description' => esc_html__( '-1 = Tous', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'default'     => 10,
					'condition'   => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_content_pagging_display',
				array(
					'label'        => esc_html__( 'Pagination AJAX', 'eac-components' ),
					'description'  => esc_html__( 'Sans rechargement de la page', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'al_article_nombre',
								'operator' => '!in',
								'value'    => array( -1, 0, '' ),
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
							array(
								'name'     => 'al_content_nav_display',
								'operator' => '!==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_content_nav_display',
				array(
					'label'        => esc_html__( 'Pagination', 'eac-components' ),
					'description'  => esc_html__( 'Avec rechargement de la page', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'and',
						'terms'    => array(
							array(
								'name'     => 'al_article_nombre',
								'operator' => '!in',
								'value'    => array( -1, 0, '' ),
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
							array(
								'name'     => 'al_content_pagging_display',
								'operator' => '!==',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_article_id',
				array(
					'label'        => esc_html__( 'Exclure IDs', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_article_exclude',
				array(
					'label'       => esc_html__( 'IDs', 'eac-components' ),
					'description' => esc_html__( 'Les ID séparés par une virgule sans espace', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'ai'          => array( 'active' => false ),
					'label_block' => true,
					'default'     => '',
					'condition'   => array(
						'al_article_id' => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_article_include',
				array(
					'label'        => esc_html__( 'Inclure les enfants', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'conditions'   => array(
						'relation' => 'and',
						'terms' => array(
							array(
								'name'     => 'al_article_type',
								'operator' => '!in',
								'value'    => array( 'post', 'product' ),
							),
							array(
								'name'     => 'al_layout_type',
								'operator' => '!==',
								'value'    => 'slider',
							),
						),
					),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_layout_settings',
			array(
				'label' => esc_html__( 'Disposition', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_layout_type',
				array(
					'label'   => esc_html__( 'Mode', 'eac-components' ),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => array(
						'masonry'     => esc_html__( 'Mosaïque', 'eac-components' ),
						'equalHeight' => esc_html__( 'Grille', 'eac-components' ),
						'slider'      => esc_html( 'Slider' ),
					),
				)
			);

			// Add default values for all active breakpoints.
			$columns_device_args = array();
		foreach ( $active_breakpoints as $breakpoint_name => $breakpoint_instance ) {
			if ( Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_LAPTOP === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '4' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET_EXTRA === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_TABLET === $breakpoint_name ) {
					$columns_device_args[ $breakpoint_name ] = array( 'default' => '3' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '2' );
			} elseif ( Breakpoints_manager::BREAKPOINT_KEY_MOBILE === $breakpoint_name ) {
				$columns_device_args[ $breakpoint_name ] = array( 'default' => '1' );
			}
		}

			$this->add_responsive_control(
				'al_columns',
				array(
					'label'        => esc_html__( 'Nombre de colonnes', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => '3',
					'device_args'  => $columns_device_args,
					'options'      => array(
						'1' => '1',
						'2' => '2',
						'3' => '3',
						'4' => '4',
						'5' => '5',
						'6' => '6',
					),
					'prefix_class' => 'responsive%s-',
					'render_type'  => 'template',
					'condition'    => array( 'al_layout_type!' => 'slider' ),
				)
			);

			$this->add_control(
				'al_layout_image',
				array(
					'label'     => esc_html__( "Disposition de l'image", 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Image_Size::get_type(),
				array(
					'name'    => 'al_image_dimension',
					'label'   => esc_html__( 'Dimension', 'eac-components' ),
					'default' => 'medium',
					'exclude' => array( 'custom' ),
				)
			);

			$this->add_control(
				'al_enable_image_lazy',
				array(
					'label'     => esc_html( 'Lazy load' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'   => '',
					'toggle'    => false,
				)
			);

			$this->add_control(
				'al_enable_image_ratio',
				array(
					'label'        => esc_html__( 'Activer le ratio image', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'yes' => array(
							'title' => esc_html__( 'Oui', 'eac-components' ),
							'icon'  => 'eicon-check',
						),
						'no'  => array(
							'title' => esc_html__( 'Non', 'eac-components' ),
							'icon'  => 'eicon-ban',
						),
					),
					'default'      => 'yes',
					'toggle'       => false,
					'render_type'  => 'template',
					'prefix_class' => 'al-post__ratio-',
					'condition' => array( 'al_layout_type' => array( 'equalHeight', 'fitRows' ) ),
				)
			);

			$this->add_responsive_control(
				'al_image_ratio',
				array(
					'label'       => esc_html__( 'Ratio', 'eac-components' ),
					'type'           => Controls_Manager::SELECT,
					'default'        => '1 / 1',
					'tablet_default' => '1 / 1',
					'mobile_default' => '9 / 16',
					'options'        => array(
						'1 / 1'  => esc_html__( 'Défaut', 'eac-components' ),
						'9 / 16' => esc_html( '9-16' ),
						'4 / 3'  => esc_html( '4-3' ),
						'3 / 2'  => esc_html( '3-2' ),
						'16 / 9' => esc_html( '16-9' ),
						'21 / 9' => esc_html( '21-9' ),
					),
					'selectors'   => array( '{{WRAPPER}} .al-posts__wrapper .al-post__image-loaded' => 'aspect-ratio:{{SIZE}};' ),
					'condition'   => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => array( 'equalHeight', 'fitRows' ),
					),
					'render_type' => 'template',
				)
			);

			$this->add_responsive_control(
				'al_image_ratio_position_y',
				array(
					'label'      => esc_html__( 'Position verticale', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'size' => 50,
						'unit' => '%',
					),
					'range'      => array(
						'%' => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 5,
						),
					),
					'selectors'  => array( '{{WRAPPER}} .al-posts__wrapper .al-post__image-loaded' => 'object-position: 50% {{SIZE}}%;' ),
					'condition'  => array(
						'al_enable_image_ratio' => 'yes',
						'al_layout_type'        => array( 'equalHeight', 'fitRows' ),
					),
				)
			);

			$this->add_control(
				'al_layout_content',
				array(
					'label'     => esc_html__( 'Disposition du contenu', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_layout_side_by_side',
				array(
					'label'     => esc_html__( 'Côte à côte', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'al_layout_texte',
				array(
					'label'        => esc_html__( 'Droite', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Image à gauche Contenu à droite', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__right-',
					'condition'    => array( 'al_layout_texte_left!' => 'yes' ),
				)
			);

			$this->add_control(
				'al_layout_texte_left',
				array(
					'label'        => esc_html__( 'Gauche', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => esc_html__( 'Contenu à gauche Image à droite', 'eac-components' ),
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
					'render_type'  => 'template',
					'prefix_class' => 'layout-text__left-',
					'condition'    => array( 'al_layout_texte!' => 'yes' ),
				)
			);

			$this->add_responsive_control(
				'al_image_width',
				array(
					'label'      => esc_html__( "Largeur de l'image (%)", 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( '%' ),
					'default'    => array(
						'unit' => '%',
						'size' => 100,
					),
					'range'      => array(
						'%' => array(
							'min'  => 20,
							'max'  => 100,
							'step' => 10,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}}.layout-text__right-yes .al-post__content-wrapper .al-post__image-wrapper,
						{{WRAPPER}}.layout-text__left-yes .al-post__content-wrapper .al-post__image-wrapper' => 'inline-size: {{SIZE}}%;',
					),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'name'     => 'al_layout_texte',
								'operator' => '===',
								'value'    => 'yes',
							),
							array(
								'name'     => 'al_layout_texte_left',
								'operator' => '===',
								'value'    => 'yes',
							),
						),
					),
				)
			);

			$this->add_control(
				'al_layout_content_alignment',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_responsive_control(
				'al_content_text_align_v',
				array(
					'label'       => esc_html__( 'Vertical', 'eac-components' ),
					'type'        => Controls_Manager::CHOOSE,
					'options'     => array(
						'flex-start'    => array(
							'title' => esc_html__( 'Haut', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-start-v',
						),
						'center'        => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-center-v',
						),
						'flex-end'      => array(
							'title' => esc_html__( 'Bas', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-end-v',
						),
						'space-between' => array(
							'title' => esc_html__( 'Espace entre', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-between-v',
						),
						'space-around'  => array(
							'title' => esc_html__( 'Espace autour', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-around-v',
						),
						'space-evenly'  => array(
							'title' => esc_html__( 'Espace uniforme', 'eac-components' ),
							'icon'  => 'eicon-flex eicon-justify-space-evenly-v',
						),
					),
					'default'     => 'flex-start',
					'label_block' => true,
					'selectors'   => array(
						'{{WRAPPER}} .al-post__text-wrapper' => 'justify-content: {{VALUE}};',
					),
					'condition'   => array( 'al_layout_type' => [ 'equalHeight', 'fitRows', 'slider' ] ),
				)
			);

			$this->add_control(
				'al_content_text_align_h',
				array(
					'label'     => esc_html__( 'Horizontal', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'flex-start' => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center'     => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'flex-end'   => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'center',
					'selectors_dictionary' => array(
						'flex-start' => 'start',
						'center'     => 'center',
						'flex-end'   => 'end',
					),
					'selectors' => array(
						'{{WRAPPER}} .al-post__text-wrapper' => 'align-items: {{VALUE}};',
						'{{WRAPPER}} .buttons-wrapper' => 'justify-content: {{VALUE}};',
						'{{WRAPPER}} .shop-product__excerpt-wrapper' => 'text-align: {{VALUE}};',
					),
				)
			);

		$this->end_controls_section();

		/** Les controls du slider Trait */
		$this->start_controls_section(
			'al_slider_settings',
			array(
				'label'     => 'Slider',
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'al_layout_type' => 'slider' ),
			)
		);

			$this->register_slider_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_product_content',
			array(
				'label' => esc_html__( 'Contenu', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'al_filter_heading',
				array(
					'label'     => esc_html__( 'Filtres', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'al_layout_type!' => 'slider',
						'al_content_nav_display!' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_filter',
				array(
					'label'        => esc_html__( 'Filtres', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition' => array(
						'al_layout_type!' => 'slider',
						'al_content_nav_display!' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_filter_align',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'   => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'  => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'default'   => 'left',
					'selectors_dictionary' => array(
						'left'  => 'start',
						'right' => 'end',
					),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper, {{WRAPPER}} .al-filters__wrapper-select' => 'text-align: {{VALUE}};',
					),
					'condition' => array(
						'al_layout_type!'         => 'slider',
						'al_content_nav_display!' => 'yes',
						'al_filter'               => 'yes',
					),
				)
			);

			$this->start_controls_tabs(
				'al_content_settings',
				array(
					'separator' => 'before',
				)
			);

				$this->start_controls_tab(
					'al_content_product',
					array(
						'label' => '<span class="eicon eicon-products-archive" title="Product"></span>',
					)
				);

					$this->add_control(
						'al_product_heading',
						array(
							'label' => esc_html__( 'Produit', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_title',
						array(
							'label'        => esc_html__( 'Titre', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_title_tag',
						array(
							'label'   => esc_html__( 'Étiquette du titre', 'eac-components' ),
							'type'    => Controls_Manager::SELECT,
							'default' => 'h2',
							'options' => array(
								'h1'   => 'H1',
								'h2'   => 'H2',
								'h3'   => 'H3',
								'h4'   => 'H4',
								'h5'   => 'H5',
								'h6'   => 'H6',
								'div'  => 'div',
								'p'    => 'p',
							),
							'condition' => array( 'al_title' => 'yes' ),
						)
					);

					$this->add_control(
						'al_excerpt',
						array(
							'label'        => esc_html__( 'Description', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_excerpt_length',
						array(
							'label'   => esc_html__( 'Nombre de mots', 'eac-components' ),
							'type'    => Controls_Manager::NUMBER,
							'min'     => 3,
							'max'     => 100,
							'step'    => 5,
							'default' => 25,
							'condition' => array( 'al_excerpt' => 'yes' ),
						)
					);

					$this->add_control(
						'al_reviews',
						array(
							'label'        => esc_html__( 'Avis', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_reviews_format',
						array(
							'label'     => esc_html__( 'Format avis', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => array(
								'average_rating'    => esc_html__( 'Moyenne des notes', 'eac-components' ),  // Average rating
								'average_html'      => esc_html__( 'Moyenne HTML', 'eac-components' ),
								'average_html_long' => esc_html__( 'Moyenne HTML + Avis', 'eac-components' ),
								'rating_count'      => esc_html__( 'Nombre de notes', 'eac-components' ),      // Rating count
								'review_count'      => esc_html__( "Nombre d'avis", 'eac-components' ),        // Review count
							),
							'default'   => 'average_rating',
							'condition' => array( 'al_reviews' => 'yes' ),
						)
					);

					$this->add_control(
						'al_prices',
						array(
							'label'        => esc_html__( 'Prix', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'al_prices_format',
						array(
							'label'     => esc_html__( 'Format prix', 'eac-components' ),
							'type'      => Controls_Manager::SELECT,
							'options'   => array(
								'regular' => esc_html__( 'Régulier', 'eac-components' ),
								'promo'   => esc_html__( 'Promotion', 'eac-components' ),
								'both'    => esc_html__( 'Les deux', 'eac-components' ),
								'dateto'  => esc_html__( 'Date de fin de promo', 'eac-components' ),
							),
							'default'   => 'regular',
							'condition' => array(
								'al_prices' => 'yes',
							),
						)
					);

					$this->add_control(
						'al_stock',
						array(
							'label'        => esc_html__( 'Stock', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

					$this->add_control(
						'al_stock_format',
						array(
							'label'     => esc_html__( 'Format stock', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'yes' => array(
									'title' => esc_html__( 'Afficher', 'eac-components' ),
									'icon'  => 'eicon-check',
								),
								'no'  => array(
									'title' => esc_html__( 'Cacher', 'eac-components' ),
									'icon'  => 'eicon-ban',
								),
							),
							'default'   => 'yes',
							'toggle'    => false,
							'condition' => array(
								'al_stock'          => 'yes',
								'shop_is_a_catalog' => 'false',
							),
						)
					);

					$this->add_control(
						'al_quantity_sold',
						array(
							'label'        => esc_html__( 'Quantité vendue', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

					$this->add_control(
						'al_quantity_sold_fallback',
						array(
							'label'       => esc_html__( 'Texte alternatif', 'eac-components' ),
							'type'        => Controls_Manager::TEXT,
							'dynamic'     => array( 'active' => true ),
							'ai'          => array( 'active' => false ),
							'description' => esc_html__( 'Si la quantité est égale à zero', 'eac-components' ),
							'placeholder' => esc_html__( 'Soyez le premier à acheter ce produit', 'eac-components' ),
							'label_block' => true,
							'condition'   => array(
								'al_quantity_sold'  => 'yes',
								'shop_is_a_catalog' => 'false',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_metas',
					array(
						'label' => '<span class="eicon eicon-product-meta" title="Metas"></span>',
					)
				);

					$this->add_control(
						'al_terms_heading',
						array(
							'label' => esc_html__( 'Metas', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_term',
						array(
							'label'        => esc_html__( 'Étiquettes', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_author',
						array(
							'label'        => esc_html__( 'Auteur', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_avatar',
						array(
							'label'        => esc_html__( 'Avatar auteur', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

					$this->add_control(
						'al_date',
						array(
							'label'        => esc_html__( 'Date', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_badges',
					array(
						'label'     => '<span class="eicon eicon-product-info" data-tooltip="Badges" title="Badges"></span>',
						'condition' => array( 'shop_is_a_catalog' => 'false' ),
					)
				);

					$this->add_control(
						'al_badges_heading',
						array(
							'label' => esc_html__( 'Badges', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'promo_activate',
						array(
							'label'        => esc_html__( 'Badge promotion', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-sale-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

					$this->add_control(
						'stock_activate',
						array(
							'label'        => esc_html__( 'Badge stock', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-stock-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

					$this->add_control(
						'new_activate',
						array(
							'label'        => esc_html__( 'Badge nouveau produit', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-new-',
							'render_type'  => 'template',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

					$this->add_control(
						'cart_quantity_activate',
						array(
							'label'        => esc_html__( 'Badge quantité dans le panier', 'eac-components' ),
							'description'  => esc_html__( "Ajoute au bouton 'Ajouter au panier' un badge indiquant la quantité du produit dans le panier", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'prefix_class' => 'badge-cart-quantity-',
							'render_type'  => 'template',
							'condition'    => array(
								'button_cart'       => 'yes',
								'shop_is_a_catalog' => 'false',
							),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_buttons',
					array(
						'label' => '<span class="eicon eicon-button" title="Buttons"></span>',
					)
				);

					$this->add_control(
						'al_buttons_heading',
						array(
							'label' => esc_html__( 'Boutons', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'button_more',
						array(
							'label'        => esc_html__( 'En savoir plus', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
						)
					);

					$this->add_control(
						'button_cart',
						array(
							'label'        => esc_html__( 'Ajouter au panier', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => 'yes',
							'condition'    => array( 'shop_is_a_catalog' => 'false' ),
						)
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'al_content_links',
					array(
						'label' => '<span class="eicon eicon-link tooltip-target" data-tooltip="Links" title="Links"></span>',
					)
				);

					$this->add_control(
						'al_links_heading',
						array(
							'label' => esc_html__( 'Liens', 'eac-components' ),
							'type'  => Controls_Manager::HEADING,
						)
					);

					$this->add_control(
						'al_title_link',
						array(
							'label'        => esc_html__( 'Lien de la page produit sur le titre', 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_title' => 'yes' ),
						)
					);

					$this->add_control(
						'al_image_link',
						array(
							'label'        => esc_html__( "Lien de la page produit sur l'image", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_lightbox!' => 'yes' ),
						)
					);

					$this->add_control(
						'al_article_link',
						array(
							'label'        => esc_html__( 'Appliquer le lien globalement', 'eac-components' ),
							'description'  => esc_html__( 'Le lien enveloppe chaque item', 'eac-components' ),
							'type'      => Controls_Manager::CHOOSE,
							'options'   => array(
								'yes' => array(
									'title' => esc_html__( 'Oui', 'eac-components' ),
									'icon'  => 'eicon-check',
								),
								'no'  => array(
									'title' => esc_html__( 'Non', 'eac-components' ),
									'icon'  => 'eicon-ban',
								),
							),
							'default'   => 'no',
							'toggle'    => false,
							'conditions'   => array(
								'relation' => 'or',
								'terms'    => array(
									array(
										'name'     => 'button_more',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_title_link',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_image_link',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						)
					);

					$this->add_control(
						'al_lightbox',
						array(
							'label'        => esc_html__( "Visionneuse sur l'image", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array(
								'al_image_link!'  => 'yes',
								'al_article_link!' => 'yes',
							),
						)
					);

					$this->add_control(
						'al_avatar_link',
						array(
							'label'        => esc_html__( "Lien sur l'avatar", 'eac-components' ),
							'description'  => esc_html__( "Archives de l'auteur", 'eac-components' ),
							'type'         => Controls_Manager::SWITCHER,
							'label_on'     => esc_html__( 'oui', 'eac-components' ),
							'label_off'    => esc_html__( 'non', 'eac-components' ),
							'return_value' => 'yes',
							'default'      => '',
							'condition'    => array( 'al_avatar' => 'yes' ),
						)
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_more_settings',
			array(
				'label'     => esc_html__( "Bouton 'En savoir plus'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array( 'button_more' => 'yes' ),
			)
		);

			// Trait du contenu du bouton read more
			$this->register_button_more_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_cart_settings',
			array(
				'label'     => esc_html__( "Bouton 'Ajouter au panier'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'button_cart'       => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			// Le trait du bouton 'Add to cart'
			$this->register_button_cart_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_promo_settings',
			array(
				'label'     => esc_html__( 'Badge promotion', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'promo_activate'    => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			/** Trait badge Promotion */
			$this->register_sale_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_stock_settings',
			array(
				'label'     => esc_html__( 'Badge stock', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'stock_activate'    => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			/** Trait badge Stock */
			$this->register_stock_content_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_cart_quantity_settings',
			array(
				'label'     => esc_html__( 'Badge quantité du panier', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'cart_quantity_activate' => 'yes',
					'button_cart'            => 'yes',
					'shop_is_a_catalog'      => 'false',
				),
			)
		);

			$order_start = is_rtl() ? 'start' : 'end';
			$order_end   = is_rtl() ? 'end' : 'start';
			$this->add_control(
				'cart_quantity_position',
				array(
					'label'        => esc_html__( 'Position', 'eac-components' ),
					'type'         => Controls_Manager::CHOOSE,
					'options'      => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-order-{$order_start}",
						),
						'right' => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-order-{$order_end}",
						),
					),
					'default'      => 'left',
					'toggle'       => false,
					'prefix_class' => 'badge-cart-quantity-pos-',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_new_settings',
			array(
				'label'     => esc_html__( 'Badge nouveau produit', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => array(
					'new_activate'      => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			/** Trait badge nouveau produit */
			$this->register_new_content_controls();

		$this->end_controls_section();

		/**
		 * Generale Style Section
		 */
		$this->start_controls_section(
			'al_general_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			/** Conteneur */
			$this->add_control(
				'al_container_style',
				array(
					'label'     => esc_html__( 'Conteneur', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
				)
			);

			$this->add_control(
				'al_wrapper_bg_color',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .swiper .swiper-slide, {{WRAPPER}} .al-posts__wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			/** Produit */
			$this->add_control(
				'al_items_style',
				array(
					'label'     => esc_html__( 'Produit', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_wrapper_style',
				array(
					'label'        => esc_html__( 'Style', 'eac-components' ),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'style-1',
					'options'      => array(
						'style-0'  => esc_html__( 'Défaut', 'eac-components' ),
						'style-1'  => 'Style 1',
						'style-2'  => 'Style 2',
						'style-3'  => 'Style 3',
						'style-4'  => 'Style 4',
						'style-5'  => 'Style 5',
						'style-8'  => 'Style 6',
						'style-10' => 'Style 7',
						'style-11' => 'Style 8',
						'style-12' => 'Style 9',
					),
					'prefix_class' => 'al-post__wrapper-',
				)
			);

			$this->add_responsive_control(
				'al_wrapper_margin',
				array(
					'label'      => esc_html__( 'Marge entre les items', 'eac-components' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'default'    => array(
						'size' => 6,
						'unit' => 'px',
					),
					'range'      => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 1,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__inner-wrapper' => 'block-size: calc(100% - {{SIZE}}{{UNIT}}); margin-block: calc({{SIZE}}{{UNIT}} / 2); margin-inline: calc({{SIZE}}{{UNIT}} / 2);',
					),
				)
			);

			$this->add_control(
				'al_items_bgcolor',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__inner-wrapper' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_wrapper_border',
					'selector'  => '{{WRAPPER}} .al-post__inner-wrapper',
					'condition' => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			$this->add_control(
				'al_wrapper_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__inner-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'          => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_wrapper_shadow',
					'label'     => esc_html__( 'Ombre', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__inner-wrapper',
					'condition' => array( 'al_wrapper_style' => 'style-0' ),
				)
			);

			/** Filtre */
			$this->add_control(
				'al_filter_style',
				array(
					'label'     => esc_html__( 'Filtre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_filter_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_filter_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_background',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'background-color: {{VALUE}};' ),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_outline',
				array(
					'label'     => esc_html__( 'Couleur du filtre sélectionné', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item.al-active:after' => 'border-bottom: 3px solid {{VALUE}};',
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item.al-active a' => 'color: {{VALUE}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_responsive_control(
				'al_filter_padding',
				array(
					'label'     => esc_html__( 'Marges internes', 'eac-components' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'al_filter_border',
					'selector' => '{{WRAPPER}} .al-filters__wrapper .al-filters__item a',
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			$this->add_control(
				'al_filter_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .al-filters__wrapper .al-filters__item a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition' => array(
						'al_filter'       => 'yes',
						'al_layout_type!' => 'slider',
					),
				)
			);

			/** Image */
			$this->add_control(
				'al_image_style',
				array(
					'label'     => esc_html__( 'Image', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'     => 'al_image_border',
					'selector' => '{{WRAPPER}} .al-post__image-wrapper img',
				)
			);

			$this->add_control(
				'al_image_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'default'            => array(
						'top'      => 0,
						'right'    => 0,
						'bottom'   => 0,
						'left'     => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
				)
			);

			/** Titre */
			$this->add_control(
				'al_title_style',
				array(
					'label'     => esc_html__( 'Titre', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_title' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_title_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__content-title, {{WRAPPER}} .al-post__content-title a' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_title' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'al_title_typo',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'global'   => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector' => '{{WRAPPER}} .al-post__content-title',
					'condition' => array( 'al_title' => 'yes' ),
				)
			);

			/** Description excerpt */
			$this->add_control(
				'al_excerpt_style',
				array(
					'label'     => esc_html__( 'Description', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_excerpt' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_excerpt_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_TEXT ),
					'selectors' => array( '{{WRAPPER}} .shop-product__excerpt-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_excerpt' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_excerpt_typo',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_TEXT ),
					'selector'  => '{{WRAPPER}} .shop-product__excerpt-wrapper',
					'condition' => array( 'al_excerpt' => 'yes' ),
				)
			);

			/** Avis */
			$this->add_control(
				'al_reviews_style',
				array(
					'label'     => esc_html__( 'Avis', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_reviews' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_reviews_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array(
						'{{WRAPPER}} .shop-product__notes-wrapper,
						{{WRAPPER}} .al_post_customer-review,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating:before,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating span:before' => 'color: {{VALUE}};',
					),
					'condition' => array( 'al_reviews' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_reviews_typo',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__notes-wrapper,
						{{WRAPPER}} .al_post_customer-review,
						{{WRAPPER}} .woocommerce.shop-product__notes-wrapper .star-rating',
					'condition' => array( 'al_reviews' => 'yes' ),
				)
			);

			/** Prix */
			$this->add_control(
				'al_prices_style',
				array(
					'label'     => esc_html__( 'Prix', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			$this->add_control(
				'al_prices_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__prices-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_prices_typo',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__prices-wrapper',
					'condition' => array(
						'al_prices' => 'yes',
					),
				)
			);

			/** Stock */
			$this->add_control(
				'al_stock_style',
				array(
					'label'     => esc_html__( 'Stock', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			$this->add_control(
				'al_stock_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__stock-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_stock_typo',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__stock-wrapper',
					'condition' => array(
						'al_stock'          => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			/** Quantité vendue */
			$this->add_control(
				'al_sold_style',
				array(
					'label'     => esc_html__( 'Vendu', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			$this->add_control(
				'al_sold_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'selectors' => array( '{{WRAPPER}} .shop-product__sold-wrapper' => 'color: {{VALUE}};' ),
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_sold_typo',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'selector'  => '{{WRAPPER}} .shop-product__sold-wrapper',
					'condition' => array(
						'al_quantity_sold'  => 'yes',
						'shop_is_a_catalog' => 'false',
					),
				)
			);

			/** Style de l'avatar */
			$this->add_control(
				'al_avatar_style',
				array(
					'label'     => esc_html__( 'Avatar', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_avatar' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_avatar_size',
				array(
					'label'       => esc_html__( 'Dimension', 'eac-components' ),
					'type'        => Controls_Manager::NUMBER,
					'min'         => 40,
					'max'         => 150,
					'default'     => 60,
					'step'        => 10,
					'condition' => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'           => 'al_avatar_image_border',
					'selector'       => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition'      => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_control(
				'al_avatar_border_radius',
				array(
					'label'              => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'size_units'         => array( 'px', '%' ),
					'allowed_dimensions' => array( 'top', 'right', 'bottom', 'left' ),
					'selectors'          => array(
						'{{WRAPPER}} .al-post__avatar-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'    => array( 'al_avatar' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'      => 'al_avatar_box_shadow',
					'label'     => esc_html__( 'Ombre', 'eac-components' ),
					'selector'  => '{{WRAPPER}} .al-post__avatar-wrapper',
					'condition' => array( 'al_avatar' => 'yes' ),
				)
			);

			/** Balises meta */
			$this->add_control(
				'al_metas_style',
				array(
					'label'      => esc_html__( 'Balises meta', 'eac-components' ),
					'type'       => Controls_Manager::HEADING,
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'separator'  => 'before',
				)
			);

			$this->add_control(
				'al_metas_color',
				array(
					'label'      => esc_html__( 'Couleur', 'eac-components' ),
					'type'       => Controls_Manager::COLOR,
					'global'     => array( 'default' => Global_Colors::COLOR_SECONDARY ),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'       => 'al_metas_typo',
					'label'      => esc_html__( 'Typographie', 'eac-components' ),
					'global'     => array( 'default' => Global_Typography::TYPOGRAPHY_SECONDARY ),
					'conditions' => array(
						'relation' => 'or',
						'terms'    => array(
							array(
								'terms' => array(
									array(
										'name'     => 'al_author',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_date',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
							array(
								'terms' => array(
									array(
										'name'     => 'al_layout_type',
										'operator' => '!==',
										'value'    => 'slider',
									),
									array(
										'name'     => 'al_filter',
										'operator' => '===',
										'value'    => 'yes',
									),
									array(
										'name'     => 'al_term',
										'operator' => '===',
										'value'    => 'yes',
									),
								),
							),
						),
					),
					'selector'   => '{{WRAPPER}} .al-post__meta-tags,
						{{WRAPPER}} .al-post__meta-author,
						{{WRAPPER}} .al-post__meta-date',
				)
			);

			$this->add_control(
				'al_navigation_style',
				array(
					'label'     => esc_html__( 'Navigation', 'eac-components' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => array( 'al_content_nav_display' => 'yes' ),
					'separator' => 'before',
				)
			);

			$this->add_control(
				'al_navigation_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__navigation .page-numbers:not(.current)' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_control(
				'al_navigation_color_current',
				array(
					'label'     => esc_html__( 'Couleur nombre page courante', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => array( 'default' => Global_Colors::COLOR_PRIMARY ),
					'selectors' => array( '{{WRAPPER}} .al-post__navigation .page-numbers.current' => 'color: {{VALUE}};' ),
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'      => 'al_navigation_typography',
					'label'     => esc_html__( 'Typographie', 'eac-components' ),
					'global'    => array( 'default' => Global_Typography::TYPOGRAPHY_PRIMARY ),
					'selector'  => '{{WRAPPER}} .al-post__navigation',
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'al_navigation_border',
					'selector'  => '{{WRAPPER}} .al-post__navigation .page-numbers:not(.dots):not(.next):not(.prev):not(.current)',
					'condition' => array( 'al_content_nav_display' => 'yes' ),
				)
			);

			$this->add_control(
				'al_navigation_radius',
				array(
					'label'      => esc_html__( 'Rayon de la bordure', 'eac-components' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .al-post__navigation .page-numbers:not(.dots):not(.next):not(.prev):not(.current)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					),
					'condition'  => array( 'al_content_nav_display' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'al_slider_section_style',
			array(
				'label'      => esc_html__( 'Contrôles du slider', 'eac-components' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => array(
					'relation' => 'or',
					'terms'    => array(
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_navigation',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
						array(
							'terms' => array(
								array(
									'name'     => 'al_layout_type',
									'operator' => '===',
									'value'    => 'slider',
								),
								array(
									'name'     => 'slider_pagination',
									'operator' => '===',
									'value'    => 'yes',
								),
							),
						),
					),
				),
			)
		);

			/** Slider styles du trait */
			$this->register_slider_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_more_style',
			array(
				'label'     => esc_html__( "Bouton 'En savoir plus'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'button_more' => 'yes' ),
			)
		);

			// Trait Style du bouton read more
			$this->register_button_more_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_cart_style',
			array(
				'label'     => esc_html__( "Bouton 'Ajouter au panier'", 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'button_cart'       => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			// Trait des styles du bouton 'Add to cart'
			$this->register_button_cart_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_promo_style',
			array(
				'label'     => esc_html__( 'Badge promotion', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'promo_activate'    => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			// Trait des styles du badge 'promotion'
			$this->register_sale_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_stock_style',
			array(
				'label'     => esc_html__( 'Badge stock', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'stock_activate'    => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			// Trait des styles du badge 'stock'
			$this->register_stock_style_controls();

		$this->end_controls_section();

		$this->start_controls_section(
			'al_badge_new_style',
			array(
				'label'     => esc_html__( 'Badge nouveau produit', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'new_activate'      => 'yes',
					'shop_is_a_catalog' => 'false',
				),
			)
		);

			// Trait des styles du badge nouveau produit
			$this->register_new_style_controls();

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$id             = 'slider_post_grid_' . $this->get_id();
		$has_swiper     = 'slider' === $settings['al_layout_type'] ? true : false;
		$has_navigation = $has_swiper && 'yes' === $settings['slider_navigation'] ? true : false;
		$has_pagination = $has_swiper && 'yes' === $settings['slider_pagination'] ? true : false;
		$has_scrollbar  = $has_swiper && 'yes' === $settings['slider_scrollbar'] ? true : false;

		if ( $has_swiper ) { ?>
		<div id="<?php echo esc_attr( $id ); ?>" class='eac-post-grid swiper'>
		<?php } else { ?>
		<div class='eac-post-grid'>
		<?php }
		/** Ajout d'un message avant la grille si mode catalog */
		if ( 'true' === $settings['shop_is_a_catalog'] ) {
			$message = '';
			$message = apply_filters( 'eac_woo_catalog_product_message', $message );
			if ( ! empty( $message ) ) { ?>
				<div class='woocommerce-info'><?php echo esc_html( $message ); ?></div>
			<?php }
		}
		$this->render_articles();
		if ( $has_navigation ) { ?>
			<div class='swiper-button-prev'></div>
			<div class='swiper-button-next'></div>
		<?php } ?>
		<?php if ( $has_scrollbar ) { ?>
			<div class='swiper-scrollbar'></div>
		<?php } ?>
		<?php if ( $has_pagination ) { ?>
			<div class='swiper-pagination-bullet'></div>
		<?php } ?>
		<div class='eac-skip-grid' tabindex='0'>
			<span class='visually-hidden'><?php esc_html_e( 'Sortir de la grille', 'eac-components' ); ?></span>
		</div>
		</div>
		<?php
	}

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_articles() {
		$settings   = $this->get_settings_for_display();
		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;
		// Affichage du contenu du produit
		$has_avatar         = 'yes' === $settings['al_avatar'] ? true : false;
		$has_avatar_link    = $has_avatar && 'yes' === $settings['al_avatar_link'] ? true : false;
		$avatar_size        = ! empty( $settings['al_avatar_size'] ) ? $settings['al_avatar_size'] : 60;
		$has_image_lightbox = ! $has_swiper && 'yes' === $settings['al_lightbox'] ? true : false;
		$has_image_link     = ! $has_image_lightbox && 'yes' === $settings['al_image_link'] ? true : false;
		$lazy_load          = 'yes' === $settings['al_enable_image_lazy'] ? 'lazy' : 'eager';
		$has_term           = 'yes' === $settings['al_term'] ? true : false;
		$has_auteur         = 'yes' === $settings['al_author'] ? true : false;
		$has_date           = 'yes' === $settings['al_date'] ? true : false;
		$has_title          = 'yes' === $settings['al_title'] ? true : false;
		$has_title_link     = $has_title && 'yes' === $settings['al_title_link'] ? true : false;
		$has_global_link    = isset( $settings['al_article_link'] ) && 'yes' === $settings['al_article_link'] ? true : false;
		$max_num_pages      = 0;

		$has_resum  = 'yes' === $settings['al_excerpt'] ? true : false;
		$has_stock_badge_initial    = 'yes' === $settings['stock_activate'] ? true : false;
		$has_stock_initial          = 'yes' === $settings['al_stock'] ? true : false;
		$has_stock_format           = $has_stock_initial && 'yes' === $settings['al_stock_format'] ? true : false;
		$has_new_badge              = 'yes' === $settings['new_activate'] ? true : false;
		$has_date_expire            = $has_new_badge && ! empty( $settings['new_date'] ) ? absint( $settings['new_date'] ) : '';
		$has_quantity_sold          = 'yes' === $settings['al_quantity_sold'] ? true : false;
		$has_quantity_sold_fallback = $has_quantity_sold && ! empty( $settings['al_quantity_sold_fallback'] ) ? esc_html( $settings['al_quantity_sold_fallback'] ) : '';
		$has_reviews                = 'yes' === get_option( 'woocommerce_enable_reviews' ) && 'yes' === $settings['al_reviews'] ? true : false;
		$notes_format               = $settings['al_reviews_format'];
		$is_a_catalog               = 'true' === $settings['shop_is_a_catalog'] ? true : false;
		/** Affiche le prix si le user est logué ou ce n'est pas un catalogue */
		$has_prices            = $settings['al_prices'] && ( is_user_logged_in() || false === $is_a_catalog );
		$prices_format         = $settings['al_prices_format'];
		$has_promo_percent     = 'yes' === $settings['sale_format'] ? true : false;
		$has_readmore          = 'yes' === $settings['button_more'] ? true : false;
		$has_more_button_picto = 'yes' === $settings['button_add_more_picto'] && ! empty( $settings['button_more_picto'] ) ? true : false;
		$has_cart_initial      = ! $is_a_catalog && 'yes' === $settings['button_cart'] ? true : false;
		$has_cart_button       = 'yes' === $settings['button_add_cart_picto'] && ! empty( $settings['button_cart_picto'] ) ? true : false;
		$has_cart_quantity     = 'yes' === $settings['cart_quantity_activate'] ? true : false;
		// Filtre Users. Champ TEXT
		$has_users        = ! empty( $settings['al_content_user'] ) ? true : false;
		$user_filters     = esc_html( $settings['al_content_user'] );
		$has_filters      = ! $has_swiper && isset( $settings['al_filter'] ) && 'yes' === $settings['al_filter'] ? true : false;

		// Filtre Taxonomie. Champ SELECT2
		$taxonomy_filters = $settings['al_article_taxonomy'];
		// Filtre Étiquettes, on prélève le slug. Champ SELECT2
		$term_filters = array();
		// Extrait les slugs du tableau de terms
		if ( ! empty( $settings['al_article_term'] ) ) {
			foreach ( $settings['al_article_term'] as $term_filter ) {
				$term_filters[] = explode( '::', $term_filter )[1];  // Format term::term->slug
			}
		}

		// Pagination/Navigation
		$has_pagging  = ! $has_swiper && isset( $settings['al_content_pagging_display'] ) && 'yes' === $settings['al_content_pagging_display'] ? true : false;
		$has_navigate = ! $has_swiper && isset( $settings['al_content_nav_display'] ) && 'yes' === $settings['al_content_nav_display'] ? true : false;

		/** Formate le titre avec son tag */
		$title_tag   = ! empty( $settings['al_title_tag'] ) ? Utils::validate_html_tag( $settings['al_title_tag'] ) : 'div';
		// Ajoute l'ID de l'article au titre
		$has_id = 'yes' === $settings['al_article_id'] ? true : false;
		// Formate les arguments et exécute la requête WP_Query et mets en cache les résultats de la requête
		$post_args = Eac_Helpers_Util::get_post_args( $settings );
		$the_query = new \WP_Query( $post_args );
		// La liste des meta_query
		$meta_query_list = Eac_Helpers_Util::get_meta_query_list( $post_args );
		$has_meta_query  = ! empty( $meta_query_list ) ? true : false;

		// Wrapper de la liste des posts et du bouton de pagination avec l'ID du widget Elementor
		$unique_id     = $this->get_id();
		$id            = 'al_posts_wrapper_' . $unique_id;
		$pagination_id = 'al_pagination_' . $unique_id;
		$navigation_id = 'al_navigation_' . $unique_id;

		// La div wrapper
		$layout = 'equalHeight' === $settings['al_layout_type'] ? 'fitRows' : $settings['al_layout_type'];
		$max_num_pages = absint( $the_query->max_num_pages );

		if ( ! $has_swiper ) {
			$class = sprintf( 'al-posts__wrapper shop-products__wrapper layout-type-%s', $layout );
		} else {
			$class = 'al-posts__wrapper shop-products__wrapper swiper-wrapper';
		}

		$this->add_render_attribute( 'posts_wrapper', 'class', esc_attr( $class ) );
		$this->add_render_attribute( 'posts_wrapper', 'id', esc_attr( $id ) );
		if ( $has_filters || ( $has_pagging && $the_query->found_posts > 0 ) || $has_navigate ) {
			$this->add_render_attribute( 'posts_wrapper', 'role', 'region' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-relevant', 'additions,removals' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-live', 'polite' );
			$this->add_render_attribute( 'posts_wrapper', 'aria-atomic', $has_filters ? 'true' : 'false' );
		}
		$this->add_render_attribute( 'posts_wrapper', 'data-settings', $this->get_settings_json( $max_num_pages, $the_query->found_posts ) );

		// Wrapper image et contenu
		$this->add_render_attribute( 'inner_wrapper', 'class', 'al-post__inner-wrapper' );

		/** Affiche les arguments de la requête */
		if ( 'yes' === $settings['al_display_content_args'] && \Elementor\Plugin::$instance->editor->is_edit_mode() ) { ?>
			<div class='al-posts_query-args'>
				<?php highlight_string( "<?php\nQuery Args =\n" . var_export( Eac_Helpers_Util::get_posts_query_args(), true ) . ";\n?>" ); ?>
			</div>
		<?php }

		ob_start();
		if ( $the_query->have_posts() ) {
			/** Création et affichage des filtres avant le widget */
			if ( $has_filters ) {
				// phpcs:disable WordPress.Security.EscapeOutput
				if ( $has_users && ! $has_meta_query ) {
					echo Eac_Helpers_Util::get_user_filters( $user_filters, $unique_id );
				} elseif ( $has_meta_query ) {
					echo Eac_Helpers_Util::get_meta_query_filters( $post_args, $unique_id );
				} elseif ( ! empty( $taxonomy_filters ) ) {
					echo Eac_Helpers_Util::get_taxo_tag_filters( $taxonomy_filters, $term_filters, $unique_id );
				}
				// phpcs:enable WordPress.Security.EscapeOutput
			}
			?>
			<div <?php $this->print_render_attribute_string( 'posts_wrapper' ); ?>>
				<?php if ( ! $has_swiper ) { ?>
					<div class='al-posts__wrapper-sizer'></div>
				<?php }
				/** Le loop */
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$terms_slug = array(); // Tableau de slug concaténé avec la class de l'article
					$terms_name = array(); // Tableau du nom des slugs Concaténé pour les étiquettes

					$product = null;
					$product = wc_get_product( get_the_ID() );
					if ( ! is_a( $product, 'WC_Product' ) ) {
						continue;
					}

					$product_id               = $product->get_id();
					$product_title            = $product->get_name();
					$product_url              = $product->get_permalink();
					$product_id_cart_quantity = 0;
					$product_sold             = $product->get_total_sales();

					if ( $has_cart_quantity && ! is_null( WC()->cart ) && ! WC()->cart->is_empty() ) {
						foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
							if ( $cart_item['product_id'] === $product_id ) {
								$product_id_cart_quantity = $cart_item['quantity'];
							}
						}
					}

					/**
					 * Gérer le stock de chaque produit
					 * Les variables sont reinitialisées à chaque boucle
					 *
					 * $has_stock       Affichage du libellé et du niveau de stock
					 * $has_stock_badge Affichage du badge 'Out of stock'
					 * $has_cart        Affichage du bouton 'Add to cart'
					 */
					$has_stock       = $has_stock_initial;
					$has_stock_badge = $has_stock_badge_initial;
					$has_cart        = $has_cart_initial;

					// Produit en stock
					if ( $product->is_in_stock() ) {
						$has_stock       = true === $has_stock ? true : false;
						$has_stock_badge = false;
						$has_cart        = true === $has_cart ? true : false;
					} else {
						$has_stock       = true === $has_stock ? true : false;
						$has_stock_badge = true;
						$has_cart        = false;
					}

					/**
					 * error_log('has_stock::'.$has_stock.'::'.$product_title);
					 * error_log('managing_stock::'.$product->managing_stock().'::'.$product_title);
					 * error_log('is_in_stock::'.$product->is_in_stock().'::'.$product_title);
					 * error_log('get_stock_quantity::'.$product->get_stock_quantity().'::'.$product_title);
					 * error_log('is_purchasable::'.$product->is_purchasable().'::'.$product_title);
					 * error_log('======================================================');
					 */

					// Redirection ?
					if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
						$product_cart_url = wc_get_cart_url() . '?add-to-cart=' . $product_id;
					} else {
						$product_cart_url = $product_url . '?add-to-cart=' . $product_id;
					}

					$product_taxo = '';
					if ( ! empty( $taxonomy_filters ) ) {
						$product_taxo = get_the_term_list( $product_id, $taxonomy_filters[0], '', ' | ' );
					}

					$product_promo = esc_html( $settings['sale_text'] );
					if ( $has_promo_percent && $product->is_on_sale() ) {
						$product_promo = '-' . round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 ) . '%';
					}

					/** Affichage du badge 'NEW' */
					$has_expired = true;
					if ( ! empty( $has_date_expire ) ) {
						$product_created   = $product->get_date_created(); // Date de création du produit
						$timestamp_created = $product_created->getTimestamp(); // Le timestamp de la date de création du produit
						$datetime_now      = new \WC_DateTime(); // La date du jour
						$timestamp_now     = $datetime_now->getTimestamp(); // Le timestamp de la date du jour

						// Création du produit jusqu'à aujourd'hui en nombre de jours
						$created_to_today = round( ( $timestamp_now - $timestamp_created ) / 86400 );

						$has_expired = $created_to_today > $has_date_expire ? true : false;
					}

					/** Champ user renseigné */
					if ( $has_users && ! $has_meta_query ) {
						$user                = get_the_author_meta( 'display_name' );
						$terms_slug[ $user ] = sanitize_title( $user );
						$terms_name[ $user ] = ucfirst( $user );
					} elseif ( $has_meta_query ) {
						$array_post_meta_values = array();
						foreach ( $meta_query_list as $meta_query ) {
							$term_tmp               = array();
							$array_post_meta_values = get_post_custom_values( $meta_query['key'], $product_id );

							if ( ! is_wp_error( $array_post_meta_values ) && ! empty( $array_post_meta_values ) ) {
								$term_tmp = Eac_Helpers_Util::compare_meta_values( $array_post_meta_values, $meta_query );
								if ( ! empty( $term_tmp ) ) {
									foreach ( $term_tmp as $idx => $tmp ) {
										$terms_slug = array_replace( $terms_slug, array( $idx => sanitize_title( $tmp ) ) );
										$terms_name = array_replace( $terms_name, array( $idx => ucfirst( $tmp ) ) );
									}
								}
							}
						}
					} elseif ( ! empty( $taxonomy_filters ) ) { /** Champ taxonomie renseigné */
						$terms_array = array();
						foreach ( $taxonomy_filters as $post_term ) {
							$terms_array = wp_get_post_terms( $product_id, $post_term );
							if ( ! is_wp_error( $terms_array ) && ! empty( $terms_array ) ) {
								foreach ( $terms_array as $term ) {
									if ( 0 !== $term->parent ) {
										$term_parent = get_term( $term->parent );
										if ( ! is_wp_error( $term_parent ) ) {
											if ( ! empty( $term_filters ) ) {
												if ( in_array( $term_parent->slug, $term_filters, true ) ) {
													$terms_slug[ $term_parent->slug ] = $term_parent->slug;
													$terms_name[ $term_parent->name ] = ucfirst( $term_parent->name );
												}
											} else {
												$terms_slug[ $term_parent->slug ] = $term_parent->slug;
												$terms_name[ $term_parent->name ] = ucfirst( $term_parent->name );
											}
										}
									}
									if ( ! empty( $term_filters ) ) {
										if ( in_array( $term->slug, $term_filters, true ) ) {
											$terms_slug[ $term->slug ] = $term->slug;
											$terms_name[ $term->name ] = ucfirst( $term->name );
										}
									} else {
										$terms_slug[ $term->slug ] = $term->slug;
										$terms_name[ $term->name ] = ucfirst( $term->name );
									}
								}
							}
						}
					}

					// Wrapper interne du contenu
					if ( $has_global_link && $product_url ) {
						$this->add_render_attribute( 'content_wrapper', 'class', 'al-post__content-wrapper global__card-link' );
					} else {
						$this->add_render_attribute( 'content_wrapper', 'class', 'al-post__content-wrapper' );
					}

					if ( ! $has_swiper ) {
						$post_class = sprintf( '%1$s %2$s %3$s %4$s', $unique_id, 'al-post__wrapper', implode( ' ', $terms_slug ), implode( ' ', get_post_class( '', get_the_ID() ) ) );
					} else {
						$post_class = sprintf( '%1$s %2$s %3$s', $unique_id, 'al-post__wrapper swiper-slide', implode( ' ', get_post_class( '', get_the_ID() ) ) );
					}
					$attachment = Eac_Helpers_Util::wp_get_attachment_data( get_post_thumbnail_id( $product_id ), $settings['al_image_dimension_size'] );
					?>
					<article id="<?php echo 'post-' . esc_attr( $product_id ); ?>" class="<?php echo esc_attr( $post_class ); ?>">
						<div <?php $this->print_render_attribute_string( 'inner_wrapper' ); ?>>

							<?php if ( $product->is_on_sale() && ! $is_a_catalog ) : ?>
								<span class='badge-sale' dir='auto'><?php echo esc_html( $product_promo ); ?></span>
							<?php endif; ?>
							<?php if ( $has_stock_badge && ! $is_a_catalog ) : ?>
								<span class='badge-stock'><?php echo esc_html( $settings['stock_text'] ); ?></span>
							<?php endif; ?>
							<?php if ( ! $has_expired ) : ?>
								<span class='badge-new'><?php echo esc_html( $settings['new_text'] ); ?></span>
							<?php endif; ?>

							<div <?php $this->print_render_attribute_string( 'content_wrapper' ); ?>>
								<?php if ( has_post_thumbnail() && $attachment ) : ?>
									<div class='al-post__image-wrapper'>
										<?php
										$this->add_render_attribute(
											'post_image',
											array(
												'class'  => 'img-focusable al-post__image-loaded',
												'src'    => esc_url( $attachment['src'] ),
												'srcset' => esc_attr( $attachment['srcset'] ),
												'sizes'  => esc_attr( $attachment['srcsize'] ),
												'width'  => esc_attr( $attachment['width'] ),
												'height' => esc_attr( $attachment['height'] ),
												'alt'    => esc_html( $attachment['alt'] ),
											)
										);
										if ( 'eager' === $lazy_load ) {
											$this->add_render_attribute( 'post_image', 'loading', $lazy_load );
										}
										if ( $has_image_lightbox ) : ?>
											<a class='eac-accessible-link' href="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" data-elementor-open-lightbox='no' data-fancybox="al-gallery-<?php echo esc_attr( $unique_id ); ?>" data-caption="<?php echo esc_html( $product_title ); ?>" aria-label="<?php echo esc_html__( "Voir l'image", 'eac-components' ) . ' ' . esc_html( $product_title ); ?>">
										<?php endif; ?>
										<?php if ( $has_image_link && $product_url ) :
											$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link'; ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_html__( 'Voir la page produit', 'eac-components' ) . ' ' . esc_html( $product_title ); ?>">
										<?php endif; ?>
											<img <?php $this->print_render_attribute_string( 'post_image' ); ?>>
										<?php if ( $has_image_lightbox || ( $has_image_link && $product_url ) ) : ?>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if ( $has_title || $has_reviews || $has_prices || $has_stock || $has_quantity_sold || $has_readmore || $has_cart ) : ?>
								<div class='al-post__text-wrapper'>
								<?php endif; ?>

									<!-- Le titre -->
									<?php if ( $has_title ) :
										$class_link = $has_global_link ? 'eac-accessible-link card-link' : 'eac-accessible-link';
										?>
										<!-- Affiche les IDs -->
										<?php if ( $has_id && $has_title_link && $product_url ) : ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>">
												<?php echo '<' . esc_attr( $title_tag ) . ' class="al-post__content-title">' . absint( $product_id ) . ' : ' . esc_html( $product_title ) . '</' . esc_attr( $title_tag ) . '>'; ?>
											</a>
										<?php elseif ( $has_id && ! $has_title_link ) : ?>
											<?php echo '<' . esc_attr( $title_tag ) . ' class="al-post__content-title">' . absint( $product_id ) . ' : ' . esc_html( $product_title ) . '</' . esc_attr( $title_tag ) . '>'; ?>
										<?php elseif ( ! $has_id && $has_title_link && $product_url ) : ?>
											<a class="<?php echo esc_attr( $class_link ); ?>" href="<?php echo esc_url( $product_url ); ?>">
												<?php echo '<' . esc_attr( $title_tag ) . ' class="al-post__content-title">' . esc_html( $product_title ) . '</' . esc_attr( $title_tag ) . '>'; ?>
											</a>
										<?php else : ?>
											<?php echo '<' . esc_attr( $title_tag ) . ' class="al-post__content-title">' . esc_html( $product_title ) . '</' . esc_attr( $title_tag ) . '>'; ?>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Le résumé de l'article. fonction dans helper.php -->
									<?php if ( $has_resum ) : ?>
										<div class='shop-product__excerpt-wrapper'>
											<span dir='ltr'><?php echo Eac_Tools_Util::get_post_excerpt( $product_id, absint( $settings['al_excerpt_length'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
									</div>
									<?php endif; ?>

									<!-- Avis -->
									<?php
									if ( $has_reviews ) :
										$notes       = '';
										$long_format = '';
										if ( absint( $product->get_review_count() ) > 0 ) {
											$long_format = '<span class="al_post_customer-review"> (' . absint( $product->get_review_count() ) . esc_html__( ' Avis clients', 'eac-components' ) . ')</span>';
										}
										// Au moins une note
										// if (absint($product->get_review_count()) > 0) {
										switch ( $notes_format ) {
											case 'average_rating':
												$notes = esc_html__( 'Moyenne des notes', 'eac-components' ) . ' ' . $product->get_average_rating();
												break;
											case 'average_html':
												$notes = wc_get_rating_html( $product->get_average_rating() );
												break;
											case 'average_html_long':
												$notes = wc_get_rating_html( $product->get_average_rating() ) . $long_format;
												break;
											case 'rating_count':
												$notes = esc_html__( 'Nombre de notes', 'eac-components' ) . ' ' . absint( $product->get_rating_count() );
												break;
											case 'review_count':
												$notes = esc_html__( "Nombre d'avis", 'eac-components' ) . ' ' . absint( $product->get_review_count() );
												break;
										}

										if ( ! empty( $notes ) ) : ?>
											<div class="woocommerce shop-product__notes-wrapper"><?php echo $notes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Prix -->
									<?php
									if ( $has_prices && $product->get_regular_price() ) :
										$prices         = '';
										$sales_price_to = '';
										$sales_price_to = get_post_meta( $product_id, '_sale_price_dates_to', true );
										switch ( $prices_format ) {
											case 'both':
												$prices = $product->get_price_html();
												break;
											case 'dateto':
												$prices = $product->get_price_html();
												if ( $product->is_type( 'simple' ) && '' !== $sales_price_to ) {
													$sales_price_date_to = date( get_option( 'date_format' ), $sales_price_to );
													$prices              = str_replace( '</ins>', ' </ins> (' . esc_html__( "jusqu'au ", 'eac-components' ) . $sales_price_date_to . ')', $prices );
												}
												break;
											case 'regular':
												$prices = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
												break;
											case 'promo' && $product->is_on_sale():
												$prices = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
												break;
										}
										if ( ! empty( $prices ) ) : ?>
											<div class='shop-product__prices-wrapper'><?php echo $prices; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Stock -->
									<?php
									if ( $has_stock ) :
										$stock = '';
										if ( $has_stock_format ) {
											$stock = wc_get_stock_html( $product );
										} else {
											$stock = esc_html__( 'Quantité', 'eac-components' ) . ' ' . (int) $product->get_stock_quantity();
										}
										?>
										<div class='woocommerce shop-product__stock-wrapper'><?php echo $stock; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
									<?php endif; ?>

									<!-- Quantité vendue -->
									<?php
									/** Affiche le fallback quand le produit n'a pas été vendu et qu'il n'est pas en rupture de stock */
									if ( $has_quantity_sold ) :
										$quantity_sold = '';
										if ( 0 !== absint( $product_sold ) ) :
											$quantity_sold = esc_html__( 'Quantité vendue', 'eac-components' ) . ' ' . absint( $product_sold );
										elseif ( 0 === absint( $product_sold ) && false === $has_stock_badge ) :
											$quantity_sold = esc_html( $has_quantity_sold_fallback );
										endif;

										if ( ! empty( $quantity_sold ) ) : ?>
											<div class='shop-product__sold-wrapper'><?php echo $quantity_sold; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
										<?php endif; ?>
									<?php endif; ?>

									<!-- Les boutons -->
									<?php if ( $has_readmore || $has_cart ) : ?>
										<div class='buttons-wrapper'>
											<?php if ( $has_readmore && $product_url ) :
												$class_link = $has_global_link ? 'button-readmore card-link' : 'button-readmore'; ?>
												<a href="<?php echo esc_url( $product_url ); ?>" class="<?php echo esc_attr( $class_link ); ?>" role='button' aria-label="<?php echo esc_html( $settings['button_more_label'] ) . ' ' . esc_html( $product_title ); ?>">
													<span class='button__readmore-wrapper'>
													<?php if ( $has_more_button_picto && 'before' === $settings['button_more_position'] ) { ?>
														<span class='button-icon eac-icon-svg'>
															<?php Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) ); ?>
														</span>
													<?php }
													echo '<span class="label-icon">' . esc_html( $settings['button_more_label'] ) . '</span>';
													if ( $has_more_button_picto && 'after' === $settings['button_more_position'] ) { ?>
														<span class='button-icon eac-icon-svg'>
															<?php Icons_Manager::render_icon( $settings['button_more_picto'], array( 'aria-hidden' => 'true' ) ); ?>
														</span>
													<?php } ?>
													</span>
												</a>
											<?php endif; ?>

											<?php if ( $has_cart && $product->get_regular_price() ) : ?>
												<a href="<?php echo esc_url( $product_cart_url ); ?>" class='button-cart' role='button' aria-label="<?php echo esc_html__( 'Ajouter au panier', 'eac-components' ) . ' ' . esc_html( $product_title ); ?>">
													<span class='button__cart-wrapper'>
													<?php if ( $has_cart_quantity && 0 !== absint( $product_id_cart_quantity ) ) : ?>
														<span class='badge-cart__quantity'><?php echo absint( $product_id_cart_quantity ); ?></span>
													<?php endif; ?>
													<?php if ( $has_cart_button && 'before' === $settings['button_cart_position'] ) { ?>
														<span class='button-icon eac-icon-svg'>
														<?php Icons_Manager::render_icon( $settings['button_cart_picto'], array( 'aria-hidden' => 'true' ) ); ?>
														</span>
													<?php }
													echo '<span class="label-icon">' . esc_html( $settings['button_cart_label'] ) . '</span>';
													if ( $has_cart_button && 'after' === $settings['button_cart_position'] ) { ?>
														<span class='button-icon eac-icon-svg'>
														<?php Icons_Manager::render_icon( $settings['button_cart_picto'], array( 'aria-hidden' => 'true' ) ); ?>
														</span>
													<?php } ?>
													</span>
												</a>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								<?php if ( $has_title || $has_reviews || $has_prices || $has_stock || $has_quantity_sold || $has_readmore || $has_cart ) : ?>
								</div>
								<?php endif; ?>
							</div>

							<?php if ( $has_avatar || $has_term || $has_auteur || $has_date ) : ?>
								<div class="al-post__meta-wrapper">
									<!-- Ajout de l'attribut 'loading' à l'avatar -->
									<?php if ( $has_avatar ) : ?>
										<?php
										$author_url      = get_avatar_url( get_the_author_meta( 'ID' ), array( 'size' => absint( $avatar_size ) ) );
										$author_archives = get_author_posts_url( get_the_author_meta( 'ID' ) );
										$author_name     = get_the_author_meta( 'display_name' );
										?>
										<div class="al-post__avatar-wrapper">
											<?php if ( $has_avatar_link ) : ?>
												<a href="<?php echo esc_url( $author_archives ); ?>" class='eac-accessible-link avatar-link' aria-label="<?php echo esc_html__( 'Voir tous les articles de', 'eac-components' ) . ' ' . esc_html( $author_name ); ?>">
											<?php endif; ?>
												<img class='avatar photo' src="<?php echo esc_url( $author_url ); ?>" alt="Avatar of <?php echo esc_html( $author_name ); ?>" loading='lazy' width="<?php echo absint( $avatar_size ); ?>" height="<?php echo absint( $avatar_size ); ?>" />
											<?php if ( $has_avatar_link ) : ?>
												</a>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<?php if ( $has_term || $has_auteur || $has_date ) : ?>
										<div class='al-post__meta'>
											<!-- Les étiquettes -->
											<?php if ( $has_term ) :
												$terms = $this->get_svg_icon_terms() . '<span>' . implode( '|', array_map( 'esc_html', $terms_name ) ) . '</span>'; ?>
												<div class='al-post__meta-tags eac-icon-svg'>
													<?php echo wp_kses_post( $terms ); ?>
												</div>
											<?php endif; ?>

											<!-- L'auteur de l'article -->
											<?php if ( $has_auteur ) :
												$auteur = $this->get_svg_icon_user() . '<span>' . esc_html( get_the_author_meta( 'display_name' ) ) . '</span>'; ?>
												<div class='al-post__meta-author eac-icon-svg'>
													<?php echo wp_kses_post( $auteur ); ?>
												</div>
											<?php endif; ?>

											<!-- Le date de création ou de dernière modification -->
											<?php if ( $has_date ) :
												if ( 'modified' === $settings['al_article_orderby'] ) :
													$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( $product->get_date_modified( 'Y-m-d' ) ) ) . '</span>';
												else :
													$calendar = $this->get_svg_icon_calendar() . '<span>' . date_i18n( get_option( 'date_format' ), strtotime( $product->get_date_created( 'Y-m-d' ) ) ) . '</span>';
												endif; ?>
												<div class='al-post__meta-date eac-icon-svg'>
													<?php echo wp_kses_post( $calendar ); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</article>
					<?php
					$this->remove_render_attribute( 'post_image' );
					$this->remove_render_attribute( 'content_wrapper' );
				} ?>
			</div>
			<?php if ( $has_pagging && $the_query->post_count < $the_query->found_posts ) :
				$this->add_render_attribute( 'read_button', 'class', 'eac__read-button' );
				$this->add_render_attribute( 'read_button', 'type', 'button' );
				$this->add_render_attribute( 'read_button', 'aria-label', esc_html__( 'Pagination charger la page suivante', 'eac-components' ) );
				$button = '<button ' . $this->get_render_attribute_string( 'read_button' ) . '>' . esc_html__( 'Plus de produits', 'eac-components' ) . ' <span class="al-more-button-paged">' . $the_query->post_count . '/' . $the_query->found_posts . '</span></button>';
				?>
				<div class='al-post__pagination' id="<?php echo esc_attr( $pagination_id ); ?>">
					<div class='eac__button'><?php echo wp_kses_post( $button ); ?></div>
					<div class='al-page-load-status'>
						<div class='infinite-scroll-request eac__loader-spin'></div>
						<p class='infinite-scroll-last'><?php esc_html_e( "Plus d'article", 'eac-components' ); ?></p>
						<p class='infinite-scroll-error'><?php esc_html_e( 'Aucun article à charger', 'eac-components' ); ?></p>
					</div>
				</div>
			<?php elseif ( $has_navigate ) :
				$page_links = paginate_links(
					array(
						'format'             => '?paged=%#%',
						'prev_text'          => '&laquo;',
						'next_text'          => '&raquo;',
						'mid_size'           => 1,
						'total'              => $max_num_pages,
						'current'            => max( 1, get_query_var( 'paged' ) ),
					)
				); ?>
				<nav class='al-post__navigation' id="<?php echo esc_attr( $navigation_id ); ?>" aria-label="<?php esc_attr_e( 'Pagination', 'eac-components' ); ?>">
					<div class='al-post__navigation-digit'><?php echo wp_kses_post( $page_links ); ?></div>
				</nav>
			<?php endif;

			wp_reset_postdata();
		}
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * get_settings_json
	 *
	 * Retrieve fields values to pass at the widget container
	 * Convert on JSON format
	 * Modification de la règles 'data_filtre'
	 *
	 * @uses      wp_json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 */
	protected function get_settings_json( $max_num_pages, $found_posts ) {
		$settings = $this->get_settings_for_display();
		$unique_id     = $this->get_id();
		$wrapper_id    = 'al_posts_wrapper_' . $unique_id;
		$pagination_id = 'al_pagination_' . $unique_id;
		$navigation_id = 'al_navigation_' . $unique_id;

		$effect = $settings['slider_effect'];
		if ( in_array( $effect, array( 'fade', 'creative' ), true ) ) {
			$nb_images = 1;
		} elseif ( isset( $settings['slider_images_centered'] ) && 'yes' === $settings['slider_images_centered'] ) {
			$nb_images = 2;
		} elseif ( empty( $settings['slider_images_number'] ) ) {
			$nb_images = 3;
		} elseif ( 0 === absint( $settings['slider_images_number'] ) ) {
			$nb_images = 'auto';
			$effect    = 'slide';
		} else {
			$nb_images = absint( $settings['slider_images_number'] );
		}

		$has_swiper = 'slider' === $settings['al_layout_type'] ? true : false;

		$module_settings = array(
			'data_id'                  => $wrapper_id,
			'data_pagination'          => ! $has_swiper && 'yes' === $settings['al_content_pagging_display'] ? true : false,
			'data_pagination_id'       => $pagination_id,
			'data_navigation'          => ! $has_swiper && 'yes' === $settings['al_content_nav_display'] ? true : false,
			'data_navigation_id'       => $navigation_id,
			'data_layout'              => 'equalHeight' === $settings['al_layout_type'] ? 'fitRows' : $settings['al_layout_type'],
			'data_equalheight'         => in_array( $settings['al_layout_type'], array( 'equalHeight', 'fitRows' ), true ) ? true : false,
			'data_article'             => $unique_id,
			'data_filtre'              => ! $has_swiper && isset( $settings['al_filter'] ) && 'yes' === $settings['al_filter'] ? true : false,
			'data_fancybox'            => 'yes' === $settings['al_lightbox'] ? true : false,
			'data_max_pages'           => $max_num_pages,
			'data_found_posts'         => $found_posts,
			'data_sw_id'               => 'eac_post_grid_' . $unique_id,
			'data_sw_swiper'           => $has_swiper,
			'data_sw_autoplay'         => 'yes' === $settings['slider_autoplay'] ? true : false,
			'data_sw_loop'             => 'yes' === $settings['slider_loop'] ? true : false,
			'data_sw_delay'            => absint( $settings['slider_delay'] ),
			'data_sw_imgs'             => $nb_images,
			'data_sw_centered'         => 'yes' === $settings['slider_images_centered'] ? true : false,
			'data_sw_dir'              => 'horizontal',
			'data_sw_rtl'              => 'right' === $settings['slider_rtl'] ? true : false,
			'data_sw_effect'           => $effect,
			'data_sw_free'             => true,
			'data_sw_pagination_click' => 'yes' === $settings['slider_pagination'] && 'yes' === $settings['slider_pagination_click'] ? true : false,
			'data_sw_scroll'           => 'yes' === $settings['slider_scrollbar'] ? true : false,
		);

		return wp_json_encode( $module_settings );
	}

	/** Force le chargement du script sans dépendances */
	public function force_load_script_for_fse() {
		wp_enqueue_script( 'eac-post-grid', EAC_Plugin::instance()->get_script_url( 'assets/js/elementor/eac-post-grid' ), array( 'jquery' ), '1.9.8', true );
	}

	protected function content_template() {}
}
