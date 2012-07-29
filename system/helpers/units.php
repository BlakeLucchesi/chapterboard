<?php defined('SYSPATH') or die('No direct script access.');

class units_Core {
  
  /**
   * Convert bytes to KB, MB, GB.
   */
  public static function byte($value, $unit = 'MB', $display_unit = TRUE) {
    switch ($unit) {
      case 'KB':
        $output = $value / 1024;
        break;
      case 'MB':
        $output = $value / pow(1024, 2);
        break;
      case 'GB':
        $output = $value / pow(1024, 3);
        break;
      default:
        $output = $value;
    }
    $output = number_format($output, 1);
    return $display_unit ? $output . $unit : $output;
  }
  
}