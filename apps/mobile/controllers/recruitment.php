<?php defined('SYSPATH') or die('No direct script access.');

class Recruitment_Controller extends Web_Controller {
    
  public function index($list = 'active') {
    $this->title = "Recruits";
    $this->view = 'recruitment/index';
    
    $lists = array(
      'active' => 0,
      'bidded' => 1,
      'not-recruiting' => 2
    );
    $list_id = $lists[$list];
    if (is_null($list_id))
      Event::run('system.404');
    
    Router::$routed_uri = 'recruitment/'. $list;
    $this->recruits = ORM::factory('recruit')->orderby('name', 'ASC')->find_by_list($list_id);
    $this->list_counts = ORM::factory('recruit')->list_counts();
  }
  
  public function bidded() {
    $this->index('bidded');
  }
  
  public function active() {
    $this->index('active');
  }
  
  public function show($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! $this->recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->recruit, 'view'))
      Event::run('system.403');

    $this->title = $this->recruit->name;

    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->recruit->id;
      $this->comment->object_type = 'recruit';
      if ($this->comment->validate($post, TRUE)) {
        message::add('success', 'Your comment has been posted.');
      }
      else {
        message::add('error', 'Error adding comment. Please try again.');
      }
    }
  }
  
}