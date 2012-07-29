<?php defined('SYSPATH') or die('No direct script access.');

class Error_Controller extends Private_Controller {
  
  public function _pre_controller() {
    css::add('styles/login.css');
  }
  
}