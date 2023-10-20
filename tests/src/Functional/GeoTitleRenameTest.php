<?php

namespace Drupal\Tests\localgov_geo\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Check hooks renaming Geo to Location work.
 *
 * @group localgov_geo
 */
class GeoTitleRenameTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'system',
    'localgov_geo',
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $settings = [
      'theme' => 'stark',
      'region' => 'content',
    ];
    $this->placeBlock('local_tasks_block', $settings);
    $this->placeBlock('local_actions_block', $settings);
    $this->placeBlock('page_title_block', $settings);

    $user = $this->drupalCreateUser([
      'access geo overview',
      'delete geo',
      'create geo',
      'edit geo',
      'administer geo types',
    ]);
    $this->drupalLogin($user);
  }

  /**
   * Check 'Geo' has been renamed to 'Locations'.
   */
  public function testGeoRenamedToLocation() {

    $this->drupalGet('/admin/content/geo');
    $this->assertSession()->titleEquals('Locations | Drupal');
    $this->assertSession()->responseContains('<h1>Locations</h1>');
    $this->assertSession()->pageTextContains('Add location');
    $this->assertSession()->pageTextNotContains('Add geo');
  }

}
