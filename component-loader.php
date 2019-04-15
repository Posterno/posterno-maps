<?php
/**
 * Detect which map provider is enabled and load the appropriate files accordingly.
 *
 * @package     posterno-maps
 * @copyright   Copyright (c) 2019, Sematico LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Determine the currently active maps provider and load the appropriate component file.
 *
 * @return void
 */
function pno_map_component_loader() {

	$maps_disabled = current_theme_supports( 'posterno_disable_maps' );

	if ( $maps_disabled ) {
		return;
	}

	$provider = pno_get_option( 'map_provider', 'googlemaps' );

	if ( $provider === 'googlemaps' ) {
		( new PNO\MapsProvider\GoogleMaps() )->init();
	}

}
add_action( 'after_setup_theme', 'pno_map_component_loader' );
