<?php

class fixture_core {

  public static $path;
  
  public static $spyc;

  public static $ext;
  
  public static $cache = NULL;
  
  public static $loaded = FALSE;
  
  /**
   * Make sure we can load the proper paths for our fixtures.
   */
  static public function _bootstrap() {
    if (self::$loaded) // If we've already loaded just return.
      return;
    
    self::$path = Kohana::config('fixture.path');
    if ( ! is_dir(self::$path)){
      throw new Kohana_Exception('fixture.not_directory', self::$path);
    }
    
    self::$ext = Kohana::config('fixture.ext');
    self::$spyc = new Spyc;
    self::$loaded = TRUE;
  }
  
  
  /**
   * Load a fixture into a php array and return result.
   */
  static public function load($key) {
    self::_bootstrap();

    list($filename, $element) = explode('.', $key);
    
    if (isset(self::$cache[$filename][$element]))
      return self::$cache[$filename][$element];

    $file = self::$path.DIRECTORY_SEPARATOR.$filename.self::$ext;
    if ( ! is_file($file)) {
      throw new Kohana_Exception('fixture.not_file', $file);
    }
    $data = self::$spyc->YAMLLoad($file);
    self::$cache[$filename][$element] = $data[$element];
    return ($data[$element]);
  }
  
  
}