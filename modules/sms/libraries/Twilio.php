<?php defined('SYSPATH') or die('No direct script access.');

class Twilio {

  protected $config;
  
  protected $connection;
  
  /**
   * Constructor
   */
  public function __construct() {
    require Kohana::find_file('vendor', 'twilio', true);
    $this->config = Kohana::config('sms');
    $this->connection = new TwilioRestClient($this->config['auth']['sid'], $this->config['auth']['token']);
    return $this->connection;
  }
  
  /**
   * Send a message to a recipient
   *
   * @param 10 digit number of the recipient.
   * @param 160 character message.
   * @return boolean based on message sent success or failure.
   */
  public function send($to, $body, $from = null) {
    $url = "{$this->config['version']}/Accounts/{$this->config['auth']['sid']}/SMS/Messages";
    $method = 'POST';
    $params = array(
      'To' => $to,
      'Body' => $body,
      'From' => $from ? $from : $this->config['number']
    );
    Kohana::log('debug', 'Sending SMS message via Twilio.', array($url, $method, $params));
    
    // Begin HTTP request to Twilio servers. Catch any errors while processing.
    try {
      $request = $this->connection->request($url, $method, $params);
      if ($request->IsError) {
        Kohana::log('error', 'Error while sending SMS message via Twilio.', $request->ResponseXml);
        return FALSE;
      }
      else {
        return TRUE; 
      }
    } catch (TwilioResponseException $e) {
      Kohana::log('error', 'Exception thrown while sending SMS message via Twilio.', $e);
      return FALSE;
    }
  }
  
}