<?php defined('SYSPATH') or die('No direct script access.');

class log {
  
  private static $conn;
  
  /**
   * Insert a record in the logs.
   *
   * @param string The name of the collection to store the data in.
   * @param array  The data object to store in the collection.
   *
   * @return boolean  True on success and False if no mongo connection.
   */
  public static function insert($collection, $data) {
    if ( ! self::$conn) {
      try {
        self::$conn = new Mongo;
      }
      catch (Exception $e) {
        return FALSE;
      }
    }
    $data['environment'] = Kohana::config('config.site_domain');
    $c = self::$conn->chapterboard->$collection;
    $c->insert($data);
    return TRUE;
  }
  
  public static function system($type, $message, $severity = 'notice', $extra = array()) {
    $data = array(
      'type' => $type,
      'message' => is_array($message) || is_object($message) ? print_r($message, true) : $message,
      'created' => date::to_db(),
      'severity' => $severity,
    );
    foreach ($extra as $key => $item) {
      $data[$key] = is_array($item) || is_object($item) ? print_r($item, true) : $item;
    }
    log::insert('logs', $data);
  }
}