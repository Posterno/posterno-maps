<?php
/**
 * Maps provider laoder.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2020, Sematico LTD
 * @license     http://opensource.org/licenses/gpl_2.0.php GNU Public License
 */

namespace Posterno\MapsProvider;

use Posterno\MapsProvider\Helper;
use Posterno\MapsProvider\Providers\GoogleMaps;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Maps providers hooks initialization.
 */
class Loader {

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance
	 *
	 * @return static
	 */
	public static function get_instance() {
		return null === self::$instance ? ( self::$instance = new self() ) : self::$instance;
	}

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init() {

		if ( Helper::are_maps_disabled() ) {
			return;
		}

		$provider = Helper::get_current_provider();

		if ( $provider === 'googlemaps' ) {

			add_action( 'pno_before_taxonomy_loop', [ GoogleMaps::class, 'taxonomy_map_markup' ] );
			add_action( 'pno_before_listings_page', [ GoogleMaps::class, 'listings_page_map_markup' ], 10, 2 );

			add_action( 'wp_enqueue_scripts', [ GoogleMaps::class, 'single_listing_map_scripts' ], 11 );
			add_action( 'wp_enqueue_scripts', [ GoogleMaps::class, 'taxonomy_map_scripts' ], 11 );
			add_action( 'wp_enqueue_scripts', [ GoogleMaps::class, 'listings_page_map_scripts' ], 11 );
		}

	}

}
