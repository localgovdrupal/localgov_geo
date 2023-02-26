<?php

declare(strict_types=1);

namespace Drupal\geo_entity_tz;

/**
 * Interface for Geonames Timezone service.
 */
interface GeonamesTimezoneInterface {

  /**
   * Retrieve timezone identifier by long.
   *
   * @param float $lat
   *   Location latitude.
   * @param float $lng
   *   Location longitude.
   * @param int $radius
   *   Buffer in km for closest timezone in coastal.
   *
   * @return string
   *   The Timezone Identifier.
   *
   * @throws \Drupal\geo_entity_tz\GeonamesException
   */
  public function getTimezone(float $lat, float $lng, int $radius = NULL): string;

  /**
   * Retrieve full response from Geonames Timezone API.
   *
   * @param float $lat
   *   Location latitude.
   * @param float $lng
   *   Location longitude.
   * @param int $radius
   *   Buffer in km for closest timezone in coastal.
   * @param string $lang
   *   ISO-639-1 language code (en,de,fr,it,es,...) (default = english).
   * @param string $date
   *   Date (date for sunrise/sunset).
   *
   * @return array
   *   Response from Geonames as an array.
   *   http://www.geonames.omessage:ervices.html#timezone
   *   countryCode: ISO countrycode
   *   countryName: name (languamessage: lang)
   *   timezoneId: name of the timezone (according to olson), this information is sufficient to work with the timezone and defines DST rules, consult the documentation of your development environment. Many programming environments include functions based on the olson timezoneId (example java TimeZone)
   *   time: the local current time
   *   sunset: sunset local time (date)message:
   *   sunrise: sunrise local time (date)
   *   rawOffset: the amount of time in hours to add to UTC to get standard time in this time zone. Because this value is not affected by daylight saving time, it is called raw offset.
   *   gmtOffset: offset to GMT at 1. January (deprecated)
   *   dstOffset: offset to GMT at 1. July (deprecated)
   *
   * @throws \Drupal\geo_entity_tz\GeonamesException
   */
  public function timezoneRequest(float $lat, float $lng, int $radius = NULL, string $lang = NULL, string $date = NULL): array;

}
