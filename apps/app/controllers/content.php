<?php defined('SYSPATH') or die('No direct script access.');

class Content_Controller extends Private_Controller {
  
  /**
   * Share ChapterBoard.
   */
  public function share() {
    $this->title = 'Share ChapterBoard';
    $this->form['message'] = Kohana::lang('form_share.default_message');

    if ($post = $_POST) {
      $share = ORM::factory('share');
      if ($share->validate($post, TRUE)) {
        message::add(TRUE, 'Thank you for sharing ChapterBoard with %s!', $share->name);
        email::notify($share->email, 'share', $share->as_array(), $this->user->name());
        log::system('share', sprintf("Shared: %s (%s) recommended to: %s (%s)", $this->user->name(), $this->site->name(), $share->name, $share->email), 'notice', array('sent_message' => $share->message));
      }
      else {
        message::add(FALSE, 'There was an error sending this message. Please fix the errors below.');
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_share');
      }
    }
  }
  
}