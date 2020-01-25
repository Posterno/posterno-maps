<?php
/**
 * Google Maps provider methods.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl_2.0.php GNU Public License
 */

namespace Posterno\MapsProvider\Providers;

use Posterno\MapsProvider\Helper;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Collection of Google Maps related methods for frontend usage.
 */
class GoogleMaps {

	/**
	 * Enqueue scripts for the single listing map page.
	 *
	 * @return void
	 */
	public static function single_listing_map_scripts() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-single-listing-googlemap', PNO_PLUGIN_URL . 'vendor/posterno/posterno-maps/dist/js/single-listing-googlemaps.js', [ 'jquery' ], $version, true );

		if ( is_singular( 'listings' ) ) {

			wp_enqueue_script( 'pno-single-listing-googlemap' );

			$marker_html = false;
			$marker_type = Helper::get_marker_type();

			if ( $marker_type !== 'default' ) {

				ob_start();

				posterno()->templates
					->set_template_data(
						[
							'listing_id' => get_queried_object_id(),
						]
					)
					->get_template_part( Helper::get_marker_template_name() );

				$marker_html = ob_get_clean();

			}

			$js_vars = [
				'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
				'zoom'                => pno_get_option( 'single_listing_map_zoom', 12 ),
				'marker_type'         => $marker_type,
				'marker_content'      => esc_js( str_replace( "\n", '', $marker_html ) ),
				'requires_consent'    => pno_get_option( 'map_gdpr', false ),
				'consent_enabled'     => pno_map_was_given_consent(),
			];

			wp_localize_script( 'pno-single-listing-googlemap', 'pnoMapSettings', $js_vars );
		}

	}

	/**
	 * Load scripts related for taxonomy pages maps.
	 *
	 * @return void
	 */
	public static function taxonomy_map_scripts() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-taxonomy-googlemap', PNO_PLUGIN_URL . 'vendor/posterno/posterno-maps/dist/js/taxonomy-googlemaps.js', [ 'jquery' ], $version, true );

		$current_taxonomy = Helper::get_current_taxonomy();

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
				'marker_type'         => Helper::get_marker_type(),
				'marker_geolocated'   => esc_js( str_replace( "\n", '', $marker_geolocated ) ),
				'requires_consent'    => pno_get_option( 'map_gdpr', false ),
				'consent_enabled'     => pno_map_was_given_consent(),
			];

			wp_localize_script( 'pno-taxonomy-googlemap', 'pnoMapSettings', $js_vars );

		}

	}

	/**
	 * Load scripts required for the listings page map.
	 *
	 * @return void
	 */
	public static function listings_page_map_scripts() {

		$version = PNO_VERSION;

		wp_register_script( 'pno-listings-page-googlemap', PNO_PLUGIN_URL . 'vendor/posterno/posterno-maps/dist/js/taxonomy-googlemaps.js', [ 'jquery' ], $version, true );

		ob_start();

		posterno()->templates->get_template_part( 'maps/marker-geolocated' );

		$marker_geolocated = ob_get_clean();

		$js_vars = [
			'google_maps_api_key' => pno_get_option( 'google_maps_api_key' ),
			'starting_lat'        => pno_get_option( 'map_starting_lat', '40.7484405' ),
			'starting_lng'        => pno_get_option( 'map_starting_lng', '-73.9944191' ),
			'zoom'                => pno_get_option( 'map_zoom', 12 ),
			'marker_type'         => Helper::get_marker_type(),
			'marker_geolocated'   => esc_js( str_replace( "\n", '', $marker_geolocated ) ),
			'requires_consent'    => pno_get_option( 'map_gdpr', false ),
			'consent_enabled'     => pno_map_was_given_consent(),
		];

		wp_localize_script( 'pno-listings-page-googlemap', 'pnoMapSettings', $js_vars );

	}

	/**
	 * Load the markup required for the taxonomy maps.
	 *
	 * @return void
	 */
	public static function taxonomy_map_markup() {

		$current_taxonomy = Helper::get_current_taxonomy();

		if ( ! $current_taxonomy || ! pno_is_map_enabled_for_taxonomy( $current_taxonomy ) || ! is_tax( $current_taxonomy ) ) {
			return;
		}

		if ( pno_get_option( 'map_gdpr', false ) && ! pno_map_was_given_consent() ) {
			posterno()->templates->get_template_part( 'maps/consent-message' );
			return;
		}

		?>
		<script type="text/javascript">
			var pnoTaxonomyMarkers = <?php echo wp_json_encode( Helper::get_current_listings_markers() ); ?>;
		</script>
		<?php

		echo '<div class="pno-taxonomy-map mb-5"></div>';

	}

	/**
	 * Load the markup for the listings page markup.
	 *
	 * @param WP_Query $query the query passed through the action.
	 * @param object   $atts list of attributes sent through the shortcode.
	 * @return void
	 */
	public static function listings_page_map_markup( $query, $atts ) {

		if ( ! isset( $atts->map ) || isset( $atts->map ) && $atts->map !== 'yes' ) {
			return;
		}

		if ( pno_get_option( 'map_gdpr', false ) && ! pno_map_was_given_consent() ) {
			posterno()->templates->get_template_part( 'maps/consent-message' );
			return;
		}

		?>
		<script type="text/javascript">
			var pnoTaxonomyMarkers = <?php echo wp_json_encode( Helper::get_listings_markers_from_query( $query ) ); ?>;
		</script>
		<?php

		echo '<div class="pno-taxonomy-map mb-5"></div>';

	}

}
