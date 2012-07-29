<?php defined('SYSPATH') or die('No direct script access.');

class Text_Test extends Unit_Test_Case {
  
  public function mask_test() {
    $values = array(
      array(
        'input' => '12345678',
        'expected' => '*****678',
        'count' => 3
      ),
      array(
        'input' => '123234234',
        'expected' => '****34234',
        'count' => 5
      ),
    );
    
    foreach ($values as $value) {
      $this->assert_equal($value['expected'], text::mask($value['input'], $value[count]));
    }
  }
  
}