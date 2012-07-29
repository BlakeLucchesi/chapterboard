<?php defined('SYSPATH') or die('No direct script access.');

class Sms_Controller extends Api_Controller {
  
  /**
   * Receives incoming text messages.
   */
  public function receive() {
    // Make sure the request is actually coming from twilio with our account id.
    if ($post = $this->input->post()) {
      if ($post['AccountSid'] == Kohana::config('sms.auth.sid')) {
        // Load a user account off the phone number to see if they have access to
        // send out mass text messages.
        $sender = ORM::factory('user')->find_by_phone($post['From']);
        if ( ! $sender->loaded || $sender->status != 1) {
          log::system('sms', sprintf('Incoming SMS from an unknown phone number %s: %s', format::phone($post['From']), $post['Body']), 'notice', array('details' => print_r($post, TRUE)));
          die();
        }

        // If the sender has permission we store the message for redelivery.
        if (A2::instance()->is_allowed($sender, 'sms', 'send')) {
          // log::system('sms', sprintf('Incoming SMS from %s: %s', format::phone($post['From']), $post['Body']), 'notice', array('details' => print_r($post, TRUE)));

          // If the message is not a split message, insert a new record.
          if ( ! ORM::factory('sms')->append_to_sequence($sender->id, $post['Body'])) {
            $this->sms = ORM::factory('sms');
            if ($this->sms->validate($post, TRUE)) {
              sms::send($post['From'], 'Your message is being forwarded to your members.');
            }
            else {
              sms::send($post['From'], 'There was an error sending your message. Please try again.');
            }
          }
        }
        else {
          $site_name = $sender->loaded ? $sender->site->name() : 'No record';
          log::system('sms', sprintf('Incoming SMS (no-permission) from %s (%s): %s', format::phone($post['From']), $site_name, $post['Body']), 'notice', array('details' => print_r($post, TRUE)));
        }
      }
      else {
        die();
      }
    }
    else {
      die();
    }
  }
  
}