<?php
/**
 * Javascript collector library. Allows collections of JS so only required JS libraries are included on each page
 *
 * @package Javascript/CSS Collector
 * @author Sam Clark sam@clark.name
 */
class javascript_Core {

	/**
	 * Array containing URL's to scripts
	 *
	 * @var string
	 */
	static protected $scripts = array();

  static public function add($data = NULL, $type = 'file') {
    
    switch ($type) {
      case 'url':
        self::$scripts['urls'][$data] = $data;
        break;

      case 'file':
        self::$scripts['files'][$data] = $data;
        break;

      case 'inline':
        if (!array_key_exists('inline', self::$scripts)) {
          self::$scripts['inline'] = array();
        }
        self::$scripts['inline'][] = $data;
        break;

      case 'setting':
        if (!array_key_exists('settings', self::$scripts)) {
          self::$scripts['settings'] = array();
        }
        foreach($data as $k => $v) {
          self::$scripts['settings'][$k] = $v;
        }
        break;
    }
  }
  
  /**
   * 
   */
  static public function get() {
    $out = '';
    $first = TRUE;

    if (empty(self::$scripts['files'])) {
      return;
    }
    
    // urls
    if (array_key_exists('urls', self::$scripts)) {
      foreach (self::$scripts['urls'] as $url) {
        $out .= '<script type="text/javascript" charset="utf-8" src="'. $url .'"></script>'. "\n";
      }
    }

    // files
    if (array_key_exists('files', self::$scripts)) {
      $files = self::$scripts['files'];
      foreach ($files as $file) {
        $time = filemtime(realpath($file));
        $out .= '<script type="text/javascript" charset="utf-8" src="'. url::file($file) .'?'. $time .'"></script>'."\n";
      }
    }

    // settings
    if (array_key_exists('settings', self::$scripts)) {
      $settings = self::$scripts['settings'];
      $out .= "  <script type=\"text/javascript\" charset=\"utf-8\">\n";
      foreach($settings as $k => $v) {
        $out .= "Kohana.settings.$k = " . json_encode($v) . ";\n";
      }
      $out .= "\n  </script>\n";
    }
    
    // inline
    if (array_key_exists('inline', self::$scripts)) {
      $inline = self::$scripts['inline'];
      $out .= "  <script type=\"text/javascript\" charset=\"utf-8\">\n    $(document).ready(function() {\n      ".implode("\n      ", $inline)."\n   });\n  </script>\n";
    }
    
    return $out;
  }
	

	/**
	 * Generates
	 *
	 * @param boolean      print whether to echo the output or just return rendered string
	 * @return string      the rendered output
	 * @author Sam Clark
	 */
	static public function render($print = FALSE, $clear_cache = FALSE)
	{
		$output = html::script('javascript/load/'.self::package(self::$scripts, $clear_cache).'.js');

		if ($print)
			echo $output;

		return $output;
	}

	/**
	 * Packages all the scripts in a directory and returns the output.
	 *
	 * @param   string   directory name
	 * @return  string
	 * @author  Sam Clark
	 */
	public static function package($files, $clear_cache = FALSE)
	{
		// Setup cache library
		$cache = Cache::instance();

		// Create a checksum
		$checksum = self::create_checksum($files);

		if ($clear_cache OR ! $cache->get($checksum))
		{
			$cache->delete($checksum);

			$minified_js = '';

			foreach ($files as $file)
			{
				$minified_js .= self::minify($file);
			}

			$cache->set($checksum, $minified_js, array('js', 'javascript'), Kohana::config('javascript.lifetime', 3600));
		}

		return $checksum;
	}

	/**
	 * Minifies the supplied file if the file exists and is readable
	 *
	 * @param   string       file  the path to the file to minify
	 * @return  string       the minified version of the file
	 * @author  Sam Clark
	 * @author  Woody Gilk
	 */
	public static function minify($file)
	{
		$result = '';

		$file = new SplFileInfo($file);
    // var_dump($file->getPathname());

		if ($file->isFile() AND $file->isReadable())
		{
			$source = trim(file_get_contents($file->getPathname()));

			// File comment
			$comment = '// '.$file->getFilename().' -------------------';

			if (preg_match('#/\*(?:[^*]|(?:\*(?!/)))+?\*/#m', $source, $matches))
			{
				// Add the comment header, JSMin will remove it
				$comment .= "\n".$matches[0];
			}

			// Minify the source and add a semicolon on the end
			$source = trim(JSMin::minify($source), ';');

			// Minify the source and add it to the output
			$result = $comment.$source.';';
		}

		return $result;
	}

	public static function create_checksum($files)
	{
		$checksum = '';
		foreach ($files as $file)
			$checksum .= $file;

		return sha1($checksum);
	}

} // End javascript_Core