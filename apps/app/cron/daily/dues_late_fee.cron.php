<?php defined('SYSPATH') or die('No direct script access.');

$charges = ORM::factory('finance_charge')
  ->where('due', date::to_db('-1 day', 'Y-m-d')) // Fix date for real cron!!!
  ->where('late_fee >', 0)
  ->where('late_fee_type !=', '')
  ->where('late_fee_assessed', FALSE)
  ->find_all();

// Reset site_id for each charge so that we can pass validation
// to assign late fee charge to the same budget.
foreach ($charges as $charge) {
  Kohana::config_set('chapterboard.site_id', $charge->site_id);
  $charge->assess_late_fees();
}
Kohana::config_set('chapterboard.site_id', NULL);

log::system('notice', 'Automatic late fees have been assessed.');