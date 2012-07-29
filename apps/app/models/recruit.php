<?php defined('SYSPATH') or die('No direct script access.');

class Recruit_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('user', 'category', 'site', 'file');
  
  protected $sorting = array('updated' => 'DESC', 'name' => 'ASC');
  
  protected $has_many_polymorphic = array('comments' => 'object', 'votes' => 'object');
  
  
  /** Instance Methods **/
  
  public function name($bid_status = FALSE) {
    return ucwords($this->name);
  }
  
  public function list_name($url = FALSE) {
    switch ($this->list) {
      case 0:
        return $url ? '' : 'Actively Recruiting';
      case 1:
        return $url ? 'bidded' : 'Bidded Members';
      case 2:
        return $url ? 'not-recruiting' : 'No Longer Recruiting';
    }
  }
  
  public function bid_status() {
    if ($this->list == 1) {
      switch ($this->bid_status) {
        case 0: return 'Pending';
        case 1: return 'Accepted';
        case 2: return 'Declined';
      }
    }
    return 'Not Bidded';
  }
  
  public function phone($call_link = FALSE) {
    if ($call_link) {
      return '<a href="tel:'.$this->phone.'">'. format::phone($this->phone) .'</a>';
    }
    return format::phone($this->phone);
  }
  
  public function picture() {
    return $this->file->filename ? $this->file->filename : '_recruit.gif';
  }
  
  # Find all published comments that belong to a recruit.
  public function comments() {
    // custom join to preload whether or not the user liked the comment.
    $join_on = array(
      'comments.id' => 'votes.object_id',
      'votes.object_type' => '"comment"',
      'votes.user_id' => kohana::config('chapterboard.user_id')
    );
    return ORM::factory('comment')->select('value as liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'recruit', 'comments.object_id' => $this->id, 'comments.status' => 1))->find_all();
  }
  
  # Whether or not a user has voted.
  public function is_liked($user_id = null) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    return (bool) $this->db->query("SELECT id FROM votes WHERE object_type = 'recruit' AND object_id = ? AND user_id = ?", array($this->id, $user_id))->current()->id;
  }
  
  /** Instance Updaters **/

  public function unpublish() {
    $this->status = 0;
    return $this->save();
  }
  
  public function update_list($list_id) {
    $this->list = $list_id;
    return $this->save();
  }
  
  # Update bid status.
  public function update_bid_status($value) {
    $this->bid_status = $value;
    return $this->save();
  }
  

  # Mark a recruit as having been updated.
  public function updated() {
    $this->updated = date::to_db();
    return $this->save();
  }
  
  /**
   * Update comment count.
   */
  public function update_comment_count() {
    $this->comment_count = $this->db->query("SELECT COUNT(*) comments FROM comments WHERE object_type = 'recruit' AND object_id = ? AND status = 1", $this->id)->current()->comments;
    $this->save();
  }
  
  
  /** Query Methods **/

  /**
   * Get all active recruits.
   */
  public function find_by_list($list_id = NULL, $year = NULL, $hometown = NULL, $high_school = NULL) {
    $this->with('file');
    if (is_numeric($list_id)) {
      $this->where('list', $list_id);
    }
    if ( ! is_null($year)) {
      $this->where('year', $year);
    }
    if ( ! is_null($hometown)) {
      $this->where('hometown_searchable', text::searchable($hometown));
    }
    if ( ! is_null($high_school)) {
      $this->where('high_school_searchable', text::searchable($high_school));
    }
    return $this->where('recruits.site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find_all();
  }
  
  # Show a list of the most recent X recruits
  public function find_recent($count = 5, $timespan = '-5 days') {
    $this->where('site_id', kohana::config('chapterboard.site_id'));
    $this->where('status', 1);
    $this->orderby('updated', 'DESC');
    $this->where('updated >', date::to_db($timespan));
    return $this->find_all($count);
  }
    
  public function list_counts() {
    $results = array(0, 0, 0);
    $query = $this->db->query("SELECT list, COUNT(list) AS count FROM recruits WHERE status = 1 AND site_id = ? GROUP BY list", array(kohana::config('chapterboard.site_id')));
    foreach ($query as $result) {
      $results[$result->list] = $result->count;
    }
    return $results;
  }
  
  public function bid_counts() {
    $results = array(0, 0, 0);
    $query = $this->db->query("SELECT bid_status, COUNT(bid_status) as count FROM recruits WHERE status = 1 AND list = 1 AND site_id = ? GROUP BY bid_status", array(kohana::config('chapterboard.site_id')));
    foreach ($query as $result) {
      $results[$result->bid_status] = $result->count;
    }
    return $results;
  }

  /**
   * Group unpublish based on bid status.
   */
  public function archive_recruits($list_id) {
    $this->db->where('site_id', kohana::config('chapterboard.site_id'))->update('recruits', array('status' => 0), array('list' => $list_id));
  }
  
  
  /**
   * Send announcement.
   *
   * Perform validation on the announcement fields and send off
   * notices to recruits with email addresses.
   *
   *
   * @return int/boolean Count of number sent on success, False on errors.
   */
  public function send_announcement(array &$post) {
    $post = Validation::factory($post)
      ->pre_filter('trim')
      ->add_rules('subject', 'required', 'standard_text')
      ->add_rules('lists', 'required', 'is_array')
      ->add_rules('message', 'required');
    
    if ($post->validate()) {
      $user = A1::instance()->get_user();
      $values = $post->as_array();
      $recruits = ORM::factory('recruit')->where('status', 1)->where('site_id', kohana::config('chapterboard.site_id'))->in('list', $values['lists'])->find_all();
      foreach ($recruits as $recruit) {
        if (valid::email($recruit->email)) {
          $count++;
          email::announcement($recruit->email, array($user->email, $user->name()), 'recruit_announcement', $values, $values['subject']);
        }
      }
      return $count ? $count : TRUE;
    }
    return FALSE;
  }
  
  /**
   * Validation
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->pre_filter(array($this, '_clean_digits'), 'phone')
      ->add_rules('list', 'digit')
      ->add_rules('name', 'required', 'standard_text')
      ->add_rules('phone', 'standard_text', 'phone[10]')
      ->add_rules('email', 'email')
      ->add_rules('facebook', 'url')
      ->add_rules('referral', 'standard_text')
      ->add_rules('housing', 'standard_text')
      ->add_rules('year', 'standard_text')
      ->add_rules('major', 'blob')
      ->add_rules('hometown', 'blob')
      ->add_rules('high_school', 'blob')
      ->add_rules('about', 'blob');
    return parent::validate($array, $save);
  }
  
  public function _clean_digits($value) {
    return preg_replace('/[^0-9]/','', $value);
  }
  
  public function before_insert() {
    $this->site_id = kohana::config('chapterboard.site_id');
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->status = 1;
    $this->bid_status = 0;
    $this->created = date::to_db();
    $this->updated = date::to_db();
    $this->hometown_searchable = text::searchable($this->hometown);
    $this->high_school_searchable = text::searchable($this->high_school);
  }
  
  public function before_update() {
    $this->updated = date::to_db();
    $this->hometown_searchable = text::searchable($this->hometown);
    $this->high_school_searchable = text::searchable($this->high_school);
  }
  
  /**
   * ACL
   */
  public function get_resource_id() {
    return 'recruit';
  }
}