<?php defined('SYSPATH') or die('No direct script access.');

class Events_Controller extends Service_Controller {
  
  /**
   * Show a list of all events.
   */
  function index() {
    $this->title = sprintf('Service Events %s', $this->period_title);
    $this->events = ORM::factory('service_event')->events($this->period_filter);
    
    if (request::is_ajax()) {
      $response = array();
      foreach ($this->events as $event) {
        $response[] = array(
          'id' => $event->id,
          'title' => $event->title,
          'date' => date::display($event->date, 'short', TRUE),
          'hours' => number_format($event->hours, 1),
          'dollars' => money::display($event->dollars),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Display a summary of hours and donations for a particular event.
   */
  function show($id = NULL) {
    $this->event = ORM::factory('service_event', $id);
    $this->title = sprintf('%s on %s', $this->event->title, date::display($this->event->date, 'M d, Y'));
    if ( ! $this->event->loaded ) {
      message::add('error', 'Sorry, that event does not exist, please choose from the list below.');
      url::redirect('service');
    }
    if ($this->event->site_id != $this->site->id) {
      message::add('error', 'Sorry, that event does not exist, please choose from the list below.');
      url::redirect('service');
    }
    if (request::is_ajax()) {
      $response = array(
        'event' => array(
          'id' => $this->event->id,
          'title' => $this->event->title,
          'date' => date::display($this->event->date, 'F jS, Y', TRUE),
        ),
        'members' => array()
      );
      foreach ($this->event->service_hours as $hours) {
        $response['members'][] = array(
          'id' => $hours->user_id,
          'name' => $hours->user->name(),
          'hours' => number_format($hours->hours, 1),
          'dollars' => money::display($hours->dollars),
        );
        $total_hours += $hours->hours;
        $total_dollars += $hours->dollars;
      }
      $response['event']['hours'] = number_format($total_hours, 1);
      $response['event']['dollars'] = money::display($total_dollars);
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Edit an event.
   */
  function edit($id) {
    $this->view = 'service/events/add';
    $this->event = ORM::factory('service_event', $id);
    $this->title = sprintf('Editing Event:  %s', $this->event->title);
    
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event, 'edit'))
      Event::run('system.403');

    $this->form = $this->event->as_array();
    if ($post = $this->input->post()) {
      $this->event = ORM::factory('service_event', $id);
      if ($this->event->validate($post, TRUE)) {
        message::add(TRUE, 'Changes saved');
        url::redirect('service/events/'. $this->event->id);
      }
      else {
        message::add(FALSE, 'Please fix any errors below and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_service_event');
      }
    }
  }
  
  /**
   * Delete an event.
   */
  function delete($id) {
    $this->event = ORM::factory('service_event', $id);
    
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->event, 'delete'))
      Event::run('system.403');
    
    message::add('success', 'Deleted Event: '. $this->event->title);
    $this->event->delete();
    url::redirect('service');
  }
  
}