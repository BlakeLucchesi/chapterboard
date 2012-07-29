<?php defined('SYSPATH') or die('No direct script access.');

class Collections_Controller extends Finances_Controller {

  public function _pre_controller() {
    if (Router::$method == 'index' && ! $this->site->parson_bishop) {
      url::redirect('finances/collections/intro');
    }
    $this->sidebar = View::factory('finances/collections/_sidebar-menu');
  }
  
  /**
   * Information for chapters who have not signed up for ParsonBishop.
   */
  public function index() {
    $this->title = 'Debt Collection';
    $this->members = ORM::factory('finance_charge_member')->balances();
  }
  
  /**
   * Parson Bishop signup form.
   */
  public function signup() {
    $this->title = 'Enroll in Debt Collections Service';
    if ($post = $this->input->post()) {
      message::add(TRUE, 'Thank you for signing up for debt collection services.  You will be contacted directly by a Parson-Bishop representative and provided with further details on how you can begin collecting your delinquent accounts.');
    }
  }
  
  /**
   * Static content.
   */
  public function intro() {
    $this->title = 'Introduction to Collection Services';
  }
  public function explanation() {
    $this->title = 'Explanation of Collection Services';
  }
  public function concerns() {
    $this->title = 'Common Concerns';
  }
  public function suggestions() {
    $this->title = 'Suggestions to Improve Collections';
  }
  public function testimonials() {
    $this->title = 'Parson-Bishop Testimonials';
  }
  
}