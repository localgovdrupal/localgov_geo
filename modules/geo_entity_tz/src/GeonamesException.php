<?php

declare(strict_types=1);

namespace Drupal\geo_entity_tz;

/**
 * Geonames retrieval exception.
 *
 * Exception codes.
 *
 * Service errors:
 * 0 Contains guzzle exception.
 * 1 Missing username.
 * And errors from Geonames:
 * http://www.geonames.org/export/webservice-exception.html
 * 10 Authorization Exception.
 * 11 record does not exist.
 * 12 other error.
 * 13 database timeout.
 * 14 invalid parameter.
 * 15 no result found.
 * 16 duplicate exception.
 * 17 postal code not found.
 * 18 daily limit of credits exceeded.
 * 19 hourly limit of credits exceeded.
 * 20 weekly limit of credits exceeded.
 * 21 invalid input.
 * 22 server overloaded exception.
 * 23 service not implemented.
 * 24 radius too large.
 * 27 maxRows too large.
 */
class GeonamesException extends \RuntimeException {
}
