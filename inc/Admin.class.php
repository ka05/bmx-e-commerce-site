<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 3/9/2015
 * Time: 10:00 PM
 */

/*
 * Admin: Class to handle all encounters and database calls for admin's
 *
 */

class Admin {

  /**
   * validateAdmin: calls isAdmin() from Account class to validate that the user is an admin
   * @return bool
   */
  public function validateAdmin(){
    $Account = new Account();
    return $Account->isAdmin();
  }

  /*
   * getProduct: gets a specific Product from product table
   * given a productID from POST array
   *
   */
  public function getProduct(){

    //*************************
    // and admin session check before all calls
    //*************************


    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    try{
      $product_id = $_POST["selectedProductID"];
      $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // query insert
      $stmt = $dbh->prepare("select ProductID, ProductName, ProductDescription, ProductPrice, ProductSalePrice, ProductQty, ProductImgSrc from product where ProductID = :id");
      $stmt->execute(array("id"=>$product_id));
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
  }



  /*
   * addProduct: adds a product to the DB
   * cleanses inputs from user and handles
   * errors and logic to avoid exceeding 5 on sale items
   */
  public function addProduct(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    // max number of items on sale
    $MAX_SALE_ITEMS = 5;

    $product_name = sanitize($_POST['productName']);
    $product_description = sanitize($_POST['productDescription']);
    $product_price = sanitize($_POST['productPrice']);
    $product_sale_price = sanitize($_POST['productSalePrice']);
    $product_img_src = sanitize($_POST['productImgSrc']);
    $product_qty = sanitize($_POST['productQty']);
    $confirmation_email = sanitize($_POST['confirmationEmail']);
    $email = sanitize($_POST['email']);
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
        if(isset($_FILES["file"])){
          $this->insertProductIntoDB(true);

        } // end if (file)
        else{
          $this->insertProductIntoDB(false);
        }
        // end file upload sect
      }
    }

  } // end addProduct

  /**
   * insertProductIntoDB: inserts product into DB
   * @param $hasImage
   */
  private function insertProductIntoDB($hasImage){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $product_name = sanitize($_POST['productName']);
    $product_description = sanitize($_POST['productDescription']);
    $product_price = sanitize($_POST['productPrice']);
    $product_sale_price = sanitize($_POST['productSalePrice']);
    $product_img_src = sanitize($_POST['productImgSrc']);
    $product_qty = sanitize($_POST['productQty']);
    $confirmation_email = sanitize($_POST['confirmationEmail']);
    $email = sanitize($_POST['email']);

    $MAX_SALE_ITEMS = 5;

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
        if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
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
            if($hasImage == true) {
              $this->uploadImg(); // upload corresponding image
            }
            else{
              echo json_success_msg("Product was successfully added! saleprice" . $product_sale_price);
            }
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
              if($hasImage == true) {
                $this->uploadImg(); // upload corresponding image
              }else{
                echo json_success_msg("Product was successfully added! saleprice" . $product_sale_price);
              }
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
    }//end db insert
  }

  /*
   * deleteProduct: remove product from DB
   */
  public function deleteProduct(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

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
  } // end deleteProduct

  /*
   * updateProduct: handles checking files for updating product
   */
  public function updateProduct(){
    if($_FILES["file"]) {
      $this->handleUpdateProduct(true);
    }else{
      $this->handleUpdateProduct(false);
    }
  }

  /*
   * handleUpdateProduct:
   * @params: $updateProductHasImageBool = if product being updated has image being uploaded as well
   */
  private function handleUpdateProduct($updateProductHasImageBool){

    $product_id = sanitize($_POST["productID"]);
    $product_name = sanitize($_POST['productName']);
    $product_description = sanitize($_POST['productDescription']);
    $product_price = sanitize($_POST['productPrice']);
    $product_sale_price = sanitize($_POST['productSalePrice']);
    $product_qty = sanitize($_POST['productQty']);
    $product_img_src = sanitize($_POST['productImgSrc']);

    $MAX_SALE_ITEMS = 5;
    $MIM_SALE_ITEMS = 3;

    $numItemsOnSale = $this->getSaleItemCount();

    // need to figure out what the previous sale price for this update is
    if ($this->getSalePrice() > 0) {
      // this means the product we are trying to edit is currently on sale
      // therefore we must check if the user is trying to change the sale price to 0
      // if so we must then check if the "total number of items on sale" is > 3 otherwise they cannot be allowed to update the product.

      // first detect if they are trying to change saleprice
      if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
        // if so get count
        if($numItemsOnSale > 3){
          // number of items currently on sale is above 3 -> we can allow them to remove the sale price
          $this->updateProductInDB($updateProductHasImageBool);
        }
        else{
          echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than: " . $MIM_SALE_ITEMS);
        }
      }
      // they are providing a sale price -> meaning that they want to make this item on sale
      else{
        // this is checking for less than 6 because the item we are
        // currently editing is the 5th item in the total of 5 since it has a saleprice > 0
        if($numItemsOnSale < 6){
          // the number of items on sale currently is < 5
          // so we can allow them to make this item on sale -> which then increases the sale count when completed
          // query insert
          $this->updateProductInDB($updateProductHasImageBool);
        }
        else{
          // num items on sale is 5 so we cannot allow them to add another item on sale
          echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than: " . $MAX_SALE_ITEMS);
        }
      }
    } else {
      // the product we are trying to edit is not on sale
      if($product_sale_price == "" || $product_sale_price == "0"|| $product_sale_price == 0){
        // if so get count
        if($numItemsOnSale > 3){
          // number of items currently on sale is above 3 -> we can allow them to remove the sale price
          $this->updateProductInDB($updateProductHasImageBool);
        }
        else{
          echo json_error_msg("Error: You have " .$numItemsOnSale." Items on sale, you cannot have less than: " . $MIM_SALE_ITEMS);
        }
      }
      // they are providing a sale price -> meaning that they want to make this item on sale
      else{
        if($numItemsOnSale < 5){
          // the number of items on sale currently is < 5
          // so we can allow them to make this item on sale -> which then increases the sale count when completed
          // query insert
          $this->updateProductInDB($updateProductHasImageBool); // hasimage = true
        }
        else{
          // num items on sale is 5 so we cannot allow them to add another item on sale
          echo json_error_msg("Error: You already have " .$numItemsOnSale." Items on sale, you cannot have more than: " . $MAX_SALE_ITEMS);
        }
      }
    }
  }

  /*
   * updateProductInDB: actually updates product in DB if
   * all validation passed in previous functions
   */
  private function updateProductInDB($hasImage){
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
          $this->uploadImg();
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

  /*
   * getSalePrice: Returns Sale price of prodcutID sent in from server
   */
  private function getSalePrice(){
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    try {
      $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // need to figure out what the previous sale price for this update is
      $stmt = $dbh->prepare("select * from product where ProductID = :id");
      $stmt->execute(array("id" => $_POST["productID"]));
      // the result of the product with the product id we are trying to edit
      $row = $stmt->fetch();
      return $row["ProductSalePrice"];
    }catch (PDOException $e) {
      echo json_error_msg("PDO Error: ". $e->getMessage());
    }
  }

  /*
   * getSaleItemCount: returns the number of items on sale
   */
  private function getSaleItemCount(){
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

  /*
   * uploadImg: uploads the given image from POST[file]
   * provided all validation passes
   */
  private function uploadImg(){
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
          if (file_exists(".././file/projectimg/" . $_FILES["file"]["name"])) {
            echo  json_error_msg($_FILES["file"]["name"] . " already exists. ");
          }
          else{
            $sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
            $targetPath = ".././file/".basename($_FILES['file']['name']); // Target path where file is to be stored

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


  /*
   * getNumSaleItems: returns the number of items on sale
   */
  public function getNumSaleItems(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // query insert
    $stmt = $dbh->prepare("select * from product where ProductSalePrice > 0");
    $stmt->execute();

    echo json_encode('{"numSaleItems":"'. $stmt->rowCount() .'", "success":"success"}');
  }

} // end Admin class