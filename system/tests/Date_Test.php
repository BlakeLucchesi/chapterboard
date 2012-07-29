<?php

class Date_Test extends Unit_Test_Case {
  
  public $format = 'M d Y H:i:s';
  
  /**
   * Tests the creation of a local based time and translating
   * into UTC format for storage.
   */
  function date_create_test() {
    $dates = array();
    $dates[] = array( // PDT
      'date' => 'Aug 01 2009 13:00:00',
      'zone' => 'America/Los_Angeles',
      'UTC' => 'Aug 01 2009 20:00:00',
    );
    
    $dates[] = array( // PST
      'date' => 'Dec 01 2009 12:00:00',
      'zone' => 'America/Los_Angeles',
      'UTC' => 'Dec 01 2009 20:00:00',
    );
    
    foreach ($dates as $date) {
      $test = date_create($date['date'], timezone_open($date['zone'])); // Create new date based on site timezone.
      date_timezone_set($test, timezone_open(Kohana::config('locale.timezone'))); // Translate date into UTC
      $this->assert_equal(date_format($test, $this->format), $date['UTC']); // Verify translation worked properly.
    }
  }
  
  /**
   * Tests the retrieval of a UTC date and then
   * converting to a local time zone for the user.
   */
  function date__display_test() {
    $dates[] = array(
      'date' => 'Aug 01 2009 13:00:00',
      'zone' => 'America/Los_Angeles',
      'UTC' => 'Aug 01 2009 20:00:00',
    );
    
    foreach ($dates as $date) {
      $test = date_create($date['UTC']); // Grab date from DB
      date_timezone_set($test, timezone_open($date['zone'])); // Translate date into site timezone
      $this->assert_equal(date_format($test, $this->format), $date['date']); // Confirm they are equivalent.
    }
  }
  
  function date__to_db_test() {
    $dates[] = array(
      'date' => '2009-08-01 13:00:00',
      'zone' => 'America/Los_Angeles',
      'UTC' => '2009-08-01 20:00:00',
    );

    foreach ($dates as $date) {
      date::timezone($date['zone']);
      $this->assert_equal($date['UTC'], date::to_db($date['date']));
    }
  }
  
  function date__modify_test() {
    $this->assert_equal('2011-10-15 00:00:00', date::modify('+1 year', '2010-10-15'));
    $this->assert_equal('2011-10-15 10:00:10', date::modify('+1 year', '2010-10-15 10:00:10'));
  }
  
  function date__day_difference_test() {
    $this->assert_equal(4, date::day_difference('2010-10-15', '2010-10-11'), 'Date difference.');
    $this->assert_equal(5, date::day_difference('2011-02-02', '2011-01-28 14:00:00'), 'Date with time and month difference.');
    $this->assert_equal(9, date::day_difference('2011-02-05 19:00:00', '2011-01-28 12:00:00'), 'Date and time with time and month difference.');
    $this->assert_equal(30, date::day_difference('2011-01-05', '2010-12-06'), 'Date with month and year difference.');
    $this->assert_equal(0, date::day_difference('2011-01-05 22:00:00', '2011-01-05 18:00:00', FALSE), 'Same date, no day difference.');
    $this->assert_equal(0, date::day_difference('2011-05-03', '2011-05-03'));
    $this->assert_equal(1, date::day_difference('2011-05-03', '2011-05-02'));
  }
  
}