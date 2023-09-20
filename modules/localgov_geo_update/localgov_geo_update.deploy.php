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

  foreach ($storage->loadMultiple($ids) as $entity) {
    $geo = GeoEntity::create($entity->toArray());
    $geo->save();
    $entity->delete();
    $sandbox['progress']++;
  }

  // Try to update the percentage but avoid division by zero.
  $sandbox['#finished'] = empty($sandbox['max']) ? 1 : ($sandbox['progress'] / $sandbox['max']);

  // Show a status update for the current progress.
  return t("Updated the label for @progress out of @max geo entities.", [
    '@progress' => $sandbox['progress'],
    '@max' => $sandbox['max'],
  ]);
}
