(function (window, document, $, undefined) {

	'use strict';

	const createHTMLMapMarker = require('@posterno/google-maps-html-marker');

	const MapRequiresConsent = pnoMapSettings.requires_consent === '1' ? true : false;
	const MapConsentGiven = pnoMapSettings.consent_enabled === '1' ? true : false;

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

		console.log( MapRequiresConsent )

		if ( MapRequiresConsent && ! MapConsentGiven ) {
			return
		}

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
		if (!PosternoSingleListingMap.map_elements) {
			return;
		}

		loadGoogleMapsApi(apiConfig).then(function (googleMaps) {

			$(PosternoSingleListingMap.map_elements).each(function () {

				var singleMap = $(this)

				if (singleMap.length) {

					var singleLat = singleMap.data('lat')
					var singleLng = singleMap.data('lng')

					// Create coordinates for the starting marker.
					var singleLatLng = {
						lat: parseFloat(singleLat),
						lng: parseFloat(singleLng),
					};

					const map = new googleMaps.Map(singleMap[0], {
						center: singleLatLng,
						zoom: parseFloat(pnoMapSettings.zoom),
						fullscreenControl: false,
						streetViewControl: false,
						mapTypeControl: false,
					})

					if ( markerType === 'default' ) {

						var marker = new google.maps.Marker({
							position: singleLatLng,
							map: map,
						});

					} else {

						const latLng = new google.maps.LatLng(parseFloat(singleLat), parseFloat(singleLng));

						let marker = createHTMLMapMarker({
							latlng: latLng,
							map: map,
							html: pnoMapSettings.marker_content
						});

					}

				}

			});

		}).catch(function (error) {
			console.error(error)
		})

	}

	PosternoSingleListingMap.init();

})(window, document, jQuery);
