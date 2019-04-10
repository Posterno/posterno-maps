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

/**
 * Handles scripts registration for all google maps locations.
 */
class GoogleMaps {

	/**
	 * Get things started.
	 *
	 * @return void
	 */
	public function init() {
		$this->hook();
	}

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'wp_enqueue_scripts', [ $this, 'assets' ], 11 );
	}

	/**
	 * Register frontend scripts.
	 *
	 * @return void
	 */
	public function assets() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-single-listing-googlemap', PNO_PLUGIN_URL . 'includes/components/posterno-maps/dist/js/single-listing-googlemaps.js', [ 'jquery' ], $version, true );

		if ( is_singular( 'listings' ) ) {
			wp_enqueue_script( 'pno-single-listing-googlemap' );
		}

		$marker_html = false;
		$marker_type = pno_get_option( 'marker_type', 'default' );

		if ( $marker_type !== 'default' ) {

			$marker_template = 'maps/marker-category';

			switch ( $marker_type ) {
				case 'image':
					$marker_template = 'maps/marker-image';
					break;
			}

			ob_start();

			posterno()->templates
				->set_template_data(
					[
						'listing_id' => get_queried_object_id(),
					]
				)
				->get_template_part( $marker_template );

			$marker_html = ob_get_clean();

		}

		$js_vars = [
			'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
			'zoom'                => pno_get_option( 'single_listing_map_zoom', 12 ),
			'marker_type'         => $marker_type,
			'marker_content'      => esc_js( str_replace( "\n", '', $marker_html ) ),
		];

		wp_localize_script( 'pno-single-listing-googlemap', 'pnoMapSettings', $js_vars );

	}

}
