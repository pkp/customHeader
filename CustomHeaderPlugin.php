<?php

/**
 * @file CustomHeaderPlugin.php
 *
 * Copyright (c) 2013-2025 Simon Fraser University
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief CustomHeader plugin class
 */

namespace APP\plugins\generic\customHeader;

use APP\core\Application;
use Exception;
use PKP\config\Config;
use PKP\core\JSONMessage;
use PKP\core\PKPApplication;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use PKP\plugins\Hook;

class CustomHeaderPlugin extends GenericPlugin
{
    /** Whether the header has been injected */
    public bool $injected = false;

    /**
     * @copydoc Plugin::register()
     * @throws Exception
     */
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path, $mainContextId);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) {
            return true;
        }
        if ($success && $this->getEnabled()) {
            // Insert CustomHeader page tag to page header
            Hook::add('TemplateManager::display', array($this, 'displayTemplateHook'));
            // Insert custom script to the page footer
            Hook::add('Templates::Common::Footer::PageFooter', array($this, 'insertFooter'));
        }
        return $success;
    }

    /**
     * Get the plugin display name.
     */
    public function getDisplayName(): string
    {
        return __('plugins.generic.customHeader.displayName');
    }

    /**
     * Get the plugin description.
     */
    public function getDescription(): string
    {
        return __('plugins.generic.customHeader.description');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $verb): array
    {
        $router = $request->getRouter();
        return array_merge(
            $this->getEnabled() ? array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, ['verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic']),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ) : array(),
            parent::getActions($request, $verb)
        );
    }

    /**
     * @copydoc Plugin::manage()
     * @throws Exception
     */
    public function manage($args, $request)
    {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $form = new CustomHeaderSettingsForm(
                    $this,
                    $context ? $context->getId() : PKPApplication::SITE_CONTEXT_ID
                );

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
     * Register the CustomHeader script tag
     */
    public function displayTemplateHook(string $hookName, array $params)
    {
        if (!$this->injected) {
            $this->injected = true;
            $templateMgr =& $params[0];
            $request = Application::get()->getRequest();
            $context = $request->getContext();
            $templateMgr->addHeader(
                'custom',
                $this->getSetting($context ? $context->getId() : PKPApplication::SITE_CONTEXT_ID, 'content')
            );
            $templateMgr->addHeader(
                'custombackend',
                $this->getSetting(
                    $context ? $context->getId() : PKPApplication::SITE_CONTEXT_ID,
                    'backendContent'
                ),
                ['contexts' => ['backend']]
            );
        }
        return false;
    }

    /**
     * Add custom footer to the page
     */
    public function insertFooter(string $hookName, array $params)
    {
        $templateMgr =& $params[0];
        $output =& $params[2];
        $request = Application::get()->getRequest();
        $context = $request->getContext();

        $output .= $this->getSetting($context ? $context->getId() : PKPApplication::SITE_CONTEXT_ID, 'footerContent');

        return false;
    }

    /**
     * This plugin can be used site-wide or in a specific context. The
     * isSitePlugin check is used to grant access to different users, so this
     * plugin must return true only if the user is currently in the site-wide
     * context.
     *
     * @see PluginGridRow::_canEdit()
     */
    public function isSitePlugin(): bool
    {
        return !(Application::get()->getRequest()->getContext());
    }
}
