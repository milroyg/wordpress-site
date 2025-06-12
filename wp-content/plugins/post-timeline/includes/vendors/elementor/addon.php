<?php

namespace PostTimeline\Vendors\Elementor;
use PostTimeline\Vendors\Elementor\Widget;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



/**
 * Class Visual_Portfolio_3rd_Elementor
 */
class Addon {
    
    /**
     * Visual_Portfolio_3rd_Elementor constructor.
     */
    public function __construct() {
        add_action( 'elementor/widgets/widgets_registered', array( $this, 'widgets_registered' ) );
    }

    /**
     * Register widget
     */
    public function widgets_registered() {

        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widget() );
    }
}
