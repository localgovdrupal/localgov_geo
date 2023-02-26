<?php

namespace Drupal\Tests\geo_entity_tz\Unit;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\geo_entity_tz\GeonamesException;
use Drupal\geo_entity_tz\GeonamesTimezone;
use Drupal\Tests\UnitTestCase;
use GuzzleHttp\ClientInterface;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Test Geonames connection service.
 *
 * @group geo_entity_tz
 */
class GeonamesServiceTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
  }

  /**
   * Test configuration.
   */
  public function testConfigMissing(): void {
    $prophesy = $this->prophesize(ImmutableConfig::class);
    $prophesy->get('username')->willReturn('');
    $config_item = $prophesy->reveal();
    $prophesy = $this->prophesize(ConfigFactoryInterface::class);
    $prophesy->get('geo_entity_tz.settings')->willReturn($config_item);
    $config = $prophesy->reveal();

    $prophesy = $this->prophesize(ClientInterface::class);
    $client = $prophesy->reveal();

    $service = new GeonamesTimezone($client, $config);
    $this->expectException(GeonamesException::class);
    $this->expectExceptionCode(1);
    $service->getTimezone(0, 0);
  }

  /**
   * Test error.
   */
  public function testError(): void {
    $prophesy = $this->prophesize(ImmutableConfig::class);
    $prophesy->get('username')->willReturn('test');
    $prophesy->get('token')->willReturn(NULL);
    $config_item = $prophesy->reveal();
    $prophesy = $this->prophesize(ConfigFactoryInterface::class);
    $prophesy->get('geo_entity_tz.settings')->willReturn($config_item);
    $config = $prophesy->reveal();

    $prophesy = $this->prophesize(StreamInterface::class);
    $prophesy->getContents()->willReturn('{"status":{"message":"radius is too large.","value":24}}');
    $stream = $prophesy->reveal();
    $prophesy = $this->prophesize(ResponseInterface::class);
    $prophesy->getBody()->willReturn($stream);
    $response = $prophesy->reveal();
    $prophesy = $this->prophesize(ClientInterface::class);
    $prophesy->request('GET', Argument::type('string'), Argument::type('array'))->willReturn($response);
    $client = $prophesy->reveal();

    $service = new GeonamesTimezone($client, $config);
    $this->expectException(GeonamesException::class);
    $this->expectExceptionCode(24);
    $service->getTimezone(0, 0, 10000);
  }

  /**
   * Test response.
   */
  public function testResponse(): void {
    $prophesy = $this->prophesize(ImmutableConfig::class);
    $prophesy->get('username')->willReturn('test');
    $prophesy->get('token')->willReturn(NULL);
    $config_item = $prophesy->reveal();
    $prophesy = $this->prophesize(ConfigFactoryInterface::class);
    $prophesy->get('geo_entity_tz.settings')->willReturn($config_item);
    $config = $prophesy->reveal();

    $prophesy = $this->prophesize(StreamInterface::class);
    $prophesy->getContents()->willReturn('{"sunrise":"2023-02-18 07:19","lng":10.2,"countryCode":"AT","gmtOffset":1,"rawOffset":1,"sunset":"2023-02-18 17:47","timezoneId":"Europe/Vienna","dstOffset":2,"countryName":"Austria","time":"2023-02-18 14:50","lat":47.01}');
    $stream = $prophesy->reveal();
    $prophesy = $this->prophesize(ResponseInterface::class);
    $prophesy->getBody()->willReturn($stream);
    $response = $prophesy->reveal();
    $prophesy = $this->prophesize(ClientInterface::class);
    $prophesy->request('GET', Argument::type('string'), [
      'query' => [
        'username' => 'test',
        'lat' => 47.01,
        'lng' => 10.2,
      ]
    ])->willReturn($response);
    $client = $prophesy->reveal();

    $service = new GeonamesTimezone($client, $config);
    $this->assertEquals('Europe/Vienna', $service->getTimezone(47.01, 10.2));
  }

}
