<?php defined('SYSPATH') or die('No direct script access.');

class Dashboard_Controller extends Web_Controller {
  
  /**
   * Dashboard index.
   */
  function index() {
    $this->title = 'Dashboard';
    $this->announcements = ORM::factory('announcement')->find_by_site();
    $this->topics = ORM::factory('topic')->recent_topics(10);
    $this->events = ORM::factory('event')->find_upcoming_events('+6 days');
    
    $this->outstanding_balance = $this->user->balance();
  }
}

