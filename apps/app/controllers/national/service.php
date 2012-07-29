<?php defined('SYSPATH') or die('No direct script access.');

class Service_Controller extends National_Controller {
  
  public $secondary = 'menu/service';
  
  public function __construct($arg) {
    parent::__construct($arg);
    javascript::add('scripts/service.js');
    $this->members = ORM::factory('user')->active_users_list();
    
    // Handle the school year periods.
    $this->periods = $this->_periods();
    $this->current_period = $this->_this_period();
    
    if ($this->input->get('period')) {
      $this->period = $this->input->get('period');
      $this->session->set('service_period', $this->period);
    }
    else if ($this->session->get('service_period')) {
      $this->period = $this->session->get('service_period');
    }
    else {
      $this->period = $this->_this_period();
    }
    list($start, $end) = str_split($this->period, 2);
    $this->start_year = sprintf('20%02d', $start);
    $this->end_year = sprintf('20%02d', $end);
  }
  
  /**
   * Display summary of hours by chapter for the given period.
   */
  public function index() {
    $this->title = sprintf('Service By Chapter %d-%d', $this->start_year, $this->end_year);
    $this->chapters = ORM::factory('service_hour')->service_by_chapter($this->site->chapter_id, $this->period);
  }

  /**
   * Display an overview of hours and dollars per member for a specific chapter.
   */
  function chapter($id, $type = 'active') {
    $this->chapter = ORM::factory('site', $id);
    $this->title = sprintf('%s %d-%d', $this->chapter->chapter_name(), $this->start_year, $this->end_year);
    $this->members_title = sprintf('Member Report %d-%d', $this->start_year, $this->end_year);

    // Split members list up by type.
    $this->list_counts = ORM::factory('user')->count_types($this->chapter->id);
    $this->type = $type;

    // Show all members with hours for current period. Mark active/inactive next to name.
    if ($this->period == $this->current_period) {
      $this->members = ORM::factory('user')->where('site_id', $this->chapter->id)->orderby('searchname', 'ASC')->find_all();
    }
    // Show only members who have logged hours for a previous period, independent of current account status.
    else {
      $this->members = ORM::factory('user')->where('site_id', $this->chapter->id)->where('status', 1)->find_all();
    }
    
    // Fetch records and compute summaries and sums.
    $this->summary = array(
      'active' => array('title' => 'Active Members', 'hours' => 0, 'dollars' => 0),
      'pledge' => array('title' => 'New Members', 'hours' => 0, 'dollars' => 0),
      'alumni' => array('title' => 'Alumni Members', 'hours' => 0, 'dollars' => 0)
    );    

    $records = ORM::factory('service_hour')->hours_by_member($this->period, $this->chapter->id);
    foreach ($records as $record) {
      $this->records[$record->id] = $record;
      // Only sum hours for records shown based on member type.
      if ($this->type == $record->type || $this->type == 'all') {
        $this->sum['hours'] += $record->hours;
        $this->sum['dollars'] += $record->dollars;
      }
      // Add all hours to top group summary.
      $type = ($record->type == 'archive') ? 'active' : $record->type;
      $this->summary[$type]['hours'] += $record->hours;
      $this->summary[$type]['dollars'] += $record->dollars;
      $this->summary_sum['hours'] += $record->hours;
      $this->summary_sum['dollars'] += $record->dollars;
    }
  }
  
  /**
   * Show a list of the top service contributors.
   */
  public function members() {
    $this->title = sprintf('Top Contributors %d-%d', $this->start_year, $this->end_year);
    $limit = 50;
    $offset = NULL;
    $sort = in_array($_GET['sort'], array('hours', 'dollars')) ? $_GET['sort'] : 'hours';
    $this->members = ORM::factory('service_hour')->top_members($this->site->chapter_id, $sort, $this->period, $limit, $offset);
  }
  
  /**
   * Helpers for handling period navigation.
   */
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