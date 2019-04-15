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
		add_action( 'pno_before_listings_page', [ $this, 'listings_page_map_markup' ], 10, 2 );

		add_action( 'wp_enqueue_scripts', [ $this, 'single_listing_map' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'taxonomy_map' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'listings_page_map' ], 11 );
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
			$marker_type = $this->get_marker_type();

			if ( $marker_type !== 'default' ) {

				ob_start();

				posterno()->templates
					->set_template_data(
						[
							'listing_id' => get_queried_object_id(),
						]
					)
					->get_template_part( $this->get_marker_template_name() );

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

			ob_start();

			posterno()->templates->get_template_part( 'maps/marker-geolocated' );

			$marker_geolocated = ob_get_clean();

			$js_vars = [
				'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
				'starting_lat'        => pno_get_option( 'map_starting_lat', '40.7484405' ),
				'starting_lng'        => pno_get_option( 'map_starting_lng', '-73.9944191' ),
				'zoom'                => pno_get_option( 'map_zoom', 12 ),
				'marker_type'         => $this->get_marker_type(),
				'marker_geolocated'   => esc_js( str_replace( "\n", '', $marker_geolocated ) ),
			];

			wp_localize_script( 'pno-taxonomy-googlemap', 'pnoMapSettings', $js_vars );

		}

	}

	/**
	 * Register scripts for the listings page shortcode.
	 * These are only actually loaded when the shortcode is displayed.
	 *
	 * @return void
	 */
	public function listings_page_map() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-listings-page-googlemap', PNO_PLUGIN_URL . 'includes/components/posterno-maps/dist/js/taxonomy-googlemaps.js', [ 'jquery' ], $version, true );

		ob_start();

		posterno()->templates->get_template_part( 'maps/marker-geolocated' );

		$marker_geolocated = ob_get_clean();

		$js_vars = [
			'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
			'starting_lat'        => pno_get_option( 'map_starting_lat', '40.7484405' ),
			'starting_lng'        => pno_get_option( 'map_starting_lng', '-73.9944191' ),
			'zoom'                => pno_get_option( 'map_zoom', 12 ),
			'marker_type'         => $this->get_marker_type(),
			'marker_geolocated'   => esc_js( str_replace( "\n", '', $marker_geolocated ) ),
		];

		wp_localize_script( 'pno-listings-page-googlemap', 'pnoMapSettings', $js_vars );

	}

	/**
	 * Load taxonomy map markup when in a listing taxonomy.
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

	/**
	 * Load the markup for the listings page shortcode map.
	 *
	 * @param WP_Query $query the query passed through the action.
	 * @param object   $atts list of attributes sent through the shortcode.
	 * @return void
	 */
	public function listings_page_map_markup( $query, $atts ) {

		if ( ! isset( $atts->map ) || isset( $atts->map ) && $atts->map !== 'yes' ) {
			return;
		}

		?>
		<script type="text/javascript">
			var pnoTaxonomyMarkers = <?php echo wp_json_encode( $this->get_listings_from_query( $query ) ); ?>;
		</script>
		<?php

		echo '<div class="pno-taxonomy-map mb-5"></div>';

	}

}
