module.exports = function (map) {

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

				var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};

				/*
				var marker = createHTMLMapMarker({
					latlng: latLng,
					map: map,
					html: $.parseHTML(Listing.marker_content)[0]['wholeText']
				});*/

			}, function () {

				alert( pno_settings.labels.geolocationFailed )

			});
		} else {

			alert( pno_settings.labels.geolocationNotSupported )

		}

	});

	return buttonUI

};
