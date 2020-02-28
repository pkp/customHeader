<?php

/**
 * @defgroup plugins_generic_customHeader Piwik Plugin
 */

/**
 * @file index.php
 *
 * Copyright (c) 2013-2020 Simon Fraser University
 * Copyright (c) 2003-2020 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file LICENSE.
 *
 * @ingroup plugins_generic_customHeader
 * @brief Wrapper for cutom header plugin.
 *
 */

require_once('CustomHeaderPlugin.inc.php');

return new CustomHeaderPlugin();
