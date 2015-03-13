
# Project 1 - Server Programming ISTE - 341 @ RIT
An e-commerce website created for Server Programming ISTE-341 at RIT. 
---


##This projects is an e-commerce website for Server Programming 341

NOTE: Admin Account Login Credentials

Username: admin
Password: admin

File Requirements:

1. index.php

Since i used knockout.js for my client side templating,
my index file is named "index.html" instead of "index.php".

"Sales" and "Catalog" sections are present in index.html
via templates that are bound to the view when necessary.

15+ Products -> Currently at least 18 products for sale



2. cart.php

Due to the same reason as 1 i do not have a cart.php file.
Instead i have all of the templates required for the cart UI and functionality
in my "index.html" file. The Javascript code for this portion of the site is
located in the "cart.js" file which is a require module that i wrote.

Instead of altering the QTY ON HAND for a given product when the user adds
the item to the cart, i decided to be more realistic here and only alter
the QOH when the user clicks the checkout button.

Also when this occurs the products and info associated with those products
are pushed to a table called "purchaseproducts", which the customer can later view.

3. admin.php

Due to the same reason as 1 i do not have a admin.php file.
Instead i have all of the templates required for the cart UI and functionality
in my "index.html" file. The Javascript code for this portion of the site is
located in the "admin.js" file, another require module that i wrote.

4. LIB_project1.php

I did this a bit differently than what the requirements were looking for.
Since i used Client side templating and jQuery ajax calls i wrote this script to fit my needs

Workflow example:

(psudo code)

1. "add to cart" button clicked
2. ajax call to LIB_project1.php sending "action=addToCart" as POST variable
3. LIB_project1.php determines which function from which class
to call based on the value of the "action" POST variable.
4. function addToCart() from Cart.class.php is called and JSON data is returned to update the view.

---

### Above and Beyond

* Product Search

* Grid Expander

* Uploading image for products

* showing purchased items (purchase history)

* update quantity of item in cart

* checkout functionality

---

### Technologies Utilized

#### Client Side (Front-end)

1. JQuery
2. Knockout.js
3. Require.js
4. Bootstrap
5. JQuery DataTables Plugin
6. NotificationFX

#### Server Side (Back-end)

Nothing as of right now - Would like to implement PHP libraries
=======
