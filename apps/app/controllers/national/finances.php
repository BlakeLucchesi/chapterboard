<?php defined('SYSPATH') or die('No direct script access.');

class Finances_Controller extends National_Controller {
  
  public function index() {
    $this->title = 'Finances';
    $this->chapters = ORM::factory('site')->finances_by_chapter($this->site->chapter_id);
  }
  
}