<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Provides payment support for credit cards and other providers like PayPal.
 *
 * $Id: Payment.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Payment
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Payment_Core {

	// Configuration
	protected $config = array
	(
		// The driver string
		'driver'      => NULL,
		// Test mode is set to true by default
		'test_mode'   => TRUE,
	);

	protected $driver = NULL;

	/**
	 * Sets the payment processing fields.
	 * The driver will translate these into the specific format for the provider.
	 * Standard fields are (Providers may have additional or different fields):
	 *
	 * card_num
	 * exp_date
	 * cvv
	 * description
	 * amount
	 * tax
	 * shipping
	 * first_name
	 * last_name
	 * company
	 * address
	 * city
	 * state
	 * zip
	 * email
	 * phone
	 * fax
	 * ship_to_first_name
	 * ship_to_last_name
	 * ship_to_company
	 * ship_to_address
	 * ship_to_city
	 * ship_to_state
	 * ship_to_zip
	 *
	 * @param  array  the driver string
	 */
	public function __construct($config = '')
	{
		if (empty($config))
		{
			// Load the default group
			$config = Kohana::config('payment.default');
		}
    elseif (is_string($config))
    {
     $config = Kohana::config('payment.'. $config);
    }

		// Merge the default config with the passed config
		is_array($config) AND $this->config = array_merge($this->config, $config);

		// Set driver name
		$driver = 'Payment_'.ucfirst($this->config['driver']).'_Driver';

		// Load the driver
		if ( ! Kohana::auto_load($driver))
			throw new Kohana_Exception('core.driver_not_found', $this->config['driver'], get_class($this));

		// Get the driver specific settings
		$this->config = array_merge($this->config, Kohana::config('payment.'.$this->config['driver']));

		// Initialize the driver
		$this->driver = new $driver($this->config);

		// Validate the driver
		if ( ! ($this->driver instanceof Payment_Driver))
			throw new Kohana_Exception('core.driver_implements', $this->config['driver'], get_class($this), 'Payment_Driver');
	}

	/**
	 * Sets the credit card processing fields
	 *
	 * @param  string  field name
	 * @param  string  value
	 */
	public function __set($name, $val)
	{
		$this->set_field($name, $val);
	}
	
	/**
	 * Alias of the get_response_value method.
	 */
	public function __get($key) {
	  return $this->get_response_value($key);
	}

	/**
	 * Bulk setting of payment processing fields.
	 *
	 * @param   array   array of values to set
	 * @return  object  this object
	 */
	public function set_fields($fields)
	{
		$this->driver->set_fields((array) $fields);

		return $this;
	}
	
	/**
	 * Set an individual field.
	 */
	public function set_field($name, $value) {
	  $this->driver->set_fields(array($name => $value));
	}

  /**
   * Get the value of a field.
   */
  public function get_value($field) {
    return $this->driver->get_value($field);
  }
  
	/**
	 * Runs the transaction
	 *
	 * @return  TRUE|string  TRUE on successful payment, an error string on failure
	 */
	public function process()
	{
		return $this->driver->process();
	}
	
	/**
	 * Get response code.
	 *
	 * @return  int  Returns the response code from the payment processor.
	 */
	public function get_response_code() {
	  return $this->driver->get_response_code();
	}
	
	/**
	 * Get response reason.
	 */
  function get_response_reason() {
    return $this->driver->get_response_reason();
  }
  
  /**
   * Get a response value.
   */
  function get_response_value($key) {
    return $this->driver->get_response_value($key);
  }
  
  /**
   * Get the latest transaction id.
   */
  function get_transaction_id() {
    return $this->driver->get_transaction_id();
  }
}
