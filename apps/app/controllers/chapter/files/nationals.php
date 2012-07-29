<?php defined('SYSPATH') or die('No direct script access.');

class Nationals_Controller extends Files_Controller {
  
  public function index() {
    $this->title = 'National File Folders';
    $this->folders = ORM::factory('folder')->find_by_chapter($this->site->chapter_id);
  }
  
  public function folder($id) {
    $this->folder = ORM::factory('folder', $id);
    if ( ! $this->folder->loaded)
      Event::run('system.404');
    if ( ! ($this->folder->national && $this->folder->chapter_id == $this->site->chapter_id))
      Event::run('system.403');
      
    $this->title = $this->folder->name;
  }
  
}