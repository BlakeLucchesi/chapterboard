<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Kohana facade for the FPDF library.
 *
 * This class allows us to use normal kohana autoloading
 * to interact with the FPDF library found in SYSPATH/vendor/fpdf.
 */
require Kohana::find_file('vendor', 'fpdf/fpdf');

class PDF extends FPDF {
  
}