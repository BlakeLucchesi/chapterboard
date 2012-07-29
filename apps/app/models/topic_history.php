<?php defined('SYSPATH') or die('No direct script access.');

class Topic_history_Model extends ORM {
  
  protected $belongs_to = array('user');
  
  protected $table_name = 'topic_history';
  
  protected $primary_key = 'topic_id';
  
}