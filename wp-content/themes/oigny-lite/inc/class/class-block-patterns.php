<?php
/**
 * Block Pattern Class
 *
 * @author Jegstudio
 * @package oigny-lite
 */

namespace Oigny_Lite;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WP_Block_Pattern_Categories_Registry;

/**
 * Init Class
 *
 * @package oigny-lite
 */
class Block_Patterns {

	/**
	 * Instance variable
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Class instance.
	 *
	 * @return BlockPatterns
	 */
	public static function instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->register_block_patterns();
	}

	/**
	 * Register Block Patterns
	 */
	private function register_block_patterns() {
		$block_pattern_categories = array(
			'oigny-lite-basic' => array( 'label' => __( 'Oigny lite Basic Patterns', 'oigny-lite' ) ),
		);

		if ( defined( 'GUTENVERSE' ) ) {
			$block_pattern_categories['oigny-lite-gutenverse'] = array( 'label' => __( 'Oigny lite Gutenverse Patterns', 'oigny-lite' ) );
			$block_pattern_categories['oigny-lite-pro'] = array( 'label' => __( 'Oigny lite Gutenverse PRO Patterns', 'oigny-lite' ) );
		}

		$block_pattern_categories = apply_filters( 'oigny-lite_block_pattern_categories', $block_pattern_categories );

		foreach ( $block_pattern_categories as $name => $properties ) {
			if ( ! WP_Block_Pattern_Categories_Registry::get_instance()->is_registered( $name ) ) {
				register_block_pattern_category( $name, $properties );
			}
		}

		$block_patterns = array(
            'oigny-lite-home-core-about',
		);

		if ( defined( 'GUTENVERSE' ) ) {
            $block_patterns[] = 'oigny-lite-home-gutenverse-hero';
            
		}

		$block_patterns = apply_filters( 'oigny-lite_block_patterns', $block_patterns );

		if ( function_exists( 'register_block_pattern' ) ) {
			foreach ( $block_patterns as $block_pattern ) {
				$pattern_file = get_theme_file_path( '/inc/patterns/' . $block_pattern . '.php' );

				register_block_pattern(
					'oigny-lite/' . $block_pattern,
					require $pattern_file
				);
			}
		}
	}
}