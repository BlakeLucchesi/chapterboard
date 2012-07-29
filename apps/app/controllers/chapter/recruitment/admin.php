<?php defined('SYSPATH') or die('No direct script access.');

class Admin_Controller extends Recruitment_Controller {
  
  /**
   * List the categories of recruits.
   */
  function index() {
    Event::run('system.404');
    if ( ! A2::instance()->allowed('recruit', 'manage'))
      Event::run('system.403');
      
    $this->title = 'Manage Recruitment Lists';
    $this->lists = ORM::factory('recruit_list')->get();
  }
  
  /**
   * Empty recruits from a list.
   */
  function clear($list_id) {
    // $this->list = ORM::factory('recruit_list', $list_id);
    // if ( ! $this->list->loaded)
    //   Event::run('system.404');
    if ( ! A2::instance()->allowed('recruit', 'manage'))
      Event::run('system.403');
    
    $lists = array('Actively Recruiting', 'Bidded Members', 'No Longer Recruiting');
    $name = $lists[$list_id];
    ORM::factory('recruit')->archive_recruits($list_id);
    message::add('success', sprintf('Members from the %s list have been removed.', $name));
    url::redirect('recruitment');
  }
  
}