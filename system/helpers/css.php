<?php defined('SYSPATH') or die('No direct script access.');

class css_Core {
  
  static $files = array('system' => array(), 'module' => array(), 'theme' => array());

  // Allowed style types, in the order they are listed in output.
  static $allowed_types = array('system', 'module', 'theme');
  
  public static function add($file, $type = 'theme', $media = 'screen') {
    if (in_array($type, self::$allowed_types)) {
      $info['href'] = url::file($file);
      $info['media'] = $media;
      // Append last saved timestamp so browsers refresh cache.
      if ( ! preg_match('/^http/i', $file['href']) && (IN_PRODUCTION || IN_TESTING)) {
        $info['time'] = filemtime(realpath($file));
      }
      self::$files[$type][sha1($file)] = $info;
    }
    else {
      throw new Kohana_Exception(sprintf('CSS $type = "%s" is not in allowed types: %s.', $type, implode(', ', self::$allowed_types)));
    }
  }
  
  public static function get() {
    if (is_array(self::$files)) {
      foreach (self::$allowed_types as $type) {
        foreach (self::$files[$type] as $file) {
          $output .= "<link rel=\"stylesheet\" href=\"{$file['href']}?{$file['time']}\" type=\"text/css\" media=\"{$file['media']}\" charset=\"utf-8\">\n";
        }        
      }
    }
    return $output;
  }
}