{**
 * templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * Plugin settings
 *
 *}
<div id="customHeaderSettings">
<div id="description">{translate key="plugins.generic.customHeader.manager.settings.description"}</div>

<div class="separator"></div>

<br />

<script>
	$(function() {ldelim}
		// Attach the form handler.
		$('#customHeaderSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>
<form class="pkp_form" id="customHeaderSettingsForm" method="post" action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="settings" save=true}">
	{csrf}

	{fbvFormArea id="customHeaderSettingsFormArea"}
		{fbvFormSection for="headerContent" title="plugins.generic.customHeader.content"}
			{fbvElement type="textarea" name="content" id="headerContent" value=$content height=$fbvStyles.height.TALL}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormArea id="customHeaderSettingsFormArea"}
		{fbvFormSection for="footerContent" title="plugins.generic.customHeader.footerContent"}
			{fbvElement type="textarea" name="footerContent" id="footerContent" value=$footerContent height=$fbvStyles.height.TALL}
		{/fbvFormSection}
	{/fbvFormArea}

	{fbvFormButtons}
</form>

<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
</div>
