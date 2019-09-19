<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2017 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
error_reporting(0);
require_once( "assets/import/hybridauth/hybridauth/Hybrid/Auth.php" );
require_once( "assets/import/hybridauth/hybridauth/Hybrid/Endpoint.php" );
require_once('assets/import/hybridauth/vendor/autoload.php');
Hybrid_Endpoint::process();