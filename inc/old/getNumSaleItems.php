<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/26/2015
 * Time: 11:10 PM
 */
header('Content-Type: application/json');
include "inc/page_start.php";

$dbh = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// query insert
$stmt = $dbh->prepare("select * from product where ProductSalePrice > 0");
$stmt->execute();

echo json_encode('{"numSaleItems":"'. $stmt->rowCount() .'"}');