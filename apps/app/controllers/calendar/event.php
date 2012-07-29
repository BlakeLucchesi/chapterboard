<?php defined('SYSPATH') or die('No direct script access.');

class Event_Controller extends Calendar_Controller {
  
  /**
   * View event details
   */
  public function show($id) {
    $this->sort = in_array($this->input->get('sort'), array('name', 'timestamp')) ? $this->input->get('sort') : 'name';
    $this->event = ORM::factory('event', $id);
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event->calendar, 'view'))
      Event::run('system.403');
    if ( ! $this->event->status)
      Event::run('system.404');
    
    $this->title = $this->event->title;
    $this->signup = $this->event->user_signed_up();
    $this->signups = ORM::factory('signup')->with('user')->with('user:profile')->attendance($this->event->id, 1, $this->sort);
    $this->not_attending = ORM::factory('signup')->with('user')->with('user:profile')->attendance($this->event->id, 2);
    
    // Check to see if a comment was posted.
    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->event->id;
      $this->comment->object_type = 'event';
      if ($this->comment->validate($post, TRUE)) {
        // Comment has been saved, now save uploaded files.
        if ($uploads = $this->session->get('uploads-'. $post['key'])) {
          foreach ($uploads as $upload) {
            // Move temp files to upload directory and insert records into database.
            if ($fileinfo = upload::save($upload, $upload['filename'], Kohana::config('upload.directory'))) {
              $upload = array_merge($upload, $fileinfo);
            }
            $upload['object_type'] = 'comment';
            $upload['object_id'] = $this->comment->id;
            ORM::factory('file')->insert($upload);
          }
        }
        $this->session->delete('uploads-'. $post['key']);
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }
    else {
      $this->form['key'] = text::token();
    }

    if (request::is_ajax()) {
      $response = array(
        'id' => $this->event->id,
        'title' => $this->event->title,
        'start' => $this->event->start(),
        'date_formatted' => $this->event->show_date(),
        'location' => $this->event->location,
        'mappable' => $this->event->mappable,
        'map_url' => sprintf('http://maps.google.com/maps?q=%s', urlencode($this->event->location)),
        'body' => $this->event->body,
        'editable' => A2::instance()->allowed($this->event, 'edit') ? TRUE : FALSE,
        'attendees_formatted' => format::plural($this->signups->count(), '@count member is going', '@count members are going'),
        'attending' => $this->signup->rsvp == 1 ? TRUE : FALSE,
        'comments' => array(),
        'attendees' => array(),
      );
      foreach ($this->event->comments() as $comment) {
        $response['comments'][] = array(
          'body' => $comment->body,
          'author' => $comment->user->name(),
          'picture' => theme::image('small', $comment->user->picture(), array(), TRUE),
          'created' => date::display($comment->created, 'M d,Y g:ia'),
        );
      }
      foreach ($this->signups as $signup) {
        $response['attendees'][] = array(
          'id' => $signup->user->id,
          'name' => $signup->user->name(),
          'picture' => theme::image('small', $signup->user->picture(), array(), TRUE),
          'type' => $signup->user->type(),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Add a new event
   */
  public function add() {
    $this->title = 'Add Event';
    $this->view = 'calendar/event/form';
    if ( ! A2::instance()->allowed('event', 'add'))
      Event::run('system.404');

    $this->form = array(
      'start_day' => date::display('now', 'm/d/Y'),
      'start_time' => preg_replace('/\:[0-9]{2}/i', ':00', date::display('now', 'g:i a')),
      'end_day' => date::display('now', 'm/d/Y'),
    );
    
    if ($post = $this->input->post()) {
      $event = ORM::factory('event');
      if ($event->validate($post, TRUE)) {
        message::add('success', 'Event added successfully.');
        if (request::is_ajax()) {
          $response = array(
            'id' => $event->id,
            'title' => $event->title
          );
          response::json(TRUE, null, $response);
        }
        url::redirect(sprintf('calendar?year=%d&month=%d', date::year($event->start_day), date::month($event->start_day)));
      }
      else {
        message::add('error', 'Could not add event. Please fix the errors below and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_event');
        if (request::is_ajax()) {
          $response = array('message' => array_pop($this->errors));
          response::json(FALSE, null, $response);
        }
      }
    }
    if (request::is_ajax()) {
      $response = array(
        'calendars' => array(),
        'event' => (object) array(),
      );
      $this->calendars = ORM::factory('calendar')->get_list();
      foreach ($this->calendars as $calendar) {
        $response['calendars'][] = array(
          'id' => $calendar->id,
          'title' => $calendar->title
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Edit event.
   */
  public function edit($id) {
    Router::$routed_uri = 'calendar';
    $this->event = ORM::factory('event', $id);
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event, 'edit'))
      Event::run('system.403');

    $this->title = sprintf('Edit Event: %s', $this->event->title);
    $this->view = 'calendar/event/form';
    $this->form = $this->event->as_array();
    
    if ($post = $this->input->post()) {
      if ($post['apply_all']) {
        $parent = $this->event->is_parent() ? $this->event : $this->event->parent;
        if ($parent->validate($post, TRUE)) {
          $parent->update_repeat_events();
          message::add(TRUE, 'All repeating events have been updated.');
          url::redirect('calendar/event/'. $this->event->id);
        }
      }
      else { // Update just this instance of the event.
        if ($this->event->validate($post, TRUE)) {
          message::add('success', 'Event updated successfully.  Attending members have been notified of any date or location changes.');
          url::redirect('calendar/event/'. $this->event->id);
        }
      }
      message::add('error', 'There were errors with your information, please fix them and try again.');
      $this->form = $post->as_array();
      $this->errors = $post->errors('form_event');
    }
  }
  
  public function delete($id) {
    $this->event = ORM::factory('event', $id);
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event, 'delete'))
      Event::run('system.403');
      
    $this->event->unpublish();
    message::add('success', '%s deleted.', $this->event->title);
    url::redirect('calendar');
  }
  
  public function delete_all($id) {
    $this->event = ORM::factory('event', $id);
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event, 'delete'))
      Event::run('system.403');
    
    $parent = $this->event->is_parent() ? $this->event : $this->event->parent;
    $parent->unpublish();
    foreach ($parent->children as $child) {
      $child->unpublish();
    }
    message::add('success', '%s and all related repeating events have been deleted.', $this->event->title);
    url::redirect('calendar');
  }
}