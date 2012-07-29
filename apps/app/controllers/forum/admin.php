<?php defined('SYSPATH') or die('No direct script access.');

class Admin_Controller extends Forum_Controller {
  
  public function __construct() {
    parent::__construct();
    if ( ! A2::instance()->allowed('forum', 'manage'))
      Event::run('system.403');
  }
  
  
  function index() {
    $this->title = 'Manage Forum Boards';
    if ($post = $this->input->post()) {
      if (ORM::factory('group_rule')->set_rules('forum', 'view', $post['groups'])) {
        message::add('success', 'Forum permissions saved.');
      }
      else {
        message::add('error', 'You tried submitting some invalid values.  Please try again.');
      }
    }
    $this->forums = ORM::factory('forum')->where('site_id', $this->site->id)->where('status', 1)->find_all();
    $this->groups = ORM::factory('group')->where('site_id', $this->site->id)->find_all();
    $this->selected = ORM::factory('group_rule')->get_rules('forum');
  }
  
  
  public function add() {
    $this->title = 'Add New Forum';
    $this->groups = ORM::factory('group')->where('site_id', $this->site->id)->find_all();

    if ($post = $this->input->post()) {
      if (ORM::factory('forum')->insert($post)) {
        if (request::is_ajax())
          response::json(TRUE);
        url::redirect('forum/admin');
      }
      else {
        message::add('error', 'Forum must have a title.');
        if (request::is_ajax())
          response::json(FALSE, View::factory('forum/admin/add'));
      }
    }

    if (request::is_ajax())
      response::html(View::factory('forum/admin/add'));
  }
  
  
  public function edit($id) {
    $this->forum = ORM::factory('forum', $id);
    if ( ! A2::instance()->allowed($this->forum, 'edit'))
      Event::run('system.403');

    $this->title = 'Editing Forum: '. $this->forum->title;
    $this->form = $this->forum->as_array();
    
    if (request::is_ajax())
      response::html(View::factory('forum/admin/edit'));
  }
  
  public function rename() {
    if ($post = $this->input->post()) {
      $this->forum = ORM::factory('forum', $post['forum_id']);
      if (! A2::instance()->allowed($this->forum, 'edit'))
        Event::run('system.403');
      if (valid::standard_text($post['title']) && valid::standard_text($post['description'])) {
        $this->forum->title = $post['title'];
        $this->forum->description = $post['description'];
        $this->forum->save();
        message::add(TRUE, 'The updated forum name has been saved.');
      }
      else {
        message::add(FALSE, 'Forum names can only contain numbers, letters and spaces.');
      }
      url::redirect('forum/admin');
    }
    Event::run('system.404');
  }
  
  public function delete() {
    if ($post = $this->input->post()) {
      $this->forum = ORM::factory('forum', $post['forum_id']);      
      if ($post['confirm']) {
        if ( ! A2::instance()->allowed($this->forum, 'delete'))
          Event::run('system.403');
        $this->forum->unpublish();
        message::add(TRUE, '"%s" forum has been removed.', $this->forum->title);        
      }
      else {
        message::add('error', 'You must confirm the removal of a forum by clicking on the I agree button. The "%s" forum was not removed.', $this->forum->title);
      }
    }
    url::redirect('forum/admin');
    Event::run('system.404');
  }
  
}