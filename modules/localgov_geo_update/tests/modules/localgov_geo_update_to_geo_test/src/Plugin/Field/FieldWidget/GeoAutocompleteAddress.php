<?php

namespace Drupal\localgov_geo_update_to_geo_test\Plugin\Field\FieldWidget;

/**
 * Extends the address widget to use our custom element.
 *
 * @FieldWidget(
 *   id = "geo_entity_address",
 *   label = @Translation("Address autocomplete"),
 *   field_types = {
 *     "address"
 *   },
 * )
 */
class GeoAutocompleteAddress extends AutocompleteAddress {}
