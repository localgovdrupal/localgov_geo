<?php

namespace Drupal\Tests\localgov_geo\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\geo_entity\Entity\GeoEntityType;

/**
 * Ensures that localgov_geo UI works.
 *
 * @group localgov_geo
 */
class GeoBundleCreationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'text',
    'field_ui',
    'localgov_geo',
    'geo_entity',
    'token',
  ];

  /**
   * Permissions for the admin user that will be logged-in for test.
   *
   * @var array
   */
  protected static $adminUserPermissions = [
    'access geo overview',
    'delete geo',
    'create geo',
    'edit geo',
    'administer geo types',
  ];

  /**
   * An admin test user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * A non-admin test user account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $nonAdminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Have two users ready to be used in tests.
    $this->adminUser = $this->drupalCreateUser(static::$adminUserPermissions);
    $this->nonAdminUser = $this->drupalCreateUser([]);
    // Start off logged in as admin.
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Tests the geo_entity type creation form with only the mandatory options.
   */
  public function testMediaTypeCreationForm() {
    $machine_name = mb_strtolower($this->randomMachineName());

    $this->drupalGet('/admin/structure/geo_types/add');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldExists('label')->setValue($this->randomString());
    $this->assertSession()->fieldExists('id')->setValue($machine_name);
    $this->assertSession()->fieldExists('label_token')->setValue('token [geo_entity:id]');
    $this->assertSession()->buttonExists('Save')->press();
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->addressEquals('admin/structure/geo_types');

    $bundle = GeoEntityType::load($machine_name);
    $this->assertInstanceOf(GeoEntityType::class, $bundle);
    $this->assertEquals('token [geo_entity:id]', $bundle->labelToken());
  }

}
