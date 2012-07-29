<?php defined('SYSPATH') or die('No direct script access.');

class Event_Model extends ORM implements Acl_Resource_Interface {

  protected $belongs_to = array('user', 'calendar');

  protected $has_many = array('signups');

  protected $has_many_polymorphic = array('comments' => 'object');
  
  protected $child_name = 'events';
  
  protected $parent_key = 'parent_id';
  
  /**
   * Start/end time formatting.
   */
  function show_date() {
    if ($this->all_day) {
      if ($this->start != $this->end && $this->has_end()) {
        return date::display($this->start, 'M d, Y') .' to '. date::display($this->end, 'M d, Y') .'- All Day';
      }
      return date::display($this->start, 'M d, Y') .' - All Day';
    }
    else if ($this->end_time && $this->start_day != $this->end_day) {
      return date::display($this->start, 'F jS, Y g:ia') .' to '. date::display($this->end, 'F jS, Y g:ia');
    }
    else if ($this->end_time) {
      return date::display($this->start, 'F jS, Y g:ia') .' to '. date::time($this->end);
    }
    else {
      return date::display($this->start, 'F jS, Y g:ia');
    }
  }
  
  function start() {
    return $this->all_day ? '' : str_replace(':00', '', date::time($this->start, TRUE));
  }
  
  function end() {
    return str_replace(':00', '', date::time($this->end, TRUE));
  }
  
  
  /**
   * Show the date of the event.
   */
  function date() {
    return date::display($this->start, 'Y/m/d');
  }
  
  /**
   * Does this event have an end date/time?
   */
  public function has_end() {
    if ($this->end != '0000-00-00 00:00:00' && $this->end > $this->start) {
      return TRUE;
    }
    return FALSE;
  }
  
  public function is_parent() {
    return (bool) $this->parent_id ? FALSE : TRUE;
  }
    
  /**
   * Show map
   */
  public function map() {
    if ($this->lat && $this->long) {
      return Gmap::static_map(array($this->lat => $this->long), NULL, 14, 'mobile', Kohana::config('gmaps.small.width'), Kohana::config('gmaps.small.height'));
    }
    else if ($this->location) {
      list($this->lat, $this->long) = Gmap::address_to_ll($this->location);
      if ($this->lat && $this->long) {
        $this->save();
        return $this->map();
      }
    }
    return FALSE;
  }
  
  /**
   * Format the location
   *
   * @param boolean    Linkable to google directions
   */
  function location($text = NULL) {
    if (is_null($text)) {
      $text = $this->location;
    }
    if ($this->mappable)
      return html::anchor('http://maps.google.com/maps?saddr='. $this->location, $text, array('target' => '_blank'));
    return $this->location;
  }
  
  /**
   * Signup
   */
  public function user_signed_up($user_id = NULL) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    foreach ($this->signups as $signup) {
      if ($signup->user_id == $user_id)
        return $signup;
    }
    return FALSE;
  }
  
  /**
   * Attendee names
   */
  public function attendees() {
    $signups = array();
    foreach ($this->signups as $signup) {
      if ($signup->rsvp == 1) {
        $signups[$signup->user_id] = $signup->user_id;
      }
    }
    if (count($signups)) {
      $user_id = kohana::config('chapterboard.user_id');
      if (in_array($user_id, $signups)) {
        $args = array('@rsvp' => html::anchor('calendar/event/'. $this->id, 'change RSVP'));
        return format::plural(count($signups) - 1, 'You and @count other member are attending - @rsvp', 'You and @count other members are attending - @rsvp', $args);
      }
      else if ($this->user_signed_up()->rsvp == 2) {
        $args = array('@rsvp' => html::anchor('calendar/event/'. $this->id, 'change RSVP'));
        return format::plural(count($signups), '@count member is attending (you are not attending) - @rsvp', '@count members are attending (you are not attending) - @rsvp', $args);
      }
      else {
        $args = array('@rsvp' => html::anchor('calendar/event/'. $this->id, 'RSVP now'));
        return format::plural(count($signups), '@count member is attending - @rsvp', '@count members are attending - @rsvp', $args);
      }
    }
    else  {
      return 'No members are attending - '. html::anchor('calendar/event/'. $this->id, 'RSVP now');
    }
  }
  
  /**
   * Unpublish.
   */
  public function unpublish() {
    $this->status = 0;
    return $this->save();
  }
  
  /**
   * Show all the events for a given month taking into account ACL.
   *
   * @param int     Two digit month.
   * @param int     Four digit year.
   * @param int     Calendar Id.
   * @param boolean Whether or not to include events 7 days before/after the
   *                start/end of the month to show on a month calendar.
   */
  public function find_by_month($month = NULL, $year = NULL, $calendar_id = NULL, $strict = FALSE) {
    $this->with('calendar')->with('calendar:site');
    
    if (is_numeric($calendar_id)) {
      $this->where('calendar_id', $calendar_id);
    }
    $this->orderby('start', 'ASC');
    $this->where('events.status', 1);

    $timezone = date::timezone();
    $offset = date::offset($timezone);
    if ($strict) {
      $start = strftime('%Y-%m-%d %H:%M:%S', mktime(24, 0, 0, $month, 0, $year) - $offset);
      $end = strftime('%Y-%m-%d %H:%M:%S', mktime(0, 0, 0, $month + 1, 1, $year) - $offset);
      $this->where('events.start >', $start);
      $this->where('events.start <', $end);
    }
    else {
      $start = strftime('%Y-%m-%d %H:%M:%S', mktime(24, 0, 0, $month - 1, 22, $year) - $offset);
      $end = strftime('%Y-%m-%d %H:%M:%S', mktime(0, 0, 0, $month + 1, 8, $year) - $offset);
      $this->where('events.start >', $start);
      $this->where('events.start <', $end);
    }
    // $result = $this->custom_where("calendar.site_id = %d OR (events.shared = 1 AND `calendar:site`.school_id = %d)", array(SITE_ID, SCHOOL_ID))->find_all();
    $this->custom_where(" AND calendar.site_id = %d", array(kohana::config('chapterboard.site_id')));
    $results = $this->find_all();
    if ($results->count())
      $return = FALSE;
      foreach ($results as $result) {
        if (A2::instance()->allowed($result->calendar, 'view')) {
          $return[] = $result;
        }
      }
      return $return;
    return FALSE;
  }  
  
  /**
   * Show all the events for a given month taking into account ÅCL.
   */
  public function find_upcoming_events($until = NULL, $maximum = 0) {
    $until = date::to_db($until);
    $this->with('calendar');
    $this->orderby('start', 'ASC');
    $this->where('events.status', 1);
    $this->where('start >', date::to_db());
    $this->where('start <', $until);
    // $result = $this->custom_where("calendar.site_id = %d OR (events.shared = 1 AND `calendar:site`.school_id = %d)", array(SITE_ID, SCHOOL_ID))->find_all();    
    $results = $this->where('calendar.site_id', kohana::config('chapterboard.site_id'))->find_all();
    if ($results->count())
      $return = FALSE;
      foreach ($results as $result) {
        if (A2::instance()->allowed($result->calendar, 'view')) {
          $return[] = $result;
        }
      }
      if ($maximum) {
        return array_slice($return, 0, $maximum);
      }
      return $return;
    return FALSE;
  }

  # Retrieve items for a user's iCal feed.
  public function find_ical(User_Model $user, Site_Model $site) {
   $start = date::to_db('-2 months');
   $this->with('calendar')->with('calendar:site');
   $this->where('events.start >', $start);
   $this->where('events.status', 1);
   $results = $this->custom_where(" AND (calendar.site_id = %d)", array($site->id))->find_all();
   if ($results->count()) {
     foreach ($results as $result) {
       if (A2::instance()->is_allowed($user, $result->calendar, 'view')) {
         $return[] = $result;
       }
     }
   }
   else {
     $return = FALSE;
   }
   return $return;
  }
   
  /**
   * Find all published comments that belong to a topic.
   */
  public function comments() {
    // custom join to preload whether or not the user liked the comment.
    $join_on = array(
      'comments.id' => 'votes.object_id',
      'votes.object_type' => '"comment"',
      'votes.user_id' => kohana::config('chapterboard.user_id')
    );
    return ORM::factory('comment')->select('value AS liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'event', 'comments.object_id' => $this->id, 'comments.status' => 1))->find_all();
  }
  
  /**
   * Validation and saving user submitted input.
   */
  public function validate(array &$array, $save = FALSE, $is_child = FALSE) {
    $this->_date_translation($array);
    $array = Validation::factory($array)
        ->pre_filter('trim')
        ->add_rules('title', 'required')
        ->add_rules('body', 'blob')
        ->add_rules('start', 'date')
        ->add_rules('end', 'date')
        ->add_rules('start_day', 'date')
        ->add_rules('start_time', 'standard_text')
        ->add_rules('end_time', 'standard_text')
        ->add_rules('end_day', 'date')
        ->add_rules('all_day', 'numeric')
        ->add_rules('location', 'blob')
        ->add_rules('mappable', 'numeric')
        ->add_rules('rsvp', 'numeric')
        ->add_rules('reminder', 'numeric')
        ->add_rules('calendar_id', 'required', array($this, '_calendar_check'))
        ->add_rules('repeats', 'numeric')
        ->add_rules('period', 'standard_text')
        ->add_rules('period_option', 'standard_text')
        ->add_rules('until', 'standard_text')
        ->add_rules('until_occurrences', 'numeric')
        ->add_rules('until_date', 'date')
        ->add_callbacks('start', array($this, '_valid_start'))
        ->add_callbacks('end', array($this, '_valid_end'));
    if ( ! $is_child) {
      $array->add_callbacks('repeats', array($this, '_valid_repeat'));
    }
    return parent::validate($array, $save);
  }
  
  /**
   * Properly parse the incoming date start/end time.
   */
  public function _date_translation(&$array) {
    if ($array['all_day']) {
      $array['start'] = date::to_db($array['start_day']);
      $array['end'] = date::to_db($array['end_day']);
    }
    else {
      $array['start'] = date::to_db($array['start_day'] .' '. $array['start_time']);
      if ($array['end_time']) {
        $array['end'] = date::to_db($array['end_day'] .' '. $array['end_time']);
      }
      else {
        $array['end'] = date::to_db($array['end_day'] .' '. $array['start_time']);
      }
    }
  }
    
  /**
   * Ensure that the user is only adding an event to a calendar which they 
   * have access to.
   */
  public function _calendar_check($id) {
    $calendar = ORM::factory('calendar', $id);
    if (A2::instance()->allowed($calendar, 'view'))
      return TRUE;
    return FALSE;
  }

  public function _valid_start(Validation $array, $field) {
    if ($array[$field] == '0000-00-00 00:00:00' || $array[$field] == '') {
      $array->add_error($field, 'datetime');
    }
  }
  
  /**
   * Make sure that if the user wants an end date, we have a valid one.
   */
  public function _valid_end(Validation $array, $field) {
    if ($array['end_day'] != $array['start_day'] || $array['end_time']) {
      if ($array[$field] == '0000-00-00 00:00:00' || $array[$field] == '') {
        $array->add_error($field, 'datetime');
      }
      
      // Validate end not before start.
      $start = date::display($array['start'], 'U');
      $end = date::display($array[$field], 'U');
      if ($end < $start) { 
        $array->add_error($field, 'end_before_start');
      }
    }
  }
  
  /**
   * Validate event repeat input.
   */
  public function _valid_repeat(Validation $array, $field) {
    if ($array[$field]) {
      if ($array['until'] == 'occurrences') {
        if ($array['until_occurrences'] <= 0 || $array['until_occurrences'] > 100) {
          $array->add_error('until_occurrences', 'invalid');
        }
      }
      else if ($array['until'] == 'date') {
        if (date::day_difference($array['until_date'], $array['start_day']) <= 0) {
          $array->add_error('until_date', 'repeat_end_date');
        }
      }
    }
  }
  
  public function before_insert() {
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->created = date::now();
    $this->updated = date::now();
    $this->status = 1;
    $this->end = $this->end ? $this->end : $this->start;
    if ($this->rsvp) {
      email::notify($this->user->email, 'event_rsvp_notice', $this, $this->title);
    }
  }
  
  public function after_insert() {
    $this->send_notifications();
    if ($this->repeats && $this->is_parent()) {
      $this->create_repeat_events();
    }
  }
  
  public function before_update() {
    $this->updated = date::now();
    if ($this->changed['location']) { // reset the lat long we cached
      $this->lat = 0;
      $this->long = 0;
    }
    //  Send notification if time/day/location has changed.
    $old = ORM::factory('event', $this->id);
    if ($old->start != $this->start || $old->end != $this->end || $old->location != $this->location) {
      $this->send_notifications('change');
    }
  }
  
  /**
   * Handle repeat event creation.
   */
  public function create_repeat_events() {
    if ($this->repeats) {
      $dates = $this->_repeat_dates();
      $parent_id = $this->id;
      foreach ($dates as $order => $date) {
        $child = clone $this; // Clone existing event.
        $child->id = NULL;
        $child->loaded = NULL; // Must set to null so that we save a new record.
        $input = array_merge($child->as_array(), $date);
        if ($child->validate($input, FALSE, TRUE)) {
          $child->parent_id = $parent_id;
          $child->child_n = (int) $order;
          $child->save();          
        }
        else {
          log::system('event error', 'Failed to save repeat event properly.', 'error', array('errors' => $input->errors(), 'values' => $input->as_array()));
        }
      }
    }
  }
  
  public function update_repeat_events() {
    $dates = $this->_repeat_dates();
    $children = $this->children;
    $parent_id = $this->id;
    foreach ($dates as $order => $date) {
      if ($child = $children->current()) {
        $input = array_merge($this->as_array(), $date);
        $child->validate($input, TRUE, TRUE);
      }
      else {
        $child = clone $this;
        $child->id = NULL;
        $child->loaded = NULL;
        $input = array_merge($child->as_array(), $date);
        if ($child->validate($input, FALSE, TRUE)) {
          $child->parent_id = $parent_id;
          $child->child_n = (int) $order;
          $child->save();
        }
      }
      $children->next();
    }
    // Delete any extra events laying around.
    ORM::factory('event')->where('child_n >', (int) $order)->delete_all();
  }
  
  protected function _repeat_dates() {
    $dates = array();
    // Convert the date into a number of occurrences.
    if ($this->until == 'date') {
      $day_count = date::day_difference($this->until_date, $this->start);
      switch($this->period) {
        case 'daily':
          $this->until_occurrences = $day_count + 1;
          break;
        case 'weekly':
          $this->until_occurrences = floor($day_count / 7) + 1;
          break;
        case 'monthly':
          $this->until_occurrences = date::month_difference($this->until_date, $this->start) + 1;
          if ($this->period_option == 'day_of_week') {
            $dow = date::display($this->start, 'l');
            $week = ceil(date::display($this->start, 'd') / 7);
            $end_month = mktime(0, 0, 1, date::display($this->start, 'n') + $this->until_occurrences, 1, date::display($this->start, 'Y'));
            $end_day = date('m/d/Y', strtotime("$week $dow", $end_month));
            if (date::day_difference($this->until_date, $end_day) >= 0) {
              $this->until_occurrences++;
            }
          }
          break;
        case 'yearly':
          $this->until_occurrences = floor($day_count / 365) + 1;
          break;
      }
    }
    switch ($this->period) {
      case 'daily':
        for ($i = 1; $i < $this->until_occurrences; $i++) {
          $dates[] = array(
            'start' => date::modify("+$i days", $this->start),
            'end' => date::modify("$i days", $this->end),
            'start_day' => date::display(date::modify("+$i days", $this->start), 'm/d/Y'),
            'end_day' => date::display(date::modify("+$i days", $this->end), 'm/d/Y'),
          );
        }
        break;
      case 'weekly':
        for ($i = 1; $i < $this->until_occurrences; $i++) { 
          $offset = $i * 7;
          $dates[] = array(
            'start' => date::modify("+$offset days", $this->start),
            'end' => date::modify("+$offset days", $this->end),
            'start_day' => date::display(date::modify("+$offset days", $this->start), 'm/d/Y'),
            'end_day' => date::display(date::modify("+$offset days", $this->end), 'm/d/Y'),
          );
        }
        break;
      case 'monthly':
        if ($this->period_option == 'day_of_month') {
          for ($i = 1; $i < $this->until_occurrences; $i++) { 
            $dates[] = array(
              'start' => date::modify("+$i months", $this->start),
              'end' => date::modify("+$i months", $this->end),
              'start_day' => date::display(date::modify("+$i months", $this->start), 'm/d/Y'),
              'end_day' => date::display(date::modify("+$i months", $this->end), 'm/d/Y'),
            );
          }
        }
        else {
          $start_dow = date::display($this->start, 'l');
          $start_week = ceil(date::display($this->start, 'd') / 7);
          $end_dow = date::display($this->end, 'l');
          $end_week = ceil(date::display($this->start, 'd') / 7);
          for ($i = 1; $i < $this->until_occurrences; $i++) { 
            $start_month = mktime(0, 0, 1, date::display($this->start, 'n') + $i, 1, date::display($this->start, 'Y'));
            $start_day = date('m/d/Y', strtotime("$start_week $start_dow", $start_month));
            $difference = date::day_difference($this->end, $this->start, FALSE);
            $end_day = date('m/d/Y', strtotime(date::modify("+$difference days", $start_day)));
            
            $dates[] = array(
              'start' => date::to_db(sprintf('%s %s', $start_day, substr($this->start, -8))),
              'end' => date::to_db(sprintf('%s %s', $end_day, substr($this->end, -8))),
              'start_day' => $start_day,
              'end_day' => $end_day,
            );
          }
        }
        break;
      case 'yearly':
        for ($i = 1; $i < $this->until_occurrences; $i++) {
          $dates[] = array(
            'start' => date::modify("+$i years", $this->start),
            'end' => date::modify("+$i years", $this->end),
            'start_day' => date::display(date::modify("+$i years", $this->start), 'm/d/Y'),
            'end_day' => date::display(date::modify("+$i years", $this->end), 'm/d/Y'),
          );
        }
        break;
    }
    // print '<pre>'. print_r($dates, TRUE) .'</pre>';
    return $dates;
  }
  
  
  /**
   * Send notifications.
   *
   * $signup->value:  1 - New Events, 2 - New Events and Comments, 0 - No email
   * $this->signup->rsvp:   1 - Going,  2 - Not Going,  0 - Not Responded
   * 
   * @param string the type of notification, whether its for a topic or comment.
   *
   * @param ORM object if the type is 'comment' we pass in the comment object 
   * to use in the notification email.
   */
  public function send_notifications($type = 'event', $comment = NULL) {
    $from = email::notification_address('event', $this->id);
    $notifications = ORM::factory('notification')->with('user')->where('object_type', 'calendar')->where('object_id', $this->calendar_id)->where('value >', 0)->where('user.status', 1)->find_all();
    switch ($type) {
      case 'event':
        foreach ($notificaions as $notify) {
          // If the user has signed up for all notifications and the signup is not the current user, send email.
          if ($notify->value == 1 && $this->user_id != $notify->user_id) {
            if (A2::instance()->is_allowed($notify->user, $this->calendar, 'view')) {
              email::announcement($notify->user->email, $from, 'event_new', $this, $this->title);
            }
          }
        }
      break;
      case 'change': // Send new details to users who are attending.
        $user_id = kohana::config('chapterboard.user_id');
        foreach ($this->signups as $signup) {
          if ($signup->rsvp == 1 && $signup->user->event_notify && $signup->user_id != $user_id) {
            if (A2::instance()->is_allowed($signup->user, $this->calendar, 'view')) {
              email::announcement($signup->user->email, $from, 'event_updated', $this, $this->title); 
            }
          }
        }
      break;
      case 'comment':
        // Gather an array of all the users who are activly involved in the topic.
        $users[] = $this->user_id;
        foreach ($this->db->query("SELECT user_id FROM comments WHERE object_type = 'event' AND object_id = ?", array($this->id)) as $row) {
          $users[] = $row->user_id;
        }
        // If signup user_id is not the current user and the user has signed up for all notifications, or the user is involved in the thread, send email.
        foreach ($notifications as $notify) {
          if ($comment->user_id != $notify->user_id AND ($notify->value == 1 OR ($notify->value == 2 && in_array($notify->user_id, $users)))) {
            // Make sure the user has access to view the event.
            if (A2::instance()->is_allowed($notify->user, $this->calendar, 'view')) {
              email::announcement($notify->user->email, $from, 'event_new_comment', $comment, $this->title);
            }
          }
        }
      break;
    }
  }

	/**
	 * Overload ORM::__get to support "parent" and "children" properties.
	 *
	 * @param   string  column name
	 * @return  mixed
	 */
	public function __get($column) {
		if ($column === 'parent')
		{
			if (empty($this->related[$column]))
			{
				// Load child model
				$model = ORM::factory(inflector::singular($this->child_name));

				if (array_key_exists($this->parent_key, $this->object))
				{
					// Find children of this parent
					$model->where($model->primary_key, $this->object[$this->parent_key])->find();
				}

				$this->related[$column] = $model;
			}

			return $this->related[$column];
		}
		elseif ($column === 'children')
		{
			if (empty($this->related[$column]))
			{
				$model = ORM::factory(inflector::singular($this->child_name));

				if ($this->child_name === $this->table_name)
				{
					// Load children within this table
					$this->related[$column] = $model
						->where($this->parent_key, $this->object[$this->primary_key])
            // ->where('status', TRUE) // Show only active 
						->find_all();
				}
				else
				{
					// Find first selection of children
					$this->related[$column] = $model
						->where($this->foreign_key(), $this->object[$this->primary_key])
						->where($this->parent_key, NULL)
						->find_all();
				}
			}

			return $this->related[$column];
		}

		return parent::__get($column);
	}

  /**
   * Implementation of ACL resource id.
   */
  public function get_resource_id() {
    return 'event';
  }
  
}
