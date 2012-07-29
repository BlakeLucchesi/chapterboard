<?php defined('SYSPATH') or die('No direct script access.');

class National_Controller extends Private_Controller {
  /**
   * Only national accounts have access to this entire section.
   */
  public function _pre_controller() {
    if ( ! $this->site->is_national())
      Event::run('system.404');
    css::add('styles/national.css');
  }
  
}