<?php

/**
 * @file CustomHeaderSettingsForm.php
 *
 * Copyright (c) 2013-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Form for managers to modify custom header plugin settings
 */

namespace APP\plugins\generic\customHeader;

use PKP\form\Form;
use APP\core\Application;
use APP\template\TemplateManager;
use APP\notification\NotificationManager;

class CustomHeaderSettingsForm extends Form {

	/** @var int */
	var $_contextId;

	/** @var object */
	var $_plugin;

	/**
	 * Constructor
	 * @param $plugin CustomHeaderPlugin
	 * @param $contextId int
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

		$this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
		$this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$this->_data = array(
			'content' => $this->_plugin->getSetting($this->_contextId, 'content'),
			'footerContent' => $this->_plugin->getSetting($this->_contextId, 'footerContent')
		);
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
		$this->readUserVars(array('content', 'footerContent'));
	}

	/**
	 * Fetch the form.
	 * @copydoc Form::fetch()
	 */
	function fetch($request, $template = null, $display = false) {
		$templateMgr = TemplateManager::getManager($request);
		$templateMgr->assign('pluginName', $this->_plugin->getName());
		return parent::fetch($request, $template, $display);
	}

	/**
	 * Save settings.
	 */
	function execute(...$functionArgs) {
		parent::execute(...$functionArgs);

		$request = Application::get()->getRequest();
		$this->_plugin->updateSetting($this->_contextId, 'content', $this->getData('content'), 'string');
		$this->_plugin->updateSetting($this->_contextId, 'footerContent', $this->getData('footerContent'), 'string');
		$notificationManager = new NotificationManager();
		$notificationManager->createTrivialNotification($request->getUser()->getId(), NOTIFICATION_TYPE_SUCCESS);
	}
}
