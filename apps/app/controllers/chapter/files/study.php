<?php defined('SYSPATH') or die('No direct script access.');

class Study_Controller extends Files_Controller {
  
  public $secondary = 'menu/files';
  
  public function index() {
    $this->title = 'Study Bank';
    if ($this->input->get('search')) {
      $this->form = $this->input->get();
      $allowed_fields = array('title', 'code', 'department', 'professor');
      $this->search = array_intersect_key($_GET, array_flip($allowed_fields));
      $this->course_title = 'Search Results';
      $this->courses = ORM::factory('course')->find_by_search($this->search);
    }
    else {
      $this->course_title = 'Recently Updated Courses';
      $this->pagination = new Pagination(array('items_per_page' => 10, 'total_items' => ORM::factory('course')->count_by_site()));
      $limit = $this->pagination->items_per_page;
      $offset = $this->pagination->sql_offset();
      $this->courses = ORM::factory('course')->find_recent($limit, $offset);
    }
    $this->departments = ORM::factory('course')->departments_select_list();
    $this->professors = ORM::factory('course')->professors_select_list();
  }
  
  
}