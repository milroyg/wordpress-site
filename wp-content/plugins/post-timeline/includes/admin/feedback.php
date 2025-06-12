<?php

namespace PostTimeline\Admin;

/**
 * The file that defines the users feedback class
 *
 * @since      5.2.14
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     agilelogix <support@agilelogix.com>
 */


class Feedback  {

  /**
   * The version of this plugin.
   *
   * @since    0.0.1
   * @access   private
   * @var      string    $version    The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @since    0.0.1
   * @param      string    $plugin_name       The name of this plugin.
   * @param      string    $version    The version of this plugin.
   */
  public function __construct( $version ) {

    global $pagenow;

    $this->version     = $version;

    // FeedBack form
    if ( $pagenow == 'plugins.php') {

        add_action( 'admin_footer', [ $this, 'box_html' ] );
    }

    add_filter( 'plugin_action_links_'.POST_TIMELINE_BASE_PATH.'/post-timeline.php', array( $this, 'add_action_link' ), 10, 2 );  
    
  }
  /**
   * Ajax Post-Timeline deactivate feedback.
   *
   * Send the user feedback when Post-Timeline is deactivated.
   *
   * Fired by `ajax_post_timeline_deactivate_feedback` action.
   *
   * @since 1.0.0
   * @access public
   */
  public function ajax_post_timeline_deactivate_feedback() {
    
    if ((isset($_POST['formData']))) {

      $_post = array_map('sanitize_text_field', wp_unslash($_POST['formData']));

      if ( ! isset( $_post['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key($_post['_wpnonce']), '_post_timeline_deactivate_feedback_nonce' ) ) {
        wp_send_json_error();
      }

      if($_post['reason_key'] == 'temporary_deactivation') {
        echo json_encode(['success' => true]);die;
      }

      $reason_text = '';
      $reason_key = '';
      if ( ! empty( $_post['reason_key'] ) ) {
        $reason_key = $_post['reason_key'];
      }

      if ( ! empty( $_post[ "reason_{$reason_key}" ] ) ) {
        $reason_text = $_post[ "reason_{$reason_key}" ];
      }

      
      self::send_feedback( $reason_key, $reason_text );

      wp_send_json_success();
    }
  }


  public static function send_feedback( $feedback_key, $feedback_text ) {

    $admin_email = get_option('admin_email');
    $message     = 'FeedBack Key: '.$feedback_key.'<br>';
    $message     .= 'FeedBack: '.$feedback_text;

    //php mailer variables
    $to       = POST_TIMELINE_EMAIL;
    $subject  = esc_attr__("Post Timeline WordPress Plugin FeedBack version ".POST_TIMELINE_VERSION,'post-timeline');
    $headers = array('Content-Type: text/html; charset=UTF-8','From: '. $admin_email . "\r\n" . 'Reply-To: ' . $admin_email . "\r\n");

    // send email
    $sent = wp_mail($to,$subject, $message, $headers);

  }


  /**
   * [box_html render the feedback box for the plugin]
   * @return [type] [description]
   */
  public function box_html() {
    
    //  Register
    wp_register_script('post-timeline-feedback', POST_TIMELINE_URL_PATH . 'admin/js/feedback.js', array('jquery'), $this->version, false );

    //  Enqueue
    wp_enqueue_script('post-timeline-feedback');


    $deactivate_reasons = [
      'temporary_deactivation' => [
        'title' => esc_html__( 'It\'s a temporary deactivation', 'post-timeline' ),
        'input_placeholder' => '',
      ],
      'no_longer_needed' => [
        'title' => esc_html__( 'I no longer need the plugin', 'post-timeline' ),
        'input_placeholder' => '',
      ],
      'found_a_better_plugin' => [
        'title' => esc_html__( 'I found a better plugin', 'post-timeline' ),
        'input_placeholder' => esc_html__( 'Please share which plugin', 'post-timeline' ),
      ],
      'couldnt_get_the_plugin_to_work' => [
        'title' => esc_html__( 'I couldn\'t get the plugin to work', 'post-timeline' ),
        'input_placeholder' => '',
      ],
      // 'post_timeline_pro' => [
      //   'title' => esc_html__( 'I have Post-Timeline Pro', 'post-timeline' ),
      //   'input_placeholder' => '',
      //   'alert' => esc_html__( 'Wait! Don\'t deactivate Post-Timeline. You have to activate both Post-Timeline and Post-Timeline Pro in order for the plugin to work.', 'post-timeline' ),
      // ],
      'other' => [
        'title' => esc_html__( 'Other', 'post-timeline' ),
        'input_placeholder' => esc_html__( 'Please share the reason', 'post-timeline' ),
      ],
    ];

    ?>
    <div class="ptl-cont ptl_modal fade ptl-feedback-popup" id="ptl-feedback-popup" tabindex="-1" role="dialog" aria-labelledby="ptl-feedback-ptl_modal" aria-hidden="true">
      <div class="ptl_modal-dialog" role="document">
        <div class="ptl_modal-content">
          <div class="ptl_modal-header">
            <h5 class="ptl_modal-title" id="ptl-feedback-ptl_modal"><?php echo esc_html__( 'Plugin Feedback', 'post-timeline' ) ?></h5>
            <button type="button" class="close" data-dismiss="ptl_modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="ptl_modal-body">
            <form id="ptl-feedback-form" method="post">
              <?php wp_nonce_field( '_post_timeline_deactivate_feedback_nonce' ); ?>
              <?php foreach ( $deactivate_reasons as $reason_key => $reason ) : 
                $def_checked = '';
                if ($reason_key == 'temporary_deactivation') {
                  $def_checked = ('checked="checked"');
                }
                ?>
                <div class="form-group">
                  <input id="ptl-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" <?php echo esc_attr($def_checked); ?> class="ptl-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo esc_attr( $reason_key ); ?>" />
                  <?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
                  
                    <input class="ptl-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />

                  <?php endif; ?>
                  <label for="ptl-deactivate-feedback-<?php echo esc_attr( $reason_key ); ?>" class="ptl-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
                </div>
                  

                <?php if ( ! empty( $reason['alert'] ) ) : ?>
                  <div class="ptl-feedback-text"><?php echo esc_html( $reason['alert'] ); ?></div>
                <?php endif; ?>

              <?php endforeach; ?>

            </form>
          </div>
          <div class="ptl_modal-footer">
            <button type="button" class="btn btn-secondary" id="ptl-feedback-skip" data-dismiss="ptl_modal"><?php echo esc_attr__('Deactivate','post-timeline') ?></button>
            <button type="button" class="btn btn-primary" id="ptl-feedback-submit" data-dismiss="ptl_modal"><?php echo esc_attr__('Submit & Deactivate','post-timeline') ?></button>
          </div>
        </div>
      </div>
    </div>
    <?php
  }


  /**
   * [add_action_link render the settings button for the plugin]
   * @return [type] [description]
   */
  public function add_action_link( $links, $file ) {

    if ( $file != POST_TIMELINE_BASE_PATH.'/post-timeline.php' ) {

      return $links;
    }

    $settings_url = admin_url( 'edit.php?post_type=post-timeline&page=timeline-settings' );
    $settings_link = '<a href="' . esc_url( $settings_url ) . '" >' . __( 'Settings', 'post-timeline' ) . '</a>';
    array_unshift( $links, $settings_link );

    return $links;

  }




}
