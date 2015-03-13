/**
 * Created by Clay on 3/5/2015.
 *
 * coreData: Module containing data structures and arrays of data structures
 * Contains data structures used in app.js (the main module)
 */
define('coreData', ['jquery', 'knockout', 'bootstrap'], function ( $, ko) {
  var self = coreData = {},
    products = ko.observableArray(), // array of Products
    onSaleProducts = ko.observableArray(), // array of Products with (productSalePrice > 0)
    notOnSaleProducts = ko.observableArray(), // array of Products with (productSalePrice = 0)
    cartItems = ko.observableArray(), // array of CartItems
    purchasedProducts = ko.observableArray(); // array of CartItems

  /*
   * Account: Object for account variables
   *
   * @params: _account = account object ( stored in local storage
   */
  self.Account = function(_postObj){
    this.username = _postObj.username;
    this.email = _postObj.email;
    this.admin = _postObj.admin;
  };

  /*
   * Product: Product Object
   * @params: _postObj = object coming from database
   */
  self.Product = function(_postObj){
    this.productID = _postObj.productID;
    this.productName = _postObj.productName;
    this.productDescription = _postObj.productDescription;
    this.productPrice = _postObj.productPrice;
    this.productSalePrice = _postObj.productSalePrice;
    this.productQty = _postObj.productQty;
    var productQtyOptions = ko.observableArray();
    if(_postObj.productQty > 0){
      for(var i = 1; i <= _postObj.productQty; i++){
        productQtyOptions.push({"value": i});
      }
    }
    else{
      productQtyOptions.push({"value":"Out of Stock"});
    }
    this.productQtyOptions = productQtyOptions;
    this.productImgSrc = _postObj.productImgSrc;
    if(_postObj.productSalePrice > 0){
      this.productOnSale = "on sale";
    }else{
      this.productOnSale = "";
    }
    // add more later
  };

  /*
   * CartItem: Object for cart items to be stored
   * @params: _postObj = item coming from database
   */
  self.CartItem = function(_postObj){
    this.cartItemID = _postObj.cartItemID;
    this.cartItemQty = _postObj.cartItemQty;
    this.productID = _postObj.productID;
    this.productName = _postObj.productName;
    this.productDescription = _postObj.productDescription;
    this.productQty = _postObj.productQty;
    var productQtyOptions = ko.observableArray();
    if(_postObj.productQty > 0){
      for(var i = 1; i <= _postObj.productQty; i++){
        productQtyOptions.push({"value": i});
      }
    }
    else{
      productQtyOptions.push({"value":"Out of Stock"});
    }
    this.productQtyOptions = productQtyOptions;
    this.productPrice = _postObj.productPrice;
    this.productAddedDate = _postObj.productAddedDate;
  };

  self.PurchasedProduct = function (_postObj) {
    this.purchasedProductID = _postObj.purchasedProductID;
    this.purchasedQty = _postObj.purchasedQty;
    this.purchasedDate = _postObj.purchasedDate;
    this.productID = _postObj.productID;
    this.productName = _postObj.productName;
    this.productDescription = _postObj.productDescription;
    this.productPrice = _postObj.productPrice;
    this.productSalePrice = _postObj.productSalePrice;
    this.productImgSrc = _postObj.productImgSrc;

  };


  /****************************************
   * BINDING VARIABLES TO (coreData Module)
   ****************************************/

  // observable arrays
  self.products = products;
  self.cartItems = cartItems;
  self.purchasedProducts = purchasedProducts;
  self.onSaleProducts = onSaleProducts;
  self.notOnSaleProducts = notOnSaleProducts;

  return self;
});