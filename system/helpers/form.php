<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Form helper class.
 *
 * $Id: form.php 3802 2008-12-17 21:36:13Z samsoir $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class form_Core {

	/**
	 * Generates an opening HTML form tag.
	 *
	 * @param   string  form action attribute
	 * @param   array   extra attributes
	 * @param   array   hidden fields to be created immediately after the form tag
	 * @return  string
	 */
	public static function open($action = NULL, $attr = array(), $hidden = NULL)
	{
		// Make sure that the method is always set
		empty($attr['method']) and $attr['method'] = 'post';

		if ($attr['method'] !== 'post' AND $attr['method'] !== 'get')
		{
			// If the method is invalid, use post
			$attr['method'] = 'post';
		}

		if ($action === NULL)
		{
			// Use the current URL as the default action
			$action = url::site(Router::$complete_uri);
		}
		elseif (strpos($action, '://') === FALSE)
		{
			// Make the action URI into a URL
			$action = url::site($action);
		}

		// Set action
		$attr['action'] = $action;

		// Form opening tag
		$form = '<form'.form::attributes($attr).'>'."\n";

		// Add hidden fields immediate after opening tag
		empty($hidden) or $form .= form::hidden($hidden);

		return $form;
	}

	/**
	 * Generates an opening HTML form tag that can be used for uploading files.
	 *
	 * @param   string  form action attribute
	 * @param   array   extra attributes
	 * @param   array   hidden fields to be created immediately after the form tag
	 * @return  string
	 */
	public static function open_multipart($action = NULL, $attr = array(), $hidden = array())
	{
		// Set multi-part form type
		$attr['enctype'] = 'multipart/form-data';

		return form::open($action, $attr, $hidden);
	}

	/**
	 * Generates a fieldset opening tag.
	 *
	 * @param   array   html attributes
	 * @param   string  a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function open_fieldset($data = NULL, $extra = '')
	{
		return '<fieldset'.html::attributes((array) $data).' '.$extra.'>'."\n";
	}

	/**
	 * Generates a fieldset closing tag.
	 *
	 * @return  string
	 */
	public static function close_fieldset()
	{
		return '</fieldset>'."\n";
	}

	/**
	 * Generates a legend tag for use with a fieldset.
	 *
	 * @param   string  legend text
	 * @param   array   HTML attributes
	 * @param   string  a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function legend($text = '', $data = NULL, $extra = '')
	{
		return '<legend'.form::attributes((array) $data).' '.$extra.'>'.$text.'</legend>'."\n";
	}

	/**
	 * Generates hidden form fields.
	 * You can pass a simple key/value string or an associative array with multiple values.
	 *
	 * @param   string|array  input name (string) or key/value pairs (array)
	 * @param   string        input value, if using an input name
	 * @return  string
	 */
	public static function hidden($data, $value = '')
	{
		if ( ! is_array($data))
		{
			$data = array
			(
				$data => $value
			);
		}

		$input = '';
		foreach ($data as $name => $value)
		{
			$attr = array
			(
				'type'  => 'hidden',
				'name'  => $name,
				'value' => $value,
				'id' => 'hidden-'. text::searchable($name),
			);

			$input .= form::input($attr)."\n";
		}

		return $input;
	}

	/**
	 * Creates an HTML form input tag. Defaults to a text type.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   boolean       encode existing entities
	 * @return  string
	 */
	public static function input($data, $value = '', $extra = '', $double_encode = TRUE )
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		// Type and value are required attributes
		$data += array
		(
			'type'  => 'text',
			'value' => $value
		);

		// For safe form data
    // $data['value'] = html::specialchars($data['value'], $double_encode);

		return '<input'.form::attributes($data).' '.$extra.' />';
	}

	/**
	 * Creates a HTML form password input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function password($data, $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'password';

		return form::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form upload input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function upload($data, $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'file';

		return form::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form textarea tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @param   boolean       encode existing entities
	 * @return  string
	 */
	public static function textarea($data, $value = '', $extra = '', $double_encode = TRUE )
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		// Use the value from $data if possible, or use $value
		$value = isset($data['value']) ? $data['value'] : $value;

		// Value is not part of the attributes
		unset($data['value']);

		return '<textarea'.form::attributes($data, 'textarea').' '.$extra.'>'.html::specialchars($value, $double_encode).'</textarea>';
	}

	/**
	 * Creates an HTML form select tag, or "dropdown menu".
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   array         select options, when using a name
	 * @param   string        option key that should be selected by default
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function dropdown($data, $options = NULL, $selected = NULL, $extra = '')
	{

		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}
		else
		{
			if (isset($data['options']))
			{
				// Use data options
				$options = $data['options'];
			}

			if (isset($data['selected']))
			{
				// Use data selected
				$selected = $data['selected'];
			}
		}

		$input = '<select'.form::attributes($data, 'select').' '.$extra.'>'."\n";
		foreach ((array) $options as $key => $val)
		{
			// Key should always be a string
			$key = (string) $key;

			// Selected must always be a string
			$selected = (string) $selected;

			if (is_array($val))
			{
				$input .= '<optgroup label="'.$key.'">'."\n";
				foreach ($val as $inner_key => $inner_val)
				{
					// Inner key should always be a string
					$inner_key = (string) $inner_key;

					if (is_array($selected))
					{
						$sel = in_array($inner_key, $selected, TRUE);
					}
					else
					{
						$sel = ($selected === $inner_key);
					}

					$sel = ($sel === TRUE) ? ' selected="selected"' : '';
					$input .= '<option value="'.$inner_key.'"'.$sel.'>'.$inner_val.'</option>'."\n";
				}
				$input .= '</optgroup>'."\n";
			}
			else
			{
				$sel = ($selected === $key) ? ' selected="selected"' : '';
				$input .= '<option value="'.$key.'"'.$sel.'>'.$val.'</option>'."\n";
			}
		}
		$input .= '</select>';

		return $input;
	}

	/**
	 * Creates an HTML form checkbox input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   boolean       make the checkbox checked by default
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function checkbox($data, $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'checkbox';

		if ($checked == TRUE OR (isset($data['checked']) AND $data['checked'] == TRUE))
		{
			$data['checked'] = 'checked';
		}
		else
		{
			unset($data['checked']);
		}

		return form::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form radio input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   boolean       make the radio selected by default
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'radio';

		if ($checked == TRUE OR (isset($data['checked']) AND $data['checked'] == TRUE))
		{
			$data['checked'] = 'checked';
		}
		else
		{
			unset($data['checked']);
		}

		return form::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form submit input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function submit($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		if (empty($data['name']))
		{
			// Remove the name if it is empty
			unset($data['name']);
		}

		$data['type'] = 'submit';

		return form::input($data, $value, $extra);
	}

	/**
	 * Creates an HTML form button input tag.
	 *
	 * @param   string|array  input name or an array of HTML attributes
	 * @param   string        input value, when using a name
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function button($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		if (empty($data['name']))
		{
			// Remove the name if it is empty
			unset($data['name']);
		}

		if (isset($data['value']) AND empty($value))
		{
			$value = arr::remove('value', $data);
		}

		return '<button'.form::attributes($data, 'button').' '.$extra.'>'.$value.'</button>';
	}

	/**
	 * Closes an open form tag.
	 *
	 * @param   string  string to be attached after the closing tag
	 * @return  string
	 */
	public static function close($extra = '')
	{
		return '</form>'."\n".$extra;
	}

	/**
	 * Creates an HTML form label tag.
	 *
	 * @param   string|array  label "for" name or an array of HTML attributes
	 * @param   string        label text or HTML
	 * @param   string        a string to be attached to the end of the attributes
	 * @return  string
	 */
	public static function label($data = '', $text = NULL, $extra = '')
	{
		if ( ! is_array($data))
		{
			if (is_string($data))
			{
				// Specify the input this label is for
				$data = array('for' => $data);
			}
			else
			{
				// No input specified
				$data = array();
			}
		}

		if ($text === NULL AND isset($data['for']))
		{
			// Make the text the human-readable input name
			$text = ucwords(inflector::humanize($data['for']));
		}

		return '<label'.form::attributes($data).' '.$extra.'>'.$text.'</label>';
	}

	/**
	 * Sorts a key/value array of HTML attributes, putting form attributes first,
	 * and returns an attribute string.
	 *
	 * @param   array   HTML attributes array
	 * @return  string
	 */
	public static function attributes($attr, $type = NULL)
	{
		if (empty($attr))
			return '';

		if (isset($attr['name']) AND empty($attr['id']) AND strpos($attr['name'], '[') === FALSE)
		{
			if ($type === NULL AND ! empty($attr['type']))
			{
				// Set the type by the attributes
				$type = $attr['type'];
			}

			switch ($type)
			{
				case 'text':
				case 'textarea':
				case 'password':
				case 'select':
				case 'checkbox':
				case 'file':
				case 'image':
				case 'button':
				case 'submit':
					// Only specific types of inputs use name to id matching
					$attr['id'] = $attr['name'];
				break;
			}
		}

		$order = array
		(
			'action',
			'method',
			'type',
			'id',
			'name',
			'value',
			'src',
			'size',
			'maxlength',
			'rows',
			'cols',
			'accept',
			'tabindex',
			'accesskey',
			'align',
			'alt',
			'title',
			'class',
			'style',
			'selected',
			'checked',
			'readonly',
			'disabled'
		);

		$sorted = array();
		foreach ($order as $key)
		{
			if (isset($attr[$key]))
			{
				// Move the attribute to the sorted array
				$sorted[$key] = $attr[$key];

				// Remove the attribute from unsorted array
				unset($attr[$key]);
			}
		}

		// Combine the sorted and unsorted attributes and create an HTML string
		return html::attributes(array_merge($sorted, $attr));
	}
	
	
	/**
	 * Provide a state selection dropdown.
	 *
	 * @param   string     element name.
	 * @param   boolean    if required we add an extra value -- Select --
	 * @param   string     the default selected value
	 * @param   string     a string to be attached to the end of the attributes
	 * @param   boolean    true to show full state names, false to show two letter names.
	 * @return  string
	 */
	function state_select($data, $required = FALSE, $selected = NULL, $extra = '', $full_names = FALSE) {
    if ($full_names) {
      $options = array(
      	'AL' => 'Alabama',
      	'AK' => 'Alaska',
      	'AZ' => 'Arizona',
      	'AR' => 'Arkansas',
      	'CA' => 'California',
      	'CO' => 'Colorado',
      	'CT' => 'Connecticut',
      	'DE' => 'Delaware',
      	'DC' => 'District of Columbia',
      	'FL' => 'Florida',
      	'GA' => 'Georgia',
      	'HI' => 'Hawaii',
      	'ID' => 'Idaho',
      	'IL' => 'Illinois',
      	'IN' => 'Indiana',
      	'IA' => 'Iowa',
      	'KS' => 'Kansas',
      	'KY' => 'Kentucky',
      	'LA' => 'Louisiana',
      	'ME' => 'Maine',
      	'MD' => 'Maryland',
      	'MA' => 'Massachusetts',
      	'MI' => 'Michigan',
      	'MN' => 'Minnesota',
      	'MS' => 'Mississippi',
      	'MO' => 'Missouri',
      	'MT' => 'Montana',
      	'NE' => 'Nebraska',
      	'NV' => 'Nevada',
      	'NH' => 'New Hampshire',
      	'NJ' => 'New Jersey',
      	'NM' => 'New Mexico',
      	'NY' => 'New York',
      	'NC' => 'North Carolina',
      	'ND' => 'North Dakota',
      	'OH' => 'Ohio',
      	'OK' => 'Oklahoma',
      	'OR' => 'Oregon',
      	'PA' => 'Pennsylvania',
      	'RI' => 'Rhode Island',
      	'SC' => 'South Carolina',
      	'SD' => 'South Dakota',
      	'TN' => 'Tennessee',
      	'TX' => 'Texas',
      	'UT' => 'Utah',
      	'VT' => 'Vermont',
      	'VA' => 'Virginia',
      	'WA' => 'Washington',
      	'WV' => 'West Virginia',
      	'WI' => 'Wisconsin',
      	'WY' => 'Wyoming',
      );
      if ($required)
        array_unshift($options, '-- Select--');
      
    }
    else {
      $options = array(
      	'AL' => 'AL',
      	'AK' => 'AK',
      	'AZ' => 'AZ',
      	'AR' => 'AR',
      	'CA' => 'CA',
      	'CO' => 'CO',
      	'CT' => 'CT',
      	'DE' => 'DE',
      	'DC' => 'DC',
      	'FL' => 'FL',
      	'GA' => 'GA',
      	'HI' => 'HI',
      	'ID' => 'ID',
      	'IL' => 'IL',
      	'IN' => 'IN',
      	'IA' => 'IA',
      	'KS' => 'KS',
      	'KY' => 'KY',
      	'LA' => 'LA',
      	'ME' => 'ME',
      	'MD' => 'MD',
      	'MA' => 'MA',
      	'MI' => 'MI',
      	'MN' => 'MN',
      	'MS' => 'MS',
      	'MO' => 'MO',
      	'MT' => 'MT',
      	'NE' => 'NE',
      	'NV' => 'NV',
      	'NH' => 'NH',
      	'NJ' => 'NJ',
      	'NM' => 'NM',
      	'NY' => 'NY',
      	'NC' => 'NC',
      	'ND' => 'ND',
      	'OH' => 'OH',
      	'OK' => 'OK',
      	'OR' => 'OR',
      	'PA' => 'PA',
      	'RI' => 'RI',
      	'SC' => 'SC',
      	'SD' => 'SD',
      	'TN' => 'TN',
      	'TX' => 'TX',
      	'UT' => 'UT',
      	'VT' => 'VT',
      	'VA' => 'VA',
      	'WA' => 'WA',
      	'WV' => 'WV',
      	'WI' => 'WI',
      	'WY' => 'WY',
      );
      if ($required)
        array_unshift($options, '--');
    }      
    return form::dropdown($data, $options, $selected, $extra);
  }
  
  static public function month_select($data, $required = FALSE, $selected = NULL, $extra = '', $full_names = FALSE) {
    if ($full_names) {
      $options = array(
        '01' => 'January',
        '02' => 'February',
        '03' => 'March',
        '04' => 'April',
        '05' => 'May',
        '06' => 'June',
        '07' => 'July',
        '08' => 'August',
        '09' => 'September',
        '10' => 'October',
        '11' => 'November',
        '12' => 'December',
      );
      if ($required) {
        array_unshift($options, '-- Select --');
      }
    }
    else {
      $options = array(
        '01' => 'Jan',
        '02' => 'Feb',
        '03' => 'Mar',
        '04' => 'Apr',
        '05' => 'May',
        '06' => 'Jun',
        '07' => 'Jul',
        '08' => 'Aug',
        '09' => 'Sep',
        '10' => 'Oct',
        '11' => 'Nov',
        '12' => 'Dec',
      );
      if ($required) {
        array_unshift($options, '--');
      }      
    }
    return form::dropdown($data, $options, $selected, $extra);
  }
  
  static public function year_select($data, $max = 4, $selected = NULL, $extra = '', $empty_allowed = FALSE) {
    if ($max > 0) {
      $options = arr::range(date('Y'), date('Y') + $max);
      ksort($options);
    }
    else {
      $options = arr::range(date('Y') + $max, date('Y'));
      krsort($options);
    }
    if ($empty_allowed) {
      $options = array(0 => '') + $options;
    }
    return form::dropdown($data, $options, $selected, $extra);
  }

} // End form