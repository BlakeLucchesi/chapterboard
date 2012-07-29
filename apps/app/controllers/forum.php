<?php defined('SYSPATH') or die('No direct script access.');

class Forum_Controller extends Private_Controller {
  
  public $secondary = 'menu/forum';
    
  /**
   * Display the available forum boards.
   */
  function index() {
    $this->title = 'Forum Boards';
    $this->forums = ORM::factory('forum')->forums();
    
    if (request::is_ajax()) {
      $response = array();
      foreach ($this->forums as $forum) {
        $response[] = array(
          'id' => $forum->id,
          'title' => $forum->title,
          'unread' => $forum->has_unread_topics($this->user->id),
          'description' => $forum->description
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  function show($id) {
    if ($id > 0) {
      Router::$routed_uri = 'forum';
      $this->forum = ORM::factory('forum', $id);
      if ( ! $this->forum->loaded)
        Event::run('system.403');
      if ( ! A2::instance()->allowed($this->forum, 'view'))
        Event::run('system.403');      
      $this->title = $this->forum->loaded ? $this->forum->title : 'Topics';
      $this->forum_title = $this->forum->title;

      $this->pagination = new Pagination(array('total_items' => ORM::factory('topic')->topics_count($this->forum->id)));
      $count = $this->pagination->items_per_page;
      $start = $this->pagination->sql_offset();
      $this->topics = ORM::factory('topic')->topics($start, $count, $this->forum->id);
    }
    else {
      $this->title = 'Unread Topics';
      $this->forum_title = $this->title;
      $this->pagination = new Pagination(array('total_items' => 90));
      $count = $this->pagination->items_per_page;
      $start = $this->pagination->sql_offset();
      $this->topics = ORM::factory('topic')->unread($count, $start);
    }

    $this->admin = A2::instance()->allowed($this->forum, 'admin');
    
    if (request::is_ajax()) {
      $response['forum'] = array(
        'title' => $this->forum_title,
        'admin' => $this->admin ? TRUE : FALSE,
      );
      $response['topics'] = array();
      foreach ($this->topics as $topic) {
        $response['topics'][] = array(
          'id' => $topic->id,
          'title' => $topic->title,
          'author' => $topic->user->name(),
          'updated' => date::display($topic->updated, 'M d, Y g:ia'),
          'likes_formatted' => format::plural($topic->like_count, '@count like', '@count likes'),
          'comments_formatted' => format::plural($topic->comment_count, '@count comment', '@count comments'),
          'unread' => (bool) $topic->is_new(),
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  function recent() {
    $this->title = 'Recent Topics';
    $this->topics = ORM::factory('topic')->recent_topics(30);
    $this->admin = A2::instance()->allowed('forum', 'admin');
  }
  
  function unread() {
    $this->title = 'Unread Topics';
    $this->topics = ORM::factory('topic')->unread(30);
    $this->admin = A2::instance()->allowed('forum', 'admin');
  }
  
  function markasread() {
    $topics = ORM::factory('topic')->markallread();
    message::add('success', 'All topics have been marked as read.');
    url::redirect('forum');
  }
  
  function search() {
    $this->title = "Search";
    if ($this->input->get('q')) {
      
      // Gather forum ids the user is allowed to view.
      foreach (ORM::factory('forum')->forums() as $forum) {
        $forum_ids[] = $forum->id;
      }
      
      // Peform search if the user can view forums.
      if ( ! empty($forum_ids)) {
        $solr = solr::service();
        
        // Setup mock pagination to get default count and offsets.
        $this->pagination = new Pagination(array('total_items' => 1000000));
        $this->count = $this->pagination->items_per_page;
        $this->start = $this->pagination->sql_offset();
        
        // Query the solr search server.
        try {
          $params = array('fq' => array('forum_id:('. implode(' OR ', $forum_ids) .')'), 'hl' => 'true', 'hl.fl' => 'body,comments');
          if ($_GET['sort'] != 'score') {
            $params['sort'] = 'updated desc';
          }
          $this->response = $solr->search($this->input->get('q'), $this->start, $this->count, $params);
          // log::system('solr', sprintf('Forum search: %s [%s]', $this->input->get('q'), $this->site->name()));          
        }
        catch (Exception $e) {
          log::system('solr', 'Error connecting to search server.', 'error');
          $this->search_down = TRUE;
        }
        // Set pagination based on numFound from search results.
        $this->total_count = $this->response->response->numFound;
        $this->pagination = new Pagination(array('total_items' => $this->total_count));
        
        // Complete pagination and result counting.
        if ($this->total_count < ($this->start + $this->count)) {
          $this->count = $this->total_count - $this->start;
        }
        
        // Load full forum topic objects to display just like in the forums.
        foreach ($this->response->response->docs as $item) {
          $topic_ids[] = $item->object_id;
        }
        if ( ! empty($topic_ids)) {
          $this->topics = ORM::factory('topic')->in('topics.id', $topic_ids)->find_keyed_object();
        }
      }
    }
  }
  
  function notifications() {
    $this->title = 'Forum Notification Settings';
    
    // Perform updates if the form is being saved.
    if ($post = $this->input->post()) {
      ORM::factory('notification')->where(array('user_id' => $this->user->id, 'object_type' => 'forum'))->delete_all();
      foreach ($post['forum'] as $forum_id => $value) {
        $forum = ORM::factory('forum', $forum_id);
        if ($this->acl->allowed($forum, 'view')) {
          ORM::factory('notification')->signup('forum', $forum_id, $this->user->id, $value);
        }
      }
      message::add('success', 'Notifications settings saved.');
    }

    $this->forums = ORM::factory('forum')->forums();
    $this->notifications = ORM::factory('notification')->find_by_object('forum');
  }
  
  function archive($id) {
    $this->forum = ORM::factory('forum', $id);
    if ( ! (A2::instance()->allowed($this->forum, 'admin')))
      Event::run('system.404');
    
    $this->topics = ORM::factory('topic')->where('forum_id', $id)->find_all();
    foreach ($this->topics as $topic) {
      $topic->status(FALSE);
    }
    
    message::add(TRUE, 'All topics in this forum have been archived.');
    url::redirect('forum/'. $this->forum->id);
  }
  
}