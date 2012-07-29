<?php defined('SYSPATH') or die('No direct script access.');

class Money_Test extends Unit_Test_Case {
  
  /**
   * Test the use of money::cleanse().
   */
  function money__cleanse_test() {
    $values = array(
      array('input' => '100.00', 'output' => '100.00'),
      array('input' => '10,00', 'output' => '1000'),
      array('input' => '15,2000', 'output' => '152000'),
      array('input' => '10*_00', 'output' => '1000'),
      array('input' => '$100,000.00', 'output' => '100000.00'),
      array('input' => '-$100.00', 'output' => '-100.00'),
      array('input' => '$-100.30', 'output' => '-100.30')
    );
    
    foreach ($values as $value) {
      $this->assert_equal($value['output'], money::cleanse($value['input']));
    }
  }
  
  /**
   * Test money::valid().
   */
  // function money__valid_test() {
  //   $values = array(
  //     array('input' => '$13200.00', 'output' => TRUE),
  //     array('input' => '$13,200.00', 'output' => TRUE),
  //     array('input' => '130', 'output' => TRUE),
  //     array('input' => '140.45', 'output' => TRUE),
  //     
  //     array('input' => '155.323', 'output' => FALSE),
  //     array('input' => '%100.00', 'output' => FALSE)
  //   );
  //   
  //   foreach ($values as $value) {
  //     var_dump($value);
  //     $this->assert_equal($value['output'], money::valid($value['input']));
  //   }
  // }
  
  
  /**
   * Test the use of money::display().
   */
  function money__display_test() {
    $values = array(
      array('input' => '100', 'output' => '$100.00'),
      array('input' => '10.56', 'output' => '$10.56'),
      array('input' => '1,000', 'output' => '$1,000.00'),
      array('input' => '1520.14', 'output' => '$1,520.14'),
      array('input' => '100,000', 'output' => '$100,000.00')
    );
    
    foreach ($values as $value) {
      $this->assert_equal($value['output'], money::display($value['input']));
    }
  }
  
  /**
   * Test the use of money::round().
   */
  function money__round_test() {
    
    $values = array(
      array('input' => '1520.1423', 'output' => 1520.14),     // Round down 4 to 2 decimal places
      array('input' => '100.123', 'output' => 100.12),        // Round down 3 to 2 decimal places
      array('input' => '10', 'output' => 10.00),              // Round out to 2 digits 
      array('input' => '100,000.999', 'output' => 100001.00), // Round up 3 to 2 decimlal places.
      array('input' => '1,000.1451', 'output' => 1000.15),    // Round up 4 to 2 decimal places.
    );
    
    foreach ($values as $value) {
      $this->assert_equal($value['output'], money::round($value['input']));
    }
  }
}