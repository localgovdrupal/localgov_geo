<?php

declare(strict_types = 1);

namespace Drupal\localgov_geo\Plugin\Geocoder\Provider;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geocoder\ConfigurableProviderUsingHandlerWithAdapterBase;
use Geocoder\Query\GeocodeQuery;

/**
 * Provides an Ordnance Survey Places API-based geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id        = "localgov_os_places",
 *   name      = "LocalGov OS Places",
 *   handler   = "\LocalgovDrupal\OsPlacesGeocoder\Provider\OsPlacesGeocoder",
 *   arguments = {
 *     "genericAddressQueryUrl" = "https://api.os.uk/search/places/v1/find",
 *     "postcodeQueryUrl"       = "https://api.os.uk/search/places/v1/postcode",
 *     "apiKey"                 = "",
 *     "userAgent"              = "LocalGov Drupal"
 *   }
 * )
 */
class LocalgovOsPlacesGeocoder extends configurableProviderUsingHandlerWithAdapterBase {

  /**
   * {@inheritdoc}
   */
  protected function doGeocode($source) {
    /** @var string $source */
    $this->throttle->waitForAvailability($this->pluginId, $this->configuration['throttle'] ?? []);

    $query = GeocodeQuery::create($source)
      ->withData('local_custodian_code', $this->configuration['local_custodian_code']);

    return $this->getHandlerWrapper()->geocodeQuery($query);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {

    return [
      'apply_local_custodian_code' => FALSE,
      'local_custodian_code' => 0,
    ];
  }

  /**
   * {@inheritdoc}
   *
   * Adds the local_custodian_code option.  This option can restrict address
   * lookup within a certain local authority.
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {

    $form = parent::buildConfigurationForm($form, $form_state);

    // Place the local authority filter settings within the
    // "Geocoder Additional Options" fieldset.
    $form['options']['geocoder']['apply_local_custodian_code'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Apply local authority filter'),
      '#description'   => $this->t('Restricts address search to a single local authority'),
      '#default_value' => $this->configuration['apply_local_custodian_code'],
      '#return_value'  => TRUE,
    ];

    $form['options']['geocoder']['local_custodian_code'] = [
      '#type'          => 'number',
      '#title'         => $this->t('Local custodian code'),
      '#description' => $this->t('Code number for a local authority.  See https://osdatahub.os.uk/docs/match/technicalSpecification.  Leave empty or use 0 for country-wide address search.  Note that this can be overridden by users of this plugin such as the LocalGov address lookup Webform element.'),
      '#min'           => 0,
      '#default_value' => $this->configuration['local_custodian_code'],
      '#states'        => [
        'visible' => [
          ':input[name="apply_local_custodian_code"]' => ['checked' => TRUE],
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {

    parent::submitConfigurationForm($form, $form_state);

    $this->configuration['apply_local_custodian_code'] = $form_state->getValue('apply_local_custodian_code');
    if ($this->configuration['apply_local_custodian_code']) {
      $this->configuration['local_custodian_code'] = $form_state->getValue('local_custodian_code');
    }
    else {
      $this->configuration['local_custodian_code'] = 0;
    }
  }

}
