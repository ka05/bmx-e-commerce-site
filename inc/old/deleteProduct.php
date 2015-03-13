<?php
header('Content-Type: application/json');
include "inc/page_start.php";

try{
  $MIN_SALE_ITEMS = 3; // minimum num of items to be on sale

  $product_id = $_POST["selectedProductID"];

  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $dbh->prepare("select * from product");
  $stmt->execute();

  $saleCount = 0;
  $rows = $stmt->fetchAll();
  foreach ($rows as $row) {
    if($row["ProductSalePrice"] > 0){
      // this item has a sale price
      $saleCount .= 1;
    }
  }

  if($saleCount > $MIN_SALE_ITEMS){
    // query insert
    $stmt = $dbh->prepare("delete from product where ProductID = :id");
    $stmt->execute(array("id"=>$product_id));
    if($stmt->rowCount() > 0){
      echo json_success_msg("Product Successfully Deleted!");
    }
    else{
      echo json_error_msg("An error occurred, please resubmit!");
    }
  }
  else{
    echo json_error_msg("You cant have less than ". $MIN_SALE_ITEMS." Items on sale!");
  }

} catch (PDOException $e){
  echo json_error_msg("PDO Error: ". $e->getMessage());
}



?>
