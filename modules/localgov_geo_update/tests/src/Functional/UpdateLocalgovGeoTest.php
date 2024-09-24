<?php

namespace Drupal\Tests\localgov_geo_update\Functional;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\Entity\EntityFormMode;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\Entity\EntityViewMode;
use Drupal\Tests\BrowserTestBase;
use Drupal\geo_entity\Entity\GeoEntity;
use Drupal\geo_entity\Entity\GeoEntityType;
use Drupal\localgov_geo_update\Entity\LocalgovGeo;
use Drupal\localgov_geo_update\MigrateDisplayModes;

/**
 * Updates a localgov geo entity and checks fields and display works.
 *
 * @group localgov_geo
 */
class UpdateLocalgovGeoTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'text',
    'field_ui',
    'entity_browser',
    'views',
    'localgov_geo',
    'localgov_geo_update',
    'geo_entity',
    'token',
    'localgov_geo_update_to_geo_test',
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
    'access localgov_geo_library entity browser pages',
  ];

  /**
   * An admin test user account.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Have admin user ready to be used in tests.
    $this->adminUser = $this->drupalCreateUser(static::$adminUserPermissions);
  }

  /**
   * Test an update from a localgov_geo entity to a geo_entity.
   */
  public function testUpdateOfLocalgovGeo() {

    // Create a localgov geo entity.
    $localgov_geo_content = [
      'localgov_update_test_id' => 'A',
      'localgov_update_test_details' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Facilisis volutpat est velit egestas.',
      'location' => 'POLYGON ((-0.1993585008993 50.828841938629, -0.19921239223795 50.829163712257, -0.19910083649744 50.829446361526, -0.19874434352409 50.830304044304, -0.19840903152092 50.831120233731, -0.19833790712298 50.831296913359, -0.19880471235399 50.831375372862, -0.1986136746105 50.831840408443, -0.19881294621676 50.831870246742, -0.19866290766629 50.832273208382, -0.19745477112121 50.832092789471, -0.1974365283927 50.832105548049, -0.19592771674281 50.831887071081, -0.1951460749697 50.831772592461, -0.19419278883001 50.83163314846, -0.19416291950753 50.831715335469, -0.1941327315412 50.831798416878, -0.19410778890989 50.831867010165, -0.19410297617528 50.831881325107, -0.19407356351972 50.831962709755, -0.19406156631323 50.831997598302, -0.19404415392846 50.832047691801, -0.19402882935686 50.832091522237, -0.19401488298427 50.832129078627, -0.19399697217488 50.832177365724, -0.19398350356139 50.832213580495, -0.19396878157331 50.832252833619, -0.19395357133887 50.832293697993, -0.19394168149431 50.832325800236, -0.19392234749029 50.832377842517, -0.19391386078801 50.832400878133, -0.19389159093533 50.832460915058, -0.19388541747015 50.832477367341, -0.19386050184436 50.832545241518, -0.19385732332187 50.832554005841, -0.19383078018237 50.832627250855, -0.19380257861577 50.832702988339, -0.19379416483688 50.832725971114, -0.19377590378154 50.832775961498, -0.19376383012302 50.832809140083, -0.19374748030359 50.832853764014, -0.19373144206386 50.832897673293, -0.19372141463787 50.832925667383, -0.19370024828532 50.832984696106, -0.19369515491645 50.832998916751, -0.19366906132623 50.833071539147, -0.19364152027746 50.833148545891, -0.19363624912184 50.833159550161, -0.19361432197955 50.833224029057, -0.19360493846064 50.833250054558, -0.19358710277146 50.833300051497, -0.19356035783866 50.833374822197, -0.19354444728195 50.833419093162, -0.19353347782824 50.833449410932, -0.19351328054486 50.833505396868, -0.19350643497914 50.833524536742, -0.19348161849681 50.833593491576, -0.19347908735986 50.833600197431, -0.19345139020394 50.833673874154, -0.19344616515996 50.833687822947, -0.19342304889109 50.833749519442, -0.19341653222461 50.833767495262, -0.19339588457108 50.833824103741, -0.19339175017567 50.833835551269, -0.19336503236695 50.83390960287, -0.19296074216747 50.833861073053, -0.19276786992857 50.833902125014, -0.19235494699171 50.83385183616, -0.19185326375506 50.83379152974, -0.19132222340158 50.833723716701, -0.19075224605311 50.833657639632, -0.1901010541856 50.833575806222, -0.18948745397472 50.833498558418, -0.18932802544824 50.833544426604, -0.18861384188512 50.833463500926, -0.18864334682735 50.833350751549, -0.18823181101153 50.833302542463, -0.18801915434022 50.833380423472, -0.18773244698001 50.833347668253, -0.18765398300429 50.833338535276, -0.1876346046787 50.833336165782, -0.18760461208847 50.833332480249, -0.18757341798529 50.833328650137, -0.18751416911686 50.833321335438, -0.18742091434941 50.833309823111, -0.18744289568672 50.833233630753, -0.18736164562853 50.833223105286, -0.18726767779337 50.833210943226, -0.18710800599165 50.833190205737, -0.18705798819115 50.833179742738, -0.18689589542804 50.833227819542, -0.18676988544968 50.83321111331, -0.18672734805967 50.833205361964, -0.18666677973646 50.833197288867, -0.18656721977677 50.833184448289, -0.18656103553165 50.833199512521, -0.18652022125125 50.833194372452, -0.18651647183889 50.833206104525, -0.18607782286755 50.833150361602, -0.18608177175745 50.833140080582, -0.18590524219488 50.83311340641, -0.18590615495854 50.833110899361, -0.1858745920669 50.833106290822, -0.18578300429286 50.833094659206, -0.18580987707362 50.833010958062, -0.18552278365309 50.832975892773, -0.18555861828208 50.832872126576, -0.18556506211611 50.832852441298, -0.18559091658903 50.832775320228, -0.1855995042002 50.832751621255, -0.18562593575358 50.832677926644, -0.18563344968672 50.832656279455, -0.18565998568038 50.832579888439, -0.18566781131304 50.832557526622, -0.18569521616989 50.832480699438, -0.1857034849697 50.832457894839, -0.18572961839159 50.832384555303, -0.1857393561524 50.832356827172, -0.18575406509732 50.832314427159, -0.18576460654358 50.832284283303, -0.18576818293392 50.832274086434, -0.18577645064562 50.832249842869, -0.18578782713774 50.83221647437, -0.18579920012832 50.83218319575, -0.18581058007154 50.832149737369, -0.18582191121245 50.832117537304, -0.18583500910136 50.832080058601, -0.18584634369203 50.832047768654, -0.18586933151893 50.831982294369, -0.18589265191054 50.831915566174, -0.18591640497373 50.831848664833, -0.18591798449714 50.831844552426, -0.18594042439189 50.831782217299, -0.18594120718259 50.831780340855, -0.18596482514821 50.831713257535, -0.18596562535843 50.831710931691, -0.18598743215964 50.831646608161, -0.18601103698079 50.831576197067, -0.18559829657672 50.831519236246, -0.18542423634925 50.831492516874, -0.1850074060989 50.831434860806, -0.18487464392878 50.831416157642, -0.18494123392326 50.831226083707, -0.18500556267916 50.831057446664, -0.18505673849239 50.830906150444, -0.18509812304658 50.830783800348, -0.18704783855669 50.831059453103, -0.18712867308448 50.830835648051, -0.18740504901254 50.830874522885, -0.18750906277785 50.830601433239, -0.18828174110123 50.830714567003, -0.1884199266201 50.830354728195, -0.18850104504694 50.830185113023, -0.18871862624213 50.83021367262, -0.1887836732993 50.83000063941, -0.1890257320039 50.830035154337, -0.18919505073935 50.830059546414, -0.18921150537426 50.830008629329, -0.18922029580951 50.829979627131, -0.1892304782679 50.829947678714, -0.189247958222 50.829892280835, -0.1892586637735 50.82985782238, -0.18927230290822 50.829813606642, -0.18927343892193 50.829809936977, -0.18929159772769 50.829751671737, -0.18929906144626 50.829727595314, -0.18931208414423 50.829684629081, -0.18932463237808 50.82964291456, -0.18933456910543 50.829609973044, -0.18935216008269 50.829551698981, -0.1893617677274 50.829519921498, -0.18937152075299 50.829488056337, -0.18938839443534 50.8294336383, -0.18939846950892 50.829400788859, -0.18941536399052 50.829345831537, -0.18942442408794 50.829317193243, -0.18944933929642 50.829238437929, -0.18940764441145 50.829233384113, -0.18943297622823 50.829147530478, -0.18947469190364 50.829152045003, -0.18950048689565 50.829068896561, -0.18950374247664 50.829058154995, -0.18952967297057 50.828971501221, -0.18954774830184 50.828911705763, -0.18956046931014 50.828869184488, -0.18956602461043 50.828850384541, -0.18958451515808 50.828790865322, -0.18960387233687 50.828723625218, -0.18961314711993 50.828693101624, -0.18962423676655 50.828656041005, -0.18964166074235 50.82859839384, -0.18964507555425 50.828587205071, -0.189663602396 50.828526742093, -0.18967075233106 50.828503425214, -0.18967221730597 50.828498591507, -0.18969318047707 50.828430207164, -0.18970081696597 50.828405323987, -0.18971703514508 50.828349843498, -0.1897304076869 50.828304787177, -0.18975666633145 50.828216969296, -0.18976335044867 50.828194679425, -0.18977665181593 50.828150359454, -0.18979480724162 50.828089944651, -0.18980250531116 50.828064567785, -0.18981840914455 50.828012068214, -0.1898192264783 50.828009292939, -0.18984576164819 50.827921659194, -0.18985098074131 50.827904203027, -0.1898529270364 50.827897937839, -0.18985527510484 50.827890096046, -0.18986364191714 50.82786211247, -0.18987233495463 50.827833036759, -0.18988457746979 50.827791857031, -0.18989843027657 50.827745755912, -0.18991278179134 50.827697773913, -0.18992330640679 50.827662826939, -0.18992434882321 50.827659371657, -0.18994006152671 50.827605565058, -0.18995003906675 50.827571544909, -0.18997131710064 50.827498668691, -0.18997425387221 50.827488821505, -0.1900007397675 50.827402446033, -0.1900049508538 50.827389021253, -0.19002688960014 50.827317414352, -0.19003451181134 50.827292890666, -0.19005385316304 50.827229697266, -0.19006381315383 50.827196126503, -0.19007883623265 50.827145456878, -0.1900928618333 50.827098549007, -0.19010528061403 50.827056472647, -0.19012079536776 50.827004101895, -0.19012976477161 50.826974113123, -0.19015766975087 50.826884072328, -0.19022671291898 50.82665185436, -0.19024257296419 50.82659994155, -0.19026688482113 50.826512630013, -0.1902749761834 50.826481200481, -0.19047218501108 50.826495303641, -0.19063838120128 50.826517042965, -0.19163453932781 50.826616126356, -0.19284478439658 50.826791750937, -0.1939961060358 50.827011972415, -0.19461145497869 50.82715844152, -0.19551135462702 50.827406866393, -0.1965612383323 50.827752258288, -0.1973939328338 50.828031781048, -0.19806098400914 50.828228266686, -0.19881153342205 50.828416333693, -0.19897834704336 50.828448811923, -0.19949544005087 50.828543898489, -0.19948106037835 50.828576812822, -0.1993585008993 50.828841938629))',
    ];
    $localgov_geo_entity = LocalgovGeo::create([
      'bundle' => 'update_test',
    ] + $localgov_geo_content);
    $localgov_geo_entity->save();
    $localgov_geo_title = (string) $localgov_geo_entity->label();

    // Run the update scripts as needed.
    // Note this should be an update test, though as we are testing limited
    // number of updates for a specific scenario we can just execute directly.
    localgov_geo_update_update_8002();
    localgov_geo_update_update_8003();

    // Check that the equivilent geo entity exits.
    $geo_type = GeoEntityType::load('update_test');
    $this->assertInstanceOf(GeoEntityType::class, $geo_type);

    // Check that the geo entity has the same fields.
    $stub_geo = GeoEntity::create(['bundle' => 'update_test']);
    $stub_geo->save();
    $this->assertTrue($stub_geo->hasField('localgov_update_test_id'));
    $this->assertTrue($stub_geo->hasField('localgov_update_test_details'));
    $this->assertTrue($stub_geo->hasField('location'));

    // Delete the stub geo beacuse the entity copy intentionally copies
    // the same IDs from localgov_geo.
    $stub_geo->delete();

    // Copy the field content over from the localgov_geo to the geo_entity.
    // Since there should only be 1 entity, we can provide a fake sandbox.
    $sandbox = [];
    localgov_geo_update_update_8004($sandbox);

    // Find the geo entity equivilent of the localgov_geo entity.
    $geo_entities = \Drupal::entityTypeManager()
      ->getStorage('geo_entity')
      ->loadByProperties(['label' => $localgov_geo_title]);
    $geo_entity = reset($geo_entities);
    $this->assertInstanceOf(GeoEntity::class, $geo_entity);

    // Check the geo entity has the same fields.
    $this->assertEquals($localgov_geo_title, (string) $geo_entity->label());
    $this->assertEquals($localgov_geo_content['localgov_update_test_id'], $geo_entity->localgov_update_test_id->value);
    $this->assertEquals($localgov_geo_content['localgov_update_test_details'], $geo_entity->localgov_update_test_details->value);
    $this->assertEquals($localgov_geo_content['location'], $geo_entity->location->value);

    // Check the geo entity has the fields visible on the display.
    // Note the ID is used as part of the label so should not be displayed.
    MigrateDisplayModes::migrate('update_test', 'view', 'default');
    drupal_flush_all_caches();
    $this->drupalGet($geo_entity->toUrl()->toString());
    $this->assertSession()->pageTextContains($localgov_geo_title);
    $this->assertSession()->pageTextContains($localgov_geo_content['localgov_update_test_details']);
    $this->assertSession()->pageTextContains($localgov_geo_content['location']);

    // Check default view display has a UUID.
    $default_view = EntityViewDisplay::load('geo_entity.update_test.default');
    $this->assertNotEmpty($default_view->get('uuid'));

    // Check the geo entity type add form has the fields visible.
    MigrateDisplayModes::migrate('update_test', 'form', 'default');
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/content/geo/add/update_test');
    $this->assertSession()->fieldEnabled('location[0][value]');
    $this->assertSession()->fieldEnabled('localgov_update_test_id[0][value]');
    $this->assertSession()->fieldEnabled('localgov_update_test_details[0][value]');

    // Check the default form display has a UUID.
    $default_form = EntityFormDisplay::load('geo_entity.update_test.default');
    $this->assertNotEmpty($default_form->get('uuid'));

    // New geo values.
    $new_geo = [
      'edit-location-0-value' => 'POLYGON ((-0.10910693477343 50.863281759583, -0.10949751363862 50.86317805183, -0.11080298870983 50.864631245326, -0.11116677146513 50.865080060696, -0.11177643344994 50.865968135741, -0.11285611250185 50.867086396367, -0.11339979397688 50.867577234803, -0.11593420874983 50.867604036533, -0.11670976950246 50.86713051653, -0.11690479935804 50.866556195154, -0.11744165599715 50.865763193992, -0.11743856446797 50.865069887018, -0.11730201956961 50.864429366129, -0.11737371982871 50.863184270672, -0.11705406061646 50.862853012909, -0.1174305933075 50.861730076372, -0.11702339413082 50.8613620148, -0.11608216367471 50.861100565297, -0.11529708645883 50.861033117792, -0.1147936144512 50.860504840296, -0.11408915375145 50.860789627064, -0.11245768044565 50.859833541615, -0.11285501144634 50.858709478087, -0.11169765664349 50.857964865083, -0.10966204513576 50.85753614919, -0.10885519824842 50.856769747253, -0.10696286794504 50.857485025305, -0.10614653426335 50.857948232612, -0.10533892421101 50.857743335344, -0.10455005307472 50.857744307309, -0.10434632693353 50.857428151278, -0.10280911024738 50.857506082095, -0.10291451245111 50.858232976617, -0.10320108852482 50.859074077377, -0.10349964297226 50.859723870649, -0.1039705660753 50.860420925508, -0.10449707374334 50.860918688232, -0.10481991999126 50.861061669207, -0.10525538817252 50.861161400389, -0.10585512137307 50.861236787382, -0.10666584604068 50.861277679115, -0.10739028170006 50.861379586129, -0.10792154352126 50.861558494837, -0.1082255690515 50.861879682445, -0.10868574126421 50.862646406746, -0.10910693477343 50.863281759583))',
      'edit-localgov-update-test-id-0-value' => 'B',
      'edit-localgov-update-test-details-0-value' => 'Pulvinar neque laoreet suspendisse interdum. Faucibus purus in massa tempor. Sodales neque sodales ut etiam. Viverra maecenas accumsan lacus vel facilisis volutpat.',
    ];

    // Submit a new update_test geo and check it saves.
    $this->submitForm($new_geo, 'Save');
    $this->assertSession()->pageTextContains('Parking zone ' . $new_geo['edit-localgov-update-test-id-0-value']);
    $this->assertSession()->pageTextContains($new_geo['edit-location-0-value']);
    $this->assertSession()->pageTextContains($new_geo['edit-localgov-update-test-details-0-value']);

    // Migrate the remaining display modes.
    // Execute the update hook.
    localgov_geo_update_update_8808();

    // Load expected new display modes.
    $map_view = EntityViewDisplay::load('geo_entity.update_test.map');
    $map_base_mode = EntityViewMode::load('geo_entity.map');
    $this->assertInstanceOf(EntityViewDisplay::class, $map_view);
    $this->assertInstanceOf(EntityViewMode::class, $map_base_mode);
    $this->assertNotEmpty($map_view->get('uuid'));
    $this->assertNotEmpty($map_base_mode->get('uuid'));

    $meta_view = EntityViewDisplay::load('geo_entity.update_test.meta');
    $meta_base_mode = EntityViewMode::load('geo_entity.meta');
    $this->assertInstanceOf(EntityViewDisplay::class, $meta_view);
    $this->assertInstanceOf(EntityViewMode::class, $meta_base_mode);
    $this->assertNotEmpty($meta_view->get('uuid'));
    $this->assertNotEmpty($meta_base_mode->get('uuid'));

    $inline_form_view = EntityFormDisplay::load('geo_entity.update_test.inline');
    $inline_form_base_mode = EntityFormMode::load('geo_entity.inline');
    $this->assertInstanceOf(EntityFormDisplay::class, $inline_form_view);
    $this->assertInstanceOf(EntityFormMode::class, $inline_form_base_mode);
    $this->assertNotEmpty($inline_form_view->get('uuid'));
    $this->assertNotEmpty($inline_form_base_mode->get('uuid'));

    $label_form_view = EntityFormDisplay::load('geo_entity.update_test.label');
    $label_form_base_mode = EntityFormMode::load('geo_entity.label');
    $this->assertInstanceOf(EntityFormDisplay::class, $label_form_view);
    $this->assertInstanceOf(EntityFormMode::class, $label_form_base_mode);
    $this->assertNotEmpty($label_form_view->get('uuid'));
    $this->assertNotEmpty($label_form_base_mode->get('uuid'));

    // Check that a geo with the localgov_address field has migrated.
    // Since this will have migrated with the remainder of the displays it
    // should be present so we can load it directly and check which field widget
    // plugin it is using.
    $simple_address_form_view = EntityFormDisplay::load('geo_entity.simple_address.default');
    $form_display_fields = $simple_address_form_view->get('content');
    // $this->drupalGet('admin/content/geo/add/simple_address');
    $this->assertEquals('geo_entity_address', $form_display_fields['postal_address']['type']);

    // Run the entity browser update.
    localgov_geo_update_update_8809();

    // Check user can access geo entitiy browser.
    $this->assertTrue($this->adminUser->hasPermission('access geo_entity_library entity browser pages'));
  }

}
