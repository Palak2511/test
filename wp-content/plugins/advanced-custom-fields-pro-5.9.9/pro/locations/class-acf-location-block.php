<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('ACF_Location_Block') ) :

class ACF_Location_Block extends ACF_Location {
	
	/**
	 * Initializes props.
	 *
	 * @date	5/03/2014
	 * @since	5.0.0
	 *
	 * @param	void
	 * @return	void
	 */
	public function initialize() {
		$this->name = 'block';
		$this->label = __( "Block", 'acf' );
		$this->category = 'forms';
		$this->object_type = 'block';
	}
	
	/**
	 * Matches the provided rule against the screen args returning a bool result.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule The location rule.
	 * @param	array screen The screen args.
	 * @param	array $field The field group array.
	 * @return	bool
	 */
	public function match_rule( $rule, $screen, $field_group ) {
		
		// Check screen args.
		if( isset($screen['block']) ) {
			$block = $screen['block'];
		} else {
			return false;
		}

		// Compare rule against $block.
		return $this->compare_rule( $rule, $block );
	}
	
	/**
	 * Returns an array of possible values for this rule type.
	 *
	 * @date	9/4/20
	 * @since	5.9.0
	 *
	 * @param	array $rule A location rule.
	 * @return	array
	 */
	public function get_rule_values( $rule ) {
		$choices = array();
		
		// Append block types.
		$blocks = acf_get_block_types();
		if( $blocks ) {
			$choices[ 'all' ] = __( 'All', 'acf' );
			foreach( $blocks as $block ) {
				$choices[ $block['name'] ] = $block['title'];
			}
		} else {
			$choices[ '' ] = __( 'No block types exist', 'acf' );
		}
		
		// Return choices.
		return $choices;
	}
}

// initialize
acf_register_location_type( 'ACF_Location_Block' );

endif; // class_exists check
