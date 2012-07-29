<?php defined('SYSPATH') or die('No direct script access.');

class Fundraising_Controller extends Finances_Controller {
    
  public function index() {
    $this->title = 'Fundraising Campaigns';
    if ( ! A2::instance()->allowed('campaign', 'manage'))
      Event::run('system.403');
    
    $this->campaigns = ORM::factory('campaign')->find_by_site($this->site->id);
    $this->donations = ORM::factory('campaign_donation')->find_by_site($this->site->id, 10, 0);
  }
  
  public function show($id) {
    $this->campaign = ORM::factory('campaign', $id);
    if ( ! $this->campaign->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->campaign, 'manage'))
      Event::run('system.403');
    
    $this->title = $this->campaign->title;
    $this->donations = ORM::factory('campaign_donation')->find_by_campaign_id($this->campaign->id, 40);
  }
  
  public function add() {
    if ( ! A2::instance()->allowed('campaign', 'manage'))
      Event::run('system.403');
      
    $this->title = 'Create Campaign';
    $this->view = 'finances/fundraising/form';
    $this->deposit_account_options = ORM::factory('deposit_account')->filter_active()->select_list();
    for ($i = 0; $i < 5; $i++) {
      $this->form['payment_options'][$i] = array('label' => '', 'value' => '');
    }
    $this->form['payment_free_entry'] = TRUE;
    
    if ($post = $this->input->post()) {
      $campaign = ORM::factory('campaign');
      
      // Validate upload and save file if valid.
      $valid = upload::validate('picture', 'image');
      if ($valid->validate()) {
        $info = upload::info('picture');
        if ($fileinfo = upload::save('picture', $info['filename'], Kohana::config('upload.directory'))) {
          $post['picture'] = $fileinfo['filename'];
        }
      }
      else {
        message::add(FALSE, 'Photo was unable to save properly.  Make sure you are uploading a valid jpg, gif or png picture under 2 megabytes in size.');
      }
      
      if ($campaign->validate($post, TRUE)) {
        message::add(TRUE, 'Campaign created successfully.');
        url::redirect('finances/fundraising');
      }
      else {
        message::add(FALSE, 'Please fix the errors below.');
        $this->form = $post->as_array();
        $this->form['expires'] = $this->form['expires'] ? date::display($this->form['expires'], 'm/d/Y', FALSE) : '';
        $this->errors = $post->errors('form_campaigns_admin');
      }
    }
  }
  
  public function edit($id) {
    $this->campaign = ORM::factory('campaign', $id);

    if ( ! $this->campaign->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->campaign, 'manage'))
      Event::run('system.403');
      
    $this->title = 'Edit Campaign';
    $this->view = 'finances/fundraising/form';
    $this->form = $this->campaign->as_array();
    $this->deposit_account_options = ORM::factory('deposit_account')->filter_active()->select_list();
    
    if ($post = $this->input->post()) {
      // Validate upload and save file if valid.
      $valid = upload::validate('picture', 'image');
      if ($valid->validate()) {
        $info = upload::info('picture');
        if ($fileinfo = upload::save('picture', $info['filename'], Kohana::config('upload.directory'))) {
          $post['picture'] = $fileinfo['filename'];
        }
      }
      else {
        message::add(FALSE, 'Photo was unable to save properly.  Make sure you are uploading a valid jpg, gif or png picture under 2 megabytes in size.');
      }
      
      if ( ! $post['picture']) {
        $post['picture'] = $this->campaign->picture;
      }
      if ($post['picture_remove']) {
        $post['picture'] = NULL;
      }
      if ($this->campaign->validate($post, TRUE)) {
        message::add(TRUE, 'Campaign %s has been saved.', $this->campaign->title);
        url::redirect('finances/fundraising');
      }
      else {
        $this->form = $post->as_array();
        $this->errors = $post->errors('form_campaigns_admin');
      }
    }
    $this->form['expires'] = $this->form['expires'] ? date::display($this->form['expires'], 'm/d/Y', FALSE) : '';
    for ($i = 0; $i < 5; $i++) {
      $this->form['payment_options'][$i] = isset($this->campaign->payment_options[$i]) ? $this->campaign->payment_options[$i] : array('label' => '', 'value' => '');
    }
  }
  
  public function receive($id) {
    $this->campaign = ORM::factory('campaign', $_GET['campaign_id']);
    $this->donation = ORM::factory('campaign_donation', $id);
    if ($this->donation->loaded) {
      if ( ! A2::instance()->allowed($this->transaction->campaign, 'edit')) {
        Event::run('system.403');
      }
      $this->form = $this->donation->as_array();
    }
    else {
      $this->donation = ORM::factory('campaign_donation');
    }
    
    if ($post = $this->input->post()) {
      
    }
  }
  
  public function export($id) {
    $this->campaign = ORM::factory('campaign', $id);

    if ( ! $this->campaign->loaded)
      Event::run('system.404');
    if ( ! A2::instance()->allowed($this->campaign, 'manage'))
      Event::run('system.403');
    
    $rows[] = array(
      'Name',
      'Email',
      'Phone',
      'Address',
      'City',
      'State',
      'Zipcode',
      'Note',
      'Item',
      'Date',
      'Payment Method',
      'Payment Amount',
      'Deposited',
    );
    foreach ($this->campaign->campaign_donations as $donation) {
      $rows[] = array(
        $donation->name(),
        $donation->email,
        $donation->phone,
        $donation->address,
        $donation->city,
        $donation->state,
        $donation->zip,
        $donation->note,
        $donation->item_label,
        date::display($donation->created, 'm/d/Y'),
        $donation->payment_type(),
        money::display($donation->amount),
        money::display($donation->amount_payable)
      );
    }
    response::csv($rows, sprintf('campaign-%s', text::id_safe($this->campaign->title)));
  }
}