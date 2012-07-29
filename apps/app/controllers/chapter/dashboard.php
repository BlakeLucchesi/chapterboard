<?php defined('SYSPATH') or die('No direct script access.');

class Dashboard_Controller extends Private_Controller {
  
  protected $secondary = 'menu/dashboard';
  
  /**
   * Dashboard index.
   */
  function index() {
    $this->title = 'Dashboard';
    $this->announcements = ORM::factory('announcement')->find_by_site($this->site->id);
    $this->notices = $this->user->notices();
    $this->topics = ORM::factory('topic')->recent_topics(10);
    $this->events = ORM::factory('event')->find_upcoming_events('+4 days');
    $this->recruits = ORM::factory('recruit')->find_recent();
    $this->system_messages = ORM::factory('system_message')->get();
    
    $this->outstanding_balance = $this->user->balance();
    $this->campaigns = ORM::factory('campaign')->find_all_active($this->site->id);
    $this->birthdays = ORM::factory('user')->upcoming_birthdays();
    $this->service_member_total = ORM::factory('service_hour')->totals_for_member();
    $this->service_chapter_total = ORM::factory('service_hour')->totals_for_chapter();
    $this->photos = ORM::factory('album')->recent_photos(4);
  }
}