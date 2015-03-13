<?php
header('Content-Type: application/json');
include "inc/page_start.php";

// make code under if(file) a function and call it to reduce code

//if($_FILES["file"]) {
//  if (getSalePrice() > 0) {
//    // this means the product we are trying to edit is currently on sale
//    // therefore we must check if the user is trying to change the sale price to 0
//    // if so we must then check if the "total number of items on sale" is > 3 otherwise they cannot be allowed to update the product.
//
//    // first detect if they are trying to change saleprice
//    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
//      // if so get count
//      if($numItemsOnSale > 3){
//        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
//        updateProduct(true);
//      }
//      else{
//        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
//      }
//    }
//    // they are providing a sale price -> meaning that they want to make this item on sale
//    else{
//      if($numItemsOnSale < 5){
//        // the number of items on sale currently is < 5
//        // so we can allow them to make this item on sale -> which then increases the sale count when completed
//        // query insert
//        updateProduct(true); // hasimage = true
//      }
//      else{
//        // num items on sale is 5 so we cannot allow them to add another item on sale
//        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
//      }
//    }
//  } else {
//    // the product we are trying to edit is not on sale
//    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
//      // if so get count
//      if($numItemsOnSale > 3){
//        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
//        updateProduct(true);
//      }
//      else{
//        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
//      }
//    }
//    // they are providing a sale price -> meaning that they want to make this item on sale
//    else{
//      if($numItemsOnSale < 5){
//        // the number of items on sale currently is < 5
//        // so we can allow them to make this item on sale -> which then increases the sale count when completed
//        // query insert
//        updateProduct(true); // hasimage = true
//      }
//      else{
//        // num items on sale is 5 so we cannot allow them to add another item on sale
//        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
//      }
//    }
//  }
//}else{
//  //no file for image being altered here
//
//  // need to figure out what the previous sale price for this update is
//  if (getSalePrice() > 0) {
//    // this means the product we are trying to edit is currently on sale
//    // therefore we must check if the user is trying to change the sale price to 0
//    // if so we must then check if the "total number of items on sale" is > 3 otherwise they cannot be allowed to update the product.
//
//    // first detect if they are trying to change saleprice
//    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
//      // if so get count
//      if($numItemsOnSale > 3){
//        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
//        updateProduct(false);
//      }
//      else{
//        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
//      }
//    }
//    // they are providing a sale price -> meaning that they want to make this item on sale
//    else{
//      if($numItemsOnSale < 5){
//        // the number of items on sale currently is < 5
//        // so we can allow them to make this item on sale -> which then increases the sale count when completed
//        // query insert
//        updateProduct(false);
//      }
//      else{
//        // num items on sale is 5 so we cannot allow them to add another item on sale
//        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
//      }
//    }
//  } else {
//    // the product we are trying to edit is not on sale
//    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
//      // if so get count
//      if($numItemsOnSale > 3){
//        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
//        updateProduct(false);
//      }
//      else{
//        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
//      }
//    }
//    // they are providing a sale price -> meaning that they want to make this item on sale
//    else{
//      if($numItemsOnSale < 5){
//        // the number of items on sale currently is < 5
//        // so we can allow them to make this item on sale -> which then increases the sale count when completed
//        // query insert
//        updateProduct(false); // hasimage = true
//      }
//      else{
//        // num items on sale is 5 so we cannot allow them to add another item on sale
//        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
//      }
//    }
//  }
//}

if($_FILES["file"]) {
  handleUpdateProduct(true);
}else{
  handleUpdateProduct(false);
}


function handleUpdateProduct($updateProductHasImageBool){

  $product_id = $_POST["productID"];
  $product_name = $_POST['productName'];
  $product_description = $_POST['productDescription'];
  $product_price = $_POST['productPrice'];
  $product_sale_price = $_POST['productSalePrice'];
  $product_qty = $_POST['productQty'];
  $product_img_src = $_POST['productImgSrc'];

  $MAX_SALE_ITEMS = 5;
  $MIM_SALE_ITEMS = 3;

  $numItemsOnSale = getSaleItemCount();

  // need to figure out what the previous sale price for this update is
  if (getSalePrice() > 0) {
    // this means the product we are trying to edit is currently on sale
    // therefore we must check if the user is trying to change the sale price to 0
    // if so we must then check if the "total number of items on sale" is > 3 otherwise they cannot be allowed to update the product.

    // first detect if they are trying to change saleprice
    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
      // if so get count
      if($numItemsOnSale > 3){
        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
        updateProduct($updateProductHasImageBool);
      }
      else{
        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
      }
    }
    // they are providing a sale price -> meaning that they want to make this item on sale
    else{
      if($numItemsOnSale < 5){
        // the number of items on sale currently is < 5
        // so we can allow them to make this item on sale -> which then increases the sale count when completed
        // query insert
        updateProduct($updateProductHasImageBool);
      }
      else{
        // num items on sale is 5 so we cannot allow them to add another item on sale
        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
      }
    }
  } else {
    // the product we are trying to edit is not on sale
    if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
      // if so get count
      if($numItemsOnSale > 3){
        // number of items currently on sale is above 3 -> we can allow them to remove the sale price
        updateProduct($updateProductHasImageBool);
      }
      else{
        echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than" . $MIM_SALE_ITEMS);
      }
    }
    // they are providing a sale price -> meaning that they want to make this item on sale
    else{
      if($numItemsOnSale < 5){
        // the number of items on sale currently is < 5
        // so we can allow them to make this item on sale -> which then increases the sale count when completed
        // query insert
        updateProduct($updateProductHasImageBool); // hasimage = true
      }
      else{
        // num items on sale is 5 so we cannot allow them to add another item on sale
        echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than" . $MAX_SALE_ITEMS);
      }
    }
  }
}


function updateProduct($hasImage){
  $host = $GLOBALS['db_host'];
  $db = $GLOBALS['db_db'];
  $user = $GLOBALS['db_user'];
  $pass = $GLOBALS['db_pass'];
  try {
    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($hasImage){
      $stmt = $dbh->prepare("update product set ProductName = :productName, ProductDescription = :productDescription, ProductPrice = :productPrice, ProductSalePrice = :productSalePrice, ProductQty = :productQty, ProductImgSrc = :productImgSrc where ProductID = :id");
      $stmt->execute(array(
        "id" => $_POST["productID"],
        "productName" => $_POST["productName"],
        "productDescription" => $_POST["productDescription"],
        "productPrice" => $_POST["productPrice"],
        "productSalePrice" => $_POST["productSalePrice"],
        "productQty" => $_POST["productQty"],
        "productImgSrc" => $_POST["productImgSrc"]
      ));
    }
    else{
      $stmt = $dbh->prepare("update product set ProductName = :productName, ProductDescription = :productDescription, ProductPrice = :productPrice, ProductSalePrice = :productSalePrice, ProductQty = :productQty where ProductID = :id");
      $stmt->execute(array(
        "id" => $_POST["productID"],
        "productName" => $_POST["productName"],
        "productDescription" => $_POST["productDescription"],
        "productPrice" => $_POST["productPrice"],
        "productSalePrice" => $_POST["productSalePrice"],
        "productQty" => $_POST["productQty"]
      ));
    }


    if ($stmt->rowCount() > 0) {
      //was updated in db. go ahead and upload image
      if($hasImage) {
        uploadImg();
      }
      else{
        echo json_success_msg("You successfully updated the product!");
      }
    } else {
      // wasnt updated
      echo json_error_msg("ProductID: ". $_POST["productID"] ." Rows affected: ". $stmt->rowCount());
    }
  }catch (PDOException $e) {
    echo json_error_msg("PDO Error: " . $e->getMessage());
  }
}

//Returns Sale price of prodcutID sent in from server
function getSalePrice(){
  $host = $GLOBALS['db_host'];
  $db = $GLOBALS['db_db'];
  $user = $GLOBALS['db_user'];
  $pass = $GLOBALS['db_pass'];

  try {
    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // need to figure out what the previous sale price for this update is
    $stmt = $dbh->prepare("select * from product where ProductID = :id");
    $stmt->execute(array("id" => $_POST["produtID"]));
    // the result of the product with the product id we are trying to edit
    $row = $stmt->fetch();
    return $row["ProductSalePrice"];
  }catch (PDOException $e) {
    echo json_error_msg("PDO Error: ". $e->getMessage());
  }
}

function getSaleItemCount(){
  $host = $GLOBALS['db_host'];
  $db = $GLOBALS['db_db'];
  $user = $GLOBALS['db_user'];
  $pass = $GLOBALS['db_pass'];
  try {
    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("select * from product");
    $stmt->execute();

    $saleCount = 0;
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
      if ($row["ProductSalePrice"] > 0) {
        // this item has a sale price
        $saleCount += 1;
      }
    }
    return $saleCount;

  }catch (PDOException $e) {
    echo json_error_msg("PDO Error: ". $e->getMessage());
  }
}

function uploadImg(){
  if(isset($_FILES["file"]["type"])){
    $validextensions = array("jpeg", "jpg", "png");
    $temporary = explode(".", $_FILES["file"]["name"]);
    $file_extension = end($temporary);
    if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
      ) && ($_FILES["file"]["size"] < 1000000000)//Approx. 100kb files can be uploaded.
      && in_array($file_extension, $validextensions)) {
      if ($_FILES["file"]["error"] > 0){
        echo  json_error_msg("Return Code: " . $_FILES["file"]["error"]);
      }
      else{
        if (file_exists("./file/projectimg/" . $_FILES["file"]["name"])) {
          echo  json_error_msg($_FILES["file"]["name"] . " already exists. ");
        }
        else{
          $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
          $targetPath = "./file/".basename($_FILES['file']['name']); // Target path where file is to be stored

          if(move_uploaded_file($sourcePath,$targetPath)){
            chmod($targetPath, 0644);
            echo json_success_msg("Image Uploaded and Product Added");
          }
          else{
            echo json_error_msg("FileName ". $_FILES["file"]["name"]);
          }
        }
      }
    }
    else{
      echo  json_error_msg("***Invalid file Size or Type***");
    }
  }
  else{
    echo  json_error_msg("Files array empty");
  }
}


?>
