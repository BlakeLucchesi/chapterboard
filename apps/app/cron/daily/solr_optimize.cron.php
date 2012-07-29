<?php defined('SYSPATH') or die('No direct script access.');

try {
  $solr = solr::service();
  $solr->optimize();
  log::system('solr', 'Solr index optimized.');
}
catch (Exception $e) {
  log::system('solr', 'Solr server was not available for optimization.', 'error');
}