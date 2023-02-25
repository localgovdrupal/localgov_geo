(function (drupalSettings) {
  Drupal.behaviors.geoEntityGeocodeLeaflet = {
    attach: function (context, settings) {
      Object.keys(settings.geoEntityGeocode.leaflet).forEach(function (formId) {
        once('geoEntityGeocodeLeaflet', document.getElementById(formId)).forEach(function (form) {
          var applyPoint = function (ev) {
            var mapId = settings.geoEntityGeocode.leaflet[formId];
            var jsonElementName = settings.leaflet[mapId].leaflet_widget.jsonElement.substring(1);
            var inputField = ev.target.getElementsByClassName(jsonElementName)[0];
            inputField.value = '{"type":"Point","coordinates":[' + ev.detail.lon + ',' + ev.detail.lat + ']}';
            inputField.dispatchEvent(new Event('change', { bubbles: true } ));
          };
          form.addEventListener('geoEntityGeocode', applyPoint);
        });
      });
    }
  };
})(drupalSettings);
