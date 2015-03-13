<?php
  header('Content-Type: application/json');
  include "inc/page_start.php";

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
        set_login_session($row["UserID"]);
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


function set_login_session($user_id){
  session_start();
  $_SESSION["userID"] = $user_id;
}
?>
