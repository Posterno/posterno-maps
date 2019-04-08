(function (window, document, $, undefined) {

	'use strict';

	window.PosternoSingleListingMap = {};

	const loadGoogleMapsApi = require('load-google-maps-api')

	const apiConfig = {
		key: pnoMapSettings.google_maps_api_key,
	}

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

					const map = new googleMaps.Map( singleMap[0], {
						center: singleLatLng,
						zoom: parseFloat( pnoMapSettings.zoom ),
						fullscreenControl: false,
						streetViewControl: false,
						mapTypeControl: false,
					})

					var marker = new google.maps.Marker({
						position: singleLatLng,
						map: map,
					});

				}

			});

		}).catch(function (error) {
			console.error(error)
		})

	}

	PosternoSingleListingMap.init();

})(window, document, jQuery);
