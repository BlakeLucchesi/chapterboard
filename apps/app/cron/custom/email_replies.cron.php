<?php defined('SYSPATH') or die('No direct script access.');

$inbox = new Gmail('notification@chapterboard.com', 'rawr17');
$emails = $inbox->getNewEmails();

foreach ($emails as $email) {
  
  // Load variabls from the email we are reading.
  $user = _parse_user($email['fromaddress']);
  $from = _parse_email($email['fromaddress']);
  $object = _parse_object($email['toaddress']);
  $body = _parse_body($email['body']);
  
  // Setup the system variables and reload permissions so that
  // we can accurately check permissions for posting content.
  kohana::config_set('chapterboard.site_id', $user->site_id);
  kohana::config_set('chapterboard.user_id', $user->id);
  auth::setup_permissions();
  
  if ( ! ($object->loaded && $user->loaded && $body)) {
    log::system('email_reply', sprintf('Email Comment: Erroneous incoming reply email from %s.', $from), 'notice', $email + array('parsed body' => $body));
  }
  else if (_has_permission($user, $object)) {
    $comment = ORM::factory('comment');
    $comment->object_type = $object->object_name;
    $comment->object_id = $object->id;
    $comment->user_id = $user->id;
    $post = array('body' => $body);
    $comment->validate($post, TRUE);
  }
  else {
    log::system('email_reply', sprintf('Email Comment: Permission denied from %s.', $from));
    email::send($from, array('notification@chapterboard.com', 'ChapterBoard Notification'), 'Permission Denied', "Your response was not posted to ChapterBoard because we could not verify your identity with an active user account. If you believe this is incorrect, please contact us at team@chapterboard.com.\n\n- The ChapterBoard Team");
  }
}

/**
 * Parse the user object using the from address.
 */
function _parse_user($from_email) {
  $email = _parse_email($from_email);
  return ORM::factory('user')->find_by_email($email);
}

function _parse_email($from_address) {
  if (strpos($from_address, '<') === FALSE) {
    return $from_address;
  }
  else {
    preg_match('/<([^>]+)>/i', $from_address, $matches);
    return $matches[1];
  }
}

/**
 * Check to see whether or not the email sender (user) has access
 * to post a comment on the object (resource).
 *
 * @param User_Model (User/Role)
 *         A fully loaded user model
 * @param ORM (Resource)
 *         The object that we are checking permissions on.
 */
function _has_permission($user, $object) {
  switch ($object->object_name) {
    case 'topic':
      return A2::instance()->is_allowed($user, $object->forum, 'view');
    case 'event':
      return A2::instance()->is_allowed($user, $object->calendar, 'view');
    case 'message':
      return $object->is_allowed($user->id);
    default:
      return FALSE;
  }
}

/**
 * Parse and return the related ORM object from the email address.
 *
 * Format: notification+object:id@chapterboard.com
 *         notification+topic:100
 *         notification+event:1200
 */
function _parse_object($address, $user) {
  preg_match('/notification\+([a-zA-Z]+)-([0-9]+)@chapterboard.com/i', $address, $matches);
  $object = $matches[1];
  $id = $matches[2];
  if ($object && is_numeric($id)) {
    try {
      return ORM::factory($object, $id);
    }
    catch(Exception $e) {
      return (object)array('loaded' => FALSE);
    }
  }
  return (object)array('loaded' => FALSE);
}

/**
 * Parse the body from the email.
 *
 * Include any text that is sent above our sentinel sentence.
 */
function _parse_body($body) {
  $lines = explode("\n", $body);
  foreach ($lines as $line) {
    if (preg_match('/------Original Message------/i', $line)) {
      break;
    }
    elseif (preg_match('/^Sent from my iPhone/i', $line)) {
      break;
    }
    else if (preg_matcH('/^Sent from my HTC/i', $line)) {
      break;
    }
    elseif ($line == "_________________________________________________________________") {
      break; //original message quote
    }
    elseif (preg_match("/^-*(.*)Original Message(.*)-*/i", $line)) {
      break; //check for date wrote string  
    }
    elseif (preg_match("/^On(.*)wrote:(.*)/i", $line)) {
      break; //check for From Name email section  
    }
    elseif(preg_match("/^>(.*)/i", $line)) {
      break; //check for date wrote string with dashes  
    }
    elseif(preg_match("/^---(.*)On(.*)wrote:(.*)/i", $line)) {
      break;
    }
    else {
      $message .= "$line\n";
    }
  }
  return trim($message);
}