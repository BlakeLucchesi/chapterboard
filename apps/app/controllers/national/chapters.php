<?php defined('SYSPATH') or die('No direct script access.');

class Chapters_Controller extends National_Controller {
  
  public $secondary = 'menu/chapters';
  
  public function _pre_controller() {
    parent::_pre_controller();
    css::add('styles/members.css');
  }
  
  public function index() {
    $this->title = 'Chapter Rosters';
    $this->chapters = ORM::factory('site')->with('school')->users_by_chapter($this->site->chapter_id);
  }
  
  /**
   * Show chapter roster details.
   */
  function show($id, $type = 'active') {
    $this->chapter = ORM::factory('site', $id);
    $this->title = $this->chapter->chapter_name();

    // $this->title = 'Chapter Roster';
    // $this->view = 'members/index';
    
    // Flag whether the current user can manage members.
    if (A2::instance()->allowed('user', 'manage')) {
      $this->admin = TRUE;
    }
        
    // Filter by member type.
    $this->type = $type ? $type : 'active';
    
    if ($this->type == 'leadership') {
      $this->members = ORM::factory('user')->find_with_leadership($id);
    }
    else {
      $this->members = ORM::factory('user')->search_profile($this->name, $this->type, NULL, $id);
    }
    $this->stats = ORM::factory('user')->get_statistics($this->members);
    $this->type_count = ORM::factory('user')->count_types($id);
    $this->types = Kohana::config('chapterboard.user_types');
    $this->sizes = Kohana::config('chapterboard.shirt_sizes');
  }
  
  /**
   * Search for a member.
   */
  public function search() {
    $this->title = 'Member Search';
    if ($this->form = $this->input->get()) {
      $this->members = ORM::factory('user')->find_user_by_chapter($this->site->chapter_id, $this->form);
    }
    $this->chapter_options = ORM::factory('site')->chapter_select($this->site->chapter_id);
    $this->search_form = View::factory('chapters/search_form');
  }
  
  /**
   * Exportable membership report.
   */
  public function export() {
    $rows = array(array('Chapter Name', 'University', 'Actives', 'New Members', 'Alumni'));
    $this->chapters = ORM::factory('site')->with('school')->users_by_chapter($this->site->chapter_id);
    foreach ($this->chapters as $chapter) {
      $rows[] = array($chapter->chapter_name, $chapter->school->name, $chapter->actives, $chapter->pledges, $chapter->alumni);
    }
    response::csv($rows, 'membership-report');
  }
  
}