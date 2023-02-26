<?php

declare(strict_types=1);

namespace Drupal\geo_entity_tz\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geo_entity_tz\GeonamesException;
use Drupal\tzfield\Plugin\Field\FieldWidget\TimeZoneDefaultWidget;

/**
 * Defines the 'geo_entity_tz_geofield_geonames' field widget.
 *
 * @FieldWidget(
 *   id = "geo_entity_tz_geofield_geonames",
 *   label = @Translation("Geonames lookup from Geofield"),
 *   field_types = {"tzfield"},
 * )
 */
class GeofieldGeonamesWidget extends TimeZoneDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings(): array {
    return [
      'geofield' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {

    $element['foo'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Foo'),
      '#default_value' => $this->getSetting('foo'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary(): array {
    $summary[] = $this->t('Foo: @foo', ['@foo' => $this->getSetting('foo')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state): array {

    // if permissions / config
    return parent::formElement($items, $delta, $element, $form, $form_state);

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state): array {
    if ($geofield = $this->getSetting('geofield')) {
      $geonames = \Drupal::service('geo_entity_tz.geonames_timeze');
      $geo = $form_state->getValue($geofield);
      foreach ($geo as $delta => $value) {
        // @todo Depends on the widget.
        $lat = $value['value']['lat'];
        $lng = $value['value']['lon'];
        try {
          $timezone_id = $geonames->getTimezone($lat, $lng);
        }
        catch (GeonamesException $e) {
          // @todo If it's missing config set error. Otherwise just log.
          return $values;
        }
        $values[$delta] = ['value' => $timezone_id];
      }
    }

    return $values;
  }

}
