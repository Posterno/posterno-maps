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
class GoogleMaps extends Provider {

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
		add_action( 'pno_before_taxonomy_loop', [ $this, 'taxonomy_map_markup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'single_listing_map' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'taxonomy_map' ], 11 );
	}

	/**
	 * Register frontend scripts.
	 *
	 * @return void
	 */
	public function single_listing_map() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-single-listing-googlemap', PNO_PLUGIN_URL . 'includes/components/posterno-maps/dist/js/single-listing-googlemaps.js', [ 'jquery' ], $version, true );

		if ( is_singular( 'listings' ) ) {

			wp_enqueue_script( 'pno-single-listing-googlemap' );

			$marker_html = false;
			$marker_type = pno_get_option( 'marker_type', 'default' );

			if ( $marker_type !== 'default' ) {

				$marker_template = 'maps/marker-category';

				switch ( $marker_type ) {
					case 'image':
						$marker_template = 'maps/marker-image';
						break;
					case 'custom':
						$marker_template = 'maps/marker-text';
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

	/**
	 * Load taxonomy map js settings and scripts.
	 *
	 * @return void
	 */
	public function taxonomy_map() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-taxonomy-googlemap', PNO_PLUGIN_URL . 'includes/components/posterno-maps/dist/js/taxonomy-googlemaps.js', [ 'jquery' ], $version, true );

		$current_taxonomy = $this->get_current_taxonomy();

		if ( $current_taxonomy && pno_is_map_enabled_for_taxonomy( $current_taxonomy ) && is_tax( $current_taxonomy ) ) {

			wp_enqueue_script( 'pno-taxonomy-googlemap' );

			$js_vars = [
				'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
				'starting_lat'        => pno_get_option( 'map_starting_lat', '40.7484405' ),
				'starting_lng'        => pno_get_option( 'map_starting_lng', '-73.9944191' ),
				'marker_type'         => $this->get_marker_type(),
			];

			wp_localize_script( 'pno-taxonomy-googlemap', 'pnoMapSettings', $js_vars );

		}

	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function taxonomy_map_markup() {

		$current_taxonomy = $this->get_current_taxonomy();

		if ( ! $current_taxonomy || ! pno_is_map_enabled_for_taxonomy( $current_taxonomy ) || ! is_tax( $current_taxonomy ) ) {
			return;
		}

		?>
		<script type="text/javascript">
			var pnoTaxonomyMarkers = <?php echo wp_json_encode( $this->get_current_listings() ); ?>;
		</script>
		<?php

		echo '<div class="pno-taxonomy-map mb-5"></div>';
	}

}
