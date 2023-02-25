(function ($, Drupal, once) {
  var autocomplete;

  function autocompleteSplitValues(value) {
    var result = [];
    var quote = false;
    var current = '';
    var valueLength = value.length;
    var character;

    for (var i = 0; i < valueLength; i++) {
      character = value.charAt(i);

      if (character === '"') {
        current += character;
        quote = !quote;
      } else if (character === ',' && !quote) {
        result.push(current.trim());
        current = '';
      } else {
        current += character;
      }
    }

    if (value.length > 0) {
      result.push($.trim(current));
    }

    return result;
  }

  function searchHandler(event) {
    var options = autocomplete.options;

    if (options.isComposing) {
      return false;
    }
    
    var term = event.target.value;
    
    if (term.length > 0 && options.firstCharacterBlacklist.indexOf(term[0]) !== -1) {
      return false;
    }

    return term.length >= options.minLength;
  }

  function sourceData(request, response) {
    var wrapperId = $(this.element).data('ui-autocomplete').addressWrapperId;
    if (!(wrapperId in autocomplete.cache)) {
      autocomplete.cache[wrapperId] = {};
    }

    var term = request.term;

    function sourceCallbackHandler(data) {
      autocomplete.cache[wrapperId][term] = data;
      response(data);
    }

    if (autocomplete.cache[wrapperId].hasOwnProperty(term)) {
      response(autocomplete.cache[wrapperId][term]);
    } else {
      var options = $.extend({
        success: sourceCallbackHandler,
        data: {
          q: term
        }
      }, autocomplete.ajax);
      $.ajax($(this.element).data('ui-autocomplete').sourceUrl, options);
    }
  }

  function focusHandler() {
    return false;
  }

  function selectHandler(event, ui) {
    var addressField = $('#' + $(event.target).data('ui-autocomplete').addressWrapperId); 
    Object.keys(ui.item.key.drupal_address).forEach( function(key) {
  addressField.find('input[name*="' + key + '"]').val(ui.item.key.drupal_address[key]);
});
    // Relative and allowing for partial id naming for embedded forms.
    event.target.form.dispatchEvent(new CustomEvent('geoEntityGeocode', {
      detail: {
        lon: ui.item.key.longitude,
        lat: ui.item.key.latitude,
      },
    }));
    return false;
  }

  function renderItem(ul, item) {
    return $('<li>').append($('<a>').html(item.label)).appendTo(ul);
  }

  function extractAddressString(addressFieldId) {
    var address = {};
    $('#' + addressFieldId).find('select').each(function() {
      selectName = $(this).attr('name');
      addressPart = selectName.substring(selectName.lastIndexOf('[') + 1, selectName.lastIndexOf(']'));
      address[addressPart] = this.value;
    });
    $('#' + addressFieldId).find('input').each(function() {
      address[$(this).data('ui-autocomplete').addressPart] = this.value;
    });
    return JSON.stringify(address);
  }

  function searchMultipleFieldValues ( value, event ) {
    value = value != null ? value : extractAddressString($(event.target).data('ui-autocomplete').addressWrapperId);

    // Always save the actual value, not the one passed as an argument
    this.term = this._value();
    if ( value.length < this.options.minLength ) {
      return this.close( event );
    }
    if ( this._trigger( "search", event ) === false ) {
      return;
    }

    return this._search( value );
  }

  Drupal.behaviors.geo_entity_address_autocomplete = {
    attach: function attach(context) {
      var $address = $(context).find('.geo-entity-autocomplete');
      var $addressFields = once('autocomplete', '.geo-entity-autocomplete input', context);

      if ($addressFields.length) {
        $addressFields.forEach(function (value, i) {
          $(value).autocomplete(autocomplete.options);
          var uiAutocomplete = $(value).data('ui-autocomplete');
          var inputName = $(value).attr('name');
          uiAutocomplete.search = searchMultipleFieldValues;
          uiAutocomplete._renderItem = autocomplete.options.renderItem;
          uiAutocomplete.addressWrapperId = $address.attr('id');
          uiAutocomplete.sourceUrl = $address.attr('data-autocomplete-path');
          uiAutocomplete.addressPart = inputName.substring(inputName.lastIndexOf('[') + 1, inputName.lastIndexOf(']'));
        });
        $($addressFields).on('compositionstart.autocomplete', function () {
          autocomplete.options.isComposing = true;
        });
        $($addressFields).on('compositionend.autocomplete', function () {
          autocomplete.options.isComposing = false;
        });
      }
    },
    detach: function detach(context, settings, trigger) {
      if (trigger === 'unload') {
        once.remove('autocomplete', '.geo-entity-autocomplete input', context).forEach( function (value) {
          $(value).autocomplete('destroy');
        });
      }
    }
  };
  autocomplete = {
    cache: {},
    splitValues: autocompleteSplitValues,
    options: {
      source: sourceData,
      focus: focusHandler,
      search: searchHandler,
      select: selectHandler,
      renderItem: renderItem,
      minLength: 1,
      firstCharacterBlacklist: '',
      isComposing: false
    },
    ajax: {
      dataType: 'json',
      jsonp: false
    }
  };
  Drupal.geo_entity_autocomplete = autocomplete;
}(jQuery, Drupal, once));
