# LocalGov Drupal: Geo

Provides a entity for storing, and reusing, geographic information.

Pre-configured to use openstreetmap tiles, and geocoder openstreetmap backend.
The intention is that this can be exchanged for preferred services.

There are two default bundle types:

## Address

Uses the fully featured address field to keep a structured address,
populated from a geofield geocoder search for a point stored in the
geofield.

## Area

TODO. Area stored in the geofield, with a descriptive string in a text field.
