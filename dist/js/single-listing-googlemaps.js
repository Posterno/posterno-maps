/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./node_modules/@posterno/google-maps-html-marker/index.js":
/*!*****************************************************************!*\
  !*** ./node_modules/@posterno/google-maps-html-marker/index.js ***!
  \*****************************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

/* global google */

module.exports = ({
	OverlayView = google.maps.OverlayView,
	...args
}) => {
	class HTMLMapMarker extends OverlayView {
		constructor() {
			super();
			this.latlng = args.latlng;
			this.html = args.html;
			this.setMap(args.map);
		}

		createDiv() {
			this.div = document.createElement("div");
			this.div.style.position = "absolute";
			if (this.html) {
				this.div.innerHTML = this.html;
			}
			google.maps.event.addDomListener(this.div, "click", event => {
				google.maps.event.trigger(this, "click");
			});
		}

		appendDivToOverlay() {
			const panes = this.getPanes();
			panes.overlayImage.appendChild(this.div);
		}

		positionDiv() {
			const point = this.getProjection().fromLatLngToDivPixel(this.latlng);
			let offset = 25;
			if (point) {
				this.div.style.left = `${point.x - offset}px`;
				this.div.style.top = `${point.y - offset}px`;
			}
		}

		draw() {
			if (!this.div) {
				this.createDiv();
				this.appendDivToOverlay();
			}
			this.positionDiv();
		}

		remove() {
			if (this.div) {
				this.div.parentNode.removeChild(this.div);
				this.div = null;
			}
		}

		getPosition() {
			return this.latlng;
		}

		getDraggable() {
			return false;
		}
	}

	return new HTMLMapMarker();
};


/***/ }),

/***/ "./node_modules/load-google-maps-api/index.js":
/*!****************************************************!*\
  !*** ./node_modules/load-google-maps-api/index.js ***!
  \****************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

var CALLBACK_NAME = '__googleMapsApiOnLoadCallback'

var OPTIONS_KEYS = ['channel', 'client', 'key', 'language', 'region', 'v']

var promise = null

module.exports = function (options) {
  options = options || {}

  if (!promise) {
    promise = new Promise(function (resolve, reject) {
      // Reject the promise after a timeout
      var timeoutId = setTimeout(function () {
        window[CALLBACK_NAME] = function () {} // Set the on load callback to a no-op
        reject(new Error('Could not load the Google Maps API'))
      }, options.timeout || 10000)

      // Hook up the on load callback
      window[CALLBACK_NAME] = function () {
        if (timeoutId !== null) {
          clearTimeout(timeoutId)
        }
        resolve(window.google.maps)
        delete window[CALLBACK_NAME]
      }

      // Prepare the `script` tag to be inserted into the page
      var scriptElement = document.createElement('script')
      var params = ['callback=' + CALLBACK_NAME]
      OPTIONS_KEYS.forEach(function (key) {
        if (options[key]) {
          params.push(key + '=' + options[key])
        }
      })
      if (options.libraries && options.libraries.length) {
        params.push('libraries=' + options.libraries.join(','))
      }
      scriptElement.src =
        'https://maps.googleapis.com/maps/api/js?' + params.join('&')

      // Insert the `script` tag
      document.body.appendChild(scriptElement)
    })
  }

  return promise
}


/***/ }),

/***/ "./resources/js/single-listing-googlemaps.js":
/*!***************************************************!*\
  !*** ./resources/js/single-listing-googlemaps.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

(function (window, document, $, undefined) {
  'use strict';

  var createHTMLMapMarker = __webpack_require__(/*! @posterno/google-maps-html-marker */ "./node_modules/@posterno/google-maps-html-marker/index.js");

  var MapRequiresConsent = pnoMapSettings.requires_consent === '1' ? true : false;
  var MapConsentGiven = pnoMapSettings.consent_enabled === '1' ? true : false;
  window.PosternoSingleListingMap = {}; // The library to load the gmap api.

  var loadGoogleMapsApi = __webpack_require__(/*! load-google-maps-api */ "./node_modules/load-google-maps-api/index.js"); // Parameters for the api request.


  var apiConfig = {
    key: pnoMapSettings.google_maps_api_key
  }; // Determine the type of marker selected.

  var markerType = pnoMapSettings.marker_type;
  /**
   * Run the script.
   */

  PosternoSingleListingMap.init = function () {
    if (MapRequiresConsent && !MapConsentGiven) {
      return;
    }

    PosternoSingleListingMap.cacheSelectors();
    PosternoSingleListingMap.loadMap();
  };
  /**
   * Cache required selectors.
   */


  PosternoSingleListingMap.cacheSelectors = function () {
    PosternoSingleListingMap.map_elements = $('.pno-single-listing-map');
  };
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
        var singleMap = $(this);

        if (singleMap.length) {
          var singleLat = singleMap.data('lat');
          var singleLng = singleMap.data('lng'); // Create coordinates for the starting marker.

          var singleLatLng = {
            lat: parseFloat(singleLat),
            lng: parseFloat(singleLng)
          };
          var map = new googleMaps.Map(singleMap[0], {
            center: singleLatLng,
            zoom: parseFloat(pnoMapSettings.zoom),
            fullscreenControl: false,
            streetViewControl: false,
            mapTypeControl: false
          });

          if (markerType === 'default') {
            var marker = new google.maps.Marker({
              position: singleLatLng,
              map: map
            });
          } else {
            var latLng = new google.maps.LatLng(parseFloat(singleLat), parseFloat(singleLng));

            var _marker = createHTMLMapMarker({
              latlng: latLng,
              map: map,
              html: pnoMapSettings.marker_content
            });
          }
        }
      });
    })["catch"](function (error) {
      console.error(error);
    });
  };

  PosternoSingleListingMap.init();
})(window, document, jQuery);

/***/ }),

/***/ 0:
/*!*********************************************************!*\
  !*** multi ./resources/js/single-listing-googlemaps.js ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! /Users/alessandrotesoro/Local Sites/posterno/app/public/wp-content/plugins/posterno-maps/resources/js/single-listing-googlemaps.js */"./resources/js/single-listing-googlemaps.js");


/***/ })

/******/ });
//# sourceMappingURL=single-listing-googlemaps.js.map