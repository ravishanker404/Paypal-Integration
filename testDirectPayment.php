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
 * THIS PAGE IS TO TEST THE DIRECT PAYMENT METHOD
 */
include 'PaymentGateway/PaypalAbstract.php';
include_once 'PaymentGateway/PaypalPaymentMethods/DirectPayment.php';

use PaymentGateway\PaypalPaymentMethods\DirectPayment;

try{
    $Paypal = new DirectPayment(23);//Amount
    $Paypal->setCreditCardNumber("4539644852839411");
    $Paypal->setCVV("000");
    $Paypal->setCardType("VISA");
    $Paypal->setExpDate("052015");
    
    var_export($Paypal->doPayment());
}
catch (Exception $ex){
    echo $ex->getMessage();
}
