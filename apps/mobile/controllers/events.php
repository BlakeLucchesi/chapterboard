<?php defined('SYSPATH') or die('No direct script access.');

class Events_Controller extends Web_Controller {
  
  public function _pre_controller() {
    Router::$routed_uri = 'events';
  }
  
  /**
   * Events listing page.
   */
  public function index($year = NULL, $month = NULL) {
    $this->month = $month ? $month : date('m');
    $this->year = $year ? $year : date('Y');
    $this->title = strftime('%B %Y', mktime(0, 0, 0, $this->month, 1, $this->year));
        
    $this->prev = 'events/'. strftime('%Y/%m', mktime(0,0,0, $this->month - 1, 1, $this->year));
    $this->next = 'events/'. strftime('%Y/%m', mktime(0,0,0, $this->month + 1, 1, $this->year));
    $this->events = ORM::factory('event')->find_by_month($this->month, $this->year, NULL, TRUE);
  }
  
  /**
   * Show an event's details.
   */
  public function show($id) {
    $this->event = ORM::factory('event', $id);
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event->calendar, 'view'))
      Event::run('system.404');

    $this->title = $this->event->title;
    $this->signup = $this->event->user_signed_up();
    $this->signups = ORM::factory('signup')->attendance($this->event->id, 1);

    // Check to see if a comment was posted.
    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->event->id;
      $this->comment->object_type = 'event';
      if ($this->comment->validate($post, TRUE)) {
        message::add('success', 'Your comment has been posted.');
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }
  }
  
  /**
   * Turn off and on a signup.
   */
  public function signup() {
    if ($post = $this->input->post()) {
      $this->event = ORM::factory('event', $post['event_id']);
      
      if ( ! $this->event->loaded)
        Event::run('system.404');
      if ( ! A2::instance()->allowed($this->event->calendar, 'view'))
        Event::run('system.403');

      $signup = ORM::factory('signup')->toggle($this->event->id, $this->user->id, $post['rsvp']);

      if ($signup->rsvp == 1) {
        message::add(TRUE, 'You are now attending this event.');
      }
      else {
        message::add(TRUE, 'You are no longer attending this event.');
      }
      url::redirect('events/'. $this->event->id);
    }
    Event::run('system.404');
  }
  
}