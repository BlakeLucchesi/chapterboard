<?php defined('SYSPATH') or die('No direct script access.');

class Sms_log_Model extends ORM {
  
  protected $table_name    = 'sms_log';
  protected $object_plural = 'sms_log';
  protected $belongs_to    = array('sms', 'user');

  public function record($sms_id, $user_id, $number) {
    $this->sms_id = $sms_id;
    $this->user_id = $user_id;
    $this->number = $number;
    $this->created = date::to_db();
    return $this->save();
  }
  
}