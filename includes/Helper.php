<?php
/**
 * Maps provider helper methods.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl_2.0.php GNU Public License
 */

namespace Posterno\MapsProvider;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Helper methods for the maps provider functionalities.
 */
class Helper {

	/**
	 * Get the currently enabled maps provider.
	 *
	 * @return string
	 */
	public static function get_current_provider() {
		return pno_get_option( 'map_provider', 'googlemaps' );
	}

	/**
	 * Check whether maps have been disabled.
	 *
	 * @return bool
	 */
	public static function are_maps_disabled() {
		return current_theme_supports( 'posterno_disable_maps' );
	}

	/**
	 * Get the enabled type of marker.
	 *
	 * @return string
	 */
	public static function get_marker_type() {
		return pno_get_option( 'marker_type', 'default' );
	}

	/**
	 * Get the current taxonomy.
	 *
	 * @return string|boolean
	 */
	public static function get_current_taxonomy() {

		$current_taxonomy = get_queried_object();
		$current_taxonomy = isset( $current_taxonomy->taxonomy ) && ! empty( $current_taxonomy->taxonomy ) ? $current_taxonomy->taxonomy : false;

		return $current_taxonomy;

	}

	/**
	 * Get the name of the marker template to load.
	 *
	 * @return string|boolean
	 */
	public static function get_marker_template_name() {

		$marker_template_name = false;

		$marker_type = self::get_marker_type();

		if ( $marker_type !== 'default' ) {

			$marker_template_name = 'maps/marker-category';

			switch ( $marker_type ) {
				case 'image':
					$marker_template_name = 'maps/marker-image';
					break;
				case 'custom':
					$marker_template_name = 'maps/marker-text';
					break;
			}
		}

		return $marker_template_name;

	}

	/**
	 * Get markers list for the currently active query.
	 *
	 * @return array
	 */
	public static function get_current_listings_markers( $query_args = false ) {

		$listings = [];

		if ( $query_args ) {
			return self::get_listings_markers_from_query( new \WP_Query( $query_args ) );
		}

		if ( have_posts() ) {

			while ( have_posts() ) {

				the_post();

				$marker_html = false;
				$marker_type = self::get_marker_type();

				if ( $marker_type !== 'default' ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( self::get_marker_template_name() );

					$marker_html = ob_get_clean();

				}

				$coordinates = pno_get_listing_coordinates( get_the_id() );

				if ( isset( $coordinates['lat'], $coordinates['lng'] ) ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( 'maps/marker-infowindow' );

					$infowindow = ob_get_clean();

					$listings[] = [
						'title'          => esc_html( get_the_title() ),
						'coordinates'    => $coordinates,
						'marker_content' => esc_js( str_replace( "\n", '', $marker_html ) ),
						'infowindow'     => esc_js( str_replace( "\n", '', $infowindow ) ),
					];
				}
			}
		}

		wp_reset_postdata();

		return $listings;

	}

	/**
	 * Retrieve markers list from a specific query.
	 *
	 * @param WP_Query $query the query from which we're going to get the markers.
	 * @return array
	 */
	public static function get_listings_markers_from_query( $query ) {

		$listings = [];

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {

				$query->the_post();

				$marker_html = false;
				$marker_type = self::get_marker_type();

				if ( $marker_type !== 'default' ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( self::get_marker_template_name() );

					$marker_html = ob_get_clean();

				}

				$coordinates = pno_get_listing_coordinates( get_the_id() );

				if ( isset( $coordinates['lat'], $coordinates['lng'] ) ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( 'maps/marker-infowindow' );

					$infowindow = ob_get_clean();

					$listings[] = [
						'title'          => esc_html( get_the_title() ),
						'coordinates'    => $coordinates,
						'marker_content' => esc_js( str_replace( "\n", '', $marker_html ) ),
						'infowindow'     => esc_js( str_replace( "\n", '', $infowindow ) ),
					];
				}
			}
		}

		wp_reset_postdata();

		return $listings;

	}

}
