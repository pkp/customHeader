#!/bin/bash

set -e
npx cypress run --spec "cypress/tests/data/10-ApplicationSetup/*.cy.js"
npx cypress run --config specPattern=plugins/generic/customHeader/cypress/tests/functional
