<?php

namespace PostTimeline\components;

use PostTimeline\components\RadioWalker;

/**
 * The RadioWalker Posts Class.
 *
 * @since      1.0.0
 * @package    PostTimeline
 * @subpackage PostTimeline/includes
 * @author     agilelogix <support@agilelogix.com>
 */
class RadioWalker extends \Walker_Category_Checklist {

	function walk( $elements, $max_depth, ...$args ) {
       
        $output = parent::walk( $elements, $max_depth, ...$args );
        $output = str_replace(
            array( 'type="checkbox"', "type='checkbox'" ),
            array( 'type="radio"', "type='radio'" ),
            $output
        );

        return $output;
    }
}
