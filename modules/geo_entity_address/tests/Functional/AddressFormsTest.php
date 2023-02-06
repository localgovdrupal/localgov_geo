<?php

namespace Drupal\Tests\geo_entity_address\Functional;

use Drupal\Component\Utility\Html;
use Drupal\geo_entity\Entity\GeoEntity;
use Drupal\Tests\BrowserTestBase;

/**
 * Ensures that geo_entity UI works.
 *
 * @group geo_entity
 */
class AddressFormsTest extends BrowserTestBase {

  /**
   * Set to TRUE to strict check all configuration saved.
   *
   * @var bool
   */

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'text',
    'field_ui',
    'geo_entity',
    'geo_entity_address',
    'token',
    'geocoder',
    'geofield_map',
  ];

  /**
   * Permissions for the admin user that will be logged-in for test.
   *
   * @var array
   */
  protected static $adminUserPermissions = [
    'delete geo',
    'create geo',
    'edit geo',
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
   * Test basic ceate, edit, delete.
   */
  public function testCrud() {
    $session = $this->getSession();
    $page = $session->getPage();
    $assert_session = $this->assertSession();

    $line_1 = $this->randomString();
    $locality = $this->randomString();
    $post_code = 'W1 1AA';
    $this->drupalGet('/admin/content/geo/add/address');
    $page->fillField('postal_address[0][address][address_line1]', $line_1);
    $page->fillField('postal_address[0][address][locality]', $locality);
    $page->fillField('postal_address[0][address][postal_code]', $post_code);
    $page->fillField('location[0][value][lat]', '52.123456');
    $page->fillField('location[0][value][lon]', '0.987654');
    $page->fillField('accessibility[0][value]', $this->randomString());
    $page->pressButton('Save');

    // Saved new enity.
    $assert_session->responseContains(Html::escape("$line_1") . "<br />\n" . Html::Escape($locality) . "<br />\n$post_code");
    $assert_session->responseContains('52.123456');
    $assert_session->responseContains('0.987654');
    $assert_session->pageTextContains('New geo');

    // Token generated label.
    $geo = GeoEntity::load(1);
    $this->assertEquals("$line_1\n$locality\n$post_code\nUnited Kingdom", $geo->label->value);
  }

}
