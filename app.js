// For any third party dependencies, like jQuery, place them in the lib folder.

// Configure loading modules from the lib directory,
// except for 'app' ones, which are in a sibling
// directory.
requirejs.config({
  baseUrl: 'js',
  shim : {
    "bootstrap" : { "deps" :['jquery'] },
    //"dataTables" : { "deps" :['jquery'] },
    "grid": { "deps" :['modernizr.custom', 'jquery']},
    "NotificationFx": { "deps" :['modernizr.custom-msg']}
  },
  paths: {
    jquery: 'lib/jquery',
    knockout: 'lib/knockout',
    "modernizr.custom":'lib/modernizr.custom',
    "modernizr.custom-msg":"lib/messageSystem/modernizr.custom",
    classie:'lib/messageSystem/classie',
    grid: 'lib/grid',
    bootstrap: 'lib/bootstrap',
    datatables:"lib/datatables-pagination/js/jquery.dataTables",
    NotificationFx:"lib/messageSystem/notificationFx",
    // main modules
    coreData:"coreData",
    admin:"admin",
    cart:"cart",
    util:"util"
  }
});

/*
 * Main Module: bmx
 *
 * Contains primarily functions and observables that initialize the site
 */

define('bmx', ['jquery', 'knockout', 'coreData', 'util', 'admin', 'cart','bootstrap', 'grid', 'datatables'], function ( $, ko, coreData, util, admin, cart) {
  var self = bmx = {},
    coreData = coreData, // coreData module
    util = util, // util module
    admin = admin, // admin module
    cart = cart, // cart module

    msgTemplate = ko.observable('blank-tmpl'), // template variable for message view ( errors and successes )
    displayMessage = ko.observable(), // message body to be displayed in msgTemplate
    isLoggedIn = ko.observable(false), // boolean for if user is logged in
    userAccount; // account

  /*
   * init: Initializes entire site
   */
  self.init = function(){
    initScroll();
    util.getProducts();

    checkLoggedIn();
    if(isLoggedIn()){
      // in isLoggedIn function() : call server and check session isset
      cart.getCartItems();
      util.getPurchasedProducts();
    }
    admin.getNumSaleItems();
    // will add more when we get images
  };

  /*
   * Confirm: uses text of confirmation-btn to determine which
   * function to call if user confirms
   */
  self.confirm = function(){
    var functionToCall = $('#confirmation-btn').html();
    switch(functionToCall){
      case "Logout":
        util.logout();
        break;
      case "Update":
        admin.updateProduct();
        break;
      case "Delete":
        admin.deleteProduct();
        break;
      case "Empty Cart":
        cart.emptyCart();
        break;
      case "Delete Item":
        cart.deleteCartItem();
        break;
      case "Update Qty":
        cart.updateCartItemQty();
        break;
      case "Checkout":
        cart.checkout();
        break;
    }

  };


  /*
   * initScroll: handles all scrollTo events for links
   */
  function initScroll(){
    $('a[href*=#]:not([href=#])').click(function() {
      if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && location.hostname === this.hostname) {

        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
          $('html,body').animate({
            scrollTop: target.offset().top - 51
          }, 800);
          return false;
        }
      }
    });

    $('#scroll-top-btn').on('click', function () {
      $('html,body').animate({
        scrollTop: 0
      }, 800);
    });
  }



  /*
   * displayCreateAccount: shows the modal for
   * creating an account
   */
  self.displayCreateAccount = function () {
    util.clearObservables();
    util.modalHeading("Create Account");
    util.modalBody("create-account-tmpl");
    util.modalButtonTmpl("create-account-btn");
    $('#login-modal').modal('show');
  };

  /*
   * createAccount: creates an account for the user
   * provided everything passes validation
   */
  self.createAccount = function(){
    var oneEmpty = validateCreateAccount();
    var passwordsMatch = validateCreateAccountPasswords();

    if (oneEmpty == true) {
      alert("Please fill in blank fields with * next to them!");
    }
    else {
      if(passwordsMatch == false) {
        alert("passwords dont match!");
      }else {

        var form = new FormData();
            form.append("userFullName", $("#full-name-createacc").val());
            form.append("userEmail", $("#email-createacc").val());
            form.append("userAddress", $("#address-createacc").val());
            form.append("userCity", $("#city-createacc").val());
            form.append("userState", $("#state-createacc").val());
            form.append("userZip", $("#zip-createacc").val());
            form.append("userPhone", $("#phone-createacc").val());
            form.append("username", $("#username-createacc").val());
            form.append("userPass", $("#password-createacc").val());
            form.append("action", "createAccount");


        // Returns successful data submission message when the entered information is stored in database.

        // NOTE: Couldnt use my custom "makeRequest()" because it didnt
        // like the formData being sent in for some reason
        $.ajax({
          url: "functions.php",
          type: 'POST',
          data: form,
          cache: false,
          dataType: 'json',
          contentType: false,
          processData: false
        }).done(function (data) {
          console.log(data);
          var data = JSON.parse(data);
          console.log(data);
          showMessage(data.msg, data.success, 5000);
          if (data.success == "success") {
            //$('#login-modal').modal('hide');

            // launch login modal passing in username
            util.displayLogin($("#username-createacc").val());
          }
          else {
            // there was an error

          }
        }).fail(function (jqXHR, stat, err) {
          console.log("error: " + err + " Server Response: " + jqXHR.responseText);
        });
      }
    }
  };

  /*
   * validateCreateAccount:
   * validates all inputs in the form for creating an account
   */
  function validateCreateAccount(){
    var oneEmpty = false;
    $("#sign-up-form :input").each(function(){
      if($(this).val() == ""){
        if($(this).attr("id") != "phone-createacc"){
          oneEmpty = true;
        }
      }
    });
    return oneEmpty;
  }

  /*
   * validateCreateAccountPasswords:
   * verifies that the passwords the user enters in the
   * create account modal are matching.
   */
  function validateCreateAccountPasswords(){
    if($('#password-confirm-createacc').val() == $('#password-createacc').val()){
      return true;
    }
    else{
      return false;
    }
  }


  /*
   * checkLoggedIn: grabs the users info from localStorage if it exists
   * updates the view with the account status bar
   */
  function checkLoggedIn(){
    // if "account" is stored in localStorage
    if(window.localStorage.getItem("account")){
      var user = JSON.parse(localStorage.getItem("account"));

      util.makeRequest(
        "action=checkLoggedIn" +
        "&username=" + user.username,
        function (data) {
          //console.log(data);

          if(data.success == "success"){
            // if user is admin
            if(data.admin == "true"){
              util.updateAdminStatus(data.username);

            }
            // user is not admin load shopper account bar
            else{
              util.updateShopperStatus(data.username);
            }
            isLoggedIn(true);
          }
          else{
            isLoggedIn(false);
          }

        },
        true
      ); // end makeRequest


    }

  }



  /*
   * $.fn.getAttributes: custom jquery plugin to handle
   * getting all attributes of a given element
   */
  (function($) {
      $.fn.getAttributes = function() {
          var attributes = {};

          if( this.length ) {
              $.each( this[0].attributes, function( index, attr ) {
                  attributes[ attr.name ] = attr.value;
              } );
          }

          return attributes;
      };
  })(jQuery);

  /*
   * truncatedText: custom binding handler for truncating
   * long text to make display more bearable in tables ( Cart )
   */
  ko.bindingHandlers.truncatedText = {
    update: function (element, valueAccessor, allBindingsAccessor) {
      var originalText = ko.utils.unwrapObservable(valueAccessor()),
      // 10 is a default maximum length
        length = ko.utils.unwrapObservable(allBindingsAccessor().maxTextLength) || 30,
        truncatedText = originalText.length > length ? originalText.substring(0, length) + "..." : originalText;
      // updating text binding handler to show truncatedText
      ko.bindingHandlers.text.update(element, function () {
        return truncatedText;
      });
    }
  };

  // functions
  self.displayMessage = displayMessage;

  //variables
  self.userAccount = userAccount;
  self.msgTemplate = msgTemplate;

  self.admin = admin;
  self.cart = cart;
  self.util = util;
  self.coreData = coreData;

  return self;
});

define(['bmx', 'jquery', 'knockout', 'bootstrap'], function (bmx, $, ko) {

  window._bmx = bmx; // bind bmx to window object
  ko.applyBindings(bmx, document.getElementById('bmx-main')); // apply ko bindings
  _bmx.init(); // run init

  // hides delete and update buttons when modal is finished hiding
  $('#login-modal').on('hidden.bs.modal', function () {
    // add back large class again
    $('#login-modal .modal-dialog').addClass("modal-lg");
    bmx.admin.deleteProductBtnVisible(false);
    bmx.admin.updateProductBtnVisible(false);

    $(document.body).removeClass("modal-open");
  });

  // super f***ing hack - awful!
  $('#login-modal').on('hide.bs.modal', function (e) {
    $(document.body).removeClass("modal-open");
  });

  // another hack ** bad
  $('#confirmation-modal').on('hidden.bs.modal', function (e) {
    $(document.body).addClass("modal-open");
    $('#login-modal').css("overflow-y", "scroll");
  });

});
