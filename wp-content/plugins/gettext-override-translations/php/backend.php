<?php
class MP_Gettext_Override_Admin {
	
	function __construct()
	{
		define( 'MP_GETTEXT_PLUGIN_URL', WP_CONTENT_URL . '/plugins/gettext-override-translations/' );
		define( 'MP_GETTEXT_PLUGIN_DIR', WP_PLUGIN_DIR . '/gettext-override-translations/' );
		add_action( 'admin_init', array( &$this, 'settings_init' ) );
		add_action( 'admin_menu', array( &$this, 'add_option_page' ) );
		add_action( 'admin_print_styles', array( &$this, 'add_admin_head' ) );

		add_filter( 'plugin_action_links_gettext-override-translations/gettextoverridetranslations.php', array( &$this, 'add_settings_link' ) , 2, 2 );
	}

	function add_settings_link( $links,  $plugin_file ) { 
		$settings_link =  '<a href="plugins.php?page=gettext-override-translations">Settings</a>'; 
		array_unshift( $links, $settings_link ); 
		return $links; 
	}

	function settings_init() {
		register_setting( 'defined_constants_group', 'defined_constants', array( &$this, 'validate_constants' ) );
	}
	
	function validate_constants( $constants ) {
		$output = [];
		if( is_array( $constants['name'] ) ) {
		
			foreach($constants['name'] as $key => $value) {
			
				$value = wp_kses( $value , wp_kses_allowed_html( 'post' ) );
				if( empty( $value ) ) continue;
			
				$output[] = array(
					'name' => $value,
					'value' => wp_kses( $constants['value'][$key] , wp_kses_allowed_html( 'post' ) ),
					'desc' => wp_kses( $constants['desc'][$key] , wp_kses_allowed_html( 'post' ) ),
					'internal_warning' => intval( $constants['internal_warning'][$key] )
				);
			}
		}

		update_option( MP_GETTEXT_OVERRIDE_OPTION, $output );	
		return $output;
	}
	
	function add_option_page() {
		add_submenu_page( 'plugins.php','Gettext override translations', 'Gettext override translations', 'manage_options', 'gettext-override-translations', array( &$this, 'admin_page' ) );
	}
	
	function add_admin_head() {
		if( isset( $_GET['page'] ) && $_GET['page'] == 'gettext-override-translations' ) {
			wp_enqueue_style( 'mwp_admin_css', MP_GETTEXT_PLUGIN_URL.'css/backend.css' );
			wp_enqueue_script( 'jquery');
			wp_enqueue_script( 'jquery-ui-sortable');
			wp_enqueue_script( 'mwp_admin_js', MP_GETTEXT_PLUGIN_URL.'js/backend.js' );
		}
	}
	
	function admin_page() {
		$this->setup_admin_page( 'Gettext override translations Settings' );
		$constants = get_option( MP_GETTEXT_OVERRIDE_OPTION ); 
			if( is_array( $constants ) ) {
				if ($_GET['settings-updated']==true) { ?>
					<div id="message" class="updated">
						<p><?php _e('Translation overrides succesfully updated.');?></p>
					</div>
				<?php } ?>
				<div class="postbox">
					<h3 class="hndle"><span><?php _e('Defined translation overrides');?></span></h3>
					<div class="inside">
				<form method="post" id="dc_defined_constants_form" action="options.php">
					<?php settings_fields('defined_constants_group'); ?>
					<table class="form-table">
						<thead>
							<tr valign="top">
								<th scope="column"><?php _e('Original (translated) text');?></th><th scope="column"><?php _e('Override text');?></th><th scope="column"><?php _e('Description (will only be shown in this form)');?></th><th><?php _e('Protect setting?');?></th>
							</tr>
						</thead>
						<tbody id="dc_sortable">
					<?php foreach($constants as $key => $value) : ?>
						<tr valign="top"<?php if(isset($value['internal_warning']) && $value['internal_warning'] == 1) echo ' class="internal_warning" ';?>>
							<td><img class="dc_delete<?php if(isset($value['internal_warning']) && $value['internal_warning'] == 1) echo '_iw';?>" src="<?php echo MP_GETTEXT_PLUGIN_URL;?>img/delete.png" title="Delete this constant '<?php echo wp_kses( $value['name'] , wp_kses_allowed_html( 'post' ) ); ?>'" alt="delete" /></td>
							<td>
								<textarea cols="40" rows="5" name="defined_constants[name][]"><?php if(isset($value['name'])) echo wp_kses( $value['name'] , wp_kses_allowed_html( 'post' ) ); ?></textarea>
							</td>
							<td class="td_textarea">
								<textarea cols="40" rows="5" name="defined_constants[value][]"><?php if(isset($value['value'])) echo wp_kses( $value['value'] , wp_kses_allowed_html( 'post' ) ); ?></textarea>
							</td>
							<td><input style="width:250px;" type="text" name="defined_constants[desc][]" value="<?php if(isset($value['desc'])) echo wp_kses( $value['desc'] , wp_kses_allowed_html( 'post' ) ); ?>" /></td>
							<td>
								<?php if(!isset($value['internal_warning']) || $value['internal_warning'] != 1) { ?><input class="dc_checkbox_hack" type="hidden" name="defined_constants[internal_warning][]" value="0"> <?php } ?>
								<input type="checkbox" class="dc_checkbox" name="defined_constants[internal_warning][]" value="1"<?php if(isset($value['internal_warning']) && $value['internal_warning'] === 1) echo ' checked="checked" ';?>/>
							</td>
						</tr>
					<?php endforeach; ?>
						</tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Save Changes'); ?>" />
				</p>
			</form>
			</div>
			</div>
			<?php } 
			if(!is_array($constants)) $new_index = 0;
				else $new_index = count($constants);
			?>
			<div class="postbox" width="90%">
						<h3 class="hndle"><span><?php _e('Define a new translation override');?></span></h3>
						<div class="inside">
			<form method="post" action="options.php">
				<?php settings_fields('defined_constants_group'); ?>
				<?php 
				if(is_array($constants)) {
					foreach($constants as $key => $value) : ?>
							<input type="hidden" name="defined_constants[name][]" value="<?php if(isset($value['name'])) echo wp_kses( $value['name'] , wp_kses_allowed_html( 'post' ) ); ?>" />
							<textarea style="display:none;" cols="40" rows="5" name="defined_constants[value][]"><?php if(isset($value['value'])) echo wp_kses( $value['value'] , wp_kses_allowed_html( 'post' ) ); ?></textarea>
							<input type="hidden" name="defined_constants[desc][]" value="<?php if(isset($value['desc'])) echo wp_kses( $value['desc'] , wp_kses_allowed_html( 'post' ) ); ?>" />
							<input type="hidden" name="defined_constants[internal_warning][]" value="<?php if(isset($value['internal_warning'])) echo wp_kses( $value['internal_warning'] , wp_kses_allowed_html( 'post' ) ); ?>" />
				<?php 
						$new_index = $key + 1; 
					endforeach; 
				} ?>
					<table class="form-table">
						<thead>
							<tr valign="top">
								<th scope="column"><?php _e('Original (translated) text');?></th><th scope="column"><?php _e('Override text');?></th><th scope="column"><?php _e('Description (will only be shown in this form)');?></th><th><?php _e('Protect setting?');?></th>
							</tr>
						</thead>
						<?php for($i=0;$i<5;$i++) : ?>
						<tr valign="top">
							<td><textarea cols="40" rows="5" name="defined_constants[name][]"></textarea></td>
							<td class="td_textarea">
							<textarea cols="40" rows="5" name="defined_constants[value][]"></textarea>
							</td>
							<td><input style="width:250px;" type="text" name="defined_constants[desc][]" /></td>
							<td>
								<input class="dc_checkbox_hack" type="hidden" name="defined_constants[internal_warning][]" value="0">
								<input class="dc_checkbox" type="checkbox" name="defined_constants[internal_warning][]" value="1" />
							</td>
						</tr>	
						<?php endfor; ?>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" style="margin:5px;" value="<?php _e('Add') ?>" />
				</p>
			</form>
			</div>
			</div>
		<?php
		$this->close_admin_page();
	}
	
	function setup_admin_page( $title ) {
		?>
		<div class="wrap">
		<h2><?php _e($title); ?></h2>
		<a href="https://donate.ramonfincken.com/" target="_blank" title="Buy me a coffee, opens in new window">Buy me a coffee, opens in new window</a><br/>
		<div class="postbox-container" style="width:100%;">
			<div class="metabox-holder">	
				<div class="meta-box-sortables">
		<?php
	}
	
	function close_admin_page() {
		?>
		</div></div></div></div>
		<?php
	}
}
