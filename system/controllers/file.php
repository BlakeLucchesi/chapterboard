<?php defined('SYSPATH') or die('No direct script access.');

class File_Controller extends Controller {
  
  function index($filename) {
    // if ( ! A1::instance()->logged_in())
    //   Event::run('system.404');
    echo download::deliver(Kohana::config('core.filepath').'/original/'.$filename);
  }
  
  function thumb($size, $filename) {
    // if ( ! A1::instance()->logged_in())
    //   Event::run('system.404');
    $file = sprintf('%s/%s/%s', Kohana::config('core.filepath'), $size, $filename);
    if ( ! is_readable($file)) {
      $original = Kohana::config('core.filepath').'/original/'.$filename;
      if (is_file($original)) {
        Image::factory($original)->thumbnail($filename, $size);
      }
      else {
        Event::run('system.404');
      }
    }

    if (is_file($file))
      echo download::deliver($file);
  }
  
  function backups($filename) {
    $this->acl = A2::instance();
    $this->auth = A1::instance();
    $this->user = $this->acl->get_user();
    
    $file = Kohana::config('core.filepath').'/backups/'. $filename;
    $backup = ORM::factory('backup_queue')->where('filename', $filename)->find();
    if (is_file($file) && $backup->loaded && $this->user->site_id == $backup->site_id && ! $backup->is_expired()) {
      echo download::deliver($file);
    }
    else {
      Event::run('system.404');
    }
  }
}