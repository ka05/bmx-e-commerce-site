<?php
  header('Content-Type: application/json');
  include "inc/page_start.php";

  try{
    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // query insert
    $stmt = $dbh->prepare("select ProductID, ProductName, ProductDescription, ProductPrice, ProductSalePrice, ProductQty, ProductImgSrc from product");
    $stmt->execute();
    if($stmt->rowCount() > 0){
      $data = $stmt->fetchAll();
      foreach($data as $row){
        $results[] = array(
          "productID" => $row['ProductID'],
          "productName" => $row['ProductName'],
          "productDescription" => $row['ProductDescription'],
          "productPrice" => $row['ProductPrice'],
          "productSalePrice" => $row['ProductSalePrice'],
          "productQty" => $row['ProductQty'],
          "productImgSrc" => $row['ProductImgSrc']
        );
      }
      echo json_encode($results);
    }
    else{
      echo json_error_msg("An error occurred, please resubmit!");
    }
  } catch (PDOException $e){
    echo json_error_msg("PDO Error: ". $e->getMessage());
  }



?>
