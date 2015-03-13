<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 3/9/2015
 * Time: 9:52 PM
 */

/*
 * Account: Class that contains serverside functionality for accounts
 *
 * Public Methods:
 *
 * login
 * logout
 * createAccount
 * getPurchasedProducts
 *
 */

class Account {

  /*
   * login: verifies username and password that user
   * entered and provides appropriate error handling
   * then logs the user in and calls "set_login_session()"
   *
   */
  public function login(){
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    // Username and Password
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("select * from users where Username = :username");
    $stmt->execute(array('username'=>$username));

    if($stmt->rowCount()) {
      while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if ($row['UserPass'] == $password) {
          $email = $row['UserEmail'];
          $admin = $row['UserAdminStatus'];
          $this->set_login_session($row["UserID"], $row["Username"], $row['UserAdminStatus']);
          $jsonData = '{ "username":"' . $username . '", "email":"' . $email . '", "admin":"' . $admin . '", "msg":"Welcome ' . $username . '! You are logged in!", "success":"success" }';
          echo json_encode($jsonData);
        } else {
          echo json_error_msg("Incorrect Password! Please Try Again!");
        }
      }
    }
    else{
      echo json_error_msg("please register!");
    }
  }

  /**
   * checkLoggedIn: verifies that the user is logged in
   */
  public function checkLoggedIn(){
    $username = $_POST['username'];
    session_start();

    if($_SESSION["username"] == $username){
      echo json_encode('{ "username":"' . $username . '", "admin":"' . $_SESSION['userAdminStatus'] . '", "msg":"Welcome ' . $username . '! You are logged in!", "success":"success" }');
    }
    else{
      echo json_error_msg("You must log in!");
    }
  }

  /**
   * isAdmin: verifies user is an admin
   * @return bool
   */
  public function isAdmin(){
    session_start();
    if($_SESSION["userAdminStatus"] == "true"){
      return true;
    }
    else{
      return false;
    }
  }

  /*
   * set_login_session: initializes session and stores userID for later use
   *
   * @params:
   *    $user_id = userID
   *    $user_admin_status = If user is an admin or not
   */
  private function set_login_session($user_id, $username, $user_admin_status){
    session_start();
    $_SESSION["userID"] = $user_id;
    $_SESSION["username"] = $username;
    $_SESSION['userAdminStatus'] = $user_admin_status;
  }

  /*
   * logout: logs the user out (ends session and removes session variables)
   */
  public function logout(){
    session_start();
    unset($_SESSION);
    $_SESSION['userID'] = null;
    $_SESSION['userAdminStatus'] = null;
    session_destroy();
    echo json_success_msg("You have sucessfully been logged out!" . $_SESSION['userID']);
  }

  /*
   * createAccount: creates an account in the users table
   * of the DB from POST data passed in from ajax
   */
  public function createAccount(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    // Form processing
    $user_full_name = $_POST['userFullName'];
    $user_email = $_POST['userEmail'];
    $user_address = $_POST['userAddress'];
    $user_city = $_POST['userCity'];
    $user_state = $_POST['userState'];
    $user_zip = $_POST['userZip'];
    $user_phone = $_POST['userPhone'];

    $username = $_POST['username'];
    $user_pass = $_POST['userPass'];
    $user_pass = sha1($user_pass);
    $errors = "Please fill in the following: ";

    // finish error checking***************************************************
    if( empty($username)){
      $errors .= "username, ";
    }
    if( empty($user_pass)){
      $errors .= "password, ";
    }
    if( empty($user_full_name)){
      $errors .= "full name, ";
    }
    if( empty($user_email)){
      $errors .= "email, ";
    }
    if( empty($user_address)){
      $errors .= "address, ";
    }
    if( empty($user_city)){
      $errors .= "city, ";
    }
    if( empty($user_state)){
      $errors .= "state, ";
    }
    if( empty($user_zip)){
      $errors .= "zip, ";
    }


    if($errors == "Please fill in the following: "){
      $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
      $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $stmt = $dbh->prepare("select * from users where Username = :username");
      $stmt->execute(array('username'=>$username));

      if($stmt->rowCount() > 0){
        echo json_error_msg("Account with username: $username already exists!");
      }
      else{
        // insert user
        $stmt = $dbh->prepare("insert into users (UserFullName, UserEmail, Username, UserPass, UserAddress, UserCity, UserState, UserZip, UserPhone) values (:userFullName, :userEmail, :username, :userPass, :userAddress, :userCity, :userState, :userZip, :userPhone)");
        $stmt->execute(array(
          'userFullName'=>$user_full_name,
          'userEmail'=>$user_email,
          'username'=>$username,
          'userPass'=>$user_pass,
          'userAddress'=>$user_address,
          'userCity'=>$user_city,
          'userState'=>$user_state,
          'userZip'=>$user_zip,
          'userPhone'=>$user_phone
        ));

        if($stmt->rowCount() > 0){
          echo json_success_msg($_POST['username'] . " : You have sucessfully registered!");
        }
        else{
          echo json_error_msg("There was an error inserting you into the database");
        }

      }
    }
    else{
      echo json_error_msg("Error: ". rtrim($errors, ","));
    }
  }

  /*
   * getPurchasedProducts: grabs the purchased products from the DB
   * and returns to ajax to be bound to the view with js
   *
   */
  public function getPurchasedProducts(){

    session_start();

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

    $dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $dbh->prepare("select * from purchasedproducts where UserID = :userID");
    $stmt->execute(array("userID"=>$_SESSION['userID']));

    if($stmt->rowCount() > 0){
      $data = $stmt->fetchAll();
      foreach($data as $row){

        // getProduct
        $stmt = $dbh->prepare("select * from product where ProductID = :productID");
        $stmt->execute(array("productID"=>$row['ProductID']));

        // if product exists in product table
        if($stmt->rowCount() > 0){
          $product_result = $stmt->fetch();
          $results[] = array(
            "purchasedProductID" => $row['PurchasedProductID'],
            "purchasedQty" => $row['PurchasedQty'],
            "purchasedDate" => $row['PurchasedDate'],
            "productID" => $row['ProductID'],
            "productName" => $product_result['ProductName'],
            "productDescription" => $product_result['ProductDescription'],
            "productPrice" => $product_result['ProductPrice'],
            "productSalePrice" => $product_result['ProductSalePrice'],
            "productImgSrc" => $product_result['ProductImgSrc']
          );
        }

      }
      $arr = array("purchasedProducts"=>$results, "success"=>"success", "msg"=>"Purchased Products Successfully Retrieved!");
      echo json_encode($arr);
    }
    else{
      echo json_error_msg("You have not purchased anything!");
    }
  }

  /*
   * getProducts: grabs all Products available
   * returns them in JSON array of objects
   */
  public function getProducts(){

    // database vars
    $host = $GLOBALS['db_host'];
    $db = $GLOBALS['db_db'];
    $user = $GLOBALS['db_user'];
    $pass = $GLOBALS['db_pass'];

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
  }

} // end Account Class