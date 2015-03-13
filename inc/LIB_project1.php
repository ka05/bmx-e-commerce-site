<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/22/2015
 * Time: 10:24 PM
 */

header('Content-Type: application/json');
include("page_start.php");

/*
 * LIB_project1.php
 *
 * Rather than including all my application code and logic
 * here i moved my functions to separate php classes and routed
 * to them using the switch() statement in this file
 */

// verifies that the action post variable is set and then choses
// which function to fire based on the POST['action'] value
if(isset($_POST['action'])){

  // instantiate classes to call functions from
  $Cart = new Cart();
  $Account = new Account();
  $Admin = new Admin();

  // handle which function to call
  switch($_POST['action']){
    case "login":
      $Account->login();
      break;
    case "logout":
      $Account->logout();
      break;
    case "createAccount":
      $Account->createAccount();
      break;
    case "getPurchasedProducts":
      $Account->getPurchasedProducts();
      break;
    case "checkLoggedIn":
      $Account->checkLoggedIn();
      break;
    case "getProducts":
      $Account->getProducts();
      break;
    case "getCartItems":
      $Cart->getCartItems();
      break;
    case "addCartItem":
      $Cart->addCartItem();
      break;
    case "deleteCartItem":
      $Cart->deleteCartItem();
      break;
    case "emptyCart":
      $Cart->emptyCart();
      break;
    case "updateCartItem":
      $Cart->updateCartItem();
      break;
    case "checkout":
      $Cart->checkout();
      break;
    case "getProduct":
      if($Admin->validateAdmin()){
        $Admin->getProduct();
      }else{
        echo json_error_msg("You are not an admin");
      }
      break;
    case "addProduct":
      if($Admin->validateAdmin()){
        $Admin->addProduct();
      }else{
        echo json_error_msg("You are not an admin");
      }
      break;
    case "updateProduct":
      if($Admin->validateAdmin()){
        $Admin->updateProduct();
      }else{
        echo json_error_msg("You are not an admin");
      }
      break;
    case "deleteProduct":
      if($Admin->validateAdmin()){
        $Admin->deleteProduct();
      }else{
        echo json_error_msg("You are not an admin");
      }
      break;
    case "getNumSaleItems":
      if($Admin->validateAdmin()){
        $Admin->getNumSaleItems();
      }else{
        echo json_error_msg("You are not an admin");
      }
      break;
  }
}
else{
  echo json_error_msg("Forgot Post['action'] in call" . $_POST['userFullName']);
}

?>