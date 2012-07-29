<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Login URL for our application.
 */
switch ($_SERVER['SERVER_NAME']) {
  case 'app.chapterboard.com':
    $config['admin_url'] = 'https://nidas.chapterboard.com';
    $config['public_url'] = 'http://www.chapterboard.com';
    $config['mobile_url'] = 'http://m.chapterboard.com';
    $config['static_domains'] = array('http://app.chapterboard.com', 'http://alpha.chapterboard.com', 'http://beta.chapterboard.com');
    $config['payrally_url'] = 'http://www.payrally.com';
    break;
  case 'app.chapterbox.com':
    $config['admin_url'] = 'https://nidas.chapterbox.com';
    $config['public_url'] = 'http://www.chapterbox.com';
    $config['mobile_url'] = 'http://m.chapterbox.com';
    $config['static_domains'] = array('http://app.chapterbox.com', 'http://alpha.chapterbox.com', 'http://beta.chapterbox.com');
    $config['payrally_url'] = 'http://www.payrally.com';
    break;
  default:
    $config['admin_url'] = 'http://nidas.chapterdev.com';
    $config['public_url'] = 'http://www.chapterdev.com';
    $config['mobile_url'] = 'http://m.chapterdev.com';
    $config['static_domains'] = array('http://app.chapterdev.com', 'http://alpha.chapterdev.com', 'http://beta.chapterdev.com');
    $config['payrally_url'] = 'http://payrally.local';
    break;
}

$config['sms_email'] = '';
$config['sms_email_password'] = '';