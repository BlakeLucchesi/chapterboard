<?php defined('SYSPATH') or die('No direct script access.');

class Profile_Model extends ORM {

  protected $belongs_to = array('user');
  
  protected $table_name = 'user_profiles';
  protected $primary_key = 'user_id';
  
  /**
   * Format the user's address.
   */
  public function address() {
    if ($address = $this->school_address()) {
      return $address;
    }
    else if ($address = $this->home_address()) {
      return $address;
    }
    return '';
  }

  public function school_address() {
    $o = '';
    if ($this->address1) {
      $o = $this->address1;
      if ($this->address2) {
        $o .= '<br />'. $this->address2;
      }
      $o .= '<br />';
      $o .= sprintf('%s, %s %s', $this->city, $this->state, $this->zip);
    }
    return $o;
  }
  
  public function home_address() {
    $o = '';
    if ($this->home_address1) {
      $o = $this->home_address1;
      if ($this->home_address2) {
        $o .= '<br />'. $this->home_address2;
      }
      $o .= '<br />';
      $o .= sprintf('%s, %s %s', $this->home_city, $this->home_state, $this->home_zip);
    }
    return $o;
  }
  
  public function address_printable() {
    $address = '';
    if ($this->address1 && $this->state && $this->zip) {
      $address = $this->address1;
      $address .= $this->address2 ? ' '. $this->address2 : '';
      $zip = $this->zip ? $this->zip : '';
      $state = $this->state ? $this->state : '';
      $address = sprintf('%s, %s, %s, %s', $address, $this->city, $state, $zip);
    }
    return $address;
  }
  
  public function birthday_formatted() {
    if (is_null($this->birthday) || $this->birthday == '0000-00-00') {
      return 'Unavailable';
    }
    return date::display($this->birthday, 'M d, Y', FALSE);
  }
  
  /**
   * Profile edit validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array['birthday'] = $array['birthday'] ? date::input_to_db($array['birthday']) : NULL;
    $array['initiation_date'] = $array['initiation_date'] ? date::input_to_db($array['initiation_date']) : NULL;
    $array['pledge_date'] = $array['pledge_date'] ? date::input_to_db($array['pledge_date']) : NULL;
    $array = Validation::factory($array)
      ->pre_filter('trim')
      ->pre_filter(array('text', 'searchable'), 'phone')
      ->add_rules('student_id', 'standard_text')
      ->add_rules('birthday', 'date')
      ->add_rules('phone', 'phone[10]')
      ->add_rules('phone_carrier', 'standard_text')
      ->add_rules('address1', 'standard_text')
      ->add_rules('address2', 'standard_text')
      ->add_rules('city', 'standard_text')
      ->add_rules('state', 'standard_text')
      ->add_rules('zip', 'standard_text')
      ->add_rules('home_address1', 'standard_text')
      ->add_rules('home_address2', 'standard_text')
      ->add_rules('home_city', 'standard_text')
      ->add_rules('home_state', 'standard_text')
      ->add_rules('home_zip', 'standard_text')
      ->add_rules('emergency1_name', 'standard_text')
      ->add_rules('emergency1_phone', 'phone')
      ->add_rules('emergency2_name', 'standard_text')
      ->add_rules('emergency2_phone', 'phone')
      ->add_rules('shirt_size', 'required', 'standard_text')
      ->add_rules('school_year', 'standard_text')
      ->add_rules('department', 'blob')
      ->add_rules('major', 'blob')
      ->add_rules('pledge_date', 'date')
      ->add_rules('initiation_date', 'date')
      ->add_rules('initiation_year', 'numeric')
      ->add_callbacks('birthday', array($this, '_birthday_year'))
      ->add_callbacks('initiation_year', array($this, '_initiation_year'));
    return parent::validate(&$array, $save);
  }
  
  public function _birthday_year(Validation $array, $field) {
    if ( ! is_null($array[$field]) && substr($array[$field], 0, 4) > date('Y') - 15) {
      $array->add_error($field, 'birthday_invalid');
    }
  }
  
  public function _initiation_year(Validation $array, $field) {
    if ($array[$field] && ($array[$field] < 1900 || $array[$field] > date('Y') + 1)) {
      $array->add_error($field, 'initiation_year');
    }
  }
  
  public function shirt_size_list() {
    return array(0 => '- Select Size -') + Kohana::config('chapterboard.shirt_sizes');
  }
  
}