<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Catch custom urls with chapter nicknames.  To start, this will allow
 * us to provide short urls for chapter fundraising campaign pages.
 *
 * We'll likely expand on this in the future when we offer custom chapter sites.
 */
class routing {
  
  function __construct() {
    Event::add('system.post_routing', array('routing', 'catch_campaigns'));
  }
  
  public static function catch_campaigns() {
    if (is_null(Router::$controller)) {
      $account = ORM::factory('site')->find_by_slug(Router::$segments[0]);
      if ($account->loaded) {
        $campaign = ORM::factory('campaign')->find_by_site_slug($account->id, Router::$segments[1]);
        if ($campaign->loaded) {
          if ( ! $_SERVER['HTTPS']) {
            url::redirect(url::base(FALSE, 'https') . Router::$current_uri);
          }
          Router::$controller = 'campaigns';
          Router::$controller_path = APPPATH . 'controllers/campaigns.php';
          Router::$method = 'form';
          Router::$arguments = array($account, $campaign);
        }
      }
    }
  }
  
}

new routing();