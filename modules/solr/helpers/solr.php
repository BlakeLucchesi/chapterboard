<?php defined('SYSPATH') or die('No direct script access.');

class solr_Core {
  
  public static function service() {
    self::_load_solr();
    $config = Kohana::config('solr');
    return new Apache_Solr_Service($config['host'], $config['port'], $config['path']);
  }
  
  public static function document() {
    self::_load_solr();
    return new Apache_Solr_Document();
  }
  
  public static function _load_solr() {
    require_once Kohana::find_file('vendor', 'solr/Service');
  }
  
}