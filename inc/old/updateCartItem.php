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
$product_id = $_POST["productID"];
$cart_item_qty = $_POST["cartItemQty"];

session_start();

try {
  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_SESSION['userID'])) {

    //get current productQty and check that "qty passed in" < productQty

    // update qty in table


    // query update
    $stmt = $dbh->prepare("update cartitem set CartItemQty = :cartItemQty, ProductID = :productID where CartItemID = :cartItemID");
    $stmt->execute(
      array(
        'cartItemQty' => $cart_item_qty,
        'productID' => $product_id,
        'cartItemID' => $cart_item_id
      )
    );
    // was inserted
    if ($stmt->rowCount() > 0) {
      echo json_success_msg("Item successfully updated in cart!");
    } else {
      //not inserted
      echo json_error_msg("An error occurred, please resubmit!");
    }
  }
  else{
    echo json_error_msg("You must login or create account first!");
  }
} catch (PDOException $e) {
  echo json_error_msg("PDO Error: " . $e->getMessage());
}