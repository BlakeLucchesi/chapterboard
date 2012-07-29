<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Written by Blake Lucchesi (blake@thisbythem.com) of This By Them.
 *
 */
class image_Core {

  /**
   * Thumbnail an image to one of the defined sizes in config/image.php.
   */
  public static function thumbnail($size, $filename) {
    
  }
  
  
  /**
   * Delete all cached versions of a file, or all files from all caches.
   *
   * @param string  Name of file to remove from each size cache. NULL clears all image caches.
   */
  public static function clear_cache($filename = NULL) {
    if ($filename) {
      foreach (Kohana::config('image.sizes') as $dir => $value) {
        $filepath = Kohana::config('core.filepath').DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR.$filename;
        if (is_file($filepath)) {
          unlink($filepath);
        }
      }
    }
    else {
      foreach (Kohana::config('image.sizes') as $dir => $value) {
        $directory = Kohana::config('core.filepath').DIRECTORY_SEPARATOR.$dir;
        if (is_dir($directory)) {
          $files = glob($directory.DIRECTORY_SEPARATOR.'*');
          foreach ($files as $file) unlink($file);
        }
      }
    }
  }
}