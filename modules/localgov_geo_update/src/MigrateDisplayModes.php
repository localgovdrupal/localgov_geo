<?php

namespace Drupal\localgov_geo_update;

/**
 * Migrates display modes for form and view from localgov_geo to geo_entity.
 */
class MigrateDisplayModes {

  /**
   * Migrate localgov_geo display mode to geo_entity.
   *
   * Calling this function will overwrite a geo_entity display mode with the
   * corresponsing display from localgov_geo.
   *
   * @param string $bundle
   *   The localgov_geo bundle name.
   * @param string $type
   *   Display mode type (either 'view' or 'form').
   * @param string $name
   *   Display mode name (eg. 'default').
   */
  public static function migrate(string $bundle, string $type, string $name) {

    // Load the config.
    $config_manager = \Drupal::service('config.manager');
    $config_factory = $config_manager->getConfigFactory();
    $current_config_name = 'core.entity_' . $type . '_display.localgov_geo.' . $bundle . '.' . $name;
    $current_display_config = $config_factory->get($current_config_name);
    $new_config_name = 'core.entity_' . $type . '_display.geo_entity.' . $bundle . '.' . $name;
    $new_display_config = $config_factory->getEditable($new_config_name);

    // Get all config keys.
    $keys = array_keys($current_display_config->getRawData());

    // Loop through and set the new display config.
    foreach ($keys as $key) {

      // Skip over system keys.
      if ($key == 'uuid' || $key == '_core') {
        continue;
      }

      // Get the current config.
      $current_config = $current_display_config->get($key);

      // Copy over to new config.
      $new_config = $current_config;

      // For dependencies, swap out the field storage and type dependencies.
      if ($key == 'dependencies') {
        foreach ($new_config['config'] as &$field) {
          $field = str_replace('localgov_geo', 'geo_entity', $field);
        }
      }

      // Set the id to a corresponding geo_entity.
      if ($key == 'id') {
        $new_config = 'geo_entity.' . $bundle . '.' . $name;
      }

      // Set the target entity to geo_entity.
      if ($key == 'targetEntityType') {
        $new_config = 'geo_entity';
      }

      // Set the new config.
      $new_display_config->set($key, $new_config);
    }

    // Save the new display mode config.
    $new_display_config->save();
  }

}
