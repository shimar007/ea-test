<?php

namespace Drupal\Tests\ea_festivals\src\Functional;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Tests\UnitTestCase;
use Drupal\Tests\DocumentElement;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Tests\BrowserTestBase;

/**
 * our main test to check the API service and return response status code
 */
class FestivalsControllerTest extends BrowserTestBase {

    const festivals_api_url = "https://eacp.energyaustralia.com.au/codingtest/api/v1/festivals";

    /**
     * Provides the default theme.
     *
     * @var string
     */
        protected $defaultTheme = 'stark';

    //setting default setup
    public function setUp() : void {
        parent::setUp();
    }

    //testing with get response
    public function testGetResponseCode() {

        //get api response & check
        $this->drupalGet(self::festivals_api_url);

        //check if status code is 200;
        $this->assertSession()->statusCodeEquals(200);
    }
}