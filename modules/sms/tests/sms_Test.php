<?php defined('SYSPATH') or die('No direct script access.');

class SMS_Test extends Unit_Test_Case {
  
  public $default_incoming = array();
  
  public $url = '';
  
  public function setup() {
    
    $this->url = 'http://'. Kohana::config('config.site_domain') .'/api/sms/receive';

    // Make sure our test account has priviledges.
    $role = ORM::factory('role', 'sms');
    $user = ORM::factory('user', 102); // Blake UCI Pi Kappa Alpha
    $user->add($role);
    $user->save();
    
    $this->default_incoming = array(
      'AccountSid' => Kohana::config('sms.auth.sid'),
      'From' => '9497849177',
      'SmsSid' => text::random('distinct', 34),
      'To' => Kohana::config('sms.number'),
      'Body' => 'Test the incoming sms gateway.',
    );
    
    $config = Kohana::config('sms');
    $config['debug'] = TRUE;
    Kohana::config_set('sms', $config);
  }
  
  /**
   * Test the twilio SMS API.
   *
   */
  // public function twilio_test() {  
  //   $to = '9497849177';
  //   $message = '@actives @pledges This is a test message from from ChapterBoard via twilio!';
  //   
  //   $this->assert_true(sms::send($to, $message));
  // }
  
  /**
   * Test that valid incoming sms are handled appropriately.
   */
  public function valid_receive_test() {
    $this->db = new Database;
    $start_count = $this->db->query("SELECT COUNT(*) count FROM sms")->current()->count;

    $curl = curl_init($this->url);
    $encoded = $this->_encode($this->default_incoming);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
    $result = curl_exec($curl);
    
    $end_count = $this->db->query("SELECT COUNT(*) count FROM sms")->current()->count;
    
    $this->assert_equal(200, curl_getinfo($curl, CURLINFO_HTTP_CODE));
    $this->assert_equal($start_count + 1, $end_count);

    // Cleanup
    ORM::factory('sms')->orderby('id', 'DESC')->find()->delete();
  }
  
  /**
   * Test that invalid incoming sms are handled appropriately. 
   */
  public function invalid_receive_test() {
    $start_count = ORM::factory('sms')->find_all()->count();

    $invalid = array_merge($this->default_incoming, array('From' => '4158003041', 'Body' => 'Invalid incoming message.'));

    $curl = curl_init($this->url);
    $encoded = $this->_encode($invalid);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
    $result = curl_exec($curl);
    
    $end_count = ORM::factory('sms')->find_all()->count();
    
    $this->assert_equal(200, curl_getinfo($curl, CURLINFO_HTTP_CODE));
    $this->assert_equal($start_count, $end_count);
  }
  
  /**
   * Test that group parsing works properly.
   */
  public function group_parsing_test() {
    $sms = ORM::factory('sms');
    $sms->validate($this->default_incoming, TRUE);
    $sms->parse_groups();
    $this->assert_equal(array('2' => 'Active'), $sms->groups);

    // Test two groups.
    $sms->message = '@actives @alumni Party time is gonna be dope.';
    $sms->parse_groups();
    $this->assert_equal(array('2' => 'Active', '1' => 'Alumni'), $sms->groups);

    // Test two groups, one isn't a real group though.
    $sms->message = '@actives @alumniae Party time is gonna be dope.';
    $sms->parse_groups();
    $this->assert_equal(array('2' => 'Active'), $sms->groups);
    $this->assert_equal('Party time is gonna be dope.', $sms->message);
    $sms->delete();
  }
  
  /**
   * Test split incoming messages to make sure that we properly
   * join the message before forwarding back out.
   */
  public function rejoin_split_messages_test() {
    $start_count = ORM::factory('sms')->find_all()->count();

    $part1 = array_merge($this->default_incoming, array('Body' => "Brothers of Theta Delta Chi, I'm proud to let you know that we have 11 rushees accept our bid today. Everyone did great and we had a successful rush week"));
    $part2 = array_merge($part1, array('Body' => ". I'd like to thank each and everyone of you for all your help and support. ITB, Sharvil"));
      
    $curl = curl_init($this->url);
    $encoded = $this->_encode($part1);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
    $result = curl_exec($curl);
    
    // Halt execution for a short period to mimic actual delay in receive time.
    sleep(rand(5, 15));
    
    $curl = curl_init($this->url);
    $encoded = $this->_encode($part2);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $encoded);
    $result = curl_exec($curl);    
    
    $end_count = ORM::factory('sms')->find_all()->count();
    
    $sms = ORM::factory('sms')->orderby('created', 'DESC')->find();
    $this->assert_equal($sms->message, "Brothers of Theta Delta Chi, I'm proud to let you know that we have 11 rushees accept our bid today. Everyone did great and we had a successful rush week. I'd like to thank each and everyone of you for all your help and support. ITB, Sharvil");
    $this->assert_equal(200, curl_getinfo($curl, CURLINFO_HTTP_CODE));
    $this->assert_equal($start_count + 1, $end_count);
    
    // Cleanup.
    $sms->delete();
  }
  
  /**
   * Encode url params.
   */
  function _encode($values) {
    foreach ($values as $key => $value) {
      $encoded .= $key.'='.urlencode($value).'&';
    }
    $encoded = substr($encoded, 0, -1);
    return $encoded;
  }
  
}