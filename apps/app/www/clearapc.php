<?php

// Make sure the request is coming from one of our server's ip addresses.
if (stripos(@$_SERVER['REMOTE_ADDR'], '69.167.161.') === FALSE) {
  echo json_encode(array('success' => FALSE, 'message' => 'Access denied. Could not clear APC cache.'));
}
else {
  apc_clear_cache();
  apc_clear_cache('user');
  apc_clear_cache('opcode');
  echo json_encode(array('success' => TRUE, 'message' => 'APC Cache cleared successfully.'));
}