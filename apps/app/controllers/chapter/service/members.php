<?php defined('SYSPATH') or die('No direct script access.');

class Members_Controller extends Service_Controller {
  
  /**
   * Display an overview of hours and dollars per member.
   */
  function index($type = 'active') {
    $this->title = sprintf('Chapter Summary: %s', $this->period_title);
    $this->members_title = sprintf('Member Report: %s', $this->period_title);

    // Split members list up by type.
    $this->list_counts = ORM::factory('user')->count_types();
    $this->type = $type;

    // Show all members with hours for current period. Mark active/inactive next to name.
    $this->members = ORM::factory('user')->where('site_id', $this->site->id)->orderby('searchname', 'ASC')->find_all();
    
    // Fetch records and compute summaries and sums.
    $this->summary = array(
      'active' => array('title' => 'Active Members', 'hours' => 0, 'dollars' => 0),
      'pledge' => array('title' => 'New Members', 'hours' => 0, 'dollars' => 0),
      'alumni' => array('title' => 'Alumni Members', 'hours' => 0, 'dollars' => 0),
      'archive' => array('title' => 'Archived Members', 'hours' => 0, 'dollars' => 0)
    );

    $records = ORM::factory('service_hour')->hours_by_member($this->period_filter);
    foreach ($records as $record) {
      $this->records[$record->id] = $record;
      // Only sum hours for records shown based on member type.
      if ($this->type == $record->type || $this->type == 'all') {
        $this->sum['hours'] += $record->hours;
        $this->sum['dollars'] += $record->dollars;
      }
      // Add all hours to top group summary.
      $type = $record->type;
      $this->summary[$type]['hours'] += $record->hours;
      $this->summary[$type]['dollars'] += $record->dollars;
      $this->summary_sum['hours'] += $record->hours;
      $this->summary_sum['dollars'] += $record->dollars;
    }
    
    if (request::is_ajax()) {
      $response = array(
        'groups' => array(),
        'members' => array(),
      );
      foreach ($this->summary as $group) {
        $response['groups'][] = array(
          'name' => $group['title'],
          'hours' => number_format($group['hours'], 1),
          'dollars' => money::display($group['dollars'])
        );
      }
      foreach ($this->members as $member) {
        if (in_array($member->type, array('active', 'pledge'))) {
          $response['members'][] = array(
            'id' => $member->id,
            'name' => $member->name(),
            'hours' => number_format($this->records[$member->id]->hours, 1),
            'dollars' => money::display($this->records[$member->id]->dollars)
          );
        }
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Show a member's service report.
   */
  function show($id = NULL) {
    $this->member = ORM::factory('user')->get($id);
    if ( ! $this->member->id)
      Event::run('system.404');
    if ( ! $this->member->site_id == $this->user->site_id)
      Event::run('system.404');

    $this->hours = ORM::factory('service_hour')->find_by_member($id, $this->period_filter);
    $this->title = sprintf('%s\'s Service Activites', $this->member->name());

    if (request::is_ajax()) {
      $response = array(
        'member' => array(
          'id' => $this->member->id,
          'name' => $this->member->name(),
          'hours' => number_format($this->hours->sum('hours'), 1),
          'dollars' => money::display($this->hours->sum('dollars')),
        ),
        'events' => array(),
      );
      foreach ($this->hours as $event) {
        $response['events'][] = array(
          'id' => $event->event_id,
          'title' => $event->event->title,
          'date' => date::display($event->event->date, 'F jS, Y', TRUE),
          'hours' => number_format($event->hours, 1),
          'dollars' => money::display($event->dollars),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
}