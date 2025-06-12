<?php
/**
 *
 * Description: ajout de l'option 'Edit content' dans le gestionnaire des rôles
 * Cette class 'elementor-editor-content-only' est ajoutée au Body. Source editor.js
 * https://github.com/elementor/elementor/blob/main/assets/dev/js/editor/editor-base.js#L1260
 * @since 2.3.4
 */

namespace EACCustomWidgets\Includes\Elementor\Injection;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Eac_Injection_Role_Manager {
	const ROLE_MANAGER_OPTION_NAME = 'role-manager';

	public function __construct() {
		add_action( 'elementor/role/restrictions/controls', array( $this, 'add_content_role_controls' ), 99, 2 );
	}

	public function get_role_manager_options() {
		return get_option( 'elementor_' . self::ROLE_MANAGER_OPTION_NAME, array() );
	}

	public function add_content_role_controls( $role_slug, $role_data ) { //phpcs:ignore
		$value = 'design';
		static $options = false;

		if ( ! $options ) {
			$options = array(
				'excluded_options' => \Elementor\Plugin::$instance->role_manager->get_role_manager_options(), // ????
				'advanced_options' => $this->get_role_manager_options(),
			);
		}
		$id = self::ROLE_MANAGER_OPTION_NAME . '_' . $role_slug . '_' . $value;
		$name = 'elementor_' . self::ROLE_MANAGER_OPTION_NAME . '[' . $role_slug . '][]';
		$checked = isset( $options['advanced_options'][ $role_slug ] ) ? $options['advanced_options'][ $role_slug ] : array();
		?>
		<div class='elementor-role-control'>
			<label for="<?php echo esc_attr( $id ); ?>">
				<input type='checkbox' name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( in_array( $value, $checked, true ), true ); ?>>
				<?php echo esc_html__( 'Ne peut modifier que le contenu', 'eac-components' ); ?> <!--Access to edit content only-->
			</label>
		</div>
		<style>.elementor-role-controls .elementor-role-go-pro { display: none !important; }</style>
		<?php
	}
}
new Eac_Injection_Role_Manager();
