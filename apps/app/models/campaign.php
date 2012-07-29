<?php defined('SYSPATH') or die('No direct script access.');

class Campaign_Model extends ORM implements Acl_Resource_Interface {
  
  protected $belongs_to = array('deposit_account', 'site');
  
  protected $has_many = array('campaign_donations');
  
  protected $sorting = array('created' => 'DESC');
  
  protected $_campaign_total = FALSE;
  
  protected $_campaign_count = FALSE;
  
  public function find_by_site($site_id = NULL) {
    $site_id = is_null($site_id) ? kohana::config('chapterboard.site_id') : $site_id;
    return $this->where('site_id', $site_id)->find_all();
  }
  
  public function find_by_site_slug($site_id, $slug) {
    return $this->where('site_id', $site_id)->where('slug', $slug)->find();
  }
  
  public function find_all_active($site_id = NULL) {
    if ( ! is_null($site_id)) {
      $this->where('site_id', $site_id);
    }
    return $this->where('expires >=', date::to_db())->find_all();
  }
  
  public function url() {
    return sprintf('%s/%s/%s', Kohana::config('app.payrally_url'), $this->site->slug(), $this->slug);
  }
  
  public function picture_url() {
    return sprintf('%sfiles/original/%s', url::base(), $this->picture);
  }

  public function __get($column) {
    if ($column == 'payment_options') {
      $options = parent::__get('payment_options');
      if ( ! is_array($options)) {
        $options = unserialize($options);
      }
      return $options;
    }
    // Cache calculated values like donation total and count per form.
    if (in_array($column, array('campaign_total', 'campaign_count'))) {
      if ($this->{'_'.$column} === FALSE) {
        $this->_campaign_total = 0;
        $this->_campaign_count = 0;
        foreach ($this->campaign_donations as $donation) {
          $this->_campaign_total += $donation->amount;
          $this->_campaign_count++;
        }
      }
      return $this->{'_'.$column};
    }
    return parent::__get($column);
  }
  
  /**
   * Validation.
   */
  public function validate(array &$array, $save = FALSE) {
    $array['goal'] = text::number($array['goal']);
    $array['payment_options'] = serialize($array['payment_options']);
    $array = Validation::factory($array)
    ->pre_filter('trim')
    ->pre_filter(array('date', 'input_to_db'), 'expires')
    ->add_rules('title', 'required')
    ->add_rules('slug', 'required')
    ->add_rules('picture', 'blob')
    ->add_rules('body', 'required')
    ->add_rules('expires', 'date')
    ->add_rules('goal', 'numeric')
    ->add_rules('show_goal', 'numeric')
    ->add_rules('deposit_account_id', 'numeric', 'required')
    ->add_rules('payment_free_entry', 'numeric')
    ->add_callbacks('payment_options', array($this, '_valid_payment_options'))
    ->add_callbacks('deposit_account_id', array($this, '_check_deposit_account_id'));
    return parent::validate($array, $save);
  }
  
  public function _valid_payment_options(Validation $array, $field) {
    $array[$field] = unserialize($array[$field]);
    foreach ($array[$field] as $key => $option) {
      if ($option['value'] && $option['label']) {
        if (( ! is_numeric($option['value'])) || $option['value'] < 0) {
          $array->add_error($field, 'invalid');
        }
        if (strlen($option['label']) < 2) {
          $array->add_error($field, 'invalid');
        }
      }
    }
  }
  
  public function _check_deposit_account_id(Validation $array, $field) {
    if ($array[$field] > 0) {
      $deposit_account = ORM::factory('deposit_account')->where('id', $array[$field])->where('status', 1)->find();
      if ($deposit_account->site_id != kohana::config('chapterboard.site_id')) {
        $array->add_error($field, 'site_id');
      }
    }
    else {
      $site = ORM::factory('site', kohana::config('chapterboard.site_id'));
      if ($site->fundraising_enabled()) {
        $array->add_error($field, 'required');
      }
    }
  }
  
  public function before_insert() {
    $this->created = date::to_db();
    $this->updated = $this->created;
    if ( ! $this->site_id) {
      $this->site_id = kohana::config('chapterboard.site_id');
    }
    $this->_save_payment_options();
  }
  
  public function before_update() {
    $this->updated = date::to_db();
    $this->_save_payment_options();
  }
  
  public function _save_payment_options() {
    if (is_array($this->payment_options)) {
      $options = array();
      foreach ($this->payment_options as $option) {
        if ($option['label'] && $option['value']) {
          $options[] = $option;
        }
      }
    }
    $this->payment_options = serialize($options);
  }
  
  /**
	 * Returns the unique key for a specific value. This method is expected
	 * to be overloaded in models if the model has other unique columns.
	 *
	 * @param   mixed   unique value
	 * @return  string
	 */
	public function unique_key($id) {
		return is_numeric($id) ? $this->primary_key : 'slug';
	}
	
	public function get_resource_id() {
	  return 'campaign';
	}
}