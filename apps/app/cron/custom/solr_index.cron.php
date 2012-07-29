<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Use this cron script to import all chapterboard forum topics into 
 * the solr search index.
 */
kohana::config_set('chapterboard.user_id', 1);
$start = 0;
$count = $this->db->query("SELECT COUNT(*) as count FROM topics WHERE status = 1")->current()->count;
print 'Indexing '. $count ." documents\n";
while ($start < $count) {
  $topics = ORM::factory('topic')->where('topics.status', 1)->find_all(10, $start);
  foreach ($topics as $topic) {
    $topic->index(FALSE);
    $start++;
    print 'Indexed: '. $topic->title. "\n";
  }
  print ob_get_contents();
  ob_flush();
}

// Commit the indexing.
$solr = solr::service();
$solr->commit();