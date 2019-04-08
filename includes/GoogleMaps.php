<?php
/**
 * Google Maps provider.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl_2.0.php GNU Public License
 */

namespace PNO\MapsProvider;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class GoogleMaps {

	public function init() {
		$this->hook();
	}

	public function hook() {
		add_action( 'wp_enqueue_scripts', [ $this, 'assets' ], 11 );
	}

	public function assets() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-single-listing-googlemap', PNO_PLUGIN_URL . 'includes/components/posterno-maps/dist/js/single-listing-googlemaps.js', [ 'jquery' ], $version, true );

		if ( is_singular( 'listings' ) ) {
			wp_enqueue_script( 'pno-single-listing-googlemap' );
		}

		$js_vars = [
			'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
		];

		wp_localize_script( 'pno-single-listing-googlemap', 'pnoMapSettings', $js_vars );

	}

}
