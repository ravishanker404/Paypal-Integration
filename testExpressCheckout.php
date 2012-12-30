<?php

/**
 * Paypal Integration kit
 * for direct payment and express checkout
 *
 * @author     Ravi Shanker
 * @copyright  Copyright (c) Ravi Shanker
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

/*
 * THIS PAGE IS TO TEST THE EXPRESSCHECKOUT METHOD
 */

include 'PaymentGateway/PaypalAbstract.php';
include_once 'PaymentGateway/PaypalPaymentMethods/ExpressCheckout.php';

use PaymentGateway\PaypalPaymentMethods\ExpressCheckout;
$amount = 23;

try{
    
    if(!isset($_GET['result'])){
        /*
         * NOTE-THIS ONLY SETS THE PAYMENT
         *     need to call DoExpressCheckoutPayment() after returning to complete Payment
         */
        $Paypal = new ExpressCheckout($amount);//Amount
        $Paypal->doPayment();
        
    }
    elseif($_GET['result'] == "success"){
        
        $Paypal = new ExpressCheckout($amount);//Amount
        $response = $Paypal->DoExpressCheckoutPayment($_REQUEST);
        var_export($response);
        
    }
    elseif($_GET['result'] == "cancelled"){
        echo "Payment cancelled by buyer!";
    }
}

catch (Exception $ex){
    echo $ex->getMessage();
}
