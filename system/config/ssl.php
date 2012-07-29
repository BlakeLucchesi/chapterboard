<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  SSL
 *
 * Define the paths that should be protected by ssl.
 */

/**
 * Turn On/Off
 */
if (IN_PRODUCTION) {
  $config['enabled'] = TRUE;
}
else {
  $config['enabled'] = FALSE;
}

/**
 * URLS to protected.
 *
 * An array of urls patterns to match against. 
 */
$config['protected'] = array(

);