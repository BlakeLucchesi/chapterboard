<?php defined('SYSPATH') or die('No direct script access.');

$email = Kohana::config('app.sms_email');
$password = Kohana::config('app.sms_email_password');
$inbox = new Gmail($email, $password);
$emails = $inbox->getNewEmails();

foreach ($emails as $email) {
  $to = _parse_to($email['toaddress']);
  if ($to) {
    $receiver = ORM::factory('user')->find_by_phone($to);
    $body = _parse_body($email['body'], $email['fromaddress']);

    if ($receiver->loaded && $body) {
      $from = substr($email['fromaddress'], 0, strpos($email['fromaddress'], '@'));
      $sender = ORM::factory('user')->find_by_phone($from);

      // Some replies will not come from #'s but from carrier email addresses.
      if ($sender->loaded) {
        $message = sprintf('%s -- %s %s', $body, $sender->name(), format::phone($sender->profile->phone));
      }
      else {
        $message = sprintf('%s -- %s', $body, $email['fromaddress']);
      }
      $message .= " \n= DO NOT REPLY HERE =";

      sms::send_via_email($receiver, $message);
      log::system('sms', sprintf('Sending sms reply to %s', format::phone($sender->profile->phone)), 'notice', array('contents' => $message));
    }
    else {
      email::send($email['fromaddress'], array('noreply@chapterboard.com', 'ChapterBoard No Reply'), '', "There was an error sending your reply. Please send your message directly to the recipient's phone number.");
      log::system('sms', sprintf('Could not send sms reply. From: %s', $email['fromaddress']), 'notice', array('contents' => $email));
    }
  }
}

/**
 * Parse the body from the email.  
 *
 * We need to parse the message differently depending on the
 * service provider that the email comes from.
 */
function _parse_body($body, $from) {
  $provider = substr($from, strpos($from, '@') + 1);
  switch ($provider) {
    case 'txt.att.net':
      $body = substr($body, 0, strpos($body, '-----Original Message-----'));
      return trim($body);
    case 'tmomail.net':
      $body = substr($body, 0, strpos($body, '------------------'));
      return trim($body);
    case 'vtext.com':
    case 'messaging.sprintpcs.com':
      return $body;
  }
  return substr($body, 0, 140);
}


/**
 * Parse the phone number from the email to address.
 *
 * Format: sms+[10 digit phone number]@chapterboard.com
 *
 * @return integer 10 digit phone number.
 */
function _parse_to($address) {
  preg_match_all('/^sms\+([0-9]{10})@chapterboard.com/i', $address, $matches);
  if (strlen($matches[1][0]) == 10) {
    return $matches[1][0];
  }
  return FALSE;
}

/** Example Messages

[39] => Array
        (
            [subject] => Logan Bilby
            [fromaddress] => 6025585858@vtext.com
            [toaddress] => team@chapterboard.com
            [ccaddress] => 
            [date] => 28 Sep 2010 04:13:31 +0000
            [body] =>  Logan Bilby

        )


[38] => Array
    (
        [subject] => RE:
        [fromaddress] => 4802398314@txt.att.net
        [toaddress] => team@chapterboard.com
        [ccaddress] => 
        [date] => Mon, 27 Sep 2010 23:13:22 -0500
        [body] => Chris Fanger

  -----Original Message-----
  From: team@chapterboard.com
  Sent: Tue, 28 Sep 2010 04:14:16 +0000
  To: 4802398314@txt.att.net
 Subject: 

>setting up a new texting system. If you receive this message please send me a text saying your name -- Matt Newman (516) 639-6544 

--
==================================================================
This mobile text message is brought to you by AT&T

        )
*/