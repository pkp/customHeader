<?php

/**
 * @file CustomHeaderSettingsForm.php
 *
 * Copyright (c) 2013-2025 Simon Fraser University
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @brief Form for managers to modify custom header plugin settings
 */

namespace APP\plugins\generic\customHeader;

use DOMDocument;
use APP\core\Application;
use APP\notification\NotificationManager;
use APP\template\TemplateManager;
use PKP\form\Form;
use PKP\form\validation\FormValidatorCSRF;
use PKP\form\validation\FormValidatorCustom;
use PKP\form\validation\FormValidatorPost;

class CustomHeaderSettingsForm extends Form
{
    public int $contextId;
    public CustomHeaderPlugin $plugin;

    /**
     * Constructor
     */
    public function __construct(CustomHeaderPlugin $plugin, int $contextId)
    {
        $this->contextId = $contextId;
        $this->plugin = $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->addCheck(new FormValidatorCustom($this, 'backendContent', FORM_VALIDATOR_OPTIONAL_VALUE, 'plugins.generic.customHeader.backendContent.error', function ($backendContent) {
            return $this->validateWellFormed($backendContent);
        }));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Initialize form data.
     */
    public function initData(): void
    {
        $this->_data = array(
            'content' => $this->plugin->getSetting($this->contextId, 'content'),
            'backendContent' => $this->plugin->getSetting($this->contextId, 'backendContent'),
            'footerContent' => $this->plugin->getSetting($this->contextId, 'footerContent')
        );
    }

    /**
     * Assign form data to user-submitted data.
     */
    public function readInputData(): void
    {
        $this->readUserVars(array('content', 'backendContent', 'footerContent'));
    }

    /**
     * Fetch the form.
     * @copydoc Form::fetch()
     */
    public function fetch($request, $template = null, $display = false): null|string
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * Save settings.
     */
    public function execute(...$functionArgs)
    {
        parent::execute(...$functionArgs);

        $request = Application::get()->getRequest();
        $this->plugin->updateSetting($this->contextId, 'content', $this->getData('content'), 'string');
        $this->plugin->updateSetting($this->contextId, 'backendContent', $this->getData('backendContent'), 'string');
        $this->plugin->updateSetting($this->contextId, 'footerContent', $this->getData('footerContent'), 'string');
        $notificationManager = new NotificationManager();
        $notificationManager->createTrivialNotification($request->getUser()->getId());
    }

    /**
     * Validate that the input is well-formed XML
     * We want to avoid breaking the whole HTML page with an unclosed HTML attribute quote or tag
     */
    public function validateWellFormed(string $input): bool
    {
        $libxml_errors_setting = libxml_use_internal_errors();
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new DOMDocument();
        $dom->loadHTML($input);
        $isWellFormed = count(libxml_get_errors()) == 0;
        libxml_use_internal_errors($libxml_errors_setting);
        return $isWellFormed;
    }
}
