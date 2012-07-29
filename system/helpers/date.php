<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Date helper class.
 *
 * $Id: date.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class date_Core {


  /**
   * Define the standard format for saving to mysql database.
   */
  public static $db_format = 'Y-m-d H:i:s';

  public static $timezone;
  
  /**
   * Sitewide date timezone
   */
  public static function timezone($timezone = NULL) {
    if ($timezone) {
      self::$timezone = $timezone;
    }
    if (self::$timezone) {
      return self::$timezone;
    }
    else if (Kohana::config('locale.site_timezone')) {
      return Kohana::config('locale.site_timezone');
    }
    return Kohana::config('locale.timezone');
  }
  
  /**
   * Array of Timezones for selection.
   */
  public static function timezones() {
    return Kohana::config('date.timezones');
  }

  /**
   * Date output display.
   *
   * @param datestamp $datetime
   * The date string to format.
   *
   * @param string $type
   * A predefined format used to display the date. If $type
   * is 'custom' then use $format to specify a php date format.
   *
   * @param string $format
   * A date output format based on the php date() format.
   */
  static function display($date, $type = 'short', $timezone = TRUE) {
    if ($date == '0000-00-00' || $date == '0000-00-00 00:00:00') {
      return NULL;
    }
    elseif (is_numeric($date)) {
      $date = strftime('%Y-%m-%d %H:%I:%S', $date);
    }
    if ($timezone) {
      $date = date_create($date, timezone_open(Kohana::config('locale.timezone'))); // Create the new date coming in UTC from DB.
      date_timezone_set($date, timezone_open(date::timezone()));  // Set the site timezone for display.      
    }
    else {
      $date = date_create($date);
    }
    
    switch ($type) {
      case 'short':
        $format = 'm/d/Y';
        break;
      case 'normal':
        $format = 'F jS, Y';
        break;
      case 'medium':
        $format = 'M jS, Y g:i:sa';
        break;
      case 'long':
        $format = 'F jS, Y g:i:sa';
        break;
      case 'calendar':
        $format = 'M jS, Y g:i a';
        break;
      case 'time':
        $format = 'g:ia';
        break;
      case 'daytime':
        $diff = date::timespan(date_format($date, 'U'), strtotime(date::to_db()));
        $format = ($diff['years'] > 0) ? 'M jS, Y' : 'M jS \a\t g:ia';
        break;
      default:
        $format = $type;
        break;
    }
    return date_format($date, $format);
  }

  /**
   * Return a current datetime format.
   */
  static function now($when = 'now', $format = NULL) {
    $format = $format ? $format : self::$db_format;
    $when = $when == NULL ? 'now' : $when;
    $date = date_create($when, timezone_open(date::timezone()));
    return date_format($date, $format);
  }
  
  /**
   * Translate date into format for db storage.
   */
  static function to_db($date = 'now', $format = NULL) {
    $format = $format ? $format : self::$db_format;
    $date = date_create($date, timezone_open(date::timezone()));
    date_timezone_set($date, timezone_open(Kohana::config('locale.timezone')));
    return date_format($date, $format);
  }
  
  /**
   * Translate date into the format for solr.
   */
  static function to_solr($date = 'now') {
    // 1995-12-31T23:59:59Z
    $format = $format ? $format : self::$db_format;
    $format = 'Y-m-d\TH:i:s\Z';
    $date = date_create($date, timezone_open(date::timezone()));
    date_timezone_set($date, timezone_open(Kohana::config('locale.timezone')));
    return date_format($date, $format);
  }
  
  /**
   * Input to Database conversion.
   */
  static function input_to_db($date) {
    $format = $format ? $format : self::$db_format;
    $date = date_create($date);
    return date_format($date, $format);
  }
  
  static function time($date, $short = FALSE) {
    if ($short)
      return substr(date::display($date, 'g:ia'), 0, -1);
    return date::display($date, 'g:ia');
  }
  
  static function year($date) {
    return date::display($date, 'Y');
  }
  
  static function month($date) {
    return date::display($date, 'm');
  }
  
  static function day($date) {
    return date::display($date, 'd'); 
  }
  
  static function day_of_year($date) {
    return date::display($date, 'z');
  }
  
  static function hour($date) {
    return date::display($date, 'H');
  }
  
  static function minute($date) {
    return date::display($date, 'i');
  }
  
  static function second($date) {
    return date::display($date, 's');
  }
  
  static function is_today($year, $month, $day) {
    $date = sprintf('%d-%d-%d', $year, $month, $day);
    $today = date('Y-n-d', time());
    if ($today == $date)
      return TRUE;
    return FALSE;
  }
  
  /**
   * Format the date to be displayed as time passed since a date.
   */
  static function ago($date, $string = 'ago') {
    if (is_null($date)) {
      return 'never';
    }
    $past = is_numeric($date) ? $date : strtotime($date);
    $diff = date::timespan($past, strtotime(date::to_db()));
    foreach ($diff as $unit => $value) {
      if ($value) {
        $unit = $value == 1 ? inflector::singular($unit) : $unit;
        return sprintf('%s %s %s', $value, $unit, $string);
      }
    }
    return sprintf('1 second %s', $string);
  }

	/**
	 * Converts a UNIX timestamp to DOS format.
	 *
	 * @param   integer  UNIX timestamp
	 * @return  integer
	 */
	public static function unix2dos($timestamp = FALSE)
	{
		$timestamp = ($timestamp === FALSE) ? getdate() : getdate($timestamp);

		if ($timestamp['year'] < 1980)
		{
			return (1 << 21 | 1 << 16);
		}

		$timestamp['year'] -= 1980;

		// What voodoo is this? I have no idea... Geert can explain it though,
		// and that's good enough for me.
		return ($timestamp['year']    << 25 | $timestamp['mon']     << 21 |
		        $timestamp['mday']    << 16 | $timestamp['hours']   << 11 |
		        $timestamp['minutes'] << 5  | $timestamp['seconds'] >> 1);
	}

	/**
	 * Converts a DOS timestamp to UNIX format.
	 *
	 * @param   integer  DOS timestamp
	 * @return  integer
	 */
	public static function dos2unix($timestamp = FALSE)
	{
		$sec  = 2 * ($timestamp & 0x1f);
		$min  = ($timestamp >>  5) & 0x3f;
		$hrs  = ($timestamp >> 11) & 0x1f;
		$day  = ($timestamp >> 16) & 0x1f;
		$mon  = ($timestamp >> 21) & 0x0f;
		$year = ($timestamp >> 25) & 0x7f;

		return mktime($hrs, $min, $sec, $mon, $day, $year + 1980);
	}

	/**
	 * Returns the offset (in seconds) between two time zones.
	 * @see     http://php.net/timezones
	 *
	 * @param   string          timezone that to find the offset of
	 * @param   string|boolean  timezone used as the baseline
	 * @return  integer
	 */
	public static function offset($remote, $local = TRUE)
	{
		static $offsets;

		// Default values
		$remote = (string) $remote;
		$local  = ($local === TRUE) ? date_default_timezone_get() : (string) $local;

		// Cache key name
		$cache = $remote.$local;

		if (empty($offsets[$cache]))
		{
			// Create timezone objects
			$remote = new DateTimeZone($remote);
			$local  = new DateTimeZone($local);

			// Create date objects from timezones
			$time_there = new DateTime('now', $remote);
			$time_here  = new DateTime('now', $local);

			// Find the offset
			$offsets[$cache] = $remote->getOffset($time_there) - $local->getOffset($time_here);
		}

		return $offsets[$cache];
	}

	/**
	 * Number of seconds in a minute, incrementing by a step.
	 *
	 * @param   integer  amount to increment each step by, 1 to 30
	 * @param   integer  start value
	 * @param   integer  end value
	 * @return  array    A mirrored (foo => foo) array from 1-60.
	 */
	public static function seconds($step = 1, $start = 0, $end = 60)
	{
		// Always integer
		$step = (int) $step;

		$seconds = array();

		for ($i = $start; $i < $end; $i += $step)
		{
			$seconds[$i] = ($i < 10) ? '0'.$i : $i;
		}

		return $seconds;
	}

	/**
	 * Number of minutes in an hour, incrementing by a step.
	 *
	 * @param   integer  amount to increment each step by, 1 to 30
	 * @return  array    A mirrored (foo => foo) array from 1-60.
	 */
	public static function minutes($step = 5)
	{
		// Because there are the same number of minutes as seconds in this set,
		// we choose to re-use seconds(), rather than creating an entirely new
		// function. Shhhh, it's cheating! ;) There are several more of these
		// in the following methods.
		return date::seconds($step);
	}

	/**
	 * Number of hours in a day.
	 *
	 * @param   integer  amount to increment each step by
	 * @param   boolean  use 24-hour time
	 * @param   integer  the hour to start at
	 * @return  array    A mirrored (foo => foo) array from start-12 or start-23.
	 */
	public static function hours($step = 1, $long = FALSE, $start = NULL)
	{
		// Default values
		$step = (int) $step;
		$long = (bool) $long;
		$hours = array();

		// Set the default start if none was specified.
		if ($start === NULL)
		{
			$start = ($long === FALSE) ? 1 : 0;
		}

		$hours = array();

		// 24-hour time has 24 hours, instead of 12
		$size = ($long === TRUE) ? 23 : 12;

		for ($i = $start; $i <= $size; $i += $step)
		{
			$hours[$i] = $i;
		}

		return $hours;
	}

	/**
	 * Returns AM or PM, based on a given hour.
	 *
	 * @param   integer  number of the hour
	 * @return  string
	 */
	public static function ampm($hour)
	{
		// Always integer
		$hour = (int) $hour;

		return ($hour > 11) ? 'PM' : 'AM';
	}

	/**
	 * Adjusts a non-24-hour number into a 24-hour number.
	 *
	 * @param   integer  hour to adjust
	 * @param   string   AM or PM
	 * @return  string
	 */
	public static function adjust($hour, $ampm)
	{
		$hour = (int) $hour;
		$ampm = strtolower($ampm);

		switch ($ampm)
		{
			case 'am':
				if ($hour == 12)
					$hour = 0;
			break;
			case 'pm':
				if ($hour < 12)
					$hour += 12;
			break;
		}

		return sprintf('%02s', $hour);
	}

	/**
	 * Number of days in month.
	 *
	 * @param   integer  number of month
	 * @param   integer  number of year to check month, defaults to the current year
	 * @return  array    A mirrored (foo => foo) array of the days.
	 */
	public static function days($month, $year = FALSE)
	{
		static $months;

		// Always integers
		$month = (int) $month;
		$year  = (int) $year;

		// Use the current year by default
		$year  = ($year == FALSE) ? date('Y') : $year;

		// We use caching for months, because time functions are used
		if (empty($months[$year][$month]))
		{
			$months[$year][$month] = array();

			// Use date to find the number of days in the given month
			$total = date('t', mktime(1, 0, 0, $month, 1, $year)) + 1;

			for ($i = 1; $i < $total; $i++)
			{
				$months[$year][$month][$i] = $i;
			}
		}

		return $months[$year][$month];
	}

	/**
	 * Number of months in a year
	 *
	 * @return  array  A mirrored (foo => foo) array from 1-12.
	 */
	public static function months()
	{
		return date::hours();
	}

	/**
	 * Returns an array of years between a starting and ending year.
	 * Uses the current year +/- 5 as the max/min.
	 *
	 * @param   integer  starting year
	 * @param   integer  ending year
	 * @return  array
	 */
	public static function years($start = FALSE, $end = FALSE)
	{
		// Default values
		$start = ($start === FALSE) ? date('Y') - 5 : (int) $start;
		$end   = ($end   === FALSE) ? date('Y') + 5 : (int) $end;

		$years = array();

		// Add one, so that "less than" works
		$end += 1;

		for ($i = $start; $i < $end; $i++)
		{
			$years[$i] = $i;
		}

		return $years;
	}

	/**
	 * Returns time difference between two timestamps, in human readable format.
	 *
	 * @param   integer       timestamp
	 * @param   integer       timestamp, defaults to the current time
	 * @param   string        formatting string
	 * @return  string|array
	 */
	public static function timespan($time1, $time2 = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		// Array with the output formats
		$output = preg_split('/[^a-z]+/', strtolower((string) $output));

		// Invalid output
		if (empty($output))
			return FALSE;

		// Make the output values into keys
		extract(array_flip($output), EXTR_SKIP);

		// Default values
		$time1  = max(0, (int) $time1);
		$time2  = empty($time2) ? time() : max(0, (int) $time2);

		// Calculate timespan (seconds)
		$timespan = abs($time1 - $time2);

		// All values found using Google Calculator.
		// Years and months do not match the formula exactly, due to leap years.

		// Years ago, 60 * 60 * 24 * 365
		isset($years) and $timespan -= 31556926 * ($years = (int) floor($timespan / 31556926));

		// Months ago, 60 * 60 * 24 * 30
		isset($months) and $timespan -= 2629744 * ($months = (int) floor($timespan / 2629743.83));

		// Weeks ago, 60 * 60 * 24 * 7
		isset($weeks) and $timespan -= 604800 * ($weeks = (int) floor($timespan / 604800));

		// Days ago, 60 * 60 * 24
		isset($days) and $timespan -= 86400 * ($days = (int) floor($timespan / 86400));

		// Hours ago, 60 * 60
		isset($hours) and $timespan -= 3600 * ($hours = (int) floor($timespan / 3600));

		// Minutes ago, 60
		isset($minutes) and $timespan -= 60 * ($minutes = (int) floor($timespan / 60));

		// Seconds ago, 1
		isset($seconds) and $seconds = $timespan;

		// Remove the variables that cannot be accessed
		unset($timespan, $time1, $time2);

		// Deny access to these variables
		$deny = array_flip(array('deny', 'key', 'difference', 'output'));

		// Return the difference
		$difference = array();
		foreach ($output as $key)
		{
			if (isset($$key) AND ! isset($deny[$key]))
			{
				// Add requested key to the output
				$difference[$key] = $$key;
			}
		}

		// Invalid output formats string
		if (empty($difference))
			return FALSE;

		// If only one output format was asked, don't put it in an array
		if (count($difference) === 1)
			return current($difference);

		// Return array
		return $difference;
	}

	/**
	 * Returns time difference between two timestamps, in the format:
	 * N year, N months, N weeks, N days, N hours, N minutes, and N seconds ago
	 *
	 * @param   integer       timestamp
	 * @param   integer       timestamp, defaults to the current time
	 * @param   string        formatting string
	 * @return  string
	 */
	public static function timespan_string($time1, $time2 = NULL, $output = 'years,months,weeks,days,hours,minutes,seconds')
	{
		if ($difference = date::timespan($time1, $time2, $output) AND is_array($difference))
		{
			// Determine the key of the last item in the array
			$last = end($difference);
			$last = key($difference);

			$span = array();
			foreach ($difference as $name => $amount)
			{
				if ($name !== $last AND $amount === 0)
				{
					// Skip empty amounts
					continue;
				}

				// Add the amount to the span
				$span[] = ($name === $last ? ' and ' : ', ').$amount.' '.($amount === 1 ? inflector::singular($name) : $name);
			}

			// Replace difference by making the span into a string
			$difference = trim(implode('', $span), ',');
		}
		elseif (is_int($difference))
		{
			// Single-value return
			$difference = $difference.' '.($difference === 1 ? inflector::singular($output) : $output);
		}

		return $difference;
	}
	
	/**
	 * Add the offset given in string (strtotime format) to the $date string.
	 *
	 * @param string    strtotime friendly date offset.
	 * @param string    formatted date string.
	 * @return string
	 */
  public function modify($string, $date) {
    return strftime('%Y-%m-%d %H:%M:%S', strtotime($string, strtotime($date)));
  }
  
  /**
   * Calculate the number of days difference between date1 and date2.
   *
   * @return signed int
   */
  public function day_difference($date1, $date2, $round_up = TRUE) {
    $date1 = is_numeric($date1) ? $date1 : strtotime($date1);
    $date2 = is_numeric($date2) ? $date2 : strtotime($date2);
    $round = $round_up ? 'ceil' : 'floor';
    return (int) $round(($date1 - $date2) / 86400);
  }

  public function month_difference($date1, $date2) {
    $d1 = strtotime($date1);
    $d2 = strtotime($date2);
    $min_date = min($d1, $d2);
    $max_date = max($d1, $d2);
    $i = 0;

    while (($min_date = strtotime("+1 MONTH", $min_date)) <= $max_date) {
        $i++;
    }
    return $d1 > $d2 ? $i : -$i;
  }

} // End date