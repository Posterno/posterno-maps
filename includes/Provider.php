<?php
/**
 * Provider definition.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl_2.0.php GNU Public License
 */

namespace PNO\MapsProvider;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Main Provider class that inherits common methods for all Providers.
 */
abstract class Provider {

	/**
	 * Get current listings in the query for markers display.
	 *
	 * @return array
	 */
	protected function get_current_listings() {

		$listings = [];

		if ( have_posts() ) {

			while ( have_posts() ) {

				the_post();

				$marker_html = false;
				$marker_type = $this->get_marker_type();

				if ( $marker_type !== 'default' ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( $this->get_marker_template_name() );

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

	protected function get_listings_from_query( $query ) {

		$listings = [];

		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {

				$query->the_post();

				$marker_html = false;
				$marker_type = $this->get_marker_type();

				if ( $marker_type !== 'default' ) {

					ob_start();

					posterno()->templates
						->set_template_data(
							[
								'listing_id' => get_the_id(),
							]
						)
						->get_template_part( $this->get_marker_template_name() );

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
	 * Get the enabled type of marker.
	 *
	 * @return string
	 */
	protected function get_marker_type() {
		return pno_get_option( 'marker_type', 'default' );
	}

	/**
	 * Get the current taxonomy.
	 *
	 * @return string|boolean
	 */
	protected function get_current_taxonomy() {

		$current_taxonomy = get_queried_object();
		$current_taxonomy = isset( $current_taxonomy->taxonomy ) && ! empty( $current_taxonomy->taxonomy ) ? $current_taxonomy->taxonomy : false;

		return $current_taxonomy;

	}

	/**
	 * Get the name of the marker template to load.
	 *
	 * @return string|boolean
	 */
	protected function get_marker_template_name() {

		$marker_template_name = false;

		$marker_type = $this->get_marker_type();

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

}
