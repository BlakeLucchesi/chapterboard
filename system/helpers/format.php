<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Format helper class.
 *
 * $Id: format.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class format_Core {

	/**
	 * Formats a phone number according to the specified format.
	 *
	 * @param   string  phone number
	 * @param   string  format string
	 * @return  string
	 */
	public static function phone($number, $format = '(3) 3-4')
	{
		// Get rid of all non-digit characters in number string
		$number_clean = preg_replace('/\D+/', '', (string) $number);

    // Remove leading 1.
    $number_clean = preg_replace('/^1/', '', $number_clean);

		// Array of digits we need for a valid format
		$format_parts = preg_split('/[^1-9][^0-9]*/', $format, -1, PREG_SPLIT_NO_EMPTY);

		// Number must match digit count of a valid format
		if (strlen($number_clean) !== array_sum($format_parts))
			return $number;

		// Build regex
		$regex = '(\d{'.implode('})(\d{', $format_parts).'})';

		// Build replace string
		for ($i = 1, $c = count($format_parts); $i <= $c; $i++)
		{
			$format = preg_replace('/(?<!\$)[1-9][0-9]*/', '\$'.$i, $format, 1);
		}

		// Hocus pocus!
		return preg_replace('/^'.$regex.'$/', $format, $number_clean);
	}

	/**
	 * Formats a URL to contain a protocol at the beginning.
	 *
	 * @param   string  possibly incomplete URL
	 * @return  string
	 */
	public static function url($str = '')
	{
		// Clear protocol-only strings like "http://"
		if ($str === '' OR substr($str, -3) === '://')
			return '';

		// If no protocol given, prepend "http://" by default
		if (strpos($str, '://') === FALSE)
			return 'http://'.$str;

		// Return the original URL
		return $str;
	}
	
	/**
	 * Formats a string based on the number for plural/singular.
	 */
	public function plural($count, $singular, $plural, $args = array()) {
	  $args['@count'] = $count;
    if ($count === 1 || $count === '1.0') {
      return strtr($singular, $args);
    }
    else {
      return strtr($plural, $args);
    }
	}
	
	/**
	 * Formats a body of text as html passing the text through a set of filters.
	 */
	public function html($text) {
    include_once(Kohana::find_file('vendor', 'Textile'));
    $textile = new Textile();
    $text = text::auto_link($text);
    $text = $textile->textileThis($text);
    return $text;
	}

} // End format