/**
 * @file cypress/tests/functional/CustomHeader.cy.js
 *
 * Copyright (c) 2014-2025 Simon Fraser University
 * Copyright (c) 2000-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 */

describe('Custom Header plugin tests', function () {
    it('Creates and exercises a custom header', function () {
        cy.login('admin', 'admin', 'publicknowledge');

        cy.get('nav').contains('Settings').click();
        // Ensure submenu item click despite animation
        cy.get('nav').contains('Website').click({ force: true });
        cy.get('button[id="plugins-button"]').click();

        // Find and enable the plugin
        cy.get('input[id^="select-cell-customheaderplugin-enabled"]').click();
        cy.get('div:contains(\'The plugin "Custom Header Plugin" has been enabled.\')');
        cy.reload();

        cy.get('tr[id="component-grid-settings-plugins-settingsplugingrid-category-generic-row-customheaderplugin"] a.show_extras').click();
        cy.get('a[id^="component-grid-settings-plugins-settingsplugingrid-category-generic-row-customheaderplugin-settings-button"]').click();
        cy.waitJQuery(2000); // Wait for form to settle
        cy.get('textarea[id^="headerContent-"]').type('<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>', {delay: 0});
        cy.get('textarea[id^="footerContent-"]').type('<a class="twitter-timeline" href="https://twitter.com/pkp?ref_src=twsrc%5Etfw">Tweets by pkp</a>', {delay: 0});
        cy.get('form[id="customHeaderSettingsForm"] button:contains("OK")').click();
        cy.get('div:contains("Your changes have been saved.")');

        cy.visit('');
        cy.get('iframe[id*="twitter-widget"]');
    });
})
