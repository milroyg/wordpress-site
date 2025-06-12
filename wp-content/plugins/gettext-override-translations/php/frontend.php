<?php
class MP_Gettext_Override {
	
	var $constants;
	
	function __construct()
	{
		$this->overrides = get_option( MP_GETTEXT_OVERRIDE_OPTION );
		add_filter( 'gettext', array( &$this, 'mp_gettext_change' ), 20, 3 );
	}
	
	function mp_gettext_change( $translated_text, $text, $domain )
	{
		if( !is_array( $this->overrides ) ) {
			return $translated_text;
		}
		
		foreach( $this->overrides as $override ) {
			$find = $override['name']; 
			$replace = $override['value'];
			// GOGOGO
			if( $translated_text == $find ) {
				return wp_kses( $replace , wp_kses_allowed_html( 'post' ) );
			}
		}
		return $translated_text;
	}
}
