<?php defined('SYSPATH') or die('No direct script access.');

class sms {

  // Contains an array of sent messages.
  static public $log = array();

  static protected $connection;
  
  public static function send($to, $message) {
    if ( ! self::$connection) {
      self::$connection = new Twilio;
    }
    
    // Log sms activity.
    self::$log[] = array('to' => $to, 'message' => $message);
    // log::system('sms', sprintf('Sending SMS to %s: %s', format::phone($to), $message), 'notice');
    
    // Don't send sms via gateway if in debug mode.
    if (Kohana::config('sms.debug')) {
      return TRUE;
    }
    return self::$connection->send($to, $message);
  }

  /**
   * Send a text message via email.
   *
   * @param fully loaded user object, preferably with user_profiles already joined. 
   * @param message, the full text message to send.
   *
   * @return boolean based on whether the message was sent or not.
   */
  public static function send_via_email($user, $message, $from = array()) {
    if ($user->profile->phone && $user->profile->phone_carrier) {
      $email_domain = Kohana::config('sms.carriers.'. $user->profile->phone_carrier. '.address');
      $to = sprintf('%s@%s', $user->profile->phone, $email_domain);
      if (empty($from)) {
        $from = array('sms@chapterboard.com', 'ChapterBoard');
      }
      $message = sms::clean_message($message);
      return email::send($to, $from, '', $message);
    }
    return FALSE;
  }
  
  public static function clean_message($message) {
    $message = str_replace('(', '[', $message);
    $message = str_replace(')', ']', $message);
    return $message;
  }
  
  public static function carriers_select($show_empty = TRUE) {
    $options[''] = '- No Text Messages -';
    $items = Kohana::config('sms.carriers');
    foreach ($items as $key => $item) {
      $options[$key] = $item['name'];
    }
    return $options;
  }
}