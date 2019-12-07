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

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverBy;

class CustomHeaderFunctionalTest extends WebTestCase {
	/**
	 * Enable and exercise the plugin
	 */
	function testCustomHeader() {
		$this->open(self::$baseUrl);

		$this->logIn('admin', 'admin');
		$actions = new WebDriverActions(self::$driver);
		$actions->moveToElement($this->waitForElementPresent('//ul[@id="navigationPrimary"]//a[contains(text(),"Settings")]'))
			->click($this->waitForElementPresent('//ul[@id="navigationPrimary"]//a[contains(text(),"Website")]'))
			->perform();
		$this->click('//button[@id="plugins-button"]');

		// Find and enable the plugin
		$this->click('//input[starts-with(@id, \'select-cell-customheaderplugin-enabled\')]');
		$this->waitForElementPresent('//div[contains(.,\'The plugin "Custom Header Plugin" has been enabled.\')]');
		sleep(1);

		// Edit the plugin settings
		self::$driver->executeScript('window.scroll(0,-50);'); // FIXME: Give it an extra margin of pixels
		$this->click('//tr[contains(@id,"customheaderplugin")]//a[contains(@class,"show_extras")]');
		$this->click('//a[contains(@id,\'customheaderplugin-settings\')]');
		$this->waitForElementPresent($selector='//textarea[starts-with(@id, \'headerContent-\')]');
		$this->type($selector, '<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>');
		$this->type('//textarea[starts-with(@id, \'footerContent-\')]', '<a class="twitter-timeline" href="https://twitter.com/pkp?ref_src=twsrc%5Etfw">Tweets by pkp</a>');
		$this->click('//form[@id=\'customHeaderSettingsForm\']//button[text()=\'OK\']');
		$this->waitForElementPresent('//div[contains(.,\'Your changes have been saved.\')]');

		// Check that a Twitter timeline appears on the homepage.
		sleep(1);
		$this->open(self::$baseUrl);
		sleep(5);
		$this->waitForElementPresent('//script[@src=\'https://platform.twitter.com/widgets.js\']');
		$this->waitForElementPresent('//iframe[contains(@id,\'twitter-widget\')]');

		$this->logOut();
	}
}

