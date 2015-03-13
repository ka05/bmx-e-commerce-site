<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/26/2015
 * Time: 9:24 PM
 */

header('Content-Type: application/json');
include "inc/page_start.php";


session_start();
$dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SESSION["userID"]){
  $stmt = $dbh->prepare("delete from cartitem where UserID = :userID");
  $stmt->execute(array('userID'=>$_SESSION["userID"]));

  if($stmt->rowCount() > 0){
    echo json_success_msg("Your cart has successfully been emptied!");
  }
}
else{
  echo json_error_msg("You must be logged in first! if logged in error has occurred!");
}
