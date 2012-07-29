<?php defined('SYSPATH') or die('No direct script access.');

class Signup_Model extends ORM {
  
  protected $belongs_to = array('event', 'user');
  
  // We cannot do this here since it breaks when users signup for an event.
  // Instead use this when querying as it will reduce the number of queries.
  // protected $load_with = array('user', 'user:profile');
  
  public function toggle($event_id, $user_id, $rsvp) {
    $signup = $this->where('event_id', $event_id)->where('user_id', $user_id)->find();
    // Load new object values if no object exists.
    if ( ! $signup->loaded) {
      $signup->user_id = $user_id;
      $signup->event_id = $event_id;
    }
    $signup->rsvp = $rsvp;
    return $signup->save();
  }
  
  public function before_insert() {
    $this->created = date::to_db();
  }
  
  public function before_update() {
    $this->created = date::to_db();
  }
  
  public function attendance($event_id, $rsvp = NULL, $sorting = 'name') {
    $this->with('user')->where('event_id', $event_id)->where('user.site_id', kohana::config('chapterboard.site_id'));
    if ($rsvp) {
      $this->where('rsvp', $rsvp);
    }
    if ($sorting == 'name') {
      $this->orderby(array('user:first_name' => 'ASC', 'user:last_name' => 'ASC'));
    }
    else {
      $this->custom_select("signups.*, IF(signups.created IS NULL or signups.created='', 1, 0) AS isnull");
      $this->orderby(array('isnull' => 'DESC', 'signups.created' => 'ASC', 'signups.id' => 'ASC'));
    }
    return $this->find_all();
  }
}