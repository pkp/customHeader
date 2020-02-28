<?php

/**
 * @file CustomHeaderPlugin.inc.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @class CustomHeaderPlugin
 * @ingroup plugins_generic_customHeader
 *
 * @brief CustomHeader plugin class
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class CustomHeaderPlugin extends GenericPlugin {
	/** @var bool Whether or not the header has been injected */
	var $injected = false;

	/**
	 * @copydoc Plugin::register()
	 */
	function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			// Insert CustomHeader page tag to page header
			HookRegistry::register('TemplateManager::display', array($this, 'displayTemplateHook'));
			// Insert custom script to the page footer
			HookRegistry::register('Templates::Common::Footer::PageFooter', array($this, 'insertFooter'));
		}
		return $success;
	}

	/**
	 * Get the plugin display name.
	 * @return string
	 */
	function getDisplayName() {
		return __('plugins.generic.customHeader.displayName');
	}

	/**
	 * Get the plugin description.
	 * @return string
	 */
	function getDescription() {
		return __('plugins.generic.customHeader.description');
	}

	/**
	 * @copydoc Plugin::getActions()
	 */
	function getActions($request, $verb) {
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		return array_merge(
			$this->getEnabled()?array(
				new LinkAction(
					'settings',
					new AjaxModal(
							$router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
							$this->getDisplayName()
					),
					__('manager.plugins.settings'),
					null
				),
			):array(),
			parent::getActions($request, $verb)
		);
	}

	/**
	 * @copydoc Plugin::manage()
	 */
	function manage($args, $request) {
		switch ($request->getUserVar('verb')) {
			case 'settings':
				$context = $request->getContext();

				AppLocale::requireComponents(LOCALE_COMPONENT_APP_COMMON,  LOCALE_COMPONENT_PKP_MANAGER);
				$templateMgr = TemplateManager::getManager($request);

				$this->import('CustomHeaderSettingsForm');
				$form = new CustomHeaderSettingsForm($this, $context?$context->getId():CONTEXT_ID_NONE);

				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute($request);
						return new JSONMessage(true);
					}
				} else {
					$form->initData();
				}
				return new JSONMessage(true, $form->fetch($request));
		}
		return parent::manage($args, $request);
	}

	/**
	 * Register the CustomHeader script tag
	 * @param $hookName string
	 * @param $params array
	 */
	function displayTemplateHook($hookName, $params) {
		if (!$this->injected) {
			$this->injected = true;
			$templateMgr =& $params[0];
			$request = Application::getRequest();
			$context = $request->getContext();
			$templateMgr->addHeader('custom', $this->getSetting($context?$context->getId():CONTEXT_ID_NONE, 'content'));
		}
		return false;
	}

	/**
	 * Add custom footer to the page
	 * 
	 * @param $hookName string
	 * @param $params array
	 */
	function insertFooter($hookName, $params) {
		$templateMgr =& $params[0];
		$output =& $params[2];
		$request = Application::get()->getRequest();
		$context = $request->getContext();
		
		$output .= $this->getSetting($context?$context->getId():CONTEXT_ID_NONE, 'footerContent');

		return false;
	}

	/**
	 * This plugin can be used site-wide or in a specific context. The
	 * isSitePlugin check is used to grant access to different users, so this
	 * plugin must return true only if the user is currently in the site-wide
	 * context.
	 *
	 * @see PluginGridRow::_canEdit()
	 * @return boolean
	 */
	function isSitePlugin() {
		return !(Application::get()->getRequest()->getContext());
	}
}
