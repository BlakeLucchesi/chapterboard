<?php defined('SYSPATH') or die('No direct script access.');

class Signups_Controller extends Calendar_Controller {
  
  /**
   * Show a list of events that the user has signed up for.
   */
  public function index() {
    $offset = 'today';
    $this->title = 'My Events';
    
    // Join in all the events they have rsvp'd for.
    $this->signups = ORM::factory('signup')->with('event')->where('signups.user_id', $this->user->id)->where('signups.rsvp', TRUE)->where('start>', date::to_db($offset))->orderby('start', 'ASC')->find_all();

    $calendar = Calendar::factory()->standard('today');
    foreach ($this->signups as $signup) {
      $calendar->attach(
        $calendar->event()->
        condition('year', date::year($signup->event->start))->
        condition('month', date::month($signup->event->start))->
        condition('day', date::day($signup->event->start))->add_class('event')
      );
    }
    $this->calendar_1 = $calendar->render('calendar/calendar-mini');
    $calendar->year = date::to_db('+1 month', 'Y');
    $calendar->month = date::to_db('+1 month', 'm');
    $this->calendar_2 = $calendar->render('calendar/calendar-mini');
  }
  
  /**
   * Print view.
   */
  public function printable() {
    $this->template = 'print';
    $this->index();
  }

  /**
   * Turn off and on a signup.
   */
  public function toggle() {
    if ($post = $this->input->post()) {
      $this->event = ORM::factory('event', $post['event_id']);
      
      if ( ! $this->event->loaded)
        Event::run('system.404');
      if ( ! A2::instance()->allowed($this->event->calendar, 'view'))
        Event::run('system.403');

      $signup = ORM::factory('signup')->toggle($this->event->id, $this->user->id, $post['rsvp']);

      if ($signup->rsvp == 1) {
        message::add(TRUE, 'You are now attending. Click on "My Events" to view other events you have signed up for.');
      }
      else {
        message::add(TRUE, 'You are no longer attending this event.');
      }
      
      if (request::is_ajax())
        $this->event($this->event->id);
        
      url::redirect('calendar/event/'. $this->event->id);
    }
    if (request::is_ajax())
      response::json(FALSE);
    Event::run('system.404');
  }
  
}