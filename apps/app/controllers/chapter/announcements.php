<?php defined('SYSPATH') or die('No direct script access.');

class Announcements_Controller extends Dashboard_Controller {
  
  public function index() {
    Event::run('system.404');
  }
  
  public function add() {
    if ( ! A2::instance()->allowed('announcement', 'manage'))
      Event::run('system.403');
      
    $this->title = "Post New Announcement";
    $this->options = ORM::factory('group')->find_all();
    
    if ($post = $this->input->post()) {
      if ($this->announcement = ORM::factory('announcement')->validate($post, TRUE)) {
        message::add('success', 'Announcement posted successfully.');
        url::redirect('dashboard');
      }
      else {
        message::add('error', 'Error posting new announcement, please fix the errors and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_announcement');
      }
    }
    
    if (request::is_ajax())
      response::html(View::factory('announcements/add')->render());
  }
  
  public function show($id) {
    $this->announcement = ORM::factory('announcement')->show($id);
    if ( ! $this->announcement->loaded)
      Event::run('system.404');
    
    if (request::is_ajax())
      response::html(View::factory('announcements/show')->render());
  }
  
  public function delete($id) {
    $this->announcement = ORM::factory('announcement')->show($id);
    if ( ! $this->announcement->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed('announcement', 'manage'))
      Event::run('system.403');
    
    message::add(TRUE, 'Announcement "%s" has been unpublished.', $this->announcement->title);
    $this->announcement->unpublish();
    url::redirect('');
  }
}