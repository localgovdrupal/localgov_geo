<?php

namespace Drupal\localgov_geo_update\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the Geo type configuration entity.
 *
 * @ConfigEntityType(
 *   id = "localgov_geo_type",
 *   label = @Translation("Geo type"),
 *   bundle_of = "localgov_geo",
 *   provider = "localgov_geo",
 *   config_prefix = "localgov_geo_type",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "uuid",
 *     "label_token",
 *   }
 * )
 */
class LocalgovGeoType extends ConfigEntityBundleBase {

  /**
   * The machine name of this geo type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the geo type.
   *
   * @var string
   */
  protected $label;

  /**
   * The token string to use to replace for entity label default.
   *
   * @var string
   */
  protected $label_token;

  /**
   * Return the label token.
   */
  public function labelToken() {
    return $this->label_token;
  }

}
