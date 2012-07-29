<?php defined('SYSPATH') or die('No direct script access.');

class Course_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('department', 'user');
  
  protected $has_many_polymorphic = array('files' => 'object', 'comments' => 'object');

  public function find_recent($limit = 10, $offset = 0, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->orderby('updated', 'DESC')->where('site_id', $site_id)->where('status', TRUE)->find_all($limit, $offset);
  }
  
  public function find_by_search($query, $site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    $this->where('site_id', $site_id);
    $this->where('status', TRUE);
    foreach ($query as $key => $value) {
      $this->like($key, $value);
    }
    return $this->find_all();
  }
  
  public function departments_select_list() {
    $items = $this->select('COUNT(*) AS count, department AS name')->where('status', TRUE)->where('site_id', kohana::config('chapterboard.site_id'))->groupby('department')->orderby('department', 'ASC')->find_all();
    $results = array('' => '- All Departments -');
    foreach ($items as $item) {
      $results[$item->name] = sprintf('%s (%s)', $item->name, $item->count);
    }
    return $results;
  }
  
  public function professors_select_list() {
    $items = $this->select('COUNT(*) AS count, professor AS name')->where('status', TRUE)->where('site_id', kohana::config('chapterboard.site_id'))->groupby('professor')->orderby('professor', 'ASC')->find_all();
    $results = array('' => '- All Professors -');
    foreach ($items as $item) {
      $results[$item->name] = sprintf('%s (%s)', $item->name, $item->count);
    }
    return $results;
  }
  
  public function count_by_site($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->select('COUNT(*) AS count')->where('status', TRUE)->where('site_id', $site_id)->find()->count;
  }
  
  /**
   * Find all published comments that belong to a course.
   */
  public function comments() {
    // custom join to preload whether or not the user liked the comment.
    $join_on = array(
      'comments.id' => 'votes.object_id',
      'votes.object_type' => '"comment"',
      'votes.user_id' => kohana::config('chapterboard.user_id')
    );
    return ORM::factory('comment')->select('value AS liked, comments.*')->custom_join('votes', $join_on, null, 'LEFT')->where(array('comments.object_type' => 'course', 'comments.object_id' => $this->id, 'comments.status' => 1))->orderby('created', 'DESC')->find_all();
  }
  
  public function title() {
    return sprintf('%s (%s)', $this->title, $this->code);
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->add_rules('title', 'required')
    ->add_rules('department', 'required')
    ->add_rules('code', 'required')
    ->add_rules('professor', 'required')
    ->add_rules('description', 'blob');
    
    return parent::validate($array, $save);
  }
  
  public function before_insert() {
    if ( ! $this->site_id) {
      $this->site_id = kohana::config('chapterboard.site_id');
    }
    $this->created = date::to_db();
    $this->updated = $this->created;
    $this->status = 1;
    $this->user_id = kohana::config('chapterboard.user_id');
  }
  
  public function updated() {
    $this->updated = date::to_db();
    return $this->save();
  }
  
  public function archive() {
    $this->status = 0;
    return $this->save();
  }
  
  public function __get($column) {
    if ($column == 'code') {
      return strtoupper(parent::__get($column));
    }
    return parent::__get($column);
  }
  
  public function get_resource_id() {
    return 'course';
  }
}