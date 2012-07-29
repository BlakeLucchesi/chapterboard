<?php defined('SYSPATH') or die('No direct script access.');

class Forum_Model extends ORM implements Acl_Resource_Interface {
  
  protected $primary_val = 'title';

  protected $belongs_to = array('site');
  
  protected $has_many = array('topics');
  
  protected $has_many_polymorphic = array('notifications' => 'object');
  
  protected $sorting = array('weight' => 'ASC', 'title' => 'ASC');
  
  /**
   * Grab a list of forums for a site and return the ones the user
   * has access to.
   */
  public function forums() {
    $results = array();
    $forums = $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find_all();
    foreach ($forums as $forum) {
      if (A2::instance()->allowed($forum, 'view'))
        $results[] = $forum;
    }
    return $results;
  }
  
  /**
   * Check to see if there are any unread topics in the forum.
   */
  public function has_unread_topics($user_id = NULL) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    // Get the date that the user last clicked on 'mark all read'
    // to ignore any topics updated before this date.
    $user_topic_history = $user ? $user->topic_history : $this->db->query("SELECT topic_history FROM users WHERE id = ?", $user_id)->current()->topic_history;

    // If there are any results from this query, then we have new topics in the forum.
    $new_topics = $this->db->query("SELECT t.id, t.title, th.last_viewed FROM topics t LEFT JOIN topic_history th ON (t.id = th.topic_id AND th.user_id = ?)
                                        WHERE t.status = 1 AND t.forum_id = ? AND  t.updated > ? AND (t.updated > th.last_viewed OR th.last_viewed IS NULL)
                                        ORDER BY t.updated DESC LIMIT 0, 1", $user_id, $this->id, $user_topic_history)->current()->id;
    if ($new_topics)
      return TRUE;
    return FALSE;
  }
  
  /**
   * Grab a list of forums for a site and return the ones the user
   * has access to.
   */
  public function forum_options() {
    $results = array();
    $forums = $this->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find_all();
    foreach ($forums as $forum) {
      if (A2::instance()->allowed($forum, 'view'))
        $results[$forum->id] = $forum->title;
    }
    return $results;
  }
  
  /**
   * Add new forum.
   */
  public function insert($values) {
    if (valid::standard_text($values['title'], $values['description'])) {
      $this->site_id = kohana::config('chapterboard.site_id');
      $this->title = $values['title'];
      $this->description = $values['description'];
      $this->status = 1;
      $this->save();
      return TRUE;
    }
    return FALSE;
  }
  
  /**
   * Unpublish a forum.
   */
  public function unpublish() {
    $this->status = 0;
    $this->db->query("DELETE FROM notifications WHERE object_type = ? AND object_id = ?", array('forum', $this->id));
    return $this->save();
  }
  
	/**
	 * Creates a key/value array from all of the objects available. Uses find_all
	 * to find the objects.
	 *
	 * @param   string  key column
	 * @param   string  value column
	 * @return  array
	 */
	public function select_list($key = NULL, $val = NULL)
	{
		if ($key === NULL)
		{
			$key = $this->primary_key;
		}

		if ($val === NULL)
		{
			$val = $this->primary_val;
		}

		// Return a select list from the results
		return array('all' => '-- All Categories --') + $this->select($key, $val)->where('site_id', kohana::config('chapterboard.site_id'))->find_all()->select_list($key, $val);
	}
  
  public function __get($column) {
    if ($column == 'last_updated') {
      $topic = ORM::factory('topic')->where('forum_id', $this->id)->orderby('updated', 'DESC')->find();
      if ( ! $topic->comment_count) {
        return $topic;
      }
      else {
        $comment = ORM::factory('comment')->where('object_id', $topic->id)->where('object_type', 'topic')->orderby('created', 'DESC')->find();
        return $comment;
      }
    }
    return parent::__get($column);
  }
  
  public function get_resource_id() {
    return 'forum';
  }
  
}