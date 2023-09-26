<?php

/**
 * @file
 * Hook_deploy_NAME() implementations.
 *
 * These are executed towards the end of `drush deploy`.
 */

use Drupal\geo_entity\Entity\GeoEntity;

/**
 * Recreates localgov_geo as geo entities.
 */
function localgov_geo_update_deploy_geo_entity_conversion(array &$sandbox) {
  // Set up the batch by retrieving all of the IDs.
  $storage = \Drupal::entityTypeManager()->getStorage('localgov_geo');
  if (!isset($sandbox['progress'])) {
    $sandbox['ids'] = $storage->getQuery()->accessCheck(FALSE)->execute();
    $sandbox['max'] = count($sandbox['ids']);
    $sandbox['progress'] = 0;
  }

  // Try to update 25 entities at a time.
  $ids = array_slice($sandbox['ids'], $sandbox['progress'], 25);

  $geocoder_config = Drupal::service('config.factory')->getEditable('geocoder.settings');
  $orig_geocoder_presave_setting = $geocoder_config->get('geocoder_presave_disabled');
  if ($orig_geocoder_presave_setting === FALSE) {
    $geocoder_config->set('geocoder_presave_disabled', TRUE)->save();
  }

  foreach ($storage->loadMultiple($ids) as $localgov_geo_entity) {
    $geo = GeoEntity::create($localgov_geo_entity->toArray());
    $geo->save();
    $localgov_geo_entity->delete();
    $sandbox['progress']++;
  }

  if ($orig_geocoder_presave_setting === FALSE) {
    $geocoder_config->set('geocoder_presave_disabled', FALSE)->save();
  }

  // Try to update the percentage but avoid division by zero.
  $sandbox['#finished'] = empty($sandbox['max']) ? 1 : ($sandbox['progress'] / $sandbox['max']);

  // Show a status update for the current progress.
  return t("Updated the label for @progress out of @max geo entities.", [
    '@progress' => $sandbox['progress'],
    '@max' => $sandbox['max'],
  ]);
}
