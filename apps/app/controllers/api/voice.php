<?php defined('SYSPATH') or die('No direct script access.');

class Voice_Controller extends Api_Controller {
  
  public function receive() {
    header("content-type: text/xml");
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n
    <Response>
        <Dial>949-525-4432</Dial>
    </Response>";
    die();
  }
  
}