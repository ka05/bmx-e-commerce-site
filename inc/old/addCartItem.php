<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/26/2015
 * Time: 9:25 PM
 */

// decrement count of QOH in BD

header('Content-Type: application/json');
include "inc/page_start.php";

$product_id = $_POST["productID"];
$cart_item_qty = $_POST["cartItemQty"];
session_start();

try {
  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_SESSION['userID'])) {
    // query insert
    //check if the item exists in cart already
    $stmt = $dbh->prepare("select * from cartitem where ProductID = :productID and UserID = :userID");
    $stmt->execute(
      array(
        'productID' => $product_id,
        'userID' => $_SESSION['userID']
      )
    );
    // name exists
    if ($stmt->rowCount() > 0) {
      echo json_error_msg("You already have that item in your cart!");
      // item exists - updateQty

      //need to decrement qty from product table
      //then update quantity in cart

    } else {
      //name doesnt exist yet go ahead with insert
      // query insert
      $stmt = $dbh->prepare("insert into cartitem (ProductID, CartItemQty, UserID) values (:productID, :cartItemQty, :userID)");
      $stmt->execute(
        array(
          'productID' => $product_id,
          'cartItemQty' => $cart_item_qty,
          'userID' => $_SESSION['userID']
        )
      );
      // was inserted
      if ($stmt->rowCount() > 0) {
        echo json_success_msg("Item successfully added to cart!");
      } else {
        //not inserted
        echo json_error_msg("An error occurred, please resubmit!");
      }
    }
  }
  else{
    echo json_error_msg("You must login or create account first!");
  }
} catch (PDOException $e) {
  echo json_error_msg("PDO Error: ". $e->getMessage());
}