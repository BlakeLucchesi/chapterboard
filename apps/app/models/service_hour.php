<?php defined('SYSPATH') or die('No direct script access.');

class Service_hour_Model extends ORM implements ACL_Resource_Interface {
  
  protected $belongs_to = array('event' => 'service_event', 'user', 'site');

  protected $load_with = array('user');
  
  protected $sorting = array('user.searchname' => ASC);

  function totals_for_member($user_id = NULL) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    $period = $this->parse_period();
    return $this->db->query("SELECT SUM(sh.hours) AS hours, SUM(sh.dollars) AS dollars FROM service_hours sh LEFT JOIN service_events se ON (sh.event_id = se.id) WHERE (sh.user_id = ? AND se.date >= ? AND se.date <= ?) GROUP BY sh.user_id", array($user_id, $period['start'], $period['end']))->current();
  }
  
  function totals_for_chapter() {
    $period = $this->parse_period();
    return $this->db->query("SELECT SUM(sh.hours) AS hours, SUM(sh.dollars) AS dollars FROM service_hours sh LEFT JOIN service_events se ON (sh.event_id = se.id) WHERE (se.site_id = ? AND se.date >= ? AND se.date <= ?) GROUP BY se.site_id", array(kohana::config('chapterboard.site_id'), $period['start'], $period['end']))->current();
  }
  
  function totals_for_national($chapter_id) {
    $results = array('hours' => 0, 'dollars' => 0);
    $period = $this->parse_period();
    $site_ids = ORM::factory('site')->where('chapter_id', $chapter_id)->find_keys();
    $results = $this->db->query("SELECT SUM(sh.hours) AS hours, SUM(sh.dollars) AS dollars FROM service_hours sh LEFT JOIN service_events se ON (sh.event_id = se.id) WHERE (se.site_id IN (". implode(',', $site_ids) .") AND se.date >= ? AND se.date <= ?) GROUP BY se.site_id", array($period['start'], $period['end']));
    foreach ($results as $result) {
      $total['hours'] += $result->hours;
      $total['dollars'] += $result->dollars;
    }
    return (object) $total;
  }
  
  /**
   * Takes either a period where the first two digits are the last two digits of the
   * the start date year and the second two digits are the last two digits of the end
   * date year.  Alternatively you can pass in an array with two keys: start_date, end_date.
   */
  protected function parse_period($years = NULL) {
    if ( is_array($years) && isset($years['start_date']) && isset($years['end_date'])) {
      $period['start'] = date::display($years['start_date'], 'Y-m-d h:i:s', FALSE);
      $period['end'] = date::display($years['end_date'], 'Y-m-d h:i:s', FALSE);
    }
    elseif ( ! is_null($years)) {
      list($start, $end) = str_split($years, 2);
      $period['start'] = sprintf('20%02d-07-01', $start);
      $period['end'] = sprintf('20%02d-06-31', $end);
    }
    elseif (date::month() > 6) {
      $period['start'] = sprintf('20%02d-07-01', substr(date::year(), 2, 2));
      $period['end']   = sprintf('20%02d-06-31', substr(date::year() + 1, 2, 2));
    }
    else {
      $period['start'] = sprintf('20%02d-07-01', substr(date::year() - 1, 2, 2));
      $period['end']   = sprintf('20%02d-06-31', substr(date::year(), 2, 2));
    }
    return $period;
  }

  // Show a report of hours by member.
  function hours_by_member($period, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $period = $this->parse_period($period);
    // var_dump($site_id);var_dump($period);die();
    return $this->db->select('user.*, SUM(hours) AS hours, SUM(dollars) as dollars')
      ->from('service_hours as hours')
      ->join('users AS user', 'hours.user_id', 'user.id', 'LEFT')
      ->join('service_events as event', 'event.id', 'hours.event_id', 'LEFT')
      ->where('user.site_id', $site_id)
      ->where('event.date >=', $period['start'])
      ->where('event.date <=', $period['end'])
      ->groupby('hours.user_id')
      ->get();
  }
  
  // List hours recorded by member.
  function find_by_member($id, $period) {
    $period = $this->parse_period($period);
    return $this->select('service_hours.*, SUM(hours) AS hours, SUM(dollars) AS dollars')
      ->with('event')
      ->where('user_id', $id)
      ->where('event.date >=', $period['start'])
      ->where('event.date <=', $period['end'])
      ->groupby('event_id')
      ->orderby('event.date', 'DESC')
      ->find_all();
  }

  /**
   * List of chapters with hours and dollars for the given year.
   */
  public function service_by_chapter($chapter_id, $period = NULL) {
    $period = $this->parse_period($period);
    $site_ids = ORM::factory('site')->where('chapter_id', $chapter_id)->find_keys();
    return $this->with('event')->with('site')->select("SUM(hours) as hours, SUM(dollars) as dollars, site.*")
      ->orderby('site.chapter_name', 'ASC')
      ->in('service_hours.site_id', $site_ids)
      ->custom_where("AND event.date >= '%s' AND event.date <= '%s'", array($period['start'], $period['end']))
      ->groupby('service_hours.site_id')
      ->find_all(50);
  }

  /**
   * Find top contributing members by national organization.
   */
  public function top_members($chapter_id, $sort = 'hours', $period = NULL, $limit = 50, $offset = 0) {
    $period = $this->parse_period($period);
    $site_ids = ORM::factory('site')->where('chapter_id', $chapter_id)->find_keys();
    return $this->with('event')->with('site')
    ->select('service_hours.*, SUM(hours) AS hours, SUM(dollars) AS dollars')
    ->in('service_hours.site_id', $site_ids)
    ->where('event.date >=', $period['start'])
    ->where('event.date <=', $period['end'])
    ->groupby('service_hours.user_id')
    ->orderby($sort, 'DESC')
    ->find_all($limit, $offset);
  }
  
  function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('user_id', 'numeric', 'required')
      ->add_rules('event_id', 'numeric', 'required')
      ->add_rules('hours', 'numeric')
      ->add_rules('dollars', 'numeric')
      ->add_rules('notes', 'blob')
      ->add_callbacks('dollars', array($this, 'valid_values'))
      ->add_callbacks('event_id', array($this, 'valid_event'))
      ->add_callbacks('user_id', array($this, 'valid_user'));
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
  }
  
  public function valid_values(Validation $array, $field) {
    if ( ! ($array['hours'] || $array['dollars'])) {
      $array->add_error($field, 'value_required');
    }
  }
  
  // Validate that the user can log hours for the event id specified.
  public function valid_event(Validation $array, $field) {
    $event = ORM::factory('service_event', $array[$field]);
    if ( ! $event->loaded || $event->site_id != kohana::config('chapterboard.site_id')) {
      $array->add_error($field, 'event_id_invalid');
    }
  }

  public function valid_user(Validation $array, $field) {
    $a2 = A2::instance();
    $user = $a2->logged_in();
    
    // Validate that the user can log hours for the user id specified.
    if (isset($array[$field])) {
      $array[$field] = $a2->allowed('service_hour', 'admin') ? $array[$field] : $user->id;
    }
    else {
      $array->add_error($field, 'user_id_invalid');
    }
  }

  public function get_resource_id() {
    return 'service_hour';
  }
}