<?php defined('SYSPATH') or die('No direct script access.');

class Votes_Controller extends Private_Controller {
  
  public function show($object_type, $object_id) {
    $this->votes = ORM::factory('vote')->where('object_type', $object_type)->where('object_id', $object_id)->find_all();
    $this->object_type = $object_type;
    foreach ($this->votes as $vote) {
      $this->users[] = $vote->user;
    }
    if (request::is_ajax()) {
      response::html(View::factory('votes/show')->render());
    }
  }
  
}