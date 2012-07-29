<?php defined('SYSPATH') or die('No direct script access.');

class Sms_Model extends ORM {
  
  // protected $table_name = 'sms';
  protected $object_plural = 'sms';
  
  protected $belongs_to = array('site', 'user', 'sms_billing');
  
  protected $has_many = array('sms_log');
  
  protected $sorting = array('created' => 'DESC');
  
  /**
   * Override find_all() to limit by site.
   */
  public function find_all($limit = NULL, $offset = NULL) {
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    return parent::find_all($limit, $offset);
  }
  
  /**
   * Find a matching sms message so that we can append the body.
   */
  public function append_to_sequence($sender_id, $message) {
    $parent_sms = $this->where('user_id', $sender_id)->where('created >', date::to_db('-30 seconds'))->find();
    if ($parent_sms->loaded) {
      $parent_sms->message .= $message;
      $parent_sms->save();
      return TRUE;
    }
    else {
      return FALSE;
    }
  }
  
  /**
   * Get an unprocessed message from the queue.
   *
   * Because InnoDB doesn't have atomic operations we must run
   * an update query to specify that we are grabbing a record that
   * has yet to be processed or claimed and set a timeout of 10 minutes.
   * If the item is still incomplete after 10 minutes, another worker
   * may pick it up and begin processing.
   */
  public function get_from_queue() {
    $worker_id = text::random('alnum', 16);
    $query = $this->db->query("UPDATE sms SET worker_id = ?, worker_timeout = ? WHERE status = 0 AND created < ? AND (worker_id IS NULL OR worker_timeout < ?) ORDER BY created ASC LIMIT 1", 
      array($worker_id, date::to_db('+10 minutes'), date::to_db('-1 minute'), date::to_db()));
    if ($query->count()) {
      return $this->where('status', 0)->where('worker_id', $worker_id)->find();
    }
    return FALSE;
  }
  
  /**
   * Parse the group names in the message to determine the audience.
   */
  public function users() {
    if ( ! $this->groups) {
      $this->parse_groups();
    }
    $group_ids = array_keys($this->groups);
    if ( ! empty($group_ids)) {
      return ORM::factory('user')->with('profile')->select("DISTINCT users.id, users.*")
        ->join('groups_users', 'groups_users.user_id', 'users.id')
        ->in('groups_users.group_id', $group_ids)
        ->where('profile.phone_carrier !=', '')
        ->where('site_id', $this->site_id)
        ->where('status', 1)
        ->find_all();
    }
    return array();
  }

  /**
   * Parse the groups from the message body.
   */
  public function parse_groups() {
    $groups = array();
    $regex = '/^@([a-zA-Z0-9]{3,32})/i';

    $words = split(' ', $this->message);
    while (preg_match($regex, $words[0], $match)) {
      $sms_key = $match[1];
      $group = ORM::factory('group')->where('site_id', $this->site_id)->where('sms_key', $sms_key)->find();
      if ($group->loaded) {
        $groups[$group->id] = $group->name;
      }
      array_shift($words);
    }

    // No matches, we send to active members group.
    if (empty($groups)) {
      $group = ORM::factory('group')->where('site_id', $this->site_id)->where('static_key', 'active')->find();
      $groups = array($group->id => $group->name);
    }
    
    $this->groups = serialize($groups);
    $this->message = join(' ', $words);
    $this->save();      
  }
  
  /**
   * Mark the message as being delivered.
   */
  public function delivered() {
    $this->status = 1;
    $this->save();
  }
  
  /**
   * Validate an incoming SMS.
   */
  public function validate(array &$array, $save = FALSE) {
    // translate any incoming twilio fields to our local db fields.
    $array['ref_id']    = $array['SmsSid'] ? $array['SmsSid'] : $array['ref_id'];
    $array['sent_from'] = $array['From'] ? $array['From'] : $array['sent_from'];
    $array['message']   = $array['Body'] ? $array['Body'] : $array['message'];
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('ref_id', 'standard_text')
      ->add_rules('sent_from', 'required')
      ->add_rules('message', 'required');
    return parent::validate($array, $save);
  }
  
  /**
   * Before insert add additional data.
   */
  public function before_insert() {
    $user = ORM::factory('user')->find_by_phone($this->sent_from);
    $this->user_id = $user->id;
    $this->site_id = $user->site_id;
    $this->created = date::to_db();
    $this->status = 0;
    $this->send_count = 0;
  }
  
  /**
   * __get()
   */
  public function __get($column) {
    if ($column == 'groups') {
      return unserialize(parent::__get('groups'));
    }
    return parent::__get($column);
  }
}