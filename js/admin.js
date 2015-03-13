/**
 * Created by Clay on 3/5/2015.
 *
 * Contains all login, functions and observables
 * for admin portion of site
 *
 * ex: editing products, adding products, deleting products
 */
define('admin', ['jquery', 'knockout', 'util', 'coreData', 'bootstrap'], function ( $, ko, util, coreData) {
  var self = admin = {},
    coreData = coreData,
    util = util,

    // product editing buttons
    addProductBtnVisible = ko.observable(false),
    updateProductBtnVisible = ko.observable(false),
    deleteProductBtnVisible = ko.observable(false),

    // saleItemsCounts
    numSaleItems = ko.observable(),
    numSaleItemsTooLarge = ko.observable(false);

  /**
   * getNumSaleItems: grabs the number of items that are currently on sale
   */
  self.getNumSaleItems = function(){

    util.makeRequest(
      "action=getNumSaleItems",
      function (data) {
        if(data.numSaleItems > 5){
          numSaleItemsTooLarge(true);
        }
      },
      true
    );

  };

  /**
   * displayConfirmDeleteProduct: shows confirmation dialog
   * to make the admin confirm they wish to delete the product
   */
  self.displayConfirmDeleteProduct = function () {
    util.confirmModalHeading("Delete Product?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Delete");
    util.confirmMessage("Are you sure you want to Delete the product?");
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayConfirmUpdateProduct: shows confirmation dialog
   * to make the admin confirm they wish to update the product
   */
  self.displayConfirmUpdateProduct = function () {
    util.confirmModalHeading("Update Product?");
    util.confirmModalBody("confirmation-tmpl");
    util.confirmModalButtonTmpl("confirm-btn");
    util.confirmBtnTxt("Update");
    util.confirmMessage("Are you sure you want to Update the product?");
    $('#confirmation-modal').modal('show');
  };

  /**
   * displayAddProduct: displays modal for adding a product to the site
   */
  self.displayAddProduct = function(){
    util.clearObservables();
    util.modalHeading("Add a new Product");
    util.modalBody("add-product-tmpl");
    util.modalButtonTmpl("add-product-btn");
    addProductBtnVisible(true);

    // Function to preview image after validation
    $("#file").change(function() {
      util.imgChanged(this, "previewing");
    });

    $("#edit-file").change(function() {
      util.imgChanged(this, "edit-previewing");
    });

    setValidateNumberEventListeners();

    $('#login-modal').modal('show');
  };

  /**
   * deleteProduct: removes a product from the product table in DB
   */
  function deleteProduct() {
    // deletes product
    var productID = $("#edit-product-select").val();
    if(productID != "") {

      util.makeRequest(
        "action=deleteProduct&selectedProductID=" + productID,
        function(data){
          console.log(data);

          //update view
          util.getProducts();
          $('#confirmation-modal').modal('hide');
          $('#login-modal').modal('hide');
        }
      ); // end makeRequest

    }
  } // end deleteProduct

  /**
   * updateProduct: updates a product in DB
   * validates user entry -> sends to php script
   * php script -> validates then updates product
   */
  function updateProduct() {
    // updates product
    var form = new FormData(),
      imgSrc;
    if(util.currProductImg()){
      imgSrc =  "file/" + util.currProductImg();
    }else{
      imgSrc = "img/noimage.png";
    }
    console.log($("#edit-file")[0].files[0]);
    form.append("file", $("#edit-file")[0].files[0]);
    form.append("productName", $("#edit-product-name").val());
    form.append("productPrice", $("#edit-product-price").val());
    form.append("productSalePrice", $("#edit-product-sale-price").val());
    form.append("productQty", $("#edit-product-qty").val());
    form.append("productDescription", $("#edit-product-description").val());
    form.append("productImgSrc", imgSrc);
    form.append("productID", $("#edit-product-select").val());
    form.append("action", "updateProduct");

    // Returns successful data submission message when the entered information is stored in database.
    $.ajax({
      //url: "updateProduct.php",
      url: "inc/LIB_project1.php",
      type: 'POST',
      data: form,
      cache: false,
      dataType: 'json',
      contentType: false,
      processData: false
    }).done(function(data) {
      console.log(data);
      var data = JSON.parse(data);
      console.log(data);
      showMessage(data.msg, data.success, 5000);
      if(data.success == "success"){
        util.getProducts();
        util.popFields();
        $('#login-modal').modal('hide');
        $('#confirmation-modal').modal('hide');
      }
      else {
        // there was an error

      }
    }).fail(function(jqXHR, stat, err) {
      console.log("error: " + err + " Server Response: " + jqXHR.responseText);
    });
  }

  /**
   * addProduct: adds a product to DB
   * First validates entry, then passed
   * to php script further validating user entry
   * then inserts to DB
   */
  function addProduct(){
    var oneEmpty = false,
      confirmEmail,
      tmpDiscludeArr = ["product-sale-price", "file"];

    $("#add-product-form :input").each(function(){
      if($(this).val() == ""){
        // if value of cur ele id is not in array of ids to disclude
        if($.inArray($(this).attr("id"), tmpDiscludeArr) === -1){
          oneEmpty = true;
          console.log($(this).attr('id'));
        }
      }
    });

    //verify confirmation email
    if($("#confirmation-email").is(':checked')){
      confirmEmail = "yes";
    }
    else{
      confirmEmail = "no";
    }

    if (oneEmpty == true) {
      alert("Please fill in blank fields with * next to them!");
    }
    else {
      var form = new FormData(),
        imgSrc;
      if(util.currProductImg()){
        imgSrc =  "file/" + util.currProductImg();
      }else{
        imgSrc = "img/noimage.png";
      }
      form.append("file", $("#file")[0].files[0]);
      form.append("productName", $("#product-name").val());
      form.append("productPrice", $("#product-price").val());
      form.append("productSalePrice", $("#product-sale-price").val());
      form.append("productQty", $("#product-qty").val());
      form.append("productDescription", $("#product-description").val());
      form.append("productImgSrc", imgSrc);
      form.append("confirmationEmail", confirmEmail);
      form.append("email", JSON.parse(localStorage.getItem("account")).email);
      form.append("action", "addProduct");
      // Returns successful data submission message when the entered information is stored in database.
      $.ajax({
        url: "inc/LIB_project1.php",
        type: 'POST',
        data: form,
        cache: false,
        dataType: 'json',
        contentType: false,
        processData: false
      }).done(function(data) {
        console.log(data);
        var data = JSON.parse(data);
        console.log(data);
        showMessage(data.msg, data.success, 5000);
        if(data.success == "success"){
          util.getProducts();
          $('#login-modal').modal('hide');
        }
        else {
          // there was an error

        }
      }).fail(function(jqXHR, stat, err) {
        console.log("error: " + err + " Server Response: " + jqXHR.responseText);
      });

    }
  }

  /**
   * changeVisibleBtns: change which btns are visible in modal after different tab is clicked.
   * @param tabName = name of tab clicked
   */
  self.changeVisibleBtns = function (tabName) {
    switch(tabName){
      case "add":
        resetPreviewImg();
        hideAllProductBtns();
        $("#edit-product-form").slideUp();
        $("#edit-product-select").val("");
        addProductBtnVisible(true);
        break;
      case "edit":
        //resetPreviewImg();
        hideAllProductBtns();
        deleteProductBtnVisible(true);
        updateProductBtnVisible(true);
        break;
    }
  };

  /**
   * hideAllProductBtns: hides all buttons in Edit Products Modal
   */
  function hideAllProductBtns(){
    addProductBtnVisible(false);
    deleteProductBtnVisible(false);
    updateProductBtnVisible(false);
  }

  /**
   * resetPreviewImg: sets Preview image for image
   * upload section of form to default image
   */
  function resetPreviewImg(){
    $('#edit-previewing').attr('src', 'img/noimage.png');
    $('#previewing').attr('src', 'img/noimage.png');
  }

  /**
   * setValidateNumberEventListeners: binds event listeners
   * for form inputs for (adding a product, editing a product)
   */
  function setValidateNumberEventListeners(){
    // event bindings for form elements
    $('#product-price').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

    $('#product-sale-price').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

    $('#product-qty').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

    $('#edit-product-price').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

    $('#edit-product-sale-price').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

    $('#edit-product-qty').on("keyup", function () {
      $(this).val(validateNumber($(this).val()));
    });

  }

  /**
   * validateNumber: strips the last character from a string if it is not a number or decimal
   * @param _val
   * @returns {*}
   */
  function validateNumber(_val){
    if(isNaN(_val)){
      return _val.slice(0,_val.length - 1);
    }
    else{
      return _val;
    }
  }


  /****************************************
   * BINDING VARIABLES TO (admin Module)
   ****************************************/

  // functions
  self.addProduct = addProduct;
  self.deleteProduct = deleteProduct;
  self.updateProduct = updateProduct;

  // observables
  self.addProductBtnVisible = addProductBtnVisible;
  self.updateProductBtnVisible = updateProductBtnVisible;
  self.deleteProductBtnVisible = deleteProductBtnVisible;

  self.numSaleItemsTooLarge = numSaleItemsTooLarge;

  return self;
}); // end admin module