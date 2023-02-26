<?php

declare(strict_types=1);

namespace Drupal\geo_entity_tz;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Service description.
 */
class GeonamesTimezone implements GeonamesTimezoneInterface {

  /**
   * URL of the GeoNames web service.
   *
   * @var string
   */
  protected $url = 'https://secure.geonames.org';

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $client;

  /**
   * Module settings array.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $settings;

  /**
   * Constructs a GeonamesTimezone object.
   *
   * @param \GuzzleHttp\ClientInterface $client
   *   The HTTP client.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ClientInterface $client, ConfigFactoryInterface $config_factory) {
    $this->client = $client;
    $this->settings = $config_factory->get('geo_entity_tz.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function getTimezone(float $lat, float $lng, int $radius = NULL): string {
    $result = $this->timezoneRequest($lat, $lng, $radius);
    return $result['timezoneId'];
  }

  /**
   * {@inheritdoc}
   */
  public function timezoneRequest(float $lat, float $lng, int $radius = NULL, string $lang = NULL, string $date = NULL): array {
    if (empty($this->settings->get('username'))) {
      throw new GeonamesException('Geonames username is required.', 1);
    }
    $params = [
      'username' => $this->settings->get('username'),
      'lat' => $lat,
      'lng' => $lng,
    ];
    if (!is_null($this->settings->get('token'))) {
      $params['token'] = $this->settings['token'];
    }
    if (!is_null($radius)) {
      $params['radius'] = $radius;
    }
    if (!is_null($lang)) {
      $params['lang'] = $lang;
    }
    if (!is_null($date)) {
      $params['date'] = $date;
    }

    try {
      $response = $this->client->request('GET', $this->url . '/timezoneJSON', ['query' => $params]);
    }
    catch (GuzzleException $e) {
      throw new GeonamesException('Error retrieving data', 0, $e);
    }

    $values = json_decode($response->getBody()->getContents(), TRUE);

    if (isset($values['status'])) {
      throw new GeonamesException($values['status']['message'], $values['status']['value']);
    }

    return $values;
  }

}


