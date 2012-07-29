<?php defined('SYSPATH') or die('No direct script access.');

class Comment_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('user');
  
  protected $load_with = array('user');
  
  protected $sorting = array('created' => 'ASC');
  
  protected $has_many_polymorphic = array('files' => 'object', 'votes' => 'object');  
  
  protected $belongs_to_polymorphic = array('course' => 'id', 'event' => 'id', 'file' => 'id', 'message' => 'id', 'recruit' => 'id', 'topic' => 'id');
  
  # Whether or not the user likes this comment.
  public function is_liked($user_id = null) {
    $user_id = $user_id ? $user_id : kohana::config('chapterboard.user_id');
    return $this->db->query("SELECT id FROM votes WHERE object_type = 'comment' AND object_id = ? AND user_id = ?", array($this->id, $user_id))->current()->id;
  }
    
  /**
   * link_for
   */
  public function link() {
    switch ($this->object_type) {
      case 'recruit':
        return 'recruitment/show/'. $this->object_id;
      case 'topic':
        return 'forum/topic/'. $this->object_id;
      case 'event':
        return 'calendar/event/'. $this->object_id;
      case 'course':
        return 'files/study/course/'. $this->object_id;
      case 'file':
        return 'files/photos/album/photo/'. $this->object_id;
      break;
    }
  }
  
  /**
   * Run permissions check to make sure that the user viewing the
   * comment has permissions to see it.
   */
  public function allowed() {
    switch ($this->object_type) {
      case 'topic':
        return A2::instance()->allowed($this->topic->forum, 'view');
      case 'event':
        return A2::instance()->allowed($this->event->calendar, 'view');
      case 'recruit':
        return A2::instance()->allowed($this->recruit, 'view');
      case 'course':
        return kohana::config('chapterboard.site_id') == $this->user->site_id;
      case 'file':
        return kohana::config('chapterboard.site_id') == $this->user->site_id;
    }
    return FALSE;
  }

  /**
   * Title
   */
  public function title() {
    return in_array($this->object_type, array('recruit', 'file')) ? $this->node->name : $this->node->title;
  }
  
  /**
   * Unpublish a comment.
   */
  public function unpublish() {
    $this->status = 0;
    $this->save();
    if (is_numeric($this->object_id)) {
      switch ($this->object_type) {
        case 'topic':
          ORM::factory('topic', $this->object_id)->update_comment_count();
          break;
        case 'recruit':
          ORM::factory('recruit', $this->object_id)->update_comment_count();
          break;
      }      
    }
    return $this;
  }
  
  /**
   * Validate user input before saving to the database.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->add_rules('body', 'required');
    return parent::validate($array, $save);
  }
  
  /**
   * Make sure the user has access to view the parent object before saving
   * a new comment to that object.
   */
  public function check_permission(Validation $array, $field) {
    $resource = ORM::factory(inflector::singular($array->object_type), $array->object_id);
    if ( ! $resource->loaded || ! A2::instance()->allowed($resource, 'view')) {
      $array->add_error($field, 'access_denied');
      $array->message($field, 'You cannot post a comment to a '. $array->object_type .' that you do not have access to.');
    }
  }
  
  /**
   * ORM before_insert hook.
   */
  public function before_insert() {
    if ( ! $this->user_id) {
      $this->user_id = kohana::config('chapterboard.user_id');
    }
    $this->created = date::to_db();
    $this->status = 1;
  }
  
  /**
   * ORM after_insert hook.
   */
  public function after_insert() {
    if (is_numeric($this->object_id)) {
      switch ($this->object_type) {
        case 'topic':
          ORM::factory('topic', $this->object_id)->updated();
          ORM::factory('topic', $this->object_id)->update_comment_count();
          $this->topic->send_notifications('comment', $this);
          break;
        case 'recruit':
          ORM::factory('recruit', $this->object_id)->updated();
          ORM::factory('recruit', $this->object_id)->update_comment_count();
          break;
        case 'event':
          $this->event->send_notifications('comment', $this);
          break;
        case 'course':
          $this->course->updated();
          break;
        case 'message':
          $this->message->updated($this);
          break;
      }      
    }
  }
  
  /**
   * __get().
   *
   * Return the object that the comment is linked to.
   */
  public function __get($column) {
    if ($column == 'node') {
      return $this->{$this->object_type};
    }
    return parent::__get($column);
  }
  
  /**
   * ACL Resource Interface.
   */
  public function get_resource_id() {
    return 'comment';
  }
  
}