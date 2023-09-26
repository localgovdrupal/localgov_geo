<?php

namespace Drupal\localgov_geo_update;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityFormMode;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\Entity\EntityViewMode;

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

    // $type must be of type view or form.
    if ($type != 'view' && $type != 'form') {
      throw new \Exception('Only form and view display modes can be migrated');
    }

    // Load the config.
    $config_manager = \Drupal::service('config.manager');
    $config_factory = $config_manager->getConfigFactory();
    $current_config_name = 'core.entity_' . $type . '_display.localgov_geo.' . $bundle . '.' . $name;
    $current_display_config = $config_factory->get($current_config_name);

    // Load existing display mode to check that it is a valid display mode.
    if ($type == 'view') {
      $display = EntityViewDisplay::load('localgov_geo.' . $bundle . '.' . $name);
    }
    elseif ($type == 'form') {
      $display = EntityFormDisplay::load('localgov_geo.' . $bundle . '.' . $name);
    }

    // Throw error if not a display mode.
    if (!$display instanceof EntityFormDisplay && !$display instanceof EntityViewDisplay) {
      throw new \Exception('Provided ' . $type . ' display mode ' . $current_config_name . ' is not defined for localgov_geo bundle ' . $bundle . '.');
    }

    // Migrate the base display here.
    static::migrateBaseDisplay($type, $name);

    $new_config_name = 'core.entity_' . $type . '_display.geo_entity.' . $bundle . '.' . $name;
    $new_display_array = [];

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
        if (!empty($new_config['config'])) {
          foreach ($new_config['config'] as &$field) {
            $field = str_replace('localgov_geo', 'geo_entity', $field);
          }
        }
        if (!empty($new_config['modules'])) {
          foreach ($new_config['modules'] as &$module) {
            $module = str_replace('localgov_geo', 'geo_entity', $module);
          }
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

      // Since some of the fields config may have localgov_geo field types,
      // these will need to be migrated to geo_entity equivalents.
      if (is_array($new_config)) {
        foreach ($new_config as $new_config_sub_key => $new_config_sub_value) {
          if (!empty($new_config_sub_value['type'])) {
            $new_config[$new_config_sub_key]['type'] = str_replace('localgov_geo_', 'geo_entity_', $new_config_sub_value['type']);
          }
        }
      }

      // Set the new config.
      $new_display_array[$key] = $new_config;
    }

    // Save the new display mode config.
    // If this is an existing display mode config, its ok to copy it over.
    // However if it is a new display, create a new display entitiy so a
    // UUID is generated.
    $geo_display_name = 'geo_entity.' . $bundle . '.' . $name;
    $has_existing_display = FALSE;
    if ($type == 'view') {
      $has_existing_display = EntityViewDisplay::load($geo_display_name) instanceof EntityViewDisplay;
    }
    elseif ($type == 'form') {
      $has_existing_display = EntityFormDisplay::load($geo_display_name) instanceof EntityFormDisplay;
    }
    if ($has_existing_display) {
      $new_display_config = $config_factory->getEditable($new_config_name);
      foreach ($new_display_array as $key => $value) {
        $new_display_config->set($key, $value);
      }
      $new_display_config->save();
    }
    elseif ($type == 'view') {
      EntityViewDisplay::create($new_display_array)->save();
    }
    elseif ($type == 'form') {
      EntityFormDisplay::create($new_display_array)->save();
    }
  }

  /**
   * Helper function to migrate the base display mode.
   *
   * When migrated a localgov_geo to geo_entity display mode, the base view_mode
   * or form_mode config entity needs to exist. This will migrate the
   * appropirate base display mode, except default which should not be created.
   *
   * @param string $type
   *   Display mode type (either 'view' or 'form').
   * @param string $name
   *   Display mode name (eg. 'default').
   */
  protected static function migrateBaseDisplay(string $type, string $name) {

    // $type must be of type view or form.
    if ($type != 'view' && $type != 'form') {
      throw new \Exception('Only form and view display modes can be migrated');
    }

    // The default base display mode is a Drupal provided one.
    if ($name == 'default') {
      return;
    }

    // Base config.
    $config_manager = \Drupal::service('config.manager');
    $config_factory = $config_manager->getConfigFactory();
    $current_base_display_mode_name = 'core.entity_' . $type . '_mode.localgov_geo.' . $name;
    $current_base_display_mode_config = $config_factory->get($current_base_display_mode_name);

    // Load existing new base display mode.
    if ($type == 'view') {
      $new_base_display_mode = EntityViewMode::load('geo_entity.' . $name);
    }
    elseif ($type == 'form') {
      $new_base_display_mode = EntityFormMode::load('geo_entity.' . $name);
    }

    // Only perform the migration if the new base display does not exist.
    if (!$new_base_display_mode instanceof EntityViewMode && !$new_base_display_mode instanceof EntityFormMode) {
      $base_keys = array_keys($current_base_display_mode_config->getRawData());

      // Loop through and set the new display config.
      foreach ($base_keys as $base_key) {

        // Skip over system keys.
        if ($base_key == 'uuid' || $base_key == '_core') {
          continue;
        }

        // Get the current config.
        $base_display_mode_config = $current_base_display_mode_config->get($base_key);

        // Copy over to new config.
        $new_base_config = $base_display_mode_config;

        // For dependencies, swap out the field storage and type dependencies.
        if ($base_key == 'dependencies') {
          if (!empty($new_config['modules'])) {
            foreach ($new_config['modules'] as &$module) {
              $module = str_replace('localgov_geo', 'geo_entity', $module);
            }
          }
        }

        // Set the id to a corresponding geo_entity.
        if ($base_key == 'id') {
          $new_base_config = 'geo_entity.' . $name;
        }

        // Set the target entity to geo_entity.
        if ($base_key == 'targetEntityType') {
          $new_base_config = 'geo_entity';
        }

        // Set the new config.
        $new_base_array[$base_key] = $new_base_config;
      }

      // Save the new display mode config by creating a new entity,
      // this will generate a new UUID.
      if ($type == 'view') {
        EntityViewMode::create($new_base_array)->save();
      }
      elseif ($type == 'form') {
        EntityFormMode::create($new_base_array)->save();
      }

    }
  }

}
