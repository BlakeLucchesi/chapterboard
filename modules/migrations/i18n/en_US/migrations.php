<?php defined('SYSPATH') or die('No direct script access.');

$lang = array
(
	'disabled'            => 'Tried to load migrations when disabled',
	'invalid_filename'    => 'Invalid migration file name, "%s".',
	'class_doesnt_exist'  => 'Migration class doesn\'t exist: "%s".',
	'not_found'           => 'Migration %d not found.',
	'multiple_names'      => 'Multiple migrations have the name %s.',
	'multiple_versions'   => 'Multiple migrations have version number %d.',
	'none_found'          => 'No migrations found',
	'wrong_interface'     => '"%s" has a wrong interface. (Migrations must implement public methods "up" and "down")',
	'abstract'            => 'Tried to run an abstract or undefined migration',
	'bad_column'          => 'Invalid column parameter: "%s"',
	'missing_argument'    => 'Missing a required argument',
	'column_not_found'    => 'Column "%s" was not found in table "%s"',
	'unknown_type'        => 'Invalid database or migration type "%s"',
	'bad_index_type'      => 'Unknown index type: "%s"',
);
