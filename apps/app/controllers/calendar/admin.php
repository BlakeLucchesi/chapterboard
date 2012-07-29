<?php defined('SYSPATH') or die('No direct script access.');

class Admin_Controller extends Calendar_Controller {
  
  public function __construct() {
    parent::__construct();
    if ( ! A2::instance()->allowed('calendar', 'manage'))
      Event::run('system.403');
  }
  
  public function index() {
    $this->title = 'Manage Calendars';
    if ($post = $this->input->post()) {
      if (ORM::factory('group_rule')->set_rules('calendar', 'view', $post['groups'])) {
        message::add('success', 'Calendar permissions saved.');
      }
      else {
        message::add('error', 'You tried submitting some invalid values.  Please try again.');
      }
    }
    $this->calendars = ORM::factory('calendar')->where('site_id', $this->site->id)->where('status', TRUE)->find_all();
    $this->groups = ORM::factory('group')->where('site_id', $this->site->id)->find_all();
    $this->selected = ORM::factory('group_rule')->get_rules('calendar');
  }
  
  public function add() {
    $this->title = 'Add New Calendar';
    $this->groups = ORM::factory('group')->find_all();

    if ($post = $this->input->post()) {
      if (ORM::factory('calendar')->insert($post)) {
        if (request::is_ajax())
          response::json(TRUE);
        url::redirect('calendar/admin');
      }
      else {
        message::add('error', 'Calendar must have a title.');
        if (request::is_ajax())
          response::json(FALSE, View::factory('calendar/admin/add'));
      }
    }

    if (request::is_ajax())
      response::html(View::factory('calendar/admin/add'));
  }
  
  public function edit($id) {
    $this->calendar = ORM::factory('calendar', $id);
    if ( ! A2::instance()->allowed($this->calendar, 'edit'))
      Event::run('system.403');

    $this->title = 'Editing Calendar: '. $this->calendar->title;
    $this->form = $this->calendar->as_array();
    
    if (request::is_ajax())
      response::html(View::factory('calendar/admin/edit'));
  }
  
  public function rename() {
    if ($post = $this->input->post()) {
      $this->calendar = ORM::factory('calendar', $post['calendar_id']);
      if (! A2::instance()->allowed($this->calendar, 'edit'))
        Event::run('system.403');
      if (valid::standard_text($post['title'])) {
        $this->calendar->title = $post['title'];
        $this->calendar->save();
        message::add(TRUE, 'The updated calendar name has been saved.');
      }
      else {
        message::add(FALSE, 'Calendar names can only contain numbers, letters and spaces.');
      }
      url::redirect('calendar/admin');
    }
    Event::run('system.404');
  }
  
  public function delete() {
    if ($post = $this->input->post()) {
      $this->calendar = ORM::factory('calendar', $post['calendar_id']);      
      if ($post['confirm']) {
        if ( ! A2::instance()->allowed($this->calendar, 'delete'))
          Event::run('system.403');
        $this->calendar->unpublish();
        message::add(TRUE, '"%s" calendar has been removed.', $this->calendar->title);        
      }
      else {
        message::add('error', 'You must confirm the removal of a calendar by clicking on the I agree button. The "%s" calendar was not removed.', $this->calendar->title);
      }
    }
    url::redirect('calendar/admin');
    Event::run('system.404');
  }
  
}