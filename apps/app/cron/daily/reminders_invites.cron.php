<?php defined('SYSPATH') or die('No direct script access.');

$invites = ORM::factory('invite')->find_by_needs_reminder();

foreach ($invites as $invite) {
  $invite->resend();
  $invite->reminder_sent = 1;
  $invite->save();
  $count++;
}

log::system('invite', sprintf('Member invite reminders have been sent. (%d)', $count));