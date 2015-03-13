/**
 * Created by Clay on 3/5/2015.
 *
 * Contains any utility functions reused in other modules
 */
define('util', ['jquery', 'knockout', 'coreData', 'bootstrap', 'datatables'], function ( $, ko, coreData) {
  var self = util = {},
    coreData = coreData,

    // confirm modal
    confirmModalButtonTmpl = ko.observable('blank-tmpl'), // confirmation modal btn template
    confirmModalBody = ko.observable('blank-tmpl'), // confirmation modal body
    confirmModalHeading = ko.observable(), // confirmation modal heading
    confirmMessage = ko.observable(), // confirmation modal msg
    confirmBtnTxt = ko.observable(), // confirmation modal btns

    // account observables
    accountTmpl = ko.observable('account-login'),
    accountUsername = ko.observable(),

    // current product
    currEditProduct = ko.observable(), // current product admin will edit
    currProductName = ko.observable(), // current productName
    currProductImg = ko.observable(), // current product image
    editableProducts = ko.observableArray(), // aray of editable products

    // modal observables
    modalHeading = ko.observable('blank-tmpl'),
    modalBody = ko.observable('blank-tmpl'),
    modalButtonTmpl = ko.observable("blank-tmpl"),

    saleProductsGridTmpl = ko.observable("blank-tmpl"),
    catalogProductsTableTmpl = ko.observable("blank-tmpl"),
    purchasedProductsTmpl = ko.observable("blank-tmpl"),
    purchasedProductsEmpty = ko.observable(true),

    // boolean for if user is admin or not
    isSU = ko.observable(false);

  /**
   * makeRequest: Reduces ajax call code used in other modules
   * Since i am calling the same PHP script i am able to reduce repeated code here
   *
   * @param _data = POST data being sent to server
   * (NOTE: unfortunately i have to sent it in a way that looks like GET
   * ie. "action=someFunction&variableToSend=valueOfVariableToSend" )
   *
   * @param _successFunc = callback function
   * @param _quiet == if i want the message from the server ( results )
   * to be displayed to the user or logged
   */
  self.makeRequest = function(_data, _successFunc, _quiet){
    //console.log(_data);
    $.ajax({
      url: "inc/LIB_project1.php",
      type: 'POST',
      data: _data,
      cache: false,
      dataType: 'json',
      processData: false
    }).done(function (data) {
      //console.log(data);
      if(typeof data =='object'){
        var data = data;
      }else{
        var data = JSON.parse(data);
      }

      // if user feedback is necessary
      if(!_quiet){
        _quiet = false;
        //console.log(data);
        showMessage(data.msg, data.success, 5000);
      }else{
        //console.log(data);
      }
      _successFunc(data);
    }).fail(function (jqXHR, stat, err) {
      console.log("error: " + err + " Server Response: " + jqXHR.responseText);
    });
  };

  /**
   * imgChanged: Whether the image being uploaded changed from it original state or not
   * @param _this = file input
   * @param _previewId = id of preview image element
   * @returns {boolean} = whether there is a file being uploaded or not
   */
  function imgChanged(_this, _previewId){
    var file = _this.files[0];
    var imagefile = file.type;
    var match= ["image/jpeg","image/png","image/jpg"];
    if(!((imagefile==match[0]) || (imagefile==match[1]) || (imagefile==match[2])))
    {
      $('#' +_previewId).attr('src','noimage.png');
      $("#message").html("<p id='error'>Please Select A valid Image File</p>"+"<h4>Note</h4>"+"<span id='error_message'>Only jpeg, jpg and png Images type allowed</span>");
      return false;
    }
    else
    {
      var reader = new FileReader();
      switch (_previewId){
        case "edit-previewing":
          reader.onload = editImageIsLoaded;
          break;
        case "previewing":
          reader.onload = imageIsLoaded;
          break;
      }

      reader.readAsDataURL(_this.files[0]);
      //console.log("FILE: "  + this.files[0].name);
      currProductImg(_this.files[0].name);
      console.log(currProductImg());
    }
  }

  /**
   * clearObservables: resets template binding
   * and text binding for modal
   */
  self.clearObservables = function(){
    //clearObservables
    modalBody("blank-tmpl");
    modalHeading("blank-tmpl");
  };

  /**
   * displayLogin: shows login modal with username and password fields to be filled out.
   */
  function displayLogin(_username){
    console.log(_username);
    util.clearObservables();
    modalHeading("Login");
    modalBody("login-tmpl");
    modalButtonTmpl("login-btn");
    // if username is passed in ( will be sent from createaccount )
    if(_username) {
      $('#login-username').val(_username);
    }
    $('#login-modal .modal-dialog').removeClass("modal-lg");
    $('#login-modal').modal('show');
  }

  /**
   * login: validates user entry and if passes validation
   * then send entered username and passord to php script
   * to be validated further then logged in
   */
  function login(){
    var username = $("#login-username").val();
    var password = $("#login-password").val();
    if (username == '' || password == '') {
      alert("Insertion Failed Some Fields are Blank....!!");
    }
    else {
      // Returns successful data submission message when the entered information is stored in database.
      util.makeRequest(
        "action=login&username=" + username + "&password=" + password,
        function(data){
          if (data.success == "success") {

            if (data.admin == "true") {
              updateAdminStatus(data.username);
            }
            else {
              updateShopperStatus(data.username);
            }
            cart.getCartItems();
            //give Super User privelages

            userAccount = new coreData.Account(data);
            if ($("#stay-logged-in").is(':checked')) {
              localStorage.setItem("account", JSON.stringify(userAccount));
            }
            $('#login-modal').modal('hide');
            $('#login-modal .modal-dialog').addClass("modal-lg");
          }
        }
      ); // end makeRequest

    }
  }

  /**
   * logout: logs the user out via php script and
   * removing from localstorage so user does not remain logged in on page refresh / reload
   */
  function logout(){
    accountTmpl("account-login");
    $('#confirmation-modal').modal('hide');
    localStorage.removeItem("account");

    util.makeRequest(
      "action=logout",
      function(data){
        console.log("makeRequest Call Data: " + data);
      }
    );
  }

  /**
   * displayConfirmLogout: shows confirmation dialog
   * to make the user confirm they wish to logout
   */
  self.displayConfirmLogout = function () {
    confirmModalHeading("Logout?");
    confirmModalBody("confirmation-tmpl");
    confirmModalButtonTmpl("confirm-btn");
    confirmBtnTxt("Logout");
    confirmMessage("Are you sure you want to logout?");
    $('#confirmation-modal').modal({
      show:true,
      backdrop:false
    });
  };

  /**
   * updateAdminStatus: updates view (account bar) with admin related functionality
   * @param username = username of user logged in
   */
  function updateAdminStatus(username){
    accountTmpl("admin-logged-in");
    accountUsername(username);
    isSU(true);
  }

  /**
   * updateShopperStatus: updates view to reflect that shopper is logged in
   * displays only shopper relevant functionality
   * @param username = username of user logged in
   */
  function updateShopperStatus(username){
    accountTmpl("shopper-logged-in");
    accountUsername(username);
  }

  /**
   * displayPurchasedProducts: shows modal with all
   * items the logged in user has "purchased" => clicked "checkout" btn
   */
  self.displayPurchasedProducts = function(){
    modalBody("purchased-products-tmpl");
    modalHeading("Products you purchased");
    modalButtonTmpl("close-btn");
    $('#login-modal').modal('show');
  };

  /**
   * getPurchasedProducts: grabs all PurchasedProducts from php script and
   * pushes them into observable array to be bound to view
   */
  self.getPurchasedProducts = function(){
    coreData.purchasedProducts.removeAll();
    util.makeRequest(
      "action=getPurchasedProducts",
      function (data) {
        if(data.success == "success"){
          var purchasedProducts = data.purchasedProducts;
          //console.log(purchasedProducts);
          for (var product in purchasedProducts) {
            var tmpArr = {
              "purchasedProductID": purchasedProducts[product].purchasedProductID,
              "purchasedQty": purchasedProducts[product].purchasedQty,
              "purchasedDate": purchasedProducts[product].purchasedDate,
              "productID": purchasedProducts[product].productID,
              "productName": purchasedProducts[product].productName,
              "productDescription": purchasedProducts[product].productDescription,
              "productPrice": purchasedProducts[product].productPrice,
              "productSalePrice": purchasedProducts[product].productSalePrice,
              "productImgSrc": purchasedProducts[product].productImgSrc
            };
            coreData.purchasedProducts.push(new coreData.PurchasedProduct(tmpArr));
          }
          if(coreData.purchasedProducts().length > 0){
            purchasedProductsEmpty(false);
          }
        }
      },
      true // silent return
    )
  };

  /**
   * getProducts: grabs all products from php script and
   * pushes them into observable array to be bound to view
   */
  self.getProducts = function(){
    coreData.notOnSaleProducts.removeAll();
    coreData.onSaleProducts.removeAll();

    util.makeRequest(
      "action=getProducts",
      function (data) {
        var emptyArr = {
          "productID": "",
          "productName": "--Select a Product--",
          "productDescription": "",
          "productPrice": "",
          "productSalePrice": "",
          "productQty": "",
          "productImgSrc": ""
        };
        editableProducts.push(new coreData.Product(emptyArr));
        for (var product in data) {
          var tmpArr = {
            "productID": data[product].productID,
            "productName": data[product].productName,
            "productDescription": data[product].productDescription,
            "productPrice": data[product].productPrice,
            "productSalePrice": data[product].productSalePrice,
            "productQty": data[product].productQty,
            "productImgSrc": data[product].productImgSrc
          };
          coreData.products.push(new coreData.Product(tmpArr));
          if (data[product].productSalePrice > 0) {
            // general products that arent on sale
            coreData.onSaleProducts.push(new coreData.Product(tmpArr));
          } else {
            // products on sale
            coreData.notOnSaleProducts.push(new coreData.Product(tmpArr));
          }
          editableProducts.push(new coreData.Product(tmpArr));
        }
        util.removeDataBind();
        util.resetProductBindings();
        initGrid();
        initPagination("all-products-table"); // some error here
      },
      true // true => means i dont want to display msg
    ); // end makeRequest

  }; // end getProducts

  /**
   * clearFields: clears out all fields after done editing product
   */
  function clearFields(){
    $('#edit-product-name').val("");
    $('#edit-product-description').val("");
    $('#edit-product-price').val("");
    $('#edit-product-sale-price').val("");
    $('#edit-previewing').attr("src", "img/noimage.png");
  }

  /**
   * popFields: populates fields of "Edit Product" form
   * when a product is selected from select dropdown
   */
  function popFields(){
    clearFields();
    $('#edit-product-name').val(currEditProduct().productName);
    $('#edit-product-description').val(currEditProduct().productDescription);
    $('#edit-product-price').val(currEditProduct().productPrice);
    $('#edit-product-sale-price').val(currEditProduct().productSalePrice);
    $('#edit-previewing').attr("src", currEditProduct().productImgSrc);
    $("#edit-product-qty").val(currEditProduct().productQty);
    //$('#edit-file').val(currEditProduct().productImgSrc);
  }

  /**
   * editImageIsLoaded: handles image preview element during pre - image upload
   * @param e = event
   */
  function imageIsLoaded(e) {
    $("#file").css("color","green");
    $('#image_preview').css("display", "block");
    $('#previewing').attr('src', e.target.result);
    $('#previewing').attr('width', '200px');
    $('#previewing').attr('height', '125px');
  }

  /**
   * editImageIsLoaded: handles image preview element during pre - image upload
   * @param e = event
   */
  function editImageIsLoaded(e){
    $("#edit-file").css("color","green");
    $('#edit-previewing').attr('src', e.target.result);
    $('#edit-previewing').attr('width', '200px');
    $('#edit-previewing').attr('height', '125px');
  }

  /**
   * getProductInfo: retrieves info for given product
   *
   * Used primarily when admin is trying to update a product
   */
  self.getProductInfo = function(){

    var productID = $("#edit-product-select").val();
    if(productID != ""){
      // enable btns
      $('#edit-product-form').slideDown();

      util.makeRequest(
        "action=getProduct&" +
        "selectedProductID=" + productID,
        function (data) {
          var tmpArr = {
            "productID": data[0].productID,
            "productName": data[0].productName,
            "productDescription": data[0].productDescription,
            "productPrice": data[0].productPrice,
            "productSalePrice": data[0].productSalePrice,
            "productQty": data[0].productQty,
            "productImgSrc": data[0].productImgSrc
          };
          currEditProduct(new coreData.Product(tmpArr));
          util.popFields();
        },
        true // true => means i dont want to display msg
      );  // end makeRequest

    }
  };

  /*
   * removeDataBind: removes "data-bind" attribute on all "a" tags
   * inside the grid so that the ** initGrid() ** can work properly
   *
   * THIS SHOULDNT BE WORKING THIS WAY - Will have to fix in future versions
   */
  self.removeDataBind = function() {
    $('#og-grid').removeAttr('data-bind');
    $('#og-grid img').each(function(){ $(this).removeAttr('data-bind'); });
    $('#og-grid a').each(function(){
      $(this).removeAttr('data-bind');
    });
  };

  /**
   * filterProducts: filters sale items down to selected data
   * @param _ele = btn clicked -> gives me what they were trying to filter
   */
  self.filterProducts = function(_ele){
    var filterTerm = _ele.getAttribute('data-filter');
    switch(filterTerm){
      case "all":
        // passing in null first because we don't
        // have an element with the value we are searching for
        util.runSearch(null, "");
        break;
      case "on sale":
        util.runSearch(null, "on sale");
        break;
      case "< $50":
        util.runSearch(null, "$50"); // future work. - did have time to finish
        break;
    }
  };

  /**
   * runSearch: searches through "data-" attributes in all elements
   * in the on-sale section in the grid
   * @param _ele = used to grab value that was entered (search string)
   * @param _str = used when called in filter function "util.filterProducts()"
   */
  self.runSearch = function(_ele, _str){
    if(_ele){
      var searchTerm = _ele.value;
    }
    else{
      var searchTerm = _str;
    }
    if(searchTerm != ""){
      console.log(searchTerm);
      // loop through a tags
      $("#og-grid a").each(function(){
        var item = $(this);
        var bool = false;
        var attrs = $(this).getAttributes();
        // loop through attributes of "a"
        for(attr in attrs){
          // if attribute has "data-"
          if(attr.indexOf("data-") > -1){
            // if match is found
            if(attrs[attr].toLowerCase().indexOf(searchTerm.toLowerCase()) > -1){
              bool = true;
            }
          }
        }
        // found match
        if(bool){
          item.show();
        }
        else{
          //not a match
          item.hide();
        }
      });
    }
    else{
      $("#og-grid a").each(function(){
        $(this).show();
      });
    }
  };

  /*
   * initPagination: initializes pagination jquery plugin on the catalog products table
   *
   * @params: _tblId = id of table to be paginated
   */
  function initPagination(_tblId) {
    $('#' + _tblId).dataTable({
      "pagingType": "full_numbers",
      "iDisplayLength": 5,
      "aLengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]]
    });
  }


  /**
   * resetProductBindings: reinitialize the product bindings for
   * both (on sale products and not on sale products)
   */
  function resetProductBindings(){

    // sale products
    saleProductsGridTmpl("blank-tmpl");
    saleProductsGridTmpl("product-grid");

    // catalog products
    catalogProductsTableTmpl("blank-tmpl");
    catalogProductsTableTmpl("catalog-products-table");
  }


  /****************************************
   * BINDING VARIABLES TO (util Module)
   ****************************************/

  //functions
  self.popFields = popFields;
  self.displayLogin = displayLogin;
  self.login = login;
  self.logout = logout;
  self.imgChanged = imgChanged;
  self.updateAdminStatus = updateAdminStatus;
  self.updateShopperStatus = updateShopperStatus;

  // to handle duplicates in product views
  self.resetProductBindings = resetProductBindings;

  // account observables
  self.accountTmpl = accountTmpl;
  self.accountUsername = accountUsername;

  // template for confirmation
  self.confirmModalButtonTmpl = confirmModalButtonTmpl;
  self.confirmModalHeading = confirmModalHeading;
  self.confirmModalBody = confirmModalBody;

  // modal template
  self.modalHeading = modalHeading;
  self.modalBody = modalBody;
  self.modalButtonTmpl = modalButtonTmpl;

  // grid template
  self.saleProductsGridTmpl = saleProductsGridTmpl;
  self.catalogProductsTableTmpl = catalogProductsTableTmpl;
  self.purchasedProductsTmpl = purchasedProductsTmpl;
  self.purchasedProductsEmpty = purchasedProductsEmpty;

  // variables for confirm modal
  self.confirmMessage = confirmMessage;
  self.confirmBtnTxt = confirmBtnTxt;

  // observables for ( product admin is currently editing in "edit product modal")
  self.currProductName = currProductName; // temp
  self.currProductImg = currProductImg; // temp
  self.editableProducts = editableProducts;
  self.currEditProduct = currEditProduct;

  self.isSU = isSU;

  return self; // return util object
});