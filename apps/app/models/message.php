<?php defined('SYSPATH') or die('No direct script access.');

class Message_Model extends ORM {
  
  protected $has_and_belongs_to_many = array('users');

  protected $belongs_to = array('author' => 'user');

  protected $has_many_polymorphic = array('comments' => 'object', 'files' => 'object', 'comments' => 'object');
  
  protected $recipients;
  
  public function find_by_user($user_id, $limit = NULL, $offset = NULL) {
    return $this->with('user')
      ->join('messages_users', 'messages_users.message_id', 'messages.id', 'LEFT')
      ->where('messages_users.user_id', $user_id)
      ->where('messages_users.status', TRUE)
      ->orderby('updated', 'DESC')
      ->find_all($limit, $offset);
  }
  
  public function count_by_user($user_id) {
    return $this->db->query('SELECT COUNT(*) AS count FROM messages_users WHERE user_id = ? AND status = 1', array($user_id))->current()->count;
  }
  
  public function count_unread($user_id) {
    return $this->select('COUNT(*) AS count')
      ->join('messages_users', 'messages_users.message_id', 'messages.id', 'LEFT')
      ->where('messages_users.user_id', $user_id)
      ->where('messages_users.status', 1)
      ->where('messages_users.unread', 1)
      ->find()->count;
  }
  
  public function is_unread() {
    return (bool) $this->select('unread')
      ->from('messages_users')
      ->where('messages_users.message_id', $this->id)
      ->where('messages_users.user_id', kohana::config('chapterboard.user_id'))
      ->find()->unread;
  }
  
  public function is_allowed($user_id) {
    foreach ($this->users as $user) {
      if ($user->id == $user_id)
        return TRUE;
    }
    return FALSE;
  }
  
  public function read() {
    $this->db->query("UPDATE messages_users SET unread = 0 WHERE message_id = ? AND user_id = ?", array($this->id, kohana::config('chapterboard.user_id')));
  }
  
  public function keep_unread($user_id) {
    $this->db->query("UPDATE messages_users SET unread = 1 WHERE message_id = ? AND user_id = ?", array($this->id, $user_id));
  }
  
  public function delete($user_id) {
    $this->db->query("UPDATE messages_users SET status = 0 WHERE message_id = ? AND user_id = ?", array($this->id, $user_id));
  }
  
  public function link() {
    return sprintf('%smessages/%s', url::base(FALSE), $this->id);
  }
  
  public function updated($comment) {
    // Send email notification.
    $vars = array(
      'sender' => $comment->user->name(),
      'subject' => $this->subject,
      'message' => $comment->body,
      'link' => $this->link(),
    );
    $from = email::notification_address('message', $this->id);
    foreach ($this->users as $user) {
      if ($user->id != $comment->user_id) {
        email::announcement($user->email, $from, 'message_reply', $vars, $comment->user->name());
      }
    }
    
    // Mark all as unread.
    $this->db->query("UPDATE messages_users SET unread = 1, status = 1 WHERE message_id = ?", $this->id);
    
    // Update timestamp.
    $this->updated = date::to_db();
    $this->save();
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
    return ORM::factory('comment')->select('value AS liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'message', 'comments.object_id' => $this->id, 'comments.status' => 1))->find_all();
  }
  
  /**
   * Computed fields for inbox formatting: sendees and sendee picture.
   */
  public function __get($column) {
    switch ($column) {
      // Return a concatenated list of names.
      case 'sendees':
        switch ($this->users->count()) {
          case 1:
            foreach ($this->users as $user) {
              return html::anchor('profile/'. $user->id, $user->name());
            }
          case 2:
            foreach ($this->users as $user) {
              if ($user->id != kohana::config('chapterboard.user_id'))
                return html::anchor('profile/'. $user->id, $user->name());
            }
          case 3:
            foreach ($this->users as $user) {
              if ($user->id != kohana::config('chapterboard.user_id'))
                $names[] = html::anchor('profile/'. $user->id, $user->first_name);
            }
            return sprintf('%s and %s', $names[0], $names[1]);
          case 4:
            foreach ($this->users as $user) {
              if ($user->id != kohana::config('chapterboard.user_id'))
                $names[] = html::anchor('profile/'. $user->id, $user->first_name);
            }
            return sprintf('%s, %s, and %s', $names[0], $names[1], $names[2]);
          default:
            $show = 3; // Number of names to show.
            foreach ($this->users as $user) {
              if ($user->id != kohana::config('chapterboard.user_id'))
                $names[] = html::anchor('profile/'. $user->id, $user->first_name);
            }
            return text::widont(sprintf('%s and %s', implode(', ', array_slice($names, 0, $show)), format::plural(count($names) - $show, '@count other', '@count others')));
        }
        break;
      // Return first user picture of non-current user.
      case 'sendee_picture':
        foreach ($this->users as $user) {
          if ($user->id != kohana::config('chapterboard.user_id')) {
            if ($user->picture) {
              return $user->picture();
            }
          }
        }
        return ORM::factory('user')->default_picture_filename;
    }
    return parent::__get($column);
  }
  
  /**
   * Validation.
   *
   * Order of the callbacks is important because we must add group members
   * to $this->recipients before checking the 'members' field. Other wise
   * $this->recipients doesn't have members from the groups that were
   * selected.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('subject', 'required')
    ->add_rules('body', 'required')
    ->add_rules('members', 'blob')
    ->add_rules('groups', 'is_array')
    ->add_callbacks('groups', array($this, '_check_groups'))
    ->add_callbacks('members', array($this, '_check_members'))
    ->add_callbacks('members', array($this, '_count_users'));
    return parent::validate($array, $save);
  }
  
  public function _check_members(Validation &$array, $field) {
    $ids = array_filter(split(',', trim($array[$field])));
    if ( ! empty($ids)) {
      $users = ORM::factory('user')->in('id', $ids)->where('site_id', kohana::config('chapterboard.site_id'))->where('status', 1)->find_all();
      if ($users->count() < 1) {
        $array->add_error($field, 'members_invalid');
      }
      else {
        foreach ($users as $user) {
          $this->recipients[$user->id] = $user;
        }
      }    
    }
  }
  
  public function _check_groups(Validation &$array, $field) {
    $ids = array_filter($array[$field]);
    if ( ! empty($ids)) {
      $groups = ORM::factory('group')->in('id', $ids)->where('site_id', kohana::config('chapterboard.site_id'))->find_all();
      if ($groups->count() != count($ids)) {
        $array->add_error($field, 'groups_invalid');
      }
      else {
        foreach ($groups as $group) {
          foreach ($group->users as $user) {
            $this->recipients[$user->id] = $user;
          }
        }
      }
    }
  }
  
  public function _count_users(Validation &$array, $field) {
    if (count($this->recipients) < 1) {
      $array->add_error($field, 'required');
    }
  }
  
  public function before_insert() {
    $this->created = date::to_db();
    $this->updated = $this->created;
    $this->user_id = kohana::config('chapterboard.user_id');  
    $this->groups = implode(', ', $this->groups); // Groups comes in as an array from checkboxes.
  }
  
  // Add member -> message relationships.
  public function after_insert() {
    // Insert sender.
    $this->db->query("INSERT INTO messages_users (message_id, user_id, status, unread) VALUES (?, ?, ?, ?)", array($this->id, $this->user_id, 1, 0));

    // Insert recipients and send notifications. 
    $vars = array(
      'sender' => $this->user->name(),
      'subject' => $this->subject,
      'message' => $this->body,
      'link' => $this->link(),
    );
    $from = email::notification_address('message', $this->id);
    if ( ! empty($this->recipients)) {
      foreach ($this->recipients as $user) {
        $this->db->query("INSERT INTO messages_users (message_id, user_id, status, unread) VALUES (?, ?, ?, ?)", array($this->id, $user->id, 1, 1));
        email::announcement($user->email, $from, 'message_send', $vars, $this->user->name());
      }
    }
  }
  
}