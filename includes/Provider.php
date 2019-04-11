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

abstract class Provider {

	protected function get_current_listings() {

		$listings = [];

		if ( have_posts() ) {

			while ( have_posts() ) {

				the_post();

				$listings[] = [
					'title'       => esc_html( get_the_title() ),
					'coordinates' => pno_get_listing_coordinates( get_the_id() ),
				];

			}
		}

		return $listings;

	}

	protected function get_marker_type() {
		return pno_get_option( 'marker_type', 'default' );
	}

}
