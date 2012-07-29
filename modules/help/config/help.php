<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Inline help can be turned on or off.
 */
$config['enabled'] = TRUE;

/**
 * Define rules based on Router::$routed_uri to determine
 * whether or not there is inline help available.
 */
$config['rules'] = array(

);

/**
 * View folder, relative to APPPATH.
 */
$config['view_folder'] = 'views/help';

/**
 * Link text.
 */
$config['link_text'] = 'Help';