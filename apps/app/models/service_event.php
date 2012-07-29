<?php defined('SYSPATH') or die('No direct script access.');

class Service_event_Model extends ORM implements ACL_Resource_Interface {
  
  protected $belongs_to = array('site');
  
  protected $has_many = array('service_hours');
  
  protected $foreign_key = array('service_hours' => 'event_id');
  
  public function select_list() {
    $events = $this->select('id, title, date')
      ->where('site_id', kohana::config('chapterboard.site_id'))
      ->orderby('date', 'DESC')
      ->find_all();
    
    foreach ($events as $event) {
      $options[$event->id] = $event->title .' - '. date::display($event->date, 'M d Y');
    }
    return is_array($options) ? $options : array('Please add new event');
  }
  
  public function events($period) {
    $period = $this->parse_period($period);
    return $this->db
      ->select('event.*, SUM(hours.hours) as hours, SUM(hours.dollars) as dollars')
      ->from('service_events AS event')
      ->join('service_hours as hours', 'event.id', 'hours.event_id', 'LEFT')
      ->where('event.site_id', kohana::config('chapterboard.site_id'))
      ->where('event.date >=', $period['start'])
      ->where('event.date <=', $period['end'])
      ->groupby('hours.event_id')
      ->orderby('event.date', 'DESC')
      ->get();
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

  /**
   * Event Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->post_filter(array('date', 'to_db'), 'date')
      ->add_rules('title', 'required')
      ->add_rules('date', 'required', 'date');
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->created = date::to_db();
    $this->updated = date::to_db();
  }
  
  public function before_update() {
    $this->updated = date::to_db();
  }
  
  public function get_resource_id() {
    return 'service_event';
  }
  
}