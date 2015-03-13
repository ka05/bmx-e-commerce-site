<?php

  header('Content-Type: application/json');

  define ('SITE_ROOT', realpath(dirname(__FILE__)));

  $db = true;
  include "inc/page_start.php";

  $MAX_SALE_ITEMS = 5;

  $product_name = $_POST['productName'];
  $product_description = $_POST['productDescription'];
  $product_price = $_POST['productPrice'];
  $product_sale_price = $_POST['productSalePrice'];
  $product_img_src = $_POST['productImgSrc'];
  $product_qty = $_POST['productQty'];
  $confirmation_email = $_POST['confirmationEmail'];
  $email = $_POST['email'];
  $errors = "Please fill in the following:";


  if( empty($product_name)){
    $errors .= "Product Name, ";
  }
  if( empty($product_description)){
    $errors .= "Product Description, ";
  }
  if( empty($product_price)){
    $errors .= "Product Price, ";
  }
  if( empty($product_qty)){
    $errors .= "Product Qty, ";
  }


  if($errors != "Please fill in the following:"){
    $val = rtrim($errors, ",");
    echo json_error_msg("Errors: " .$val);
  }
  else {
    if($product_sale_price && ($product_sale_price > $product_price)){
      echo json_error_msg("You Must enter a sale price that is less than the normal price" );
    }
    else {
      if(isset($_FILES["file"]["type"])){
        $validextensions = array("jpeg", "jpg", "png");
        $temporary = explode(".", $_FILES["file"]["name"]);
        $file_extension = end($temporary);
        if ((($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/jpg") || ($_FILES["file"]["type"] == "image/jpeg")
          ) && ($_FILES["file"]["size"] < 1000000000)//Approx. 100kb files can be uploaded.
          && in_array($file_extension, $validextensions)) {
          if ($_FILES["file"]["error"] > 0){
            echo json_error_msg("Return Code: " . $_FILES["file"]["error"]);
          }
          else{
            if (file_exists("./file/projectimg/" . $_FILES["file"]["name"])) {
              echo json_error_msg($_FILES["file"]["name"] . " already exists. ");
            }
            else{
              $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
              $targetPath = "./file/".basename($_FILES['file']['name']); // Target path where file is to be stored

              if(move_uploaded_file($sourcePath,$targetPath)){
                chmod($targetPath, 0644);

                // try to insert into db
                try {
                  $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
                  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                  // check if the item with the same name exists already
                  // query insert
                  $stmt = $dbh->prepare("select * from product where ProductName = :productName");
                  $stmt->execute(array('productName' => $product_name));
                  // name exists
                  if ($stmt->rowCount() > 0) {
                    echo json_error_msg("A product with that name already exists please use different name!");
                  } else {
                    //name doesnt exist yet (Check number of items on sale currently)
                    $stmt = $dbh->prepare("select * from product");
                    $stmt->execute();

                    $saleCount = 0;
                    $rows = $stmt->fetchAll();
                    foreach ($rows as $row) {
                      if($row["ProductSalePrice"] > 0){
                        // this item has a sale price
                        $saleCount += 1;
                      }
                    }

                    // check if they are trying to insert an item that is on sale
                    if($product_sale_price == "" || $product_sale_price = "0"|| $product_sale_price = 0){
                      // query insert
                      $stmt = $dbh->prepare("insert into product (ProductName, ProductDescription, ProductPrice, ProductSalePrice, ProductImgSrc, ProductQty) values ( :productName, :productDescription, :productPrice, :productSalePrice, :productImgSrc, :productQty)");
                      $stmt->execute(
                        array(
                          'productName' => $product_name,
                          'productDescription' => $product_description,
                          'productPrice' => $product_price,
                          'productSalePrice' => $product_sale_price,
                          'productQty' => $product_qty,
                          'productImgSrc' => $product_img_src
                        )
                      );
                      // was inserted
                      if ($stmt->rowCount() > 0) {
                        echo json_success_msg("Product was successfully added!");
                      } else {
                        //not inserted
                        echo json_error_msg("An error occurred, please resubmit!");
                      }
                    }
                    else{
                      // if num prices is below 5 then allow insert
                      if($saleCount < $MAX_SALE_ITEMS){
                        // query insert
                        $stmt = $dbh->prepare("insert into product (ProductName, ProductDescription, ProductPrice, ProductSalePrice, ProductImgSrc, ProductQty) values ( :productName, :productDescription, :productPrice, :productSalePrice, :productImgSrc, :productQty)");
                        $stmt->execute(
                          array(
                            'productName' => $product_name,
                            'productDescription' => $product_description,
                            'productPrice' => $product_price,
                            'productSalePrice' => $product_sale_price,
                            'productQty' => $product_qty,
                            'productImgSrc' => $product_img_src
                          )
                        );
                        // was inserted
                        if ($stmt->rowCount() > 0) {
                          echo json_success_msg("Product was successfully added!");
                        } else {
                          //not inserted
                          echo json_error_msg("An error occurred, please resubmit!");
                        }
                      }
                      // num items on sale is 5 - cant allow more than 5
                      else{
                        echo json_error_msg("You cant have more than ". $MAX_SALE_ITEMS. " Items on sale! CurrentNumSaleItems =" . $saleCount);
                      }
                    }
                  }
                } catch (PDOException $e) {
                  echo json_error_msg("PDO Error: " . $e->getMessage());
                }
                //end db insert
              }
              else{
                echo json_error_msg("FileName " . $_FILES["file"]["name"] . " was not sucessfully uploaded");
              }
            }
          }
        }
        else{
          echo json_error_msg("***Invalid file Size or Type***");
        }
      }
      else{
        echo json_error_msg("Files array empty");
      }
      // end file upload sect
    }
  }

function checkSaleProducts(){
  return true;
}

?>
