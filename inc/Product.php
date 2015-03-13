<?php
/**
 * Created by PhpStorm.
 * User: Clay
 * Date: 2/23/2015
 * Time: 6:45 PM
 */



class Product {
  private $ProductID,
    $ProductName,
    $ProductDescription,
    $ProductPrice,
    $ProductSalePrice,
    $ProductImgSrc,
    $ProductQty;

  public function getProductID(){
    return $this->ProductID;
  }
  public function getProductName(){
    return $this->ProductName;
  }
  public function getProductDescription(){
    return $this->ProductDescription;
  }
  public function getProductPrice(){
    return $this->ProductPrice;
  }
  public function getProductSalePrice(){
    return $this->ProductSalePrice;
  }
  public function getProductImgSrc(){
    return $this->ProductImgSrc;
  }
  public function getProductQty(){
    return $this->ProductQty;
  }
}