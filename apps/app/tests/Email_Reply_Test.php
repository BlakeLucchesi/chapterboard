<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Disable this test unless messing with the email reply cron.
 * It will attempt to download and mark emails as read when run.
 *
 */
class Email_Reply_Test extends Unit_Test_Case {
  
  function setup() {
    include Kohana::find_file('cron', 'custom/email_replies.cron');
  }
  
  function email_reply_parsing_test() {
    $inputs = glob(APPPATH.'/tests/Email_Reply_Inputs/*');
    $outputs = glob(APPPATH.'/tests/Email_Reply_Outputs/*');
  
    foreach ($inputs as $key => $file) {
      $input = file_get_contents($file);
      $this->assert_equal(_parse_body($input), file_get_contents($outputs[$key]));
    }
  }
  
}