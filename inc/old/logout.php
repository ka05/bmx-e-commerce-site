<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/26/2015
 * Time: 10:31 PM
 */

// empty session array
unset($_SESSION);

session_destroy();

echo json_success_msg("You have sucessfully been logged out!");

?>