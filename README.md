Paypal-Integration
==================
<b>Paypal Integration kit for DIRECT PAYMENT method and EXPRESS CHECKOUT method.</b>

Demo is given in the pages `testDirectPayment.php` and `testExpressCheckout.php` respectively.

For Use simply configure the merchant details in the page `PaymentGateway/PaypalAbstract.php` then

For `DirectPayment` 

1. Set the return urls and create a `DirectPayment` object which takes the amount as the param. 
   `$Paypal = new DirectPayment(23);`

2. Set the credit card details

   `setCreditCardNumber("4539644852839411");`   
   `setCVV("123")`   
   `setCardType("VISA")`   
   `setExpDate("MMYYYY")`

3. `doPayment()` will return the parsed paypal response as an array.

 
 For `ExpressCheckout` 
 
1. Set the Expresscheckout by
        

        `$Paypal = new ExpressCheckout($amount);//Amount`
        `$Paypal->doPayment();`
2. The page will be redirected to paypal
3. In the return page call the method `DoExpressCheckoutPayment`


       `$Paypal = new ExpressCheckout($amount);//Amount`
       
       `$response = $Paypal->DoExpressCheckoutPayment($_REQUEST);`
       
TODO : Implement <b>Paypal Parallel Payments Using Express Checkout</b> and <b>Recurring payment</b>
