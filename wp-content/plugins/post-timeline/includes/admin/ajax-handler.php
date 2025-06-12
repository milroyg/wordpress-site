<?php

namespace PostTimeline\Admin;


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
*
* AjaxHandler Responsible for handling all the AJAX Requests for the admin calls
*
* @package    PostTimeline
* @subpackage PostTimeline/Admin/AjaxHandler
* @author     AgileLogix Team <support@agilelogix.com>
*/
class AjaxHandler {

  /**
   * [__construct Register the main route to handle AJAX]
   */
  public function __construct() {

    //  Admin request ptl ajax handler for all requests
    add_action('wp_ajax_ptl_ajax_handler', [$this, 'handle_request']); 

  }

  /**
   * [$ajax_actions All the AJAX actions]
   * @var array
   */
  private $ajax_actions = [];


  /**
   * [add_routes Add all the admin routes that handle the AJAX]
   */
  public function add_routes() {


    $this->register_route('save_setting', 'Request', 'save_setting'); 

    $this->register_route('get_fonts_options', 'Request', 'get_fonts_options');

    $this->register_route('select_modal_posttype', 'Request', 'select_modal_posttype'); 

    $this->register_route('select_layout', 'Request', 'select_layout'); 

    $this->register_route('selected_post_types', 'Request', 'selected_post_types'); 

    $this->register_route('selected_taxonomy', 'Request', 'selected_taxonomy'); 
    
    $this->register_route('generate_shorcode', 'Request', 'generate_shorcode'); 

    // Update ptl tag menu order 
    $this->register_route('update_tags_order', 'Request', 'update_menu_order_tags' );
  }

  /**
   * [register_route Register the AJAX calls for the plugin]
   * @param  [type] $handle        [description]
   * @param  [type] $context_class [description]
   * @param  [type] $action        [description]
   * @return [type]                [description]
   */
  public function register_route($handle, $context_class, $action) {

    $this->ajax_actions[$handle] = [$context_class, $action];
  }


  /**
   * [handle_request Handle the AJAX Request]
   * @return [type] [description]
   */
  public function handle_request() {

    //  ptl-action
    $route = isset($_REQUEST['ptl-action'])? sanitize_text_field(wp_unslash($_REQUEST['ptl-action'])): ''; 

    //  no errors to appear on our AJAX Calls
    error_reporting(0);


    //  Make sure that user is logged in
    if(!current_user_can( 'administrator' )) {
      return $this->json_response(['error' => esc_attr__('Error! path is forbidden.', 'post-timeline')]);
    }

    //  nouce validation for CSRF
    if(!isset($_REQUEST['ptl-nounce']) || !wp_verify_nonce(sanitize_key($_REQUEST['ptl-nounce']), 'ptl-nounce')) {

      return $this->json_response(['nouce' => isset($_REQUEST['ptl-nounce']) ? sanitize_key($_REQUEST['ptl-nounce']) : '', 'error' => esc_attr__('Error! request verification fail.','post-timeline')]);
    }

    //if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //}

    //  validate the route
    if(isset($this->ajax_actions[$route])) {

      //  no errors to appear on our AJAX Calls
      error_reporting(0);

      $ptl_request = $this->ajax_actions[$route]; 

      $class_name = '\\'.__NAMESPACE__ . '\\' .$ptl_request[0];
      $class_inst = new $class_name;
      
      //  is callable method?
      if(!is_callable([$class_inst, $ptl_request[1]])) {
        return $this->json_response(['error' => esc_attr__('Error! method not exist!','ptl_locator')]);
      }

      //  Result of the execution
      $results  = null;

      try {
          
        //  Execute the method
        $results = call_user_func([$class_inst, $ptl_request[1]]);

      } 
      //  Caught in exception
      catch (\Exception $e) {
          
        $results = ['msg' => $e->getMessage()];
      }

      $this->json_response($results);
    }

    //  route not found
    $this->json_response(['error' => esc_attr__('Error! route not found.','ptl_locator')]);
  }


  /**
   * [json_response Send the $data as JSON]
   * @param  [type] $data [description]
   * @return [type]       [description]
   */
  public function json_response($data) {

    echo wp_send_json($data);
    die;
  }
}

