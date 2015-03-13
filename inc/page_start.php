<?php

  // Initialize everything I'll need for the page
  // Include any PHP function libraries or classes
  include("/home/cjh3387/Sites/341/db_conn.inc.php"); // had to use absolute path

  // DB globals for connection
  $host = $GLOBALS['db_host'];
  $user = $GLOBALS['db_user'];
  $pass = $GLOBALS['db_pass'];
  $db = $GLOBALS['db_db'];

  include("Cart.class.php");
  include("Account.class.php");
  include("Admin.class.php");

  /*
   * json_error_msg: simple error message function that
   * returns JSON object to be handled in success function of ajax
   *
   * @params: $msg = message to be sent to client
   */
  function json_error_msg($msg){
    return json_encode('{ "msg":"Error: ' . $msg . '", "success":"error" }');
  }

  /*
   * json_success_msg: simple success message function that
   * returns JSON object to be handled in success function of ajax
   *
   * @params: $msg = message to be sent to client
   */
  function json_success_msg($msg){
    return json_encode('{ "msg":"Success: ' . $msg . '", "success":"success" }');
  }

  /*
   * sanitize: handles cleaning all data from form inputs from user
   */
  function sanitize($input){
    $input = strip_tags($input);
    $input = htmlspecialchars($input);
    $input = trim($input);
    return $input;
  }

?>
