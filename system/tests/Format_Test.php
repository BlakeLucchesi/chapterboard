<?php

class Format_Test extends Unit_Test_Case {
  
  
  /**
   * Test format::plural().
   */
  function format__plural_test() {
    
    $tests[] = array(
      'count' => 0,
      'singular' => '@count person will be going to the fair',
      'plural' => '@count people will be going to the fair',
      'output' => '0 people will be going to the fair'
    );

    $tests[] = array(
      'count' => 1,
      'singular' => '@count person will be going to the fair #variable',
      'plural' => '@count people will be going #variable to the fair',
      'output' => '1 person will be going to the fair value',
      'args' => array('#variable' => 'value')
    );
    
    $tests[] = array(
      'count' => 10,
      'singular' => '@count person will be going to the fair',
      'plural' => '@count people will be going to the fair with @count friends',
      'output' => '10 people will be going to the fair with 10 friends'
    );
      
    foreach ($tests as $test) {
      $type = $test === 1 ? 'singular' : 'plural';
      $this->assert_equal($test['output'], format::plural($test['count'], $test['singular'], $test['plural'], $test['args']));
    } 
    
  }
}