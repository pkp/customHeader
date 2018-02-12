<?php

/**
 * @file CustomHeaderPlugin.inc.php
 *
 * Copyright (c) 2013-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
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
	 * Called as a plugin is registered to the registry
	 * @param $category String Name of category plugin was registered to
	 * @return boolean True iff plugin initialized successfully; if false,
	 * 	the plugin will not be registered.
	 */
	function register($category, $path) {
		$success = parent::register($category, $path);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return true;
		if ($success && $this->getEnabled()) {
			// Insert CustomHeader page tag to footer
			HookRegistry::register('TemplateManager::display', array($this, 'displayTemplateHook'));
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
				$templateMgr->register_function('plugin_url', array($this, 'smartyPluginUrl'));

				$this->import('CustomHeaderSettingsForm');
				$form = new CustomHeaderSettingsForm($this, $context?$context->getId():CONTEXT_ID_NONE);

				if ($request->getUserVar('save')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();
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
	 * Override the builtin to get the correct template path.
	 * @return string
	 */
	function getTemplatePath($inCore = false) {
		return parent::getTemplatePath($inCore) . 'templates/';
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
	 * This plugin can be used site-wide or in a specific context. The
	 * isSitePlugin check is used to grant access to different users, so this
	 * plugin must return true only if the user is currently in the site-wide
	 * context.
	 *
	 * @see PluginGridRow::_canEdit()
	 * @return boolean
	 */
	function isSitePlugin() {
		return !Application::getRequest()->getContext();
	}
}
