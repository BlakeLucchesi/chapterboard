<?php defined('SYSPATH') or die('No direct script access.');

class Calendar_Model extends ORM implements Acl_Resource_Interface {
  
  protected $primary_val = 'title';
  
  protected $has_many = array('events');
  
  protected $belongs_to = array('site');
  
  protected $has_and_belongs_to_many = array('groups');
  
  protected $sorting = array('title' => 'ASC');

  protected $has_many_polymorphic = array('notifications' => 'object');
  
  public function get_list() {
    $auth = A2::instance();
    $list = $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find_all();
    $items = array();
    foreach ($list as $item) {
      if ($auth->allowed($item, 'view')) {
        $items[] = $item;
      }
    }
    return $items;
  }
  
  public function get_resource_id() {
    return 'calendar';
  }
  
  public function list_calendars() {
    $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', TRUE);
    return $this->find_all();
  }
  
  public function result_array() {
    foreach ($this->as_array() as $result) {
      $results[$result->id] = $result->title;
    }
    return $results;
  }
  
  public function groups() {
    foreach ($this->groups as $group) {
      $results[$group->id] = $group->name;
    }
    return $results;
  }
  
  /**
   * Add new calendar.
   */
  public function insert($values) {
    if (valid::standard_text($values['title'])) {
      $this->site_id = kohana::config('chapterboard.site_id');
      $this->title = $values['title'];
      $this->status = TRUE;
      $this->save();
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Unpublish a calendar.
   */
  public function unpublish() {
    $this->status = 0;
    $this->db->query("DELETE FROM notifications WHERE object_type = ? AND object_id = ?", array('calendar', $this->id));
    return $this->save();
  }
  
  /**
   * Provide a submit handler for the manage calendars permissions form.
   */
  public function update_perms($form_values) {
    // Gather our sets of allowed values.
    $orm_calendars = ORM::factory('calendar')->list_calendars();
    foreach ($orm_calendars as $calendar) {
      $valid_calendars[$calendar->id] = $calendar->id;
    }
    unset($calendar);
    $orm_groups = ORM::factory('group')->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
    foreach ($orm_groups as $group) {
      $valid_groups[$group->id] = $group->id;
    }
    unset($group);
    
    // Go through each of the post values to make sure they are in the allowed
    // set of calendar and group ids.  Return false if data mismatch.
    foreach ($form_values as $calendar_id => $groups) {
      if (isset($valid_calendars[$calendar_id])) {
        foreach ($groups as $group_id) {
          if ( ! isset($valid_groups[$group_id]))
            return FALSE;
        }
      }
      else {
        return FALSE;
      }
    }
    
    foreach ($orm_calendars as $calendar) {
      $calendar->groups = is_array($form_values[$calendar->id]) ? $form_values[$calendar->id] : array();
      $calendar->save();
    }
    return TRUE;
  }
  
  
  /**
   * Override the default select_list ORM method.  We perform access control checking to make
   * sure that only calendars that users can see are shown.
   */
  public function select_list($all = FALSE, $key = NULL, $val = NULL) {
    if ($key === NULL)
		{
			$key = $this->primary_key;
		}

		if ($val === NULL)
		{
			$val = $this->primary_val;
		}
    $results = $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', TRUE)->orderby($val, 'ASC')->find_keyed_object();
    
    if ($all) {
      $array = array('all' => '-- All --');
    }
    else {
      $array = array();
    }
		foreach ($results as $key => $row)
		{
      if (A2::instance()->allowed($row, 'view')) {
			  $array[$key] = $row->$val;
      }
		}
		return $array;
  }
}