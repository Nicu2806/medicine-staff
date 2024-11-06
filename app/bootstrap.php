<?php
// Load Config
require_once 'config/config.php';
require_once 'config/utils.php';

// Load Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/datetime_helper.php';
require_once 'helpers/AssetHelper.php';
require_once 'helpers/UserSharedExperience.php';
require_once 'models/UserSharedExperience.php';
require_once 'models/UserSubmitExperience.php';

AssetHelper::init();

require_once 'libraries/Controller.php';
require_once 'libraries/Core.php';
require_once 'libraries/Database.php';

spl_autoload_register(function ($className)
{
  require_once 'libraries/' . $className . '.php';
});