<?php defined('SYSPATH') or die('No direct script access.');

class widget {
  
  static $widgets;
  
  /**
   * Add new content for a region. Optionally specifying a weight to determine
   * its output placement.  Lower weight is higher: 
   * $weight = -100 gets output before 100.
   *
   * @param string $name
   * Defines a string key for an output region.
   *
   * @param string $content
   * Rendered output ready to be put out to the screen.
   *
   * @param int $weight
   * An optional weight as described above.
   *
   */
  function add($name, $content, $weight = 0) {
    static $weights = array();
    while (in_array($weight, $weights)) {
      $weight++;
    }
    $weights[] = $weight; // store the newly used weight in our array of weights
    self::$widgets[$name][$weight] = $content;
  }
  
  /**
   * Get the contents of a widget region, output is "first in first out"
   * unless a weight was specified during set().
   *
   * @param string $name
   * A key that was used during set() to specify an output region.
   *
   * @return string
   * Rendered output ready to print for the screen.
   */
  function get($name) {
    if (isset(self::$widgets[$name])) {
      ksort(self::$widgets[$name]);
      return implode('', self::$widgets[$name]);
    }
    else {
      return FALSE;
    }
  }
  
  /**
   * Checks to see if there is content within a widget region.
   *
   * @param string $name
   * A key that was used during set() to specify an output region.
   *
   * @return boolean
   * Whether or not there is content defined for the region in question.
   */
  public function is_set($name) {
    return !empty(self::$widgets[$name]);
  }
  
}