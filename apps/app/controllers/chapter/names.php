<?php defined('SYSPATH') or die('No direct script access.');

class Names_Controller extends Private_Controller {
  
  // Should not be run in a production environment.
	const ALLOW_PRODUCTION = FALSE;
  
  public function index() {
    
    $names = $this->_names();
    // $users = ORM::factory('user')->where('site_id', 2)->find_all();
    $users = ORM::factory('recruit')->where('site_id', 2)->find_all();
    foreach ($users as $user) {
      $keys = array_rand($names, 2);
      $user->name = $names[$keys[0]] .' '. $names[$keys[1]];
      // $user->last_name = $names[$keys[1]];
      $user->save();
    }
    url::redirect('');
    
  }
  
  public function _names() {
    return array(
      'Daley',
      'Dallas',
      'Dallin',
      'Dalton',
      'Daly',
      'Dalziel',
      'Damek',
      'Damen',
      'Damian',
      'Damien',
      'Damodar',
      'Damon',
      'Dan',
      'Dana',
      'Danby',
      'Dane',
      'Daniel',
      'Danior',
      'Dannie',
      'Palani',
      'Pallav',
      'Palmer',
      'Palti',
      'Pan',
      'Pancho',
      'Pancras',
      'Pancrazio',
      'Pandarus',
      'Pandita',
      'Pandya',
      'Eddy',
      'Eden',
      'Edgar',
      'Edgardo',
      'Edison',
      'Boyet',
      'Brabantio',
      'Brad',
      'Braden',
      'Bradford',
      'Bradley',
      'Tarquin',
      'Tarrant',
      'Tarun',
      'Tas',
      'Tashi',
      'Tate',
      'Tathal',
      'Tathan',
      'Tatum',
      'Taurin',
      'Taurinus',
      'Taurus',
      'Tavis',
      'Tavish',
      'Tawhiri',
      'Taylor',
      'Teague',
      'Teal',
      'Tean',
      'Miles',
      'Milford',
      'Milind',
      'Matai',
      'Matanga',
      'Matareka',
      'Matari',
      'Mather',
      'Matt',
      'Matthew',
      'Michelangelo',
      'Mick',
      'Mickey',
      'Midas',
      'Miguel',
      'Mihaly',
      'Mihir',
      'Mikael',
      'Mike',
    );
  }
}