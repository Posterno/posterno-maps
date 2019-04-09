(function (window, document, $, undefined) {

	'use strict';

	const createHTMLMapMarker = require( '@posterno/google-maps-html-marker' );

	window.PosternoSingleListingMap = {};

	// The library to load the gmap api.
	const loadGoogleMapsApi = require('load-google-maps-api')

	// Parameters for the api request.
	const apiConfig = {
		key: pnoMapSettings.google_maps_api_key,
	}

	// Determine the type of marker selected.
	const markerType = pnoMapSettings.marker_type

	/**
	 * Run the script.
	*/
	PosternoSingleListingMap.init = function () {
		PosternoSingleListingMap.cacheSelectors();
		PosternoSingleListingMap.loadMap();
	}

	/**
	 * Cache required selectors.
	 */
	PosternoSingleListingMap.cacheSelectors = function () {
		PosternoSingleListingMap.map_elements = $('.pno-single-listing-map');
	}

	/**
	 * Load Google maps and then create maps.
	*/
	PosternoSingleListingMap.loadMap = function () {

		// Make sure there's an element before loading the api.
		if ( ! PosternoSingleListingMap.map_elements ) {
			return;
		}

		loadGoogleMapsApi(apiConfig).then(function (googleMaps) {

			$( PosternoSingleListingMap.map_elements ).each(function () {

				var singleMap = $(this)

				if ( singleMap.length ) {

					var singleLat = singleMap.data('lat')
					var singleLng = singleMap.data('lng')

					// Create coordinates for the starting marker.
					var singleLatLng = {
						lat: parseFloat(singleLat),
						lng: parseFloat(singleLng),
					};

					const latLng = new google.maps.LatLng( parseFloat(singleLat), parseFloat(singleLng) );

					const map = new googleMaps.Map( singleMap[0], {
						center: singleLatLng,
						zoom: parseFloat( pnoMapSettings.zoom ),
						fullscreenControl: false,
						streetViewControl: false,
						mapTypeControl: false,
					})

					console.log( markerType )

					/*
					var marker = new google.maps.Marker({
						position: singleLatLng,
						map: map,
					});*/

					let marker = createHTMLMapMarker({
						latlng: latLng,
						map: map,
						html: `<div class="pno-map-marker"><svg baseProfile="basic" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"><path d="M24 0c-9.8 0-17.7 7.8-17.7 17.4 0 15.5 17.7 30.6 17.7 30.6s17.7-15.4 17.7-30.6c0-9.6-7.9-17.4-17.7-17.4z"></path></svg><i class="fas fa-envelope"></i></div>`
					  });

				}

			});

		}).catch(function (error) {
			console.error(error)
		})

	}

	PosternoSingleListingMap.init();

})(window, document, jQuery);
