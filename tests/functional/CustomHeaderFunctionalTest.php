<?php

/**
 * @file tests/functional/CustomHeaderFunctionalTest.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2000-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class CustomHeaderFunctionalTest
 * @package plugins.generic.customHeader
 *
 * @brief Functional tests for the custom header plugin.
 */

import('lib.pkp.tests.WebTestCase');

class CustomHeaderFunctionalTest extends WebTestCase {
	/**
	 * Enable and exercise the plugin
	 */
	function testCustomHeader() {
		$this->open(self::$baseUrl);

		$this->logIn('admin', 'admin');
		$this->waitForElementPresent($selector='link=Website');
		$this->clickAndWait($selector);
		$this->click('link=Plugins');

		// Find and enable the plugin
		$this->waitForElementPresent($selector = '//input[starts-with(@id, \'select-cell-customheaderplugin-enabled\')]');
		$this->assertElementNotPresent('link=Custom Header Plugin'); // Plugin should be disabled
		$this->click($selector); // Enable plugin
		$this->waitForElementPresent('//div[contains(.,\'The plugin "Custom Header Plugin" has been enabled.\')]');

		// Edit the plugin settings
		$this->waitForElementPresent($selector='//a[contains(@id,\'customheaderplugin-settings\')]');
		$this->click($selector);
		$this->waitForElementPresent($selector='//textarea[starts-with(@id, \'headerContent-\')]');
		$this->type($selector, '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>');
		$this->type('//textarea[starts-with(@id, \'footerContent-\')]', '<a class="twitter-timeline" href="https://twitter.com/pkp?ref_src=twsrc%5Etfw">Tweets by pkp</a>');
		$this->click('//form[@id=\'customHeaderSettingsForm\']//button[text()=\'OK\']');
		$this->waitForElementPresent('//div[contains(.,\'Your changes have been saved.\')]');

		// Check that a Twitter timeline appears on the homepage.
		sleep(5);
		$this->open(self::$baseUrl);
		$this->waitForElementPresent('//script[@src=\'https://platform.twitter.com/widgets.js\']');
		$this->waitForElementPresent('//iframe[contains(@id,\'twitter-widget\')]');

		$this->logOut();
	}
}

