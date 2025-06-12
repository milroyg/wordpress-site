<?php
// Control core classes for avoid errors
add_action('init', function () {
  if (class_exists('CSF')) {

    //
    // Set a unique slug-like ID
    $prefix = 'ppv';

    //
    // Create a metabox
    CSF::createMetabox($prefix, array(
      'title'     => __('Configuration', 'ppv'),
      'post_type' => 'ppt_viewer',
    ));

    //
    // Create a section
    CSF::createSection($prefix, array(
      'title'  => '',
      'fields' => apply_filters('ppv_pro_metabox', array(
        array(
          'id'    => 'doc',
          'type'  => 'upload',
          'title' => __('Document', 'ppv'),
        ),
        array(
          'id' => 'width',
          'type' => 'dimensions',
          'title' => __('Width', 'ppv'),
          'height' => false,
          'default' => ['width' => '100', 'unit' => '%']
        ),
        array(
          'id' => 'height',
          'type' => 'dimensions',
          'title' => __('Height', 'ppv'),
          'width' => false,
          'default' => ['height' => 600, 'unit' => 'px']
        ),
        array(
          'id' => 'showName',
          'type' => 'switcher',
          'title' => __('Show file name on top', 'ppv'),
          'default' => 0
        ),
        [
          'id' => 'download',
          'type' => 'switcher',
          'title' => __('Show downlaod button', 'ppv'),
          'desc' => __('is not available for google drive and dropbox', 'ppv'),
          'default' => 1
        ],

      ))
    ));

    apply_filters('ppv_settings', '');
  }
}, 0);
