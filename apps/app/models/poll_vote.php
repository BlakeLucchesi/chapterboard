<?php defined('SYSPATH') or die('No direct script access.');

class Poll_vote_Model extends ORM {
  
  protected $belongs_to = array('poll', 'poll_choice', 'user');
  
  public function before_insert() {
    $this->created = date::to_db();
  }
  
  public function before_update() {
    $this->created = date::to_db();
  }
  
  public function after_insert() {
    $this->poll_choice->update_count();
  }

  public function after_update() {
    $this->poll_choice->update_count();
  }
  
}