<?php defined('SYSPATH') or die('No direct script access.');

class Recruitment_Controller extends Private_Controller {
  
  public $secondary = 'menu/recruitment';
  
  /**
   * Pre Controller Hook.
   */
  function _pre_controller() {
    css::add('styles/recruitment.css');
    javascript::add('scripts/recruitment.js');
    if ( ! A2::instance()->allowed('recruit', 'access'))
      Event::run('system.403');
  }
  
  /**
   * Show a list of recruitment categories.
   */
  function index($list = 'active') {
    $this->lists = array(
      'active' => 0,
      'bidded' => 1,
      'not-recruiting' => 2
    );
    $this->list_id = $this->lists[$list];

    if (request::is_ajax()) {
      $this->recruits = ORM::factory('recruit')->orderby('name', 'ASC')->find_by_list(null, null);
      $response = array();
      foreach ($this->recruits as $recruit) {
        $response[] = (object) array(
          'id' => $recruit->id,
          'name' => $recruit->name(),
          'phone' => $recruit->phone(),
          'email' => $recruit->email,
          'picture' => theme::image('small', $recruit->picture(), array(), TRUE),
          'list' => $recruit->list_name(),
          'bid_status' => $recruit->bid_status(),
          'good_fit' => format::plural($recruit->like_count, Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural'))
        );
      }
      response::json(TRUE, NULL, $response);
    }

    $this->title = 'Recruitment Lists';
    $this->year = $this->input->get('year');
    $this->hometown = $this->input->get('hometown');
    $this->high_school = $this->input->get('high_school');
    $this->recruits = ORM::factory('recruit')->find_by_list($this->list_id, $this->year, $this->hometown, $this->high_school);
    $this->list_counts = ORM::factory('recruit')->list_counts();
    if ($this->list_id == 1) {
      $this->bid_counts = ORM::factory('recruit')->bid_counts();
    }
    foreach (ORM::factory('recruit')->find_by_list($this->list_id) as $recruit) {
      $this->stats['year'][$recruit->year]++;
      $key = text::searchable($recruit->hometown);
      $this->stats['hometown'][$key]['name'] = $recruit->hometown;
      $this->stats['hometown'][$key]['count']++;
      $key = text::searchable($recruit->high_school);
      $this->stats['high_school'][$key]['name'] = $recruit->high_school;
      $this->stats['high_school'][$key]['count']++;
    }
    unset($this->stats['year']['']);
    ksort($this->stats['hometown']);
    ksort($this->stats['high_school']);
    unset($this->stats['hometown']['']);
    unset($this->stats['high_school']['']);
  }
  
  /**
   * Show an individual recruit.
   */
  function show($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! $this->recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->recruit, 'view'))
      Event::run('system.403');
    if ( ! $this->recruit->status)
      Event::run('system.404');
    
    $this->title = $this->recruit->name;

    if ($post = $this->input->post()) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->recruit->id;
      $this->comment->object_type = 'recruit';
      if ($this->comment->validate($post, TRUE)) {
        message::add('success', 'Comment added successfully.');
      }
      else {
        message::add('error', 'Error adding comment. Please try again.');
      }
    }

    if (request::is_ajax()) {
      $response = array(
        'id' => $this->recruit->id,
        'name' => $this->recruit->name(),
        'picture' => theme::image('small', $this->recruit->picture(), array(), TRUE),
        'phone' => $this->recruit->phone,
        'phone_formatted' => $this->recruit->phone(),
        'email' => $this->recruit->email,
        'school_year' => $this->recruit->year,
        'hometown' => $this->recruit->hometown,
        'high_school' => $this->recruit->high_school,
        'about' => $this->recruit->about, //strip_tags($this->recruit->about),
        'list' => $this->recruit->list_name(),
        'bid_status' => $this->recruit->bid_status(),
        'author' => $this->recruit->user->name(),
        'good_fit' => format::plural($this->recruit->like_count, Kohana::lang('recruitment.good_fit.singular'), Kohana::lang('recruitment.good_fit.plural')),
        'editable' => A2::instance()->allowed($this->recruit, 'edit'),
        'liked' => $this->recruit->is_liked(),
        'comments' => array(),
        'votes' => array(),
        'text_button' => Kohana::lang('recruitment.good_fit.button'),
        'text_voted' => Kohana::lang('recruitment.good_fit.voted'),
      );
      foreach ($this->recruit->comments() as $comment) {
        $response['comments'][] = array(
          'author' => $comment->user->name(),
          'picture' => theme::image('small', $comment->user->picture(), array(), TRUE),
          'body' => $comment->body,
          'created' => date::display($comment->created, 'M d, Y'),
          'votes' => $comment->like_count,          
        );
      }
      $this->votes = ORM::factory('vote')->with('user')->orderby('user.searchname', 'ASC')->where('object_type', 'recruit')->where('object_id', $this->recruit->id)->find_all();
      foreach ($this->votes as $vote) {
        $response['votes'][] = array(
          'id' => $vote->user->id,
          'name' => $vote->user->name(),
          'picture' => theme::image('small', $vote->user->picture(), array(), TRUE),
          'type' => $vote->user->type(),
        );
      }
      response::json(TRUE, NULL, $response);
    }
  }
  
  /**
   * Add a new recruit.
   */
  function add() {
    if ( ! A2::instance()->allowed('recruit', 'add'))
      Event::run('system.403');

    $this->title = 'Add Recruit';
    
    if ($post = $this->input->post()) {
      $this->recruit = ORM::factory('recruit');
      if ($this->recruit->validate($post, TRUE)) {
        // Validate upload and save file if valid.
        $valid = upload::validate('photo', 'image');
        if ($valid->validate()) {
          $info = upload::info('photo');
          if ($fileinfo = upload::save('photo', $info['filename'], Kohana::config('upload.directory'))) {
            $data = array_merge($info, $fileinfo);
            $data['object_type'] = 'recruit';
            $data['object_id'] = $this->recruit->id;
            $this->file = ORM::factory('file')->insert($data);
            $this->recruit->file_id = $this->file->id;
            $this->recruit->save();
          }
        }
        message::add('success', 'Your recruit was added successfully.');
        url::redirect('recruitment/show/'. $this->recruit->id);
      }
      else {
        message::add('error', 'There were errors adding your recruit.  Please make the changes below and re-try.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_recruit');
      }
    }
    else {
      
    }
  }
  
  /**
   * Edit an individual recruit.
   */
  function edit($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! $this->recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->recruit, 'edit'))
      Event::run('system.403');

    $this->title = 'Edit Recruit';
    $this->view = 'recruitment/add';
    
    if ($post = $this->input->post()){
      if ($this->recruit->validate($post, TRUE)) {
        // Validate upload and save file if valid.
        $valid = upload::validate('photo', 'image');
        if ($valid->validate()) {
          $info = upload::info('photo');
          if ($fileinfo = upload::save('photo', $info['filename'], Kohana::config('upload.directory'))) {
            $this->recruit->file->delete();
            $data = array_merge($info, $fileinfo);
            $data['object_type'] = 'recruit';
            $data['object_id'] = $this->recruit->id;
            $this->file = ORM::factory('file')->insert($data);
            $this->recruit->file_id = $this->file->id;
            $this->recruit->save();
          }
        }
        message::add('success', 'Changes saved');
        if (request::is_ajax()) response::json(TRUE);
        url::redirect('recruitment/show/'. $this->recruit->id);
      }
      else {
        if (request::is_ajax()) response::json(FALSE);
        message::add('error', 'Errors saving recruit.  Please make changes and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_recruit');
      }
    }
    else {
      $this->form = $this->recruit->as_array();
    }
  }
  
  /**
   * Delete an individual recruit.
   */
  function delete($id) {
    $this->recruit = ORM::factory('recruit', $id);
    if ( ! A2::instance()->allowed($this->recruit, 'delete'))
      Event::run('system.403');
    
    $this->recruit->unpublish();
    message::add('success', sprintf('%s has been unpublished.', $this->recruit->name));
    url::redirect('recruitment');
  }
  
  /**
   * Send an announcement to recruits.
   */
  function announcement() {
    if ( ! A2::instance()->allowed('recruit', 'manage'))
      Event::run('system.403');
          
    if ($post = $this->input->post()) {
      if ($count = ORM::factory('recruit')->send_announcement($post)) {
        if (is_numeric($count)) {
          message::add(TRUE, sprintf('Your announcement email has been sent to %s.', format::plural($count, '@count recruit', '@count recruits')));
        }
        else {
          message::add(FALSE, 'Your announcement email was not sent because there are no email addresses for the recruits you are sending to.');
        }
        url::redirect('recruitment'); // Message add is handled inside so that we can count number sent.
      }
      else {
        message::add('error', 'There was an error sending your announcement, please make the changes suggested below and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_recruit_announcement');
      }
    }
    
    $this->title = 'Send Announcement';
    $this->lists = array(0 => 'Actively Recruiting', 1 => 'Bidded Members', 2 => 'No Longer Recruiting');
  }

  /**
   * Voting
   */
  public function vote($action, $id) {
    $recruit = ORM::factory('recruit', $id);
    if ( ! $recruit->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($recruit, 'view'))
      Event::run('system.404');
      
    if ($action == 'remove') {
      ORM::factory('vote')->remove('recruit', $id, $this->user->id);
    }
    else {
      ORM::factory('vote')->insert('recruit', $id, $this->user->id);
    }
    if (request::is_ajax()){
      $this->show($id);
    }
    url::redirect('recruitment/show/'. $id);
  }

  /**
   * Export
   */
  public function export($list = null) {
    switch ($list) {
      case 'recruiting':
        $list_id = 0;
        break;
      case 'bidded':
        $list_id = 1;
        break;
    }
    $rows[] = array('Name', 'Phone', 'Email', 'School Year', 'Major', 'List', 'Bid Status', 'Good Fit Votes', 'Facebook', 'ChapterBoard');
    $this->recruits = ORM::factory('recruit')->find_by_list($list_id);
    foreach ($this->recruits as $recruit) {
      $rows[] = array($recruit->name, $recruit->phone, $recruit->email, $recruit->year, $recruit->major, $recruit->list_name(), $recruit->bid_status(), $recruit->like_count, $recruit->facebook, 'http://app.chapterboard.com/recruitment/show/'. $recruit->id);
    }
    response::csv($rows, 'chapterboard-recruits');
  }

}