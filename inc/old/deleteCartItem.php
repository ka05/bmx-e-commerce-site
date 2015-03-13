<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/26/2015
 * Time: 9:25 PM
 */
header('Content-Type: application/json');
include "inc/page_start.php";

$cart_item_id = $_POST["cartItemID"];
session_start();

try {
  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_SESSION['userID'])) {
    // query insert
    //check if the item exists in cart already
    $stmt = $dbh->prepare("delete from cartitem where CartItemID = :cartItemID");
    $stmt->execute(
      array(
        'cartItemID' => $cart_item_id
      )
    );
    echo json_success_msg("Your product has been removed from the cart!");
  }
  else{
    echo json_error_msg("You must login or create account first!");
  }
} catch (PDOException $e) {
  echo  json_error_msg("PDO Error: " . $e->getMessage());
}