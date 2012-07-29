<?php defined('SYSPATH') or die('No direct script access.');

class Topic_Controller extends Forum_Controller {

  function _pre_controller() {
    Router::$routed_uri = 'forum';
  }
  
  function index() {
    url::redirect('forum');
  }
  
  /**
   * View a topic
   */
  function show($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');
    if ( ! $this->topic->status)
      Event::run('system.404');
      
    $this->title = $this->topic->title;
    
    $topic_history = ORM::factory('topic_history')->where(array('user_id' => $this->user->id, 'topic_id' => $this->topic->id))->find();
    $this->last_viewed = $this->user->topic_history > $topic_history->last_viewed ? $this->user->topic_history : $topic_history->last_viewed;
    $this->new = $this->last_viewed < $this->topic->updated ? 1 : 0;
    $this->topic->markread();
    
    if ($this->topic->poll) {
      $this->vote = ORM::factory('poll_vote')->where('user_id', $this->user->id)->where('poll_id', $this->topic->poll->id)->find();
    }
      
    // Check to see if a comment was posted.
    if ($this->topic->locked == 0 && ($post = $this->input->post())) {
      $this->comment = ORM::factory('comment');
      $this->comment->object_id = $this->topic->id;
      $this->comment->object_type = 'topic';
      if ($this->comment->validate($post, TRUE)) {
        // Topic has been saved, now save uploaded files.
        if ($uploads = $this->session->get('uploads-'. $post['key'])) {
          foreach ($uploads as $upload) {
            // Move temp files to upload directory and insert records into database.
            if ($fileinfo = upload::save($upload, $upload['filename'], Kohana::config('upload.directory'))) {
              $upload = array_merge($upload, $fileinfo);
            }
            $upload['object_type'] = 'comment';
            $upload['object_id'] = $this->comment->id;
            ORM::factory('file')->insert($upload);
          }
          $this->session->delete('uploads-'. $post['key']);
        }
      }
      else {
        message::add('error', 'Sorry, you can\'t leave an empty comment. Please add a comment and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_comment');
        $this->uploads = $this->session->get('uploads-'. $post['key']);
      }
    }
    else {
      $this->form['key'] = text::token();
    }
    
    if (request::is_ajax()) {
      $response['topic'] = array(
        'title' => $this->topic->title,
        'author' => $this->topic->user->name(),
        'author_picture' => theme::image('small', $this->topic->user->picture(), array(), TRUE),
        'body' => $this->topic->body, 
        'created' => date::display($this->topic->created, 'M d, Y g:ia'),
        'likes_formatted' => format::plural($this->topic->like_count, '@count like', '@count likes'),
        'comments' => array(),
      );
      foreach ($this->topic->comments() as $comment) {
        $response['topic']['comments'][] = array(
          'id' => $comment->id,
          'author' => $comment->user->name(),
          'author_picture' => theme::image('small', $comment->user->picture(), array(), TRUE),
          'body' => $comment->body,
          'created' => date::display($comment->created, 'M d, Y g:ia'),
          'likes_formatted' => format::plural($comment->like_count, '@count like', '@count likes'),
        );
      }
      response::json(TRUE, NULL, $response);
    }
  }
  
  /**
   * Add new topic
   */
  function add() {
    $this->title = 'Post New Topic';
    $this->forums = ORM::factory('forum')->forum_options();

    // If there is post data, we are submitting new topic.
    if ($post = $this->input->post()) {
      
      $this->topic = ORM::factory('topic');
      if ($this->topic->validate($post, TRUE)) {
        ORM::factory('file')->save_uploads($post['key'], 'topic', $this->topic->id);
        
        // Save Poll options
        $choices = array_filter($post['poll']);
        if ($post['add_poll'] && is_string($post['question']) && $choices) {
          $poll = ORM::factory('poll');
          $poll->question = $post['question'];
          $poll->topic_id = $this->topic->id;
          $poll->private = (bool) $post['private'];
          $poll->save();

          foreach ($choices as $id => $text) {
            $data = array('text' => $post['poll'][$id], 'poll_id' => $poll->id);
            $item = ORM::factory('poll_choice');
            $item->validate($data, TRUE);
          }
        }
        
        url::redirect('forum/topic/'. $this->topic->id);
      }
      else {
        message::add('error', 'There were errors saving your new post.  Please fix them and try again.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_topic');
        $this->uploads = $this->session->get($post['key']);
      }
    }
    else {
      $this->form['key'] = text::token();
    }
    
    if (request::is_ajax()) {
      foreach ($this->forums as $id => $title) {
        $response['forums'][] = array(
          'id' => $id,
          'title' => $title
        );
      }
      response::json(TRUE, null, $response);
    }
  }
  
  /**
   * Edit a topic.
   */
  function edit($id) {
    $this->title = 'Edit Topic';
    $this->topic = ORM::factory('topic', $id);

    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin')))
      Event::run('system.403');

    $this->forums = ORM::factory('forum')->forum_options();
    
    if ($post = $this->input->post()) {
      if ($this->topic->validate($post, TRUE)) {
        ORM::factory('file')->save_uploads($post['key'], 'topic', $this->topic->id);
        
        // Save Poll options or delete poll.
        if ($post['add_poll']) {
          $choices = $post['poll'];
          if ($post['question'] && $choices) {
            if ( ! $this->topic->poll->loaded) {
              $this->topic->poll->topic_id = $this->topic->id;
            }
            $this->topic->poll->question = $post['question'];
            $this->topic->poll->private = (bool) $post['private'];
            $this->topic->poll->save();

            foreach ($choices as $id => $text) {
              $item = $id < 10 ? ORM::factory('poll_choice') : ORM::factory('poll_choice', $id);
              if ($text) {
                $data = array('text' => $choices[$id], 'poll_id' => $this->topic->poll->id);
                $item->validate($data, TRUE);
              }
              else if ($item->loaded) {
                $item->delete();
              }
            }
          }
          else {
            message::add(FALSE, 'Poll could not be saved. Please enter a question and at least one poll option.');
            $this->uploads = ORM::factory('file')->find_by_parent('topic', $this->topic->id);
            $this->form = $post;
            $this->form['key'] = text::token();
            return;
          }
        }
        else {
          $this->topic->poll->delete();
        }
        
        message::add('success', 'Topic updated successfully.');
        url::redirect('forum/topic/'. $this->topic->id);
      }
      else {
        $this->form = $post;
        $this->errors = $post->errors('form_topic');
        $this->uploads = upload::files_from_session($post['key']);
      }
    }
    else {
      $this->form = $this->topic->as_array();    
      $this->form['poll'] = $this->topic->poll->as_array();
      $this->form['key'] = text::token();
      $this->uploads = ORM::factory('file')->find_by_parent('topic', $this->topic->id);
      upload::set_files($this->form['key'], $this->uploads);
    }
  }
  
  /**
   * Make a post sticky
   */
  function sticky($id = NULL, $sticky = TRUE) {
    // Load the topic in question.
    $this->topic = ORM::factory('topic', $id);

    // Make sure the user requesting the sticky has admin access to the forum.
    if ($this->topic->loaded && A2::instance()->allowed($this->topic->forum, 'admin')) {
      if ($this->topic->sticky($sticky)) {
        $status = TRUE;
        $message = $sticky ? 'Topic "%s" is now sticky at the top.' : 'Topic "%s" is no longer sticky.';
        $message = sprintf($message, $this->topic->title);
      }
      else {
        $status = FALSE;
        $message = 'Error.';
      }
    }
    else {
      $status = FALSE;
      $message = 'Access Denied';
    }

    if (request::is_ajax()) {
      response::json($status, $message, $data);
    }
    else {
      message::add($status, $message);
      url::redirect('forum/'. $this->topic->forum_id);
    }
  }
  
  /**
   * Mark a post as not sticky.
   */
  function unsticky($id = NULL) {
    $this->sticky($id, FALSE);
  }
  
  /**
   * Archive a post.
   */
  function delete($id = NULL) {
    $topic = ORM::factory('topic', $id);    
    if ( ! (A2::instance()->allowed($topic->forum, 'admin') || A2::instance()->allowed($topic, 'delete')))
      Event::run('system.404');

    $topic->status(FALSE);
    url::redirect('forum/'. $topic->forum_id);
  }
  
  /**
   * Accept a vote for a poll choice.
   */
  function vote() {
    $this->topic = ORM::factory('topic', $this->input->post('topic_id'));
    if ( ! $this->topic->poll->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');

    if ($this->input->post('choice_id') > 0) {
      $this->topic->poll->vote($this->input->post('choice_id'), $this->user->id);
      message::add(TRUE, 'Your vote has been cast!');
    }
    else {
      message::add(FALSE, 'Please choose an option to cast your vote.');
    }
    url::redirect('forum/topic/'. $this->topic->id);
  }
  
  function unvote($id) {
    $this->topic = ORM::factory('topic', $id);
    if ( ! $this->topic->poll->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');
    
    message::add(TRUE, 'Your vote has been removed.');
    $this->topic->poll->remove_vote($this->user->id);
    url::redirect('forum/topic/'. $this->topic->id);
  }
  
  function votes($id) {
    $this->topic = ORM::factory('topic', $id);
    if ( ! $this->topic->poll->loaded)
      Event::run('system.404');
    if ( ! (A2::instance()->allowed($this->topic, 'edit') || A2::instance()->allowed($this->topic->forum, 'admin')))
      Event::run('system.403');
    
    $this->title = 'Poll Results';
    if (request::is_ajax()) {
      response::html(View::factory('forum/topic/votes')->render());
    }
  }
  
  function like($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');
    
    ORM::factory('vote')->insert('topic', $id, $this->user->id);
    url::redirect('forum/topic/'. $this->topic->id);
  }
  
  function unlike($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'view'))
      Event::run('system.403');
    
    ORM::factory('vote')->remove('topic', $id, $this->user->id);
    url::redirect('forum/topic/'. $this->topic->id);    
  }
  
  function lock($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'admin'))
      Event::run('system.403');
      
    $this->topic->lock();
    message::add(TRUE, 'This topic has been locked, to enable commenting you must unlock this topic.');
    url::redirect('forum/topic/'. $this->topic->id);
  }
  
  function unlock($id) {
    $this->topic = ORM::factory('topic', $id);
    
    if ( ! $this->topic->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->topic->forum, 'admin'))
      Event::run('system.403');
      
    $this->topic->unlock();
    message::add(TRUE, 'This topic has been unlocked.');
    url::redirect('forum/topic/'. $this->topic->id);
  }
}