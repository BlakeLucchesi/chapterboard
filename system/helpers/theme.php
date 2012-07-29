<?php defined('SYSPATH') or die('No direct script access.');

class theme_Core {
  
  static private $domains = NULL;
  
  static private $domains_ssl = NULL;
  
  static private $domains_count = 0;
  
  static private $domains_ssl_count = 0;
  
  function image($size, $filename, $attributes = array(), $url_only = FALSE, $ssl = FALSE) {
    if (is_null(self::$domains)) {
      self::$domains = Kohana::config('app.static_domains');
      self::$domains_count = count(self::$domains) - 1;
      self::$domains_ssl = Kohana::config('app.static_domains_ssl');
      self::$domains_ssl_count = count(self::$domains_ssl) - 1;
    }
    if ($ssl) {
      $domain = self::$domains_ssl[rand(0, self::$domains_ssl_count)];
    }
    else {
      $domain = self::$domains[rand(0, self::$domains_count)];
    }
    $attributes['src'] = sprintf('%s/file/%s/%s', $domain, $size, $filename);

    if ($url_only)
      return $attributes['src'];
    return html::image($attributes);
  }
}