<?php

/**
 * @defgroup plugins_generic_customHeader Piwik Plugin
 */

/**
 * @file index.php
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_customHeader
 * @brief Wrapper for cutom header plugin.
 *
 */

require_once('CustomHeaderPlugin.inc.php');

return new CustomHeaderPlugin();
