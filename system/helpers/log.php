<?php defined('SYSPATH') or die('No direct script access.');

class log {

  public static function system($namespace, $message, $severity = 'notice', $extra = array()) {
    $message =  is_array($message) || is_object($message) ? print_r($message, true) : $message;
    foreach ($extra as $key => $item) {
      $data[$key] = is_array($item) || is_object($item) ? print_r($item, true) : $item;
    }
    Kohana::log($type, $message, $data);
  }
}