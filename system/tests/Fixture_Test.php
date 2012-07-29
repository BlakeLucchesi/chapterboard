<?php

class Fixture_Test extends Unit_Test_Case {
  
  public function fixture_test() {
    $simple = array('key' => 'value');
    
    $element1 = array(
      'keyedarray' => array(
        '1' => 'item1',
        '2' => 'item2',
        '3' => 'item3',
      ),
      'unkeyedarray' => array('blake', 'alex', 'edison', 'leonidas'),
      'key' => 'value',
      'numeric' => 10234
    );
    
    
    $this->assert_equal($simple, fixture::load('test.simple'));
    $this->assert_equal($element1, fixture::load('test.element1'));
  }

  
}