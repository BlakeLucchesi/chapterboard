<?php defined('SYSPATH') or die('No direct script access.');

class Service_Controller extends Private_Controller {
  
  public $secondary = 'menu/service';

  public function __construct($arg) {
    parent::__construct($arg);
    javascript::add('scripts/service.js');
    $this->members = ORM::factory('user')->active_users_list();
    
    // Handle the school year periods.
    $this->periods = $this->_periods();
    $this->current_period = $this->_this_period();
    
    if ($this->period = $this->input->get('period')) {
      $this->session->set('service_period', $this->period);
      if ($this->period == 'custom') {
        $this->start_date = $this->input->get('start_date');
        $this->end_date = $this->input->get('end_date');
        $this->session->set('service_period_start', $this->start_date);
        $this->session->set('service_period_end', $this->end_date);
      }
      else {
        $this->session->delete('service_period_start', 'service_period_end');
      }
    }
    else if ($this->session->get('service_period')) {
      $this->period = $this->session->get('service_period');
      $this->start_date = $this->session->get('service_period_start');
      $this->end_date = $this->session->get('service_period_end');
    }
    else {
      $this->period = $this->_this_period();
    }
    list($start, $end) = str_split($this->period, 2);
    $this->start_year = sprintf('20%02d', $start);
    $this->end_year = sprintf('20%02d', $end);
    
    if ($this->period == 'custom') {
      $this->period_title = sprintf('%s-%s', date::display($this->start_date, 'F jS, Y', FALSE), date::display($this->end_date, 'F jS, Y', FALSE));
      $this->period_filter = array(
        'start_date' => $this->start_date,
        'end_date' => $this->end_date,
      );
    }
    else {
      $this->period_title = sprintf('%s-%s', $this->start_year, $this->end_year);
      $this->period_filter = $this->period;
    }
  }

  /**
   * Display overview of events the chapter has participated in.
   */
  function index() {
    $this->title = 'My Service Activities';
    $this->hours = ORM::factory('service_hour')->find_by_member($this->user->id, $this->period_filter);
    
    if (request::is_ajax()) {
      $response = array();
      foreach ($this->hours as $hours) {
        $response[] = array(
          'id' => $hours->event->id,
          'title' => $hours->event->title,
          'date' => date::display($hours->event->date, 'short', TRUE),
          'hours' => $hours->hours,
          'dollars' => money::display($hours->dollars),
        );
      }
      response::json(TRUE, NULL, $response);
    }
  }
  
  /**
   * Record hours and donations for an event.
   */
  function record($id = NULL) {
    $this->title = 'Record Service Hours and Donations';
    $this->event = ORM::factory('service_event', $id);

    // If we're recording for an event, do some setup and error checking.
    if ($this->event->loaded) {
      $this->form['event_id'] = $this->event->id;
    }
    // If an admin, let them record hours for others.
    if (A2::instance()->allowed('service_event', 'admin')) {
      $this->select_members = ORM::factory('user')->select_members($this->user);
    }
    
    if ($post = $this->input->post()) {
      // If existing event, just submit hours as normal.
      if ( ! $post['event_id']) {
        $this->event = ORM::factory('service_event');
        $event_post = $this->input->post();
        if ($this->event->validate($event_post, TRUE)) {
          $post['event_id'] = $this->event->id;
        }
        else {
          $errors = TRUE;
        }
      }
      else {
        $this->event = ORM::factory('service_event', $post['event_id']);
      }
      
      $service_hour = ORM::factory('service_hour');
      if ($service_hour->validate($post, TRUE)) {
        message::add('success', 'Service for %s recorded succesfully.', $service_hour->event->title);
        url::redirect('service/events/'. $service_hour->event_id);
      }
      else {
        $errors = TRUE;
      }
      
      if ($errors) {
        message::add('error', 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_service_record');
      }
    }
  }
  
  /**
   * Edit the details of a record. 
   */
  function edit($id) {
    $this->view = 'service/record';
    $this->title = 'Edit Record';
    $this->record = ORM::factory('service_hour', $id);
    $this->event = $this->record->event;
    $this->select_members = ORM::factory('user')->select_members($this->user);
    
    $this->form = $this->record->as_array();
    if ( ! $this->record->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->record, 'edit'))
      Event::run('system.403');
      
    if ($post = $this->input->post()) {
      $post['event_id'] = $this->event->id;
      if ($this->record->validate($post, TRUE)) {
        message::add('Changes saved.');
        url::redirect('service/events/'. $this->event->id);
      }
      else {
        message::add('error', 'There was an error while saving your changes, please try again');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_service_record');
      }

    }
  }
  
  /**
   * Delete a record.
   */
  function delete($id) {
    $this->record = ORM::factory('service_hour', $id);
    $this->event = $this->record->event;
    
    if ( ! $this->event->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->record, 'delete'))
      Event::run('system.403');
      
    $this->record->delete();
    message::add('success', 'Service record deleted successfully.');
    url::redirect('service/events/'. $this->event->id);
  }
  
  function _periods() {
    if (date::month($this->site->created) > 6) {
      $start_period = substr(date::year($this->site->created), 2, 2) . substr(date::year($this->site->created) + 1, 2, 2);
      $start_display = date::year($this->site->created) .'-'. (date::year($this->site->created) + 1);
    }
    else {
      $start_period = substr(date::year($this->site->created) - 1, 2, 2) . substr(date::year($this->site->created), 2, 2);
      $start_display = (date::year($this->site->created) - 1) .'-'. date::year($this->site->created);
    }
    $years[$start_period] = $start_display;
    
    $end_period = $this->_this_period();
    
    $start = substr($start_period, 2, 2);
    $end = substr($end_period, 0, 2);

    while ($start < $end) {
      $key = sprintf('%02d', $start) . sprintf('%02d', $start + 1);
      $years[$key] = sprintf('20%02d-20%02d', $start, $start + 1);
      $start++;
    }
    $years[$this->_this_period()] = $this->_this_period_display();
    krsort($years);
    $years['custom'] = '- Custom -';
    return $years;
  }
  
  function _end_year() {
    return date::month() > 6 ? date::year() + 1 : date::year();
  }
  
  function _this_period() {
    if (date::month() > 6) {
      $period = substr(date::year(), 2, 2) . substr(date::year() + 1, 2, 2);
    }
    else {
      $period = substr(date::year() - 1, 2, 2) . substr(date::year(), 2, 2);
    }
    return $period;
  }
  
  function _this_period_display() {
    if (date::month() > 6) {
      $period = date::year() .'-' . (date::year() + 1);
    }
    else {
      $period = (date::year() - 1) .'-'. date::year();
    }
    return $period;
  }
}