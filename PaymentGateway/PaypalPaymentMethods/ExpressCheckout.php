<?php

/**
 * Paypal Integration kit
 * for direct payment and express checkout
 *
 * @author     Ravi Shanker
 * @copyright  Copyright (c) Ravi Shanker
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */
namespace PaymentGateway\PaypalPaymentMethods;

use PaymentGateway\PaypalAbstract;
use \Exception;

class ExpressCheckout extends PaypalAbstract {
    
    private $version = "93";
    
    function __construct($amount) {
        parent::__construct($amount);
    }

    /*
     * Iniates payment and does SETS express checkout
     * 
     * NOTE-THIS ONLY SETS THE PAYMENT
     *      need to call DoExpressCheckoutPayment() to complete payment
     */
    public function doPayment(){
        
        $data['RETURNURL'] = self::RETURN_URI;
        $data['CANCELURL'] = self::CANCEL_URI;
        $data['PAYMENTREQUEST_0_PAYMENTACTION'] = "sale";
        $data['METHOD'] = "SetExpressCheckout";
        $data['VERSION'] = $this->version;
        $data['PAYMENTREQUEST_0_CURRENCYCODE'] = self::CURRENCY_CODE;
        
        $response = $this->parseResponse($this->connectPaypal($this->buildQuery($data)));
       
        if(strtolower($response['ACK']) == "success"){
            header("Location: " .self::PAYPAL_LINK.$response['TOKEN']);            
            die();
        }
        else{
            $this->PaypalError($response);
        }
    }
    
    public function DoExpressCheckoutPayment(array $request){
        //METHOD=DoExpressCheckoutPayment&VERSION=93
        $data['METHOD'] = "DoExpressCheckoutPayment";
        $data['VERSION'] = $this->version;
        $data['TOKEN'] = $request['token'];
        $data['PAYERID'] = $request['PayerID'];
        $data['PAYMENTREQUEST_0_CURRENCYCODE'] = self::CURRENCY_CODE;
        $data['PAYMENTREQUEST_0_PAYMENTACTION'] = "sale";
        $data['PAYMENTREQUEST_0_AMT'] = $this->amount;
        
        $response = $this->parseResponse($this->connectPaypal($this->buildQuery($data)));
       
        if(strtolower($response['ACK']) == "success"){
            return $response;
        }
        else{
            $this->PaypalError($response);
        }
    }
}
