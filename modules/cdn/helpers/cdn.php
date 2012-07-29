<?php defined('SYSPATH') or die('No direct script access.');

class cdn_Core {
  
	/**
	 * If cdn.enabled we provide a full url appending the $path to the 
	 * base cdn path. Otherwise we use url::base() as the base path.
	 *
	 * @param string  The path to an asset located on a CDN.
	 */
	public static function url($path) {
	  if (Kohana::config('cdn.enabled')) {
	    return Kohana::config('cdn.base_url').$path;
	  }
	  else {
	    return url::base().$path;
	  }
	}
  
  /**
	 * Creates a image link setting the base path from a CDN.
	 *
	 * @param   string        image source, or an array of attributes
	 * @param   string|array  image alt attribute, or an array of attributes
	 * @param   boolean       include the index_page in the link
	 * @return  string
	 */
	public static function image($src = NULL, $alt = NULL, $index = FALSE)
	{
		// Create attribute list
		$attributes = is_array($src) ? $src : array('src' => $src);

		if (is_array($alt))
		{
			$attributes += $alt;
		}
		elseif ( ! empty($alt))
		{
			// Add alt to attributes
			$attributes['alt'] = $alt;
		}

		if (strpos($attributes['src'], '://') === FALSE) {
      if (Kohana::config('cdn.enabled')) {
        $attributes['src'] = Kohana::config('cdn.base_url').$attributes['src'];
      }
      else {
  			// Make the src attribute into an absolute URL
  			$attributes['src'] = url::base($index).$attributes['src'];
      }
		}

		return '<img'.html::attributes($attributes).' />';
	}
	
}