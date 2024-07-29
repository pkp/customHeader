#!/bin/bash

set -e

npx cypress run --spec "cypress/tests/data/10-Installation.spec.js,cypress/tests/data/20-CreateContext.spec.js"

npx cypress run  --headless --browser chrome  --config '{"specPattern":["plugins/generic/customHeader/cypress/tests/functional/*.cy.js"]}'


