<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 3/9/2015
 * Time: 9:29 PM
 */

/*
 * Cart: Class to handle all functions and DB calls
 * pertaining to the Cart
 */

class Cart {

  /*
   * getCartItems: grabs all items that are in users cart
   * where user is the user logged in and stored in session
   */
  public function getCartItems(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

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
  } // end getCartItems


  /*
   * addCartItem: adds a product to cart using the
   * post values ( productID and cartItemQty ) passed in from ajax
   */
  public function addCartItem(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    // Post Vars
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
  }

  /*
   * deleteCartItem: removes an item from the cart given
   * (cartItemID) in POST from ajax call
   */
  public function deleteCartItem(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

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
  }

  /*
   * emptyCart: removes all items from the users cart
   * uses the userID stored in session to perform query
   */
  public function emptyCart(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

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
  }

  /*
   * updateCartItem: updates the cartItemQty for a
   * given item in the users cart.
   * Uses (cartItemID, productID, cartItemQty) POST
   * variables from ajax
   */
  public function updateCartItem(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    // post vars
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
  }

  /*
   * checkout: will decrement qty of products in DB and add
   * all other info into purchasedproducts table
   * uses (productID, cartItemQty)
   *
   * Handles only one item at a time.
   * (Looped through all items client side in JS)
   */
  public function checkout(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $product_id = $_POST['productID'];
    $cart_item_qty =  $_POST['cartItemQty'];

    session_start();

    // user is logged in
    if (isset($_SESSION['userID'])) {

      // get current qty for give product item
      $curr_qty = $this->getCurrentQty($product_id);

      $newQty = $curr_qty - $cart_item_qty;

      // ensure new qty does not go negative
      if($newQty >= 0){
        // update product
        $update_product_success = $this->updateProductQtyInDB($product_id, $newQty);

        $insert_product_success = $this->insertPurchasedProduct($product_id, $cart_item_qty);

        if($update_product_success == "success" && $update_product_success == "success"){
          echo json_success_msg("Checkout Successful");
        }else{
          echo json_error_msg("Checkout NOT Successful");
        }

      }
      else{
        echo json_error_msg(" i dont have that many");
      }

    }else{
      echo json_error_msg("You must login or create account first!");
    }
  } // end checkout

  /*
   * getCurrentQty: Grabs the qty of a product from the "product" table
   * given (ProductID) POST var from ajax
   *
   * @params: $product_id = product id from ajax call
   */
  private function getCurrentQty($product_id){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("select ProductQty from product where ProductID = :productID");
    $stmt->execute(
      array(
        'productID' => $product_id
      )
    );

    $product_result = $stmt->fetch();
    return $product_result["ProductQty"];
  }

  /*
   * updateProductQtyInDB: updates the qty for a given product
   * using (productID, newProductID)
   *
   * @params:
   *    $product_id = product id
   *    $new_product_qty = updated qty for product
   */
  private function updateProductQtyInDB($product_id, $new_product_qty){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("update product set ProductQty = :productQty where ProductID = :productID");
    $stmt->execute(
      array(
        'productID' => $product_id,
        'productQty'=> $new_product_qty
      )
    );

    // was updateds
    if ($stmt->rowCount() > 0) {
      return "success";
    } else {
      //not inserted
      return "failure";
    }
  }


  /*
   * insertPurchasedProduct: inserts a product that the user purchased
   * into the purchasedproduct table
   *
   * @params:
   *    $product_id = product id
   *    $purchased_qty = qty of product the user purchased
   */
  private function insertPurchasedProduct($product_id, $purchased_qty){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    session_start();

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("insert into purchasedproducts (UserID, ProductID, PurchasedQty) values ( :userID, :productID, :purchasedQty)");
    $stmt->execute(
      array(
        'productID' => $product_id,
        'purchasedQty' => $purchased_qty,
        'userID'=>$_SESSION['userID']
      )
    );


    // was inserted
    if ($stmt->rowCount() > 0) {
      return "success";
    } else {
      //not inserted
      return "failure";
    }
  }

} // end Cart Class