  Drupal.behaviors.geoEntityGeocodeGeofield = {
    attach: function (context, settings) {
      Object.keys(settings.geoEntityGeocode.geofield).forEach(function (formId) {
        once('geoEntityGeocodeGeofield', document.getElementById(formId)).forEach(function (form) {
          var applyPoint = function (ev) {
            var lon = context.getElementById(settings.geoEntityGeocode.geofield[formId].lon);
            var lat = context.getElementById(settings.geoEntityGeocode.geofield[formId].lat);
            lon.value = ev.detail.lon;
            lat.value = ev.detail.lat;
            lat.dispatchEvent(new Event('change', { bubbles: true } ));
          };
          form.addEventListener('geoEntityGeocode', applyPoint);
        });
      });
    }
  };
