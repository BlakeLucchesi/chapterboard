<?php defined('SYSPATH') or die('No direct script access.');

class Site_signup_Model extends ORM {
  
  public function disable() {
    $this->confirmed = 1;
    $this->save();
  }
  
}