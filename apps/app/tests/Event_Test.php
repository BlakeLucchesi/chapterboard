<?php defined('SYSPATH') or die('No direct script access.');

class Event_Test extends Unit_Test_Case {

  /**
   * Store existing config values for use in teardown().
   */
  public function setup() {
    $this->site_timezone = Kohana::config('locale.site_timezone');
    Kohana::config_set('locale.site_timezone', 'UTC');
  }

  public function teardown() {
    Kohana::config_set('locale.site_timezone', $this->site_timezone);
  }
    
  /**
   * Test date and time user input and validation.
   */
  public function event__date_time_test() {
    /**
     * Valid input data, make sure start and end match our expected values.
     */
    $tests['valid-start-with-time'] = array( // Valid start with time.
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12am',
        'end_time' => NULL,
        'end_day' => '12/11/2010',
      ),
      array(
        'start' => '2010-12-11 00:00:00',
        'end' => '2010-12-11 00:00:00',
      ),
    );
    $tests['same-day-start-end'] = array( // Valid same day start - end
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12am',
        'end_time' => '5pm',
        'end_day' => '12/11/2010',
      ),
      array(
        'start' => '2010-12-11 00:00:00',
        'end' => '2010-12-11 17:00:00'
      ),
    );
    $tests['multi-date-all-day'] = array( // Valid multi-day all day.
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12am',
        'end_time' => '5pm',
        'end_day' => '12/20/2010',
        'all_day' => TRUE,
      ),
      array(
        'start' => '2010-12-11 00:00:00',
        'end' => '2010-12-20 00:00:00',
        'all_day' => TRUE,
      ),
    );
    $tests['ignore-time-all-day'] = array( // Ignore time inputs when all_day is checked.
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12am',
        'end_time' => '5pm',
        'end_day' => '12/11/2010',
        'all_day' => TRUE,
      ),
      array(
        'start' => '2010-12-11 00:00:00',
        'end' => '2010-12-11 00:00:00',
        'all_day' => TRUE,
      ),
    );
    
    /**
     * Make sure we are catching errors properly with invalid input.
     */
    $tests['invalid-end-date'] = array( // Invalid end date.
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12am',
        'end_time' => '5pm',
        'end_day' => '12/10/2010',
      ),
      array('end' => 'end_before_start'),
    );
    $tests['invalid-start-time-end-date'] = array( // Invalid starttime and end date.
      array(
        'start_day' => '12/11/2010',
        'start_time' => '12',
        'end_time' => '5pm',
        'end_day' => '12/10/2010',
      ),
      array('start' => 'datetime', 'end' => 'end_before_start'),
    );
    $tests['invalid-start-datetime'] = array( // Invalid start datetime
      array(
        'start_day' => '12/11/2010',
        'start_time' => '4',
      ),
      array('start' => 'datetime', 'end' => 'datetime')
    );
  
    $default = array(
      'title' => 'Test Event',
      'location' => 'ChapterBoard Offices',
      'mappable' => FALSE,
      'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
      'calendar_id' => 2,
    );
    
    foreach ($tests as $key => $test) {
      $event = ORM::factory('event');
      $form_input = array_merge($default, $test[0]);  // Combine test data with defaults.
      $event->validate($form_input, TRUE);
      // Cleanup inserted rows, or assert validation errors.
      if ($event->loaded) {
        $this->assert_equal($test[1]['start'], $event->start, array("Testing $key. Assert Start", $test[0], $form_input->errors(), $event->as_array()));
        $this->assert_equal($test[1]['end'], $event->end, array("Testing $key. Assert End", $test[0], $form_input->errors(), $event->as_array()));
        $this->assert_equal($test[1]['all_day'], $event->all_day, "Testing $key. Assert All Day.");
        $event->delete();
      }
      else {
        $this->assert_equal($test[1], $form_input->errors(), array($test[0], $form_input->errors(), $event->as_array()));
      }
    }
  }
  
  /**
   * Tests to make sure that event repeating is working properly.
   */
  public function event__date_repeat_test() {
  
    // Invalid until_occurrences.
    $tests[] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'occurrences',
        'until_occurrences' => -2,
        'until_date' => '',
      ),
      array('until_occurrences' => 'invalid')
    );
    $tests[] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'occurrences',
        'until_occurrences' => 200,
        'until_date' => '',
      ),
      array('until_occurrences' => 'invalid')
    );
    $tests[] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'occurrences',
        'until_occurrences' => 'sd',
        'until_date' => '',
      ),
      array('until_occurrences' => 'invalid')
    );
    
    // Invalid until_date.
    $tests[] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'date',
        'until_occurrences' => '',
        'until_date' => '23',
      ),
      array('until_date' => 'repeat_end_date')
    );
    $tests[] = array(
      array(
        'repeats' => TRUE,
        'period' => 'daily',
        'until' => 'date',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
      ),
      array('until_date' => 'repeat_end_date'),
    );
    
    // Valid until_occurrences events.
    $tests['monthly-day-of-week-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
        'end_day' => '12/12/2010'
      ),
      array(
        'starts' => array('2011-01-08 12:00:00', '2011-02-12 12:00:00', '2011-03-12 12:00:00'),
        'ends' => array('2011-01-09 17:00:00', '2011-02-13 17:00:00', '2011-03-13 17:00:00')
      ),
    );
    $tests['monthly-day-of-week-2-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'occurrences',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
        'start_day' => '12/01/2010',
        'end_day' => '12/05/2010'
      ),
      array(
        'starts' => array('2011-01-05 12:00:00', '2011-02-02 12:00:00', '2011-03-02 12:00:00', '2011-04-06 12:00:00'),
        'ends' => array('2011-01-09 17:00:00', '2011-02-06 17:00:00', '2011-03-06 17:00:00', '2011-04-10 17:00:00')
      ),
    );
    $tests['monthly-day-of-month-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_month',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array('2011-01-11 12:00:00', '2011-02-11 12:00:00', '2011-03-11 12:00:00'),
        'ends' => array('2011-01-11 17:00:00', '2011-02-11 17:00:00', '2011-03-11 17:00:00')
      ),
    );
    $tests['yearly-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'yearly',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array('2011-12-11 12:00:00', '2012-12-11 12:00:00', '2013-12-11 12:00:00', '2014-12-11 12:00:00'),
        'ends' => array('2011-12-11 17:00:00', '2012-12-11 17:00:00', '2013-12-11 17:00:00', '2014-12-11 17:00:00')
      ),
    );
    $tests['weekly-occurrences-5'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
        'end_day' => '12/12/2010',
        'end_time' => '12pm',
      ),
      array(
        'starts' => array('2010-12-18 12:00:00', '2010-12-25 12:00:00', '2011-01-01 12:00:00', '2011-01-08 12:00:00'),
        'ends' => array('2010-12-19 12:00:00', '2010-12-26 12:00:00', '2011-01-02 12:00:00', '2011-01-09 12:00:00')
      ),
    );
    $tests['daily-occurrences-4'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'daily',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array('2010-12-12 12:00:00', '2010-12-13 12:00:00', '2010-12-14 12:00:00'),
        'ends' => array('2010-12-12 17:00:00', '2010-12-13 17:00:00', '2010-12-14 17:00:00')
      ),
    );
    
    // Valid until_date events.
    $tests['monthly-day-of-week-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 10, // Invalid on purpose, test that this isn't used.
        'until_date' => '04/01/2011',
        'end_day' => '12/12/2010'
      ),
      array(
        'starts' => array('2011-01-08 12:00:00', '2011-02-12 12:00:00', '2011-03-12 12:00:00'),
        'ends' => array( '2011-01-09 17:00:00', '2011-02-13 17:00:00', '2011-03-13 17:00:00' )
      ),
    );
    $tests['daily-3-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'daily',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '12/04/2010',
        'start_day' => '12/01/2010',
        'end_day' => '12/01/2010'
      ),
      array(
        'starts' => array('2010-12-02 12:00:00', '2010-12-03 12:00:00', '2010-12-04 12:00:00'),
        'ends' => array('2010-12-02 17:00:00', '2010-12-03 17:00:00', '2010-12-04 17:00:00'),
      ),
    );
    $tests['weekly-3-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '12/25/2010',
        'start_day' => '12/01/2010',
        'end_day' => '12/01/2010'
      ),
      array(
        'starts' => array('2010-12-08 12:00:00', '2010-12-15 12:00:00', '2010-12-22 12:00:00'),
        'ends' => array('2010-12-08 17:00:00', '2010-12-15 17:00:00', '2010-12-22 17:00:00'),
      ),
    );
    $tests['weekly-no-repeat-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '12/04/2010',
        'start_day' => '12/01/2010',
        'end_day' => '12/01/2010'
      ),
      array(
        'starts' => array(),
        'ends' => array(),
      ),
    );
    $tests['monthly-no-repeat-day-of-week-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '12/30/2010',
        'start_day' => '12/04/2010',
        'end_day' => '12/04/2010'
      ),
      array(
        'starts' => array(),
        'ends' => array(),
      ),
    );
    $tests['monthly-no-repeat-day-of-week-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 5, // Invalid on purpose.
        'until_date' => '01/01/2011',
        'start_day' => '12/04/2010',
        'end_day' => '12/04/2010'
      ),
      array(
        'starts' => array('2011-01-01 12:00:00'),
        'ends' => array('2011-01-01 17:00:00'),
      ),
    );
    $tests['monthly-3-day-of-week-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '04/1/2011',
        'start_day' => '12/02/2010',
        'end_day' => '12/02/2010'
      ),
      array(
        'starts' => array('2011-01-06 12:00:00', '2011-02-03 12:00:00', '2011-03-03 12:00:00'),
        'ends' => array('2011-01-06 17:00:00', '2011-02-03 17:00:00', '2011-03-03 17:00:00'),
      ),
    );
    $tests['monthly-5-day-of-week-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '06/30/2011',
        'start_day' => '12/04/2010',
        'end_day' => '12/04/2010'
      ),
      array(
        'starts' => array('2011-01-01 12:00:00', '2011-02-05 12:00:00', '2011-03-05 12:00:00', '2011-04-02 12:00:00', '2011-05-07 12:00:00', '2011-06-04 12:00:00'),
        'ends' => array('2011-01-01 17:00:00', '2011-02-05 17:00:00', '2011-03-05 17:00:00', '2011-04-02 17:00:00', '2011-05-07 17:00:00', '2011-06-04 17:00:00'),
      ),
    );
    
    $tests['monthly-3-day-of-month-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_month',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '04/1/2011',
        'start_day' => '12/02/2010',
        'end_day' => '12/02/2010'
      ),
      array(
        'starts' => array('2011-01-02 12:00:00', '2011-02-02 12:00:00', '2011-03-02 12:00:00'),
        'ends' => array('2011-01-02 17:00:00', '2011-02-02 17:00:00', '2011-03-02 17:00:00'),
      ),
    );
    $tests['monthly-5-day-of-month-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_month',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '06/30/2011',
        'start_day' => '12/02/2010',
        'end_day' => '12/02/2010'
      ),
      array(
        'starts' => array('2011-01-02 12:00:00', '2011-02-02 12:00:00', '2011-03-02 12:00:00', '2011-04-02 12:00:00', '2011-05-02 12:00:00', '2011-06-02 12:00:00'),
        'ends' => array('2011-01-02 17:00:00', '2011-02-02 17:00:00', '2011-03-02 17:00:00', '2011-04-02 17:00:00', '2011-05-02 17:00:00', '2011-06-02 17:00:00'),
      ),
    );
    $tests['yearly-10-repeat-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'yearly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '12/01/2020',
        'start_day' => '12/01/2010',
        'end_day' => '12/01/2010'
      ),
      array(
        'starts' => array('2011-12-01 12:00:00', '2012-12-01 12:00:00', '2013-12-01 12:00:00', '2014-12-01 12:00:00', '2015-12-01 12:00:00', '2016-12-01 12:00:00', '2017-12-01 12:00:00', '2018-12-01 12:00:00', '2019-12-01 12:00:00', '2020-12-01 12:00:00'),
        'ends' => array('2011-12-01 17:00:00', '2012-12-01 17:00:00', '2013-12-01 17:00:00', '2014-12-01 17:00:00', '2015-12-01 17:00:00', '2016-12-01 17:00:00', '2017-12-01 17:00:00', '2018-12-01 17:00:00', '2019-12-01 17:00:00', '2020-12-01 17:00:00'),
      ),
    );
    $tests['yearly-no-repeat-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'yearly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '11/30/2011',
        'start_day' => '12/01/2010',
        'end_day' => '12/01/2010'
      ),
      array(
        'starts' => array(),
        'ends' => array(),
      ),
    );
    $tests['monthly-day-of-week-2-until_date'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_week',
        'until' => 'date',
        'until_occurrences' => 15, // Invalid on purpose.
        'until_date' => '04/06/2011',
        'start_day' => '12/01/2010',
        'end_day' => '12/05/2010'
      ),
      array(
        'starts' => array('2011-01-05 12:00:00', '2011-02-02 12:00:00', '2011-03-02 12:00:00', '2011-04-06 12:00:00'),
        'ends' => array('2011-01-09 17:00:00', '2011-02-06 17:00:00', '2011-03-06 17:00:00', '2011-04-10 17:00:00')
      ),
    );
    $tests['monthly-day-of-month-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'monthly',
        'period_option' => 'day_of_month',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array(
          '2011-01-11 12:00:00',
          '2011-02-11 12:00:00',
          '2011-03-11 12:00:00',
        ),
        'ends' => array(
          '2011-01-11 17:00:00',
          '2011-02-11 17:00:00',
          '2011-03-11 17:00:00',
        )
      ),
    );
    $tests['yearly-occurrences'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'yearly',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array(
          '2011-12-11 12:00:00',
          '2012-12-11 12:00:00',
          '2013-12-11 12:00:00',
          '2014-12-11 12:00:00'
        ),
        'ends' => array(
          '2011-12-11 17:00:00',
          '2012-12-11 17:00:00',
          '2013-12-11 17:00:00',
          '2014-12-11 17:00:00'
        )
      ),
    );
    $tests['weekly-occurrences-5'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 5,
        'until_date' => '12/11/2010',
        'end_day' => '12/12/2010',
        'end_time' => '12pm',
      ),
      array(
        'starts' => array(
          '2010-12-18 12:00:00',
          '2010-12-25 12:00:00',
          '2011-01-01 12:00:00',
          '2011-01-08 12:00:00',
        ),
        'ends' => array(
          '2010-12-19 12:00:00',
          '2010-12-26 12:00:00',
          '2011-01-02 12:00:00',
          '2011-01-09 12:00:00',
        )
      ),
    );
    $tests['daily-occurrences-4'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'daily',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
      ),
      array(
        'starts' => array('2010-12-12 12:00:00', '2010-12-13 12:00:00', '2010-12-14 12:00:00'),
        'ends' => array('2010-12-12 17:00:00', '2010-12-13 17:00:00', '2010-12-14 17:00:00')
      ),
    );
    $tests['daily-occurrences-no-end-time'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'daily',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
        'end_time' => '',
      ),
      array(
        'starts' => array('2010-12-12 12:00:00', '2010-12-13 12:00:00', '2010-12-14 12:00:00'),
        'ends' => array('2010-12-12 12:00:00', '2010-12-13 12:00:00', '2010-12-14 12:00:00')
      ),
    );
    $tests['weekly-occurrences-no-end-time'] = array(
      array(
        'repeats' => TRUE,
        'period' => 'weekly',
        'period_option' => '',
        'until' => 'occurrences',
        'until_occurrences' => 4,
        'until_date' => '12/11/2010',
        'end_time' => '',
      ),
      array(
        'starts' => array('2010-12-18 12:00:00', '2010-12-25 12:00:00', '2011-01-01 12:00:00'),
        'ends' => array('2010-12-18 12:00:00', '2010-12-25 12:00:00', '2011-01-01 12:00:00')
      ),
    );
  
    $default = array(
      'title' => 'Test Event',
      'location' => 'ChapterBoard Offices',
      'mappable' => FALSE,
      'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
      'calendar_id' => 2,
      'start_day' => '12/11/2010',
      'start_time' => '12pm',
      'end_time' => '5pm',
      'end_day' => '12/11/2010',
    );
    
    foreach ($tests as $key => $test) {
      $event = ORM::factory('event');
      $form_input = array_merge($default, $test[0]);  // Combine test data with defaults.
      $event->validate($form_input, TRUE);
      // Cleanup inserted rows, or assert validation errors.
      if ($event->loaded) {
        $this->assert_true($event->is_parent(), "Test: $key. Testing is_parent() on parent event.");
        $this->assert_equal(count($test[1]['starts']), $event->children->count(), "Test: $key. Testing child count.");
        foreach ($event->children as $child) {
          $this->assert_equal($test[1]['starts'][$child->child_n], $child->start, "Test: $key. Testing child start dates {$child->child_n}.");
          $this->assert_equal($test[1]['ends'][$child->child_n], $child->end, "Test: $key. Testing child end date {$child->child_n}.");
          $this->assert_false($child->is_parent(), "Test: $key. Testing is_parent() on child event.");
          $child->delete();
        }
        $event->delete();
      }
      else {
        $this->assert_equal($test[1], $form_input->errors(), array($test[0], $form_input->errors(), $event->as_array()));
      }
    }
  }
  
  public function event__date_update_repeated_test() {
    $default = array(
      'title' => 'Test Event',
      'location' => 'ChapterBoard Offices',
      'mappable' => FALSE,
      'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
      'calendar_id' => 2,
    );
    $tests['update-new-repeat-more'] = array(
      'original' => array(
        'start_day' => '12/11/2010',
        'start_time' => '12pm',
        'end_time' => '5pm',
        'end_day' => '12/11/2010',
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'date',
        'until_date' => '12/20/2010',
      ),
      'updated' => array(
        'title' => 'Test Event 2',
        'location' => 'ChapterBoard Location',
        'mappable' => FALSE,
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'calendar_id' => 2,
        'start_day' => '01/11/2011',
        'start_time' => '2pm',
        'end_time' => '9pm',
        'end_day' => '01/11/2011',
        'period' => 'daily',
        'until' => 'occurrences',
        'until_occurrences' => 4,
      ),
      'starts' => array('2011-01-12 14:00:00', '2011-01-13 14:00:00', '2011-01-14 14:00:00'),
      'ends' => array('2011-01-12 21:00:00', '2011-01-13 21:00:00', '2011-01-14 21:00:00'),
    );
    
    $tests['update-new-repeat-less-values'] = array(
      'original' => array(
        'start_day' => '12/11/2010',
        'start_time' => '12pm',
        'end_time' => '5pm',
        'end_day' => '12/11/2010',
        'repeats' => TRUE,
        'period' => 'weekly',
        'until' => 'date',
        'until_date' => '12/20/2011',
      ),
      'updated' => array(
        'title' => 'Test Event 2',
        'location' => 'ChapterBoard Location',
        'mappable' => FALSE,
        'body' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'calendar_id' => 2,
        'start_day' => '01/11/2011',
        'start_time' => '2pm',
        'end_time' => '9pm',
        'end_day' => '01/11/2011',
        'period' => 'daily',
        'until' => 'occurrences',
        'until_occurrences' => 4,
      ),
      'starts' => array('2011-01-12 14:00:00', '2011-01-13 14:00:00', '2011-01-14 14:00:00'),
      'ends' => array('2011-01-12 21:00:00', '2011-01-13 21:00:00', '2011-01-14 21:00:00'),
    );
  
    foreach ($tests as $key => $test) {
      $event = ORM::factory('event');
      $form_input = array_merge($default, $test['original']);  // Combine test data with defaults.
      $event->validate($form_input, TRUE);
      
      $form_input = array_merge($event->as_array(), $test['updated']);
      $event->validate($form_input, TRUE);
      $event->update_repeat_events();
      
      // Cleanup inserted rows, or assert validation errors.
      if ($event->loaded) {
        $event->reload();
        $this->assert_true($event->is_parent(), "Test: $key. Testing is_parent() on parent event.");
        $this->assert_equal(count($test['starts']), $event->children->count(), "Test: $key. Testing child count.");
        foreach ($event->children as $child) {
          $this->assert_equal($test['updated']['title'], $child->title, "Test: $key. Testing that child title was updated. {$child->child_n}");
          $this->assert_equal($test['starts'][$child->child_n], $child->start, "Test: $key. Testing child start dates {$child->child_n}.");
          $this->assert_equal($test['ends'][$child->child_n], $child->end, "Test: $key. Testing child end date {$child->child_n}.");
          $this->assert_false($child->is_parent(), "Test: $key. Testing is_parent() on child event.");
          $child->delete();
        }
        $event->delete();
      }
    }
  }
}