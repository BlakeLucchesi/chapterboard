<?php defined('SYSPATH') or die('No direct script access.');

function cron_send_notification($charges) {
  $count = 0;
  foreach ($charges as $charge) {
    $charge->notify_members();
    $count++;
  }
  return $count;
}

// 2 day reminder.
$charges = ORM::factory('finance_charge')
  ->where('due', date::to_db('+2 days', 'Y-m-d'))
  ->find_all();

$sent = cron_send_notification($charges);
log::system('notice', sprintf('Finance charge reminders have been sent. (2 days out) [%s charges]', $sent));

// Day of reminder.
$charges = ORM::factory('finance_charge')
  ->where('due', date::to_db('now', 'Y-m-d'))
  ->find_all();
  
$sent = cron_send_notification($charges);
log::system('notice', sprintf('Finance charge reminders have been sent. (day of due date) [%s charges]', $sent));

// 1 day late reminder.
$charges = ORM::factory('finance_charge')
  ->where('due', date::to_db('-1 day', 'Y-m-d'))
  ->find_all();

$sent = cron_send_notification($charges);
log::system('notice', sprintf('Finance charge reminders have been sent. (1 day after due) [%s charges]', $sent));