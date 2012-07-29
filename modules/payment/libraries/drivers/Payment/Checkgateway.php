<?php defined('SYSPATH') or die('No direct script access.');

class Payment_Checkgateway_Driver implements Payment_Driver {
  
  var $field_string;
  var $fields = array();

  var $response_string;
  var $response = array();

  var $gateway_url = "https://www.CheckGateway.com/EpnPublic/ACHXML.aspx";

  var $curl_config = NULL;
  
  var $allowed_fields = array(
    'AccountNumber',
    'RoutingNumber',
    'Amount',
    'ReferenceNumber',
    'Version',
    'Method',
    'Login',
    'Password',
    'Name',
    'Address1',
    'Address2',
    'City',
    'State',
    'Zip',
    'Phone',
    'Email',
    'Birthday',
    'SSN',
    'DLN',
    'DLS',
    'Test'
  );
  
  /**
   * Setup default values from configuration file.
   */
  public function __construct($config) {
    $this->set_fields($config);
    
    if ($config['test_mode']) {
      $this->set_field('Test', "TRUE");
    }
  }
  
  /**
	 * Sets driver fields and marks reqired fields as TRUE.
	 *
	 * @param  array  array of key => value pairs to set
	 */
	public function set_fields($fields) {
	  foreach ($fields as $key => $value) {
      $this->set_field($key, $value);
	  }
	}
	
	public function set_field($key, $value) {
	  $this->fields[ucfirst($key)] = $value;
	}

  /**
   * Get a field's value from the driver.
   */
  public function get_value($field) {
    return $this->fields[$field];
  }
  
  /**
   * Return prepared XML for curl POST.
   */
  public function xml_post_fields() {
    $post = new XMLWriter;
    $post->openMemory();
	  $post->startDocument('1.0');
  	  $post->startElement('ACH');
        foreach ($this->fields as $key => $value) {
          if (in_array(ucwords($key), $this->allowed_fields)) {
            $post->writeElement(ucwords($key), $value);
          }
        }
        // If generic fields are used, convert to Check Gateway API specific names.
        if ( ! $this->fields['Name'] && ($this->fields['first_name'] || $this->fields['last_name'])) {
          $post->writeElement('Name', sprintf('%s %s', $this->fields['first_name'], $this->fields['last_name']));
        }
        if ( ! $this->fields['Address1'] && $this->fields['address']) {
          $post->writeElement('Address1', $this->fields['address']);
        }
        if ( ! $this->fields['Amount'] && $this->fields['amount']) {
          $post->writeElement('Amount', $this->fields['amount']);
        }
      $post->endElement();
    $post->endDocument();
    return $post->outputMemory();
  }
  
	/**
	 * Runs the transaction.
	 *
   * Example response:
   * <?xml version="1.0"?>
   * <Response Method="Debit" Version="1.4.2.05" Test="True" Success="True" Severity="0" TransactionID="1792191846" Status="Accepted">
   *   <Message>Transaction processed.</Message>
   *   <Notes>
   *     <Note>PrevPay: nil +0</Note>
   *     <Note>Score: 0</Note>
   *   </Notes>
   * </Response>
   *
	 * @return  boolean
	 */
	public function process() {
  	$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->gateway_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);	// return response
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml_post_fields());
		$response_string = curl_exec($ch);
		if (curl_errno($ch)) {
      log::system('finance', 'Check Gateway Payment Gateway Connection Error', 'error', curl_error($ch));
      throw new Kohana_Exception('payment.gateway_connection_error');
      return FALSE;
		}
		else curl_close($ch);
		
		log::system('finance', 'Data: '. $this->xml_post_fields());
		log::system('finance', $response_string);
		$this->response = new SimpleXMLElement($response_string);
		return $this->response['Success'] == 'True' ? TRUE : FALSE;
	}

  /**
   * Return the numeric response code.
   */
  function get_response_code() {
    return $this->response['Severity'];
  }

  /**
   * Return the response text from Authorize.net.
   */
  function get_response_reason() {
    return $this->response->Message;
  }

  /**
   * Return a value from the response.
   */
  function get_response_value($key) {
    try {
      return $this->response->$key;
    }
    catch (Exception $e) {
      log::system('finance', 'Error getting response value for key: '. $key);
    }
  }
  
  /**
   * Get the last Transaction Id.
   */
  function get_transaction_id() {
    if ($this->fields['test_mode']) {
      return 'TEST TRANSACTION';
    }
    return $this->response['TransactionID'];
  }
  
  
  
}