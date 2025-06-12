<?php
/**
 * Class: Site_Thumbnails_Widget
 * Name: Miniature de site
 * Slug: eac-addon-site-thumbnail
 *
 * Description: Affiche la miniature d'un site web local ou distant
 *
 * @since 1.7.70
 */

namespace EACCustomWidgets\Includes\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use EACCustomWidgets\Includes\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Site_Thumbnails_Widget extends Widget_Base {

	/**
	 * Constructeur de la class Site_Thumbnails_Widget
	 */
	public function __construct( $data = array(), $args = null ) {
		parent::__construct( $data, $args );

		wp_register_style( 'eac-site-thumbnail', EAC_Plugin::instance()->get_style_url( 'assets/css/site-thumbnail' ), array( 'eac-frontend' ), '1.7.70' );
	}

	/**
	 * Le nom de la clé du composant dans le fichier de configuration
	 *
	 * @var $slug
	 *
	 * @access private
	 */
	private $slug = 'site-thumbnail';

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
	 * Load dependent styles
	 *
	 * Les styles sont chargés dans le footer
	 *
	 * @return CSS list.
	 */
	public function get_style_depends(): array {
		return array( 'eac-site-thumbnail' );
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

		$this->start_controls_section(
			'st_site_settings',
			array(
				'label' => esc_html__( 'Réglages', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);

			$this->add_control(
				'st_site_url',
				array(
					'label'       => esc_html__( 'URL', 'eac-components' ),
					'type'        => Controls_Manager::URL,
					'description' => esc_html__( "Coller l'URL complète du site", 'eac-components' ),
					'placeholder' => 'http://your-link.com',
					'dynamic'     => array(
						'active' => true,
					),
				)
			);

			$this->add_control(
				'st_site_url_warning',
				array(
					'type'            => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
					'raw'             => esc_html__( 'SAMEORIGIN: Certains sites interdisent le chargement de la ressource dans une iframe en dehors de leur domaine.', 'eac-components' ),
				)
			);

			$start = is_rtl() ? 'right' : 'left';
			$end   = is_rtl() ? 'left' : 'right';
			$this->add_responsive_control(
				'st_site_url_align_h',
				array(
					'label'     => esc_html__( 'Alignement', 'eac-components' ),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => array(
						'left'  => array(
							'title' => is_rtl() ? esc_html__( 'Droite', 'eac-components' ) : esc_html__( 'Gauche', 'eac-components' ),
							'icon'  => "eicon-h-align-{$start}",
						),
						'center' => array(
							'title' => esc_html__( 'Centre', 'eac-components' ),
							'icon'  => 'eicon-h-align-center',
						),
						'right'    => array(
							'title' => is_rtl() ? esc_html__( 'Gauche', 'eac-components' ) : esc_html__( 'Droite', 'eac-components' ),
							'icon'  => "eicon-h-align-{$end}",
						),
					),
					'selectors_dictionary' => array(
						'left'   => '0 auto',
						'center' => 'auto',
						'right'  => 'auto 0',
					),
					'default'   => 'center',
					'selectors' => array(
						'{{WRAPPER}} .site-thumbnail-container' => 'margin-inline: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'st_add_link',
				array(
					'label'        => esc_html__( 'Ajouter le lien vers le site', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'st_add_caption',
				array(
					'label'        => esc_html__( 'Ajouter une légende', 'eac-components' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => esc_html__( 'oui', 'eac-components' ),
					'label_off'    => esc_html__( 'non', 'eac-components' ),
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'st_site_caption',
				array(
					'label'       => esc_html__( 'Légende', 'eac-components' ),
					'type'        => Controls_Manager::TEXT,
					'dynamic'     => array( 'active' => true ),
					'ai'          => array( 'active' => false ),
					'description' => esc_html__( 'Coller la légende', 'eac-components' ),
					'label_block' => true,
					'condition'   => array( 'st_add_caption' => 'yes' ),
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'st_site_container_style',
			array(
				'label' => esc_html__( 'Général', 'eac-components' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				array(
					'name'      => 'st_site_container_border',
					'selector'  => '{{WRAPPER}} .site-thumbnail-container',
				)
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => 'st_site_container_shadow',
					'label'    => esc_html__( 'Ombre', 'eac-components' ),
					'selector' => '{{WRAPPER}} .site-thumbnail-container',
				)
			);

		$this->end_controls_section();

		$this->start_controls_section(
			'st_site_legende_style',
			array(
				'label'     => esc_html__( 'Légende', 'eac-components' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array( 'st_add_caption' => 'yes' ),
			)
		);

			$this->add_control(
				'st_site_legende_color',
				array(
					'label'     => esc_html__( 'Couleur', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .thumbnail-caption' => 'color: {{VALUE}};' ),
				)
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				array(
					'name'     => 'st_site_legende_typography',
					'label'    => esc_html__( 'Typographie', 'eac-components' ),
					'selector' => '{{WRAPPER}} .thumbnail-caption',
				)
			);

			$this->add_control(
				'st_site_legende_bg',
				array(
					'label'     => esc_html__( 'Couleur du fond', 'eac-components' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => array( '{{WRAPPER}} .thumbnail-caption' => 'background-color: {{VALUE}};' ),
				)
			);

			$this->add_control(
				'st_site_container_margin',
				array(
					'label'              => esc_html__( 'Marges internes', 'eac-components' ),
					'type'               => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => array( 'top', 'bottom' ),
					'size_units'         => array( 'px' ),
					'default'            => array(
						'top'      => 0,
						'bottom'   => 0,
						'unit'     => 'px',
						'isLinked' => true,
					),
					'range'              => array(
						'px' => array(
							'min'  => 0,
							'max'  => 50,
							'step' => 5,
						),
					),
					'selectors'          => array( '{{WRAPPER}} .thumbnail-caption' => 'padding-block: {{TOP}}{{UNIT}} {{BOTTOM}}{{UNIT}}; padding-inline: {{LEFT}}{{UNIT}} {{RIGHT}}{{UNIT}};' ),
				)
			);

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
		if ( empty( $settings['st_site_url']['url'] ) ) {
			return;
		}

		$has_url = 'yes' === $settings['st_add_link'] ? true : false;
		$url     = $settings['st_site_url']['url'];
		$this->add_link_attributes( 'st-link-to', $settings['st_site_url'] );
		if ( $settings['st_site_url']['is_external'] ) {
			$this->add_render_attribute( 'st-link-to', 'rel', 'noopener noreferrer' );
		}

		$has_caption = 'yes' === $settings['st_add_caption'] && ! empty( $settings['st_site_caption'] );
		?>
		<div class='eac-site-thumbnail' dir='ltr'>
			<div class='site-thumbnail-container'>
				<?php if ( $has_caption ) { ?>
					<div class='thumbnail-caption'><?php echo esc_html( $settings['st_site_caption'] ); ?></div>
				<?php } ?>
				<?php if ( $has_url ) { ?>
					<a <?php $this->print_render_attribute_string( 'st-link-to' ); ?>>
				<?php } ?>
					<span class='site-thumbnail__wrapper-overlay'></span>
				<?php if ( $has_url ) { ?>
					</a>
				<?php } ?>
				<div class='thumbnail-container'>
					<div class='site-thumbnail'>
						<iframe src="<?php echo esc_url( $url ); ?>" frameborder='0' onload="var that=this;setTimeout(function() { that.style.opacity=1 }, 500)" loading='lazy' tabindex='-1'></iframe>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	protected function content_template() {}
}
