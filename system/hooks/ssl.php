<?php defined('SYSPATH') or die('No direct script access.');

class SSL_Hook {
  
  function __construct() {
    if (Kohana::config('ssl.enabled')) {
      Event::add('system.post_routing', array($this, 'ssl_check'));
    }
  }

  /**
   * If the user is visiting a page without ssl, force ssl.
   */
  function ssl_check() {
    $protected = Kohana::config('ssl.protected');
    $indifferent = Kohana::config('ssl.indifferent');
    $url = Router::$current_uri;

    // Redirect to HTTPS.
    if ( ! $_SERVER['HTTPS']) {
      foreach ($protected as $regex) {
        if (preg_match($regex, $url)) {
          url::redirect(url::base(FALSE, 'https') . self::redirect_url(Router::$current_uri));
        }
      }
    }
    // Redirect to HTTP
    else {
      $redirect = TRUE;
      foreach ((array) $protected as $regex) {
        if (preg_match($regex, $url)) {
          $redirect = FALSE;
          break;
        }
      }
      foreach ((array) $indifferent as $regex) {
        if (preg_match($regex, $url)) {
          $redirect = FALSE;
          break;
        }
      }
      if ($redirect) {
        url::redirect(url::base(FALSE, 'http') . self::redirect_url(Router::$current_uri));
      }
    }
  }
  
  public static function redirect_url($uri) {
    return Router::$current_uri === Router::$routes['_default'] ? '' : Router::$current_uri;
  }
}

new SSL_Hook;