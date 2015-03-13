/**
 * Created by Clay on 3/5/2015.
 *
 * Cart Module :
 *
 * Contains functions and observables pertaining
 * to the cart and checkout process for normal users
 */
define('cart', ['jquery', 'knockout', 'coreData', 'util', 'bootstrap'], function ( $, ko, coreData, util) {
  var self = cart = {},

    // Modules
    util = util,
    coreData = coreData,

    // cart observables
    currCartItemID = ko.observable(),
    cartItemCount = ko.observable(0),
    cartTotal = ko.observable(0),
    cartEmpty = ko.observable(false);

  /**
   * displayConfirmDeleteCartItem: shows confirmation dialog
   * to make the user confirm they wish to delete the
   * cart item in the corresponding row
   *
   * @param _ele = button that was clicked -> button contains
   * the cartItemID used to remove the item from the cart
   * in the DB table and in the view
   */
  self.displayConfirmDeleteCartItem = function (_ele) {
    // get id from data- inside element
    var cartItemID = _ele.getAttribute("data-cartitemid");

    //set value to observable for later use
    currCartItemID(cartItemID);

    util.confirmModalHeading("Remove Item?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Delete Item");
    util.confirmMessage("Are you sure you want to Remove this item from your cart?");
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayConfirmUpdateQty: shows confirmation dialog
   * to make the user confirm they wish to update the
   * cart item in the corresponding row
   * @param _ele = button that was clicked -> button contains
   * the cartItemID used to remove the item from the cart
   * in the DB table and in the view
   */
  self.displayConfirmUpdateQty = function (_ele) {
    // get id from data- inside element
    var cartItemID = _ele.getAttribute("data-cartitemid");

    //set value to observable for later use
    currCartItemID(cartItemID);

    util.confirmModalHeading("Update Product Qty?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Update Qty");
    util.confirmMessage("Are you sure you want to Update the quantity of this product?");
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayConfirmEmptyCart: shows confirmation dialog
   * to make the user confirm they wish to empty their cart
   */
  self.displayConfirmEmptyCart = function () {
    util.confirmModalHeading("Empty Cart?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Empty Cart");
    util.confirmMessage("Are you sure you want to empty your cart?");
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayConfirmCheckout: shows confirmation dialog
   * to make the user confirm they wish to commit to
   * the purchase and checkout
   */
  self.displayConfirmCheckout = function(){
    util.confirmModalHeading("Checkout?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Checkout");
    util.confirmMessage(
      "Are you want to checkout?\n"+
      "Total: " + cartTotal()
    );
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayCart: shows the cart
   */
  self.displayCart = function () {
    util.clearObservables();
    util.modalHeading("Your Cart");
    util.modalBody("cart-tmpl");
    util.modalButtonTmpl("checkout-btn");
    $('#login-modal').modal('show');
    var total = 0;
    $('#cart-table td.price').each(function(){
      var num = (parseFloat($(this).html()) * parseFloat($(this).attr("data-qty")));
      total += Math.ceil(num * 100) / 100;
    });
    total = Math.ceil(total * 100) / 100;
    cartTotal(total);
  };

  /**
   * getCartItems: retrieves the items in the cart for
   * the user that is logged in.
   * The results from the ajax call are pushed to an observableArray to be bound to the view
   */
  function getCartItems(){
    coreData.cartItems.removeAll();

    util.makeRequest("action=getCartItems", function(data){
      if(data.success == "success"){
        var itemCount = 0;
        //get items and push into observable array
        for(var i in data.cartItems){
          var tmpArr = {
            "cartItemID": data.cartItems[i].cartItemID,
            "cartItemQty": data.cartItems[i].cartItemQty,
            "productID": data.cartItems[i].productID,
            "productName": data.cartItems[i].productName,
            "productDescription": data.cartItems[i].productDescription,
            "productPrice": data.cartItems[i].productPrice,
            "productSalePrice": data.cartItems[i].productSalePrice,
            "productQty": data.cartItems[i].productQty,
            "productImgSrc": data.cartItems[i].productImgSrc,
            "productAddedDate": data.cartItems[i].productAddedDate
          };
          coreData.cartItems.push(new coreData.CartItem(tmpArr));
          itemCount++;
        }
        cartItemCount(itemCount);
        //console.log("cartItemCount: " + cartItemCount());
        util.removeDataBind();
        cartEmpty(false);
      }
      else{
        //console.log(data.msg);
        if(data.msg == "Error: Cart is Empty!"){
          cartEmpty(true);
          cartItemCount(0); // set cartItems back to 0
        }
      }
    }, true); // end makeRequest

  } // end getCartItems


  /**
   * updateCartItemQty: updates the qty of an item in the cart
   * after the user selects a new qty from the select dropdown
   * @param _qty = qty value from changed select dropdown
   */
  self.updateCartItemQty = function(_qty){
    var cartItemQty;
    if(_qty){
      cartItemQty = _qty
    }else{
      cartItemQty = $('#qty-select-' + currCartItemID()).val();
    }
    var productID = $('#qty-select-' + currCartItemID()).attr('data-productid');

    //add to cart : pass in (qty, productID)
    util.makeRequest(
      "action=updateCartItem&cartItemID=" + currCartItemID() +
      "&productID=" + productID +
      "&cartItemQty=" + cartItemQty,
      function(data){
        if(data.success == "success"){
          getCartItems();
          // hide modal
          $('#confirmation-modal').modal('hide');
        }
      }
    );

  };

  /**
   * deleteCartItem: removes the item from the cart
   */
  self.deleteCartItem = function(){

    util.makeRequest(
      "action=deleteCartItem&" +
      "&cartItemID=" + currCartItemID(),
      function (data) {
        if(data.success == "success"){
          getCartItems();
          // hide modals
          $('#confirmation-modal').modal('hide');
          $('#login-modal').modal('hide');
        }
      }
    ); // end makeRequest

  }; // end deleteCartItem


  /**
   * emptyCart: removes all items from cart
   */
  self.emptyCart = function(){

    util.makeRequest(
      "action=emptyCart",
      function (data) {
        if(data.success == "success"){
          getCartItems();

          // need to handle decrementing count

          // hide modals
          $('#confirmation-modal').modal('hide');
          $('#login-modal').modal('hide');
        }
      }
    ); // end makeRequest

  }; // end emptyCart

  /**
   * checkout: calls php function to update qty for
   * each product in the cart based on the qty of cart item
   * then adds items to purhasedproduct table and empties cart
   */
  self.checkout = function(){
    var resSuccess = true;

    ko.utils.arrayForEach(coreData.cartItems(), function(cartItem){

      util.makeRequest(
        "action=checkout&productID=" + cartItem.productID +
        "&cartItemQty= " + cartItem.cartItemQty,
        function (data) {
          if(data.success != "success"){
            resSuccess = false;
          }
        },
        true // silent return
      );
    }); // end makeRequest

    if(resSuccess == true){
      cart.emptyCart();
      util.getPurchasedProducts();
    }

  };

  /**
   * addToCart: adds a given item to the cart
   * @param _ele = clicked button / link (a tag)
   * contains the productID in the "data-" attribute
   */
  self.addToCart = function (_ele) {
    var productID = _ele.attr('data-product-id'),
      productQty = _ele.attr('data-product-qty'),
      productName = _ele.attr('data-product-name'),
      performUpdate = false;

    // if current qty of product is > 0 then allow add to cart
    if(productQty > 0){
      var cartItemQty = $('#select-' + productID).val();

      // loop through existing cart items
      ko.utils.arrayForEach(coreData.cartItems(), function(item){
        console.log("itemQty:" + item.cartItemQty);

        // if product we are adding already exists
        // in cart then show alert stating that
        if(item.productID == productID){
          alert("Item already exists in cart");
          performUpdate = true;
          // old code for updating current cart item qty - deprecated
          //var qtySum = parseInt(item.cartItemQty) + parseInt(cartItemQty);
          //updateCartItemQty(qtySum);
        }
      });

      // product doesnt already exist so
      // we can add it by calling php
      if(performUpdate == false) {

        util.makeRequest(
          "action=addCartItem" +
          "&productID=" + productID +
          "&cartItemQty=" + cartItemQty,
          function (data) {
            if(data.success == "success"){
              getCartItems();
            }
          }
        ); // end makeRequest

      }
    }
    else{
      alert("Sorry We dont have any '"+ productName +"' in stock!");
    }
  }; // end addCartItem



  /****************************************
   * BINDING VARIABLES TO (cart Module)
   ****************************************/

  // functions
  self.getCartItems = getCartItems;

  // cart observables
  self.cartItemCount = cartItemCount;
  self.cartEmpty = cartEmpty;
  self.cartTotal = cartTotal;

  return self;
});