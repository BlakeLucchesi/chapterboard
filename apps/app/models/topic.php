<?php defined('SYSPATH') or die('No direct script access.');

class Topic_Model extends ORM implements Acl_Resource_Interface {
  
  protected $has_many_polymorphic = array('comments' => 'object', 'files' => 'object', 'votes' => 'object');

  protected $belongs_to = array('forum', 'user');

  protected $has_one = array('poll');

  protected $load_with = array('user');
  
  /**
   * __get()
   */
  public function __get($column) {
    if ($column == 'last_comment') {
      $last_comment = ORM::factory('comment')->with('user')
        ->where('object_type', 'topic')
        ->where('object_id', $this->id)
        ->orderby('created', 'DESC')
        ->find();
      if ($last_comment->loaded) {
        return $last_comment;        
      }
      else {
        return $this;
      }
    }
    return parent::__get($column);
  }
  /**
   * Returns a count of all published topics.
   *
   * @param int The specific forum we are counting for. Otherwise we count all
   * topics in forums the user has access.
   *
   * @return int count of total forum topics.
   */
  public function topics_count($forum_id = NULL) {
    if ($forum_id) {
      $result = $this->db->query("SELECT COUNT(*) as count FROM topics WHERE forum_id = ? AND status = 1", $forum_id);      
    }
    else {
      $forums = ORM::factory('forum')->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
      foreach ($forums as $forum) {
        if (A2::instance()->allowed($forum, 'view')) {
          $ids[] = $forum->id;
        }
      }
      if (count($ids)) {
        $result = $this->db->select("COUNT(*) as count")->from('topics')->where('status', '1')->in('forum_id', $ids)->get();        
      }
      else {
        return $this->find_all(0);
      }
    }
    return $result->current()->count;
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
    return ORM::factory('comment')->select('value AS liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'topic', 'comments.object_id' => $this->id, 'comments.status' => 1))->find_all();
  }
  
  /**
   * Return a list of topics filtering out for ACL.
   */
  public function topics($start = 0, $count = 20, $forum_id = NULL) {
    if (is_numeric($forum_id)) {
      $this->where('forum_id', $forum_id);
    }

    return $this->where('topics.status', 1)
      ->orderby(array('sticky' => 'DESC', 'updated' => 'DESC'))
      ->find_all($count, $start);
  }
  
  /**
   * Show a list of recent topics adhering to access control.
   */
  public function recent_topics($limit = 30, $offset = 0) {
    $results = array();
    $auth = A2::instance();

    $forums = ORM::factory('forum')->where('status', 1)->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
    foreach ($forums as $forum) {
      if ($auth->allowed($forum, 'view')) {
        $allowed[] = $forum->id;
      }
    }

    if (count($allowed)) {
      return $this->with('forum')
        ->in('forum_id', $allowed)
        ->where('topics.status', 1)
        ->orderby(array('updated' => 'DESC'))
        ->find_all($limit, $offset);
    }
    else {
      return $this->find_all(0);
    }
  }
  
  /**
   * Show a list of recent topics adhering to access control.
   */
  public function unread($limit = 30, $offset = 0) {
    $results = array();
    $auth = A2::instance();
    $user = $auth->get_user();
    
    $forums = ORM::factory('forum')->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
    foreach ($forums as $forum) {
      if ($auth->allowed($forum, 'view')) {
        $allowed[] = $forum->id;
      }
    }

    if (count($allowed)) {
      return $this->with('user')
        ->custom_join('topic_history', array('topic_history.topic_id' => 'topics.id', 'topic_history.user_id' => $user->id), NULL, 'LEFT')
        ->in('topics.forum_id', $allowed)
        ->where('topics.status', 1)
        ->custom_where(" AND topics.updated > GREATEST(COALESCE(topic_history.last_viewed, '%s'), '%s')", array($user->topic_history, $user->topic_history))
        ->orderby('topics.updated', 'DESC')
        ->find_all($limit, $offset);
    }
    else {
      return $this->find_all(0);
    }
  }
  

  # Test to see if the topic has been marked read.
  public function is_new() {
    static $history;
    static $history_limit;
    if ( ! $history) {
      $history = array();
      $user = A1::instance()->logged_in();
      $history_limit = $user->topic_history;
      
      $query = $this->db->query("SELECT topic_id, last_viewed FROM topic_history WHERE user_id = ?", array($user->id));
      foreach ($query as $row) {
        $history[$row->topic_id] = $row->last_viewed;
      }
    }
    if ($this->updated > $history_limit) {
      if ($history[$this->id]) {
        return $this->updated > $history[$this->id] ? TRUE : FALSE;
      }
      return TRUE;
    }
    return FALSE;
  }
  
  # Whether or not the user likes this topic.
  public function is_liked($user_id = null) {
    $user_id = is_null($user_id) ? kohana::config('chapterboard.user_id') : $user_id;
    return $this->db->query("SELECT id FROM votes WHERE object_type = 'topic' AND object_id = ? AND user_id = ?", array($this->id, $user_id))->current()->id;
  }
  
  /**
   * Mark an individual topic as read.
   */
  public function markread() {
    $user_id = kohana::config('chapterboard.user_id');
    $this->db->query("INSERT INTO topic_history (user_id, topic_id, last_viewed) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE last_viewed = ?", array($user_id, $this->id, date::to_db(), date::to_db()));
  }

  /**
   * Mark all topics as read.
   *
   * To mark topics as read we set the current datetime in the user table and delete any rows 
   * in the topic_history table for this user, since we now have a more recent timestamp in the
   * user table.
   * 
   */  
  public function markallread() {
    $user = ORM::factory('user', kohana::config('chapterboard.user_id'));
    $user->topic_history = date::to_db();
    $user->save();
    $this->db->query("DELETE FROM topic_history WHERE user_id = ?", array($user->id));
  }
  
  # Update the sticky property.
  public function sticky($sticky = TRUE) {
    $this->sticky = (bool) $sticky ? 1 : 0;
    return $this->save();
  }
  
  # Update the status of a topic.
  public function status($status = TRUE) {
    $this->status = (bool) $status;
    if ($this->status) {
      $this->index();
    }
    else {
      $this->index_remove();
    }
    return $this->save();
  }
  
  /**
   * Lock and unlock a topic.
   */
  public function lock() {
    $this->locked = TRUE;
    return $this->save();
  }
  
  public function unlock() {
    $this->locked = FALSE;
    return $this->save();
  }
    
  /**
   * Validations and before/after hooks.
   */
  # Validate
  public function validate(array &$array, $save = FALSE) {
   $array = Validation::factory($array)
       ->pre_filter('trim')
       ->add_rules('title', 'required')
       ->add_rules('body', 'required')
       ->add_rules('forum_id', 'required')
       ->add_callbacks('forum_id', array($this, '_forum_check'));
    return parent::validate($array, $save);
  }
  
  /**
   * Make sure this user is adding a topic to a forum that exists in their site.
   */
  public function _forum_check(Validation $array, $field) {
    $forum = ORM::factory('forum', $array->forum_id);
    if ( ! $forum->loaded || ! A2::instance()->allowed($forum, 'view'))
      $array->add_error($field, 'permission');
  }

  # Update the update timestamp.
  public function updated() {
    $this->updated = date::to_db();
    return $this->save();
  }
  
  # Update comment count.
  public function update_comment_count() {
    $this->comment_count = $this->db->query("SELECT COUNT(*) comments FROM comments WHERE object_type = 'topic' AND object_id = ? AND status = 1", $this->id)->current()->comments;
    $this->save();
  }
  
  # Before Insert
  public function before_insert() {
    $this->user_id = kohana::config('chapterboard.user_id');
    $this->status = 1;
    $this->created = date::to_db();
    $this->updated = date::to_db();
    $this->sticky = 0;
  }
  
  /**
   * If updating anything besides sticky or like count we 
   * update the updated field.
   */
  public function before_update() {
    if ( ! ($this->changed['sticky'] || $this->changed['like_count'] || $this->changed['locked'])) {
      $this->updated = date::to_db();
    }
  }
  
  # After Insert
  public function after_insert() {
    $this->send_notifications('topic');
    $this->index();
  }
  
  # After Update
  public function after_update() {
    $this->index();
  }
  
  # Index the topic in search results.
  public function index($commit = TRUE) {
    if ($this->status) {
      try {
        $solr = solr::service();
        $doc  = solr::document();

        $doc->id        = 'topic:'. $this->id;
        $doc->object_id = $this->id;
        $doc->type      = 'topic';
        $doc->title     = $this->title;
        $doc->created   = date::to_solr($this->created);
        $doc->updated   = date::to_solr($this->updated);
        $doc->body      = $this->body;
        $doc->forum_id  = $this->forum_id;

        foreach ($this->comments() as $comment) {
          $comment_text .= $comment->body .' ';
        }
        $doc->comments = $comment_text;

        $doc->author = $this->user->name();
        $doc->site_id = $this->user->site_id;

        $solr->addDocument($doc);
        if ($commit) {
          $solr->commit(FALSE, FALSE);
        }
      }
      catch (Exception $e) {
        log::system('solr', sprintf('Solr indexing failed for forum topic: (%d) %s', $this->id, $this->title), 'error', array($this->as_array()));
      }
    }
  }
  
  # Delete an item from the index
  public function index_remove($commit = TRUE) {
    $solr = solr::service();
    $solr->deleteById('topic:'. $this->id);
    if ($commit) {
      $solr->commit();
    }
  }
  
  /**
   * Send notifications.
   *
   * @param string the type of notification, whether its for a topic or comment.
   *
   * @param ORM object if the type is 'comment' we pass in the comment object 
   * to use in the notification email.
   */
  public function send_notifications($type = 'topic', $comment = NULL) {
    $from = email::notification_address('topic', $this->id);
    $signups = ORM::factory('notification')->with('user')->where('object_type', 'forum')->where('object_id', $this->forum_id)->where('value >', 0)->where('user.status', 1)->find_all();
    switch ($type) {
      case 'topic':
        foreach ($signups as $signup) {
          // If the user has signed up for all notifications and the signup is not the current user, send email.
          if ($signup->value == 1 && $this->user_id != $signup->user_id) {
            email::announcement($signup->user->email, $from, 'forum_new_topic', $this, $this->title);
          }
        }
        break;
      case 'comment':
        // Gather an array of all the users who are activly involved in the topic.
        $users[] = $this->user_id;
        foreach ($this->db->query("SELECT user_id FROM comments WHERE object_type = 'topic' AND object_id = ?", array($this->id)) as $row) {
          $users[] = $row->user_id;
        }
        
        // If signup user_id is not the current user and the user has signed up for all notifications, or the user is involved in the thread, send email.
        foreach ($signups as $signup) {
          if ($comment->user_id != $signup->user_id AND ($signup->value == 1 OR ($signup->value == 2 && in_array($signup->user_id, $users)))) {
            email::announcement($signup->user->email, $from, 'forum_new_comment', $comment, $this->title);
          }
        }
        break;
    }
  }
  
  /**
   * ACL Resource Interface
   */
  public function get_resource_id() {
    return 'topic';
  }

}