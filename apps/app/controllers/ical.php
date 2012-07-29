<?php defined('SYSPATH') or die('No direct script access.');

class Ical_Controller extends Controller {
  
  public function index($token) {
    // Load the user account based on the token.
    $this->user = ORM::factory('user')->find_by_token($token);
    if ( ! $this->user->loaded)
      Event::run('system.404');

    $this->site = $this->user->site;
    
    A1::instance()->complete_login($this->user);
    Auth::setup_globals();
    Auth::setup_permissions();
    
    date::timezone($this->site->timezone); // Set timezone, not done automatically for us via auth_hook.

    // Start building calendar meta data.
    require_once APPPATH.'libraries/Ical.php';
    $v = new vcalendar();  // initiate new CALENDAR
    $v->setConfig('unique_id', 'chapterboard.com'); // config with site domain
    $v->setProperty('X-WR-CALNAME', $this->site->chapter->name. ' ChapterBoard'); // set some X-properties, name, content.. .
    $v->setProperty('X-WR-CALDESC', $this->site->chapter->name .' at '. $this->site->school->name .' from ChapterBoard.com');
    $v->setProperty('X-WR-TIMEZONE', $this->site->timezone);
    $v->allowEmpty = FALSE;
    
    // Get all the events for this site -- make sure to filter to the user.
    $events = ORM::factory('event')->find_ical($this->user, $this->site);
    foreach ($events as $event) {
      $e = new vevent();
      $e->setProperty('URL', url::site('calendar/event/'. $event->id));
      $e->setProperty('SUMMARY', $event->title);
      if ($event->all_day) {
        $e->setProperty('DTSTART', date::display($event->start, 'Ymd'), array('VALUE' => 'DATE'));
        // Add an additional day to end date because ical sucks.
        $end = date::display($event->has_end() ? date::modify('+1 day', $event->end) : $event->start, 'Ymd');
        $e->setProperty('DTEND', $end, array('VALUE' => 'DATE'));
      }
      else {
        $e->setProperty('DTSTART', date::display($event->start, 'YmdHis'));
        $e->setProperty('DTEND', date::display($event->has_end() ? $event->end : $event->start, 'YmdHis'));
      }
      if ($event->location) {
        $e->setProperty('LOCATION', $event->location);
      }
      $v->addComponent( $e );
    }

    /* alt. production */
    if ( ! $this->input->get('test')) {
      $v->returnCalendar(); // generate and redirect output to user browser
    }

    A1::instance()->logout(TRUE);
    $str = $v->createCalendar(); // generate and get output in string, for testing?
    echo $str;
  }
}