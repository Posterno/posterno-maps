module.exports = function (map, bounds) {

	const createHTMLMapMarker = require('@posterno/google-maps-html-marker');

	// Prepare markup of the button.
	var buttonUI = document.createElement('div')

	buttonUI.id = "pno-gmap-geolocate-button"
	buttonUI.classList.add("pno-gmap-button")
	buttonUI.setAttribute('data-toggle', 'tooltip')
	buttonUI.setAttribute('data-placement', 'right')
	buttonUI.setAttribute('title', pno_settings.labels.requestGeolocation)
	buttonUI.innerHTML = '<i class="fas fa-location-arrow"></i>'

	// Add button to the map.
	map.controls[google.maps.ControlPosition.LEFT_TOP].push(buttonUI);

	// Make the button do something.
	buttonUI.addEventListener('click', function () {

		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function (position) {

				const latLng = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );

				var marker = createHTMLMapMarker({
					latlng: latLng,
					map: map,
					html: jQuery.parseHTML( pnoMapSettings.marker_geolocated )[0].outerHTML
				});

				// Extend map bound and adjust the center.
				bounds.extend(marker.getPosition())
				map.setCenter(bounds.getCenter())
				map.fitBounds(bounds)

			}, function () {

				alert( pno_settings.labels.geolocationFailed )

			});
		} else {

			alert( pno_settings.labels.geolocationNotSupported )

		}

	});

	return buttonUI

};
