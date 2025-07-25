{**
 * templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2025 Simon Fraser University
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * Plugin settings
 *
 *}

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#customHeaderSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="customHeaderSettingsForm" method="post" action="{url router=PKP\core\PKPApplication::ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}
	<p id="description">{translate key="plugins.generic.customHeader.manager.settings.description"}</p>
	{include file="controllers/notification/inPlaceNotification.tpl" notificationId="customHeaderFormNotification"}

	{fbvFormArea id="customHeaderSettingsFormArea"}
		{fbvFormSection for="headerContent" title="plugins.generic.customHeader.content"}
			{fbvElement type="textarea" name="content" id="headerContent" value=$content height=$fbvStyles.height.TALL}
		{/fbvFormSection}
		{fbvFormSection for="footerContent" title="plugins.generic.customHeader.footerContent"}
			{fbvElement type="textarea" name="footerContent" id="footerContent" value=$footerContent height=$fbvStyles.height.TALL}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormArea id="customHeaderBackendSettingsFormArea"}
		{fbvFormSection for="backendContent" title="plugins.generic.customHeader.backendContent"}
			{fbvElement type="textarea" name="backendContent" id="backendContent" value=$backendContent height=$fbvStyles.height.TALL}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>
