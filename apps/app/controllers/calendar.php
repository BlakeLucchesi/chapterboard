<?php defined('SYSPATH') or die('No direct script access.');

class Calendar_Controller extends Private_Controller {

  public $secondary = 'menu/calendar';
  
  public function __construct() {
    parent::__construct();
    javascript::add('scripts/calendar.js');
    css::add('styles/calendar.css');
  }
  
  public function index() {
    $this->title = 'Calendar';
    $this->month = $this->input->get('month', date('m'));
    $this->year = $this->input->get('year', date('Y'));
    $this->calendar_id = $this->input->get('calendar_id');
    
    if ($this->calendar_id) {
      $this->session->set('calendar_id', $this->calendar_id);
    }
    else {
      $this->calendar_id = $this->session->get('calendar_id') ? $this->session->get('calendar_id') : NULL;
    }
    
    if (request::is_ajax()) {
      $response['year'] = $this->year;
      $response['month'] = $this->month;
      $response['title'] = strftime('%B %Y', mktime(0, 0, 0, $this->month, 1, $this->year));
      $response['events'] = array();
      $this->events = ORM::factory('event')->find_by_month($this->month, $this->year, $this->calendar_id, TRUE);
      foreach ($this->events as $event) {
        $signup = $event->user_signed_up();
        $response['events'][] = array(
          'id' => $event->id,
          'title' => $event->title,
          'date' => date::display($event->start, 'Y:m:d H:i'),
          'date_formatted' => date::display($event->start, 'jS, l'),
          'time' => $event->start() == '' ? 'All Day' : $event->start(),
          'attending' => $signup->rsvp == 1 ? TRUE : FALSE,
          'location' => $event->location,
        );
      }
      response::json(TRUE, null, $response);
    }
    
    $calendar = Calendar::factory($this->month, $this->year)->standard('weekends')->standard('today');
    $this->events = ORM::factory('event')->find_by_month($this->month, $this->year, $this->calendar_id);
    foreach ($this->events as $event) {
      $calendar->attach(
        $calendar->event()->
        condition('year', date::year($event->start))->
        condition('month', date::month($event->start))->
        condition('day', date::day($event->start))->
        output(html::anchor('calendar/event/'. $event->id, '<span class="time">'. $event->start() .'</span> '. $event->title))
      );
      // Multi-day events
      if ($event->start_day != $event->end_day) {
        $count = date::day_difference($event->end_day, $event->start_day);
        for ($i = 1; $i <= $count; $i++) {
          $date = date::modify("+$i days", $event->start);
          $calendar->attach(
            $calendar->event()->
            condition('year', date::year($date))->
            condition('month', date::month($date))->
            condition('day', date::day($date))->
            output(html::anchor('calendar/event/'. $event->id, '<span class="all-day">'. $event->title .'</span>'))
          );          
        }
      }
      
    }
    $this->content = $calendar->render('calendar/calendar');
  }
  
  /**
   * Allow the user to set what notifications they want
   * to receive when it comes to the calendars and events.
   */
  function notifications() {
    $this->title = 'Calendar Notification Settings';
    
    // Perform updates if the form is being saved.
    if ($post = $this->input->post()) {
      ORM::factory('notification')->where(array('user_id' => $this->user->id, 'object_type' => 'calendar'))->delete_all();
      foreach ($post['calendar'] as $id => $value) {
        $calendar = ORM::factory('calendar', $id);
        if ($this->acl->allowed($calendar, 'view')) {
          ORM::factory('notification')->signup('calendar', $id, $this->user->id, $value);
        }
      }
      $this->user->event_notify = $post['event_notify'];
      $this->user->save();
      message::add('success', 'Notifications settings saved.');
    }

    $this->calendars = ORM::factory('calendar')->get_list();
    $this->notifications = ORM::factory('notification')->find_by_object('calendar');
  }
  
  /**
   * Display a users ical feed.
   */
  public function feedurl() {
    $this->title = 'Sync Your Calendar';
    if (request::is_ajax())
      response::html(View::factory('calendar/feedurl'));
  }
  
  /**
   * Reset a users ical feed.
   */
  public function feed_reset() {
    $this->user->calendar_token(TRUE);
    $this->feedurl();
  }
  
  /**
   * Import an XML feed from Google Calendar.
   */
  // public function import() {
  //   $this->items = array();
  //   // $this->calendars = ORM::factory('calendar')->list_calendars();
  //   $meetings = array('Chapter', 'Exec. Board', 'Pledge Meeting');
  // 
  //   $feed = "http://www.google.com/calendar/feeds/foers101%40mail.chapman.edu/public/full";
  //   $user_id = 5892;
  //   $calendar_id = 1794;
  // 
  //   // Query the calendar 1 week at a time.
  //   for ($i = 126; $i >= -60; $i -= 7) { 
  //     $start_date = date::display($i == 0 ? 'now' : '+'. $i .' days', 'Y-m-d\TH:i:s');
  //     $end_date = date::display('+'. $i + 7 .' days', 'Y-m-d\TH:i:s');
  //     $url = sprintf('%s?orderby=starttime&start-min=%s&start-max=%s', $feed, $start_date, $end_date);
  //
  //     $doc = new DOMDocument();
  //     $doc->load($url);
  //     $entries = $doc->getElementsByTagName("entry");
  //     foreach ($entries as $entry) {
  //       $unique = $entry->getElementsByTagName('id')->item(0)->nodeValue;
  //       foreach ($this->items as $existing) {
  //         if ($existing['unique'] == $unique)
  //           continue 2;
  //       }
  //       $item['unique'] = $unique; // Only import each item once.
  //       
  //       // Setup fields.
  //       $titles = $entry->getElementsByTagName("title");
  //       $item['title'] = $titles->item(0)->nodeValue;
  //       
  //       $body = $entry->getElementsByTagName('content');
  //       if ($body) {
  //         $item['body'] = $body->item(0)->nodeValue;
  //       }
  //       
  //       $item['status'] = 1;
  //       $item['calendar_id'] = $calendar_id; // Default to social.
  //       // if (in_array($item['title'], $meetings)) {
  //       //   $item['calendar_id'] = 114; // Import as meetings.
  //       // }
  //       
  //       $location = $entry->getElementsByTagName('where');
  //       if ($location->item(0)->getAttributeNode('valueString')->value) {
  //         $item['location'] = $location->item(0)->getAttributeNode('valueString')->value;
  //       }
  //       
  //       $times = $entry->getElementsByTagName("when");
  //       $item['startTime'] = $times->item(0)->getAttributeNode("startTime")->value;
  //       $item['endTime'] = $times->item(0)->getAttributeNode("endTime")->value;
  //       
  //       // Start day/time
  //       if (strlen($item['startTime']) == 10) {
  //         $item['start_day'] = date::display($item['startTime'], 'm/d/Y', FALSE);
  //         $item['start_time'] = '';
  //         $item['all_day'] = 1;
  //       }
  //       else {
  //         $item['start_day'] = date::display($item['startTime'], 'm/d/Y');
  //         $item['start_time'] = date::display($item['startTime'], 'g:ia');
  //         $item['all_day'] = 0;
  //       }
  //       
  //       // End day/time
  //       if ($item['endTime']) {
  //         if (strlen($item['endTime']) == 10) {
  // 
  //           // Note: All day events show an end day with an extra day, which is incorrect 
  //           // for how we show our events.  Subtract a day from the end day on all
  //           // day events.
  //           $start = date::display($item['startTime'], 'U', FALSE);
  //           $end = date::display($item['endTime'], 'U', FALSE);
  //           $diff = date::timespan($start, $end);
  //           if ($diff['days'] > 0) {
  //             $item['endTime'] = date::modify('-1 day', $item['endTime']);
  //           }
  // 
  //           $item['end_day'] = date::display($item['endTime'], 'm/d/Y', FALSE);
  //           $item['end_time'] = '';
  //         }
  //         else {
  //           $item['end_day'] = date::display($item['endTime'], 'm/d/Y');
  //           $item['end_time'] = date::display($item['endTime'], 'g:ia');
  //         }
  //       }
  //       else {
  //         $item['end_day'] = $item['start_day'];
  //         $item['end_time'] = '';
  //       }
  //       
  //       $this->items[] = $item;
  //     }
  //   }
  //   
  //   foreach ($this->items as $item) {
  //     $event = ORM::factory('event');
  //     if ($event->validate($item, TRUE)) {
  //       $event->user_id = $user_id;
  //       $event->save();
  //       message::add(TRUE, sprintf('Event %s on %s saved successfully.', $event->title, $event->show_date()));
  //     }
  //     else {
  //       message::add(FALSE, sprintf('Failed to save event %s', $item['title']));
  //       message::add(FALSE, print_r($item->errors(), TRUE));
  //     }
  //   }
  // }
}