<?php defined('SYSPATH') or die('No direct script access.');

class Members_Controller extends Web_Controller {
    
  public function index($type = 'active') {
    $this->view = 'members/index';
    $this->title = 'Members';
    Router::$routed_uri = 'members/'. $type;
    $this->members = ORM::factory('user')->search_profile($this->input->get('q'), $type);
  }
  
  public function active() {
    $this->index('active');
  }
  
  public function pledge() {
    $this->index('pledge');
  } 
  
  public function show($id) {
    Router::$routed_uri = 'members';
    $this->member = ORM::factory('user', $id);
    
    if ( ! $this->member->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->member, 'view'))
      Event::run('system.403');

    $this->title = $this->member->name();
  }
  
}