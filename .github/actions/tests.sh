#!/bin/bash

set -e

npx cypress run --config specPattern=plugins/generic/customHeader/cypress/tests/functional
