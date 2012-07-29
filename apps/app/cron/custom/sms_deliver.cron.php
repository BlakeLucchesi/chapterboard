<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @file
 *
 * This cron task runs every minute.  Each time we grab a single row from the sms table
 * and parse the message to determine which users will receive the message.  We send a 
 * series of emails for each of the outgoing text messages and then record
 * the total number of messages sent in the sms row and update its status and send_count
 * to show that the message has been processed.
 *
 */


if ($sms = ORM::factory('sms')->get_from_queue()) {
  $sms->parse_groups();
  $message = sprintf('%s -- %s', $sms->message, $sms->user->name());
  $from = array(sprintf('sms+%s@chapterboard.com', $sms->sent_from), 'ChapterBoard');
  foreach ($sms->users() as $user) {
    if (sms::send_via_email($user, $message, $from)) {
      $sms_log = ORM::factory('sms_log')->record($sms->id, $user->id, $user->phone());
      unset($sms_log);
      $sms->send_count++;
    }
    if ($sms->send_count % 5) {
      sleep(1);
    }
  }
  $sms->delivered();
  // log::system('sms_sent', sprintf('Automated sms delivery completed.  Sent %d messages.', $sms->send_count), 'notice');
}