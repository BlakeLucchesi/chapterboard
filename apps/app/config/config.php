<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Base path of the web site. If this includes a domain, eg: localhost/kohana/
 * then a full URL will be used, eg: http://localhost/kohana/. If it only includes
 * the path, and a site_protocol is specified, the domain will be auto-detected.
 */
$config['site_domain'] = $_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : 'app.chapterboard.com';

/**
 * Force a default protocol to be used by the site. If no site_protocol is
 * specified, then the current protocol is used, or when possible, only an
 * absolute path (with no protocol/domain) is used.
 */
$config['site_protocol'] = '';

/**
 * Name of the front controller for this application. Default: index.php
 *
 * This can be removed by using URL rewriting.
 */
$config['index_page'] = '';

/**
 * Where files are uploaded.
 */
$config['filepath'] = APPPATH.'files';

/**
 * Allows you to load views and controllers from a sub directory within the views and or
 * controllers directory. Add a trailing slash to ensure proper path finding.
 */
$config['override_path'] = 'chapter/';

/**
 * Fake file extension that will be added to all generated URLs. Example: .html
 */
$config['url_suffix'] = '';

/**
 * Windowed urls. Whether or not to open external urls in new a window when passed through html::anchor().
 */
$config['windowed_urls'] = FALSE;

/**
 * Length of time of the internal cache in seconds. 0 or FALSE means no caching.
 * The internal cache stores file paths and config entries across requests and
 * can give significant speed improvements at the expense of delayed updating.
 */
$config['internal_cache'] = 86400;

/**
 * Enable or disable gzip output compression. This can dramatically decrease
 * server bandwidth usage, at the cost of slightly higher CPU usage. Set to
 * the compression level (1-9) that you want to use, or FALSE to disable.
 *
 * Do not enable this option if you are using output compression in php.ini!
 */
$config['output_compression'] = FALSE;

/**
 * Enable or disable global XSS filtering of GET, POST, and SERVER data. This
 * option also accepts a string to specify a specific XSS filtering tool.
 */
$config['global_xss_filtering'] = TRUE;

/**
 * Enable or disable hooks.
 */
$config['enable_hooks'] = TRUE;

/**
 * Log thresholds:
 *  0 - Disable logging
 *  1 - Errors and exceptions
 *  2 - Warnings
 *  3 - Notices
 *  4 - Debugging
 */
$config['log_threshold'] = 3;

/**
 * Message logging directory.
 */
$config['log_directory'] = APPPATH.'logs';

/**
 * Enable or disable displaying of Kohana error pages. This will not affect
 * logging. Turning this off will disable ALL error pages.
 */
$config['display_errors'] = FALSE;

/**
 * Set the email address to which you would like to receive exception handling
 * notifications.
 */
$config['error_reporting_email'] = 'email@example.com';

/**
 * Enable or disable statistics in the final output. Stats are replaced via
 * specific strings, such as {execution_time}.
 *
 * @see http://docs.kohanaphp.com/general/configuration
 */
$config['render_stats'] = FALSE;

/**
 * Filename prefixed used to determine extensions. For example, an
 * extension to the Controller class would be named MY_Controller.php.
 */
$config['extension_prefix'] = 'MY_';

/**
 * Google analytics settings.
 */
$config['google_analytics_enabled'] = TRUE;
$config['google_analytics_id'] = IN_PRODUCTION ? 'UA-XXXXXX' : 'UA-YYYYYY';
$config['google_analytics_domain'] = 'example.com';

/**
 * Additional resource paths, or "modules". Each path can either be absolute
 * or relative to the docroot. Modules can include any resource that can exist
 * in your application directory, configuration files, controllers, views, etc.
 */
$config['modules'] = array(
  MODPATH.'a1',
  MODPATH.'a2',
  MODPATH.'acl',
  // MODPATH.'a2-acl-demo',
  MODPATH.'chapterboard',
  MODPATH.'cron',         // Cron jobs
  MODPATH.'email',
  MODPATH.'gmaps',        // Google Maps integration
  MODPATH.'javascript',
  MODPATH.'payment',      // Online payments
  MODPATH.'s3',
  MODPATH.'sms',
  MODPATH.'solr',
  MODPATH.'unit_test',    // Unit testing
  MODPATH.'help',
    // MODPATH.'archive',   // Archive utility
);

if ( ! IN_PRODUCTION) {
  $config['log_threshold']  = 1;
  $config['display_errors'] = TRUE;
  $config['render_stats']   = TRUE;
  $config['internal_cache'] = FALSE;
}