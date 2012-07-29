<?php

class Html_Test extends Unit_Test_Case {
  
  /**
   * Configure sitewide settings for testing purposes.
   */
  function setup() {
    Kohana::config_set('core.site_protocol', 'http');
    Kohana::config_set('core.site_domain', 'example.com');
  }

  /**
   * html::anchor()
   */
  function html__anchor_test() {
    $tests[] = array(
      'uri' => 'test',
      'title' => 'This is a test',
      'result' => '<a href="http://example.com/test">This is a test</a>',
    );
    
    foreach ($tests as $test) {
      $this->assert_same($test['result'], html::anchor($test['uri'], $test['title']));
    }
  }
  
  /**
   * html::primary_anchor()
   */
  function html__primary_anchor_test() {
    
  }
  
  /**
   * html::thickbox_anchor()
   */
  function html__thickbox_anchor_test() {
    $tests[] = array(
      'uri' => 'test/omg/test-again',
      'title' => 'Test Driven Development',
    );
    $tests[] = array(
      'uri' => 'test/omg/test-again?modal=false',
      'title' => 'Test Driven Development',
    );
    $tests[] = array(
      'uri' => 'testuri?tb=true&param=1',
      'title' => 'test',
    );
    
    foreach ($tests as $test) {
      $output = html::thickbox_anchor($test['uri'], $test['title']);
      $this->assert_pattern($output, '/(modal\=false)/i');
      $this->assert_equal(1, substr_count($output, '?'));
    }
  }
   
  
}