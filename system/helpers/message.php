<?php defined('SYSPATH') or die('No direct script access.');

class message_Core {

  /**
   * Set a message to be displayed on next page load.
   *
   * @param string $type
   * Message type, use one of the following to make styling of the messages consistent.
   *  error - an error occurred, notify user to take some action.
   *  success - successful action performed, notify user of result.
   *  warning - something went wrong but user action not required, notify user.
   *  notice - display a notice to the user (welcome, check out new feature, etc.)
   */
  static function add($type, $message) {
    if (is_bool($type)) {
      $type = $type ? 'success' : 'error';
    }
    $vars = array_slice(func_get_args(), 2);
    $messages = Session::instance()->get('messages');
    $messages[] = array('type' => $type, 'message' => vsprintf($message, $vars));
    Session::instance()->set('messages', $messages);
  }

  /**
   * Return the messages for display.
   */
  static function get() {
    if ($messages = Session::instance()->get_once('messages')) {
      foreach ($messages as $message) {
        $display[] = '<div class="message-'. $message['type'] .'">'. $message['message'] ."</div>\n";
      }
      return '<div class="system-messages">'. implode("\n", $display) .'</div>';
    }
  }
  
  /**
   * Show help text for a key.
   */
  static function help($key) {
    return '<div class="help message-hideable">'. Kohana::lang('help.new.'. $key) . html::anchor('#', 'Close', array('class' => 'close-message', 'key' => $key)) .'</div>';
  }
}