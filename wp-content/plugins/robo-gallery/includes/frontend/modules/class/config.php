<?php
/* 
*      Robo Gallery     
*      Version: 5.0.2 - 91160
*      By Robosoft
*
*      Contact: https://robogallery.co/ 
*      Created: 2025
*      Licensed under the GPLv3 license - http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'WPINC' ) ) exit;

class  roboGalleryModuleConfig{

	private $id = null;
	private $options_id = null;
	private $config = array();	

	protected $core = null;
    protected $gallery = null;

	public function __construct( $core ){
       $this->core = $core;
       $this->gallery = $core->gallery;

       $this->id = $this->gallery->id;
       $this->options_id = $this->gallery->options_id; 
         
       $this->initConfig();

       $this->initCacheConfig();
	}

	public function initCacheConfig(){
		
		$cache_id = $this->getMeta('cache_id');

		if( !$cache_id ) {
			$cache_id = uniqid();
			add_post_meta( $this->options_id, ROBO_GALLERY_PREFIX.'cache_id', $cache_id );
		}

		$this->config[ROBO_GALLERY_PREFIX.'cache_id'] = $cache_id;
	}

	public function initConfig(){
			$this->config = array();
			
			$config = get_post_meta( $this->options_id );
			
			//$this->core->doEvent('gallery.config.get', $config);
	        if( !is_array($config) || !count($config) ) return ;

	        foreach ($config as $key => $value) {

	        	if( !is_array($value) || !isset($value[0]) ) continue ;
	        	$value = $value[0];
	        	if( is_serialized($value) ) $value =  maybe_unserialize( $value );
	        	
	        	//$this->core->doEvent('gallery.config.set.', $key, $value);	        	
	        	$this->config[$key] =  $value;
	        }	
	        //print_r($this->config);
	        //print_r( array_keys($this->config) );
	        //$this->core->doEvent('gallery.config.init', $this->config);			
	}

	public function getMetaRaw( $name ){
    	if( !isset($this->config[$name]) ) return ;
    	return $this->config[$name];
    }

    public function getMeta( $name ){    	
    	return $this->getMetaRaw( ROBO_GALLERY_PREFIX.$name );	
    }

    public function getMetaCur( $name ){
    	return get_post_meta( $this->id, ROBO_GALLERY_PREFIX.$name, true );
    }
}
