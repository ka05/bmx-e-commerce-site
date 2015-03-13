<?php

  header('Content-Type: application/json');
  include "inc/page_start.php";

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

?>
