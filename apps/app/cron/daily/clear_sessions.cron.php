<?php defined('SYSPATH') or die('No direct script access.');

// Amount of time sessions are kept in the db for. (in seconds).
$cutoff = 60 * 60 * 24 * 30;  // 30 days 

$this->db = new Database;
$query = $this->db->query("DELETE FROM sessions WHERE last_activity < ?", array(time() - $cutoff));
log::system('cron', sprintf('Old sessions have been cleared. (%s)', number_format($query->count())));
$this->db->query("OPTIMIZE TABLE sessions");