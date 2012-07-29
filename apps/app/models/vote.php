<?php defined('SYSPATH') or die('No direct script access.');

class Vote_Model extends ORM {
  
  protected $belongs_to = array('user');
  
  protected $belongs_to_polymorphic = array('comment' => 'id', 'event' => 'id', 'recruit' => 'id', 'topic' => 'id');
  
  /** Instance Methods **/
  
  /** Class Query Methods **/
  
  /** Validation **/
  public function insert($object_type, $object_id, $user_id, $value = 1) {
    $vote = $this->where('object_type', $object_type)->where('object_id', $object_id)->where('user_id', $user_id)->find();
    if ( ! $vote->loaded) {
      $this->object_type = $object_type;
      $this->object_id = $object_id;
      $this->user_id = $user_id;
      $this->value = $value;
      return $this->save();      
    }
    return FALSE;
  }
  
  public function remove($object_type, $object_id, $user_id) {
    $this->where('object_type', $object_type);
    $this->where('object_id', $object_id);
    $this->where('user_id', $user_id);
    $vote = $this->find();
    if ($vote->loaded) {
      $vote->update_count(-1);
      return $vote->delete();
    }
  }
  
  /**
   * After voting occurs, we update the parent records
   * count column to minimize vote count queries.
   */
  public function after_insert() {
    $this->update_count(1);
  }
  
  /**
   * Update the cached vote count columns.
   */
  public function update_count($value) {
    switch ($this->object_type) {
      case 'recruit':
        $this->recruit->like_count = $this->recruit->like_count + $value;
        $this->recruit->save();
        break;
      case 'topic':
        $this->topic->like_count = $this->topic->like_count + $value;
        $this->topic->save();
        break;
      case 'comment':
        $this->comment->like_count = $this->comment->like_count + $value;
        $this->comment->save();
        break;
    }
  }
  
}