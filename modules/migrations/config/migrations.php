<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Migrations are disabled by default for security reasons.
 */
$config['enabled'] = TRUE;

/**
 * Path to your migrations folder.
 * Typically, it will be within your application path.
 * -> Writing permission is required within the migrations path.
 *
 * Paths are oranized by database groups
 */
$config['path'] = array
(
	'default' => APPPATH . 'migrations/',
);

/**
 * Subdirectory to store meta-information about the state of the migrations.
 */
$config['info'] = '.info';
