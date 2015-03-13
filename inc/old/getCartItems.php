<?php
header('Content-Type: application/json');
include "inc/page_start.php";

try{
  session_start();
  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


  if(isset($_SESSION["userID"])) {
    // get all cart items for given user
    $stmt = $dbh->prepare("select CartItemID, CartItemQty, ProductID, ProductAddedDate from cartitem where UserID = :userID");
    $stmt->execute(array('userID' => $_SESSION["userID"]));
    if ($stmt->rowCount() > 0) {
      $data = $stmt->fetchAll();
      foreach ($data as $row) {
        // get product info for corresponding cart item
        $stmt = $dbh->prepare("select ProductID, ProductName, ProductDescription, ProductPrice, ProductSalePrice, ProductQty, ProductImgSrc from product where ProductID = :productID");
        $stmt->execute(array('productID' => $row['ProductID']));

        if ($stmt->rowCount() > 0) {
          $product_result = $stmt->fetch();
          $productID = $product_result['ProductID'];
          $productName = $product_result['ProductName'];
          $productDescription = $product_result['ProductDescription'];
          $productPrice = $product_result['ProductPrice'];
          $productSalePrice = $product_result['ProductSalePrice'];
          $productQty = $product_result['ProductQty'];
          $productImgSrc = $product_result['ProductImgSrc'];
        }

        $results[] = array(
          "cartItemID" => $row['CartItemID'],
          "cartItemQty" => $row['CartItemQty'],
          "productID" => $row['ProductID'],
          "productID" => $productID,
          "productName" => $productName,
          "productDescription" => $productDescription,
          "productPrice" => $productPrice,
          "productSalePrice" => $productSalePrice,
          "productQty" => $productQty,
          "productImgSrc" => $productImgSrc,
          "productAddedDate" => $row['ProductAddedDate']
        );
      }

      //create array to hold
      //array above with results and then the cartItem count
      $arr = array("cartItems"=>$results, "success"=>"success", "msg"=>"cart items retrieved");
      echo json_encode($arr);
    } else {
      echo json_error_msg("Cart is Empty!");
    }
  }
  else{
    echo json_error_msg("You must be logged in first!");
  }
} catch (PDOException $e){
  echo json_error_msg("PDO Error: ". $e->getMessage());
}

?>
