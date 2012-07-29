<?php defined('SYSPATH') or die('No direct script access.');

class file extends file_Core {
  
  /**
   * Create a new unique hashed filename preserving
   * the original file extension.
   */
  static public function unique_name($file) {
    return md5(time() . basename($file));
  }

  /**
   * Based on the filetype provided by file upload.
   * This should be run in addition to the valid helper to verify
   * allowed file extension types, this only returns the extension and
   * provides NO VALIDATION.
   */
  static public function extension($type) {
    $type = strtolower(array_pop(split('/', $type)));
    switch ($type) {
      case 'jpeg':
        return 'jpg';
      default:
        return $type;
    }
  }
  
}