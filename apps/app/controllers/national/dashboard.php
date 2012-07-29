<?php defined('SYSPATH') or die('No direct script access.');

class Dashboard_Controller extends National_Controller {
  
  public $secondary = 'menu/dashboard';
  
  /**
   * Dashboard index.
   */
  function index() {
    $this->title = 'Dashboard';
    $this->notices = $this->user->notices();
    $this->topics = ORM::factory('topic')->recent_topics(10);
    $this->events = ORM::factory('event')->find_upcoming_events('+4 days');
    $this->system_messages = ORM::factory('system_message')->get();

    $this->birthdays = ORM::factory('user')->upcoming_birthdays();
    $this->service_total = ORM::factory('service_hour')->totals_for_national($this->site->chapter_id);
  }
  
}