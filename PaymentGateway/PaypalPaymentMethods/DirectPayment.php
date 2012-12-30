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

class DirectPayment extends PaypalAbstract {
    
    //Add the necessary card details
    private $CreditCardNumber;
    private $CVV = "";
    private $CardType;
    private $ExpDate;
    
    //Billing Information is for paypal.
    public $FirstName = "Test";
    public $LastName = "Test";    
    public $CountryCode = "IN";    
    public $street = "Test Street";
    public $city = "test";
    public $state = "CA";
    public $zip = "95131";

    function __construct($amount) {
        parent::__construct($amount);
    }
    
    public function setCreditCardNumber($CreditCardNumber){
        if(!is_numeric($CreditCardNumber)){
            throw new Exception("Invalid Credit card number!");
        }
        $this->CreditCardNumber = $CreditCardNumber;
    }
    
    public function setCVV($CVV){
        if(!is_numeric($CVV)){
            throw new Exception("Invalid CVV!");
        }
        $this->CVV = $CVV;
    }
    
    public function setCardType($CardType){
        $this->CardType = strtoupper($CardType);
    }
    
    public function setExpDate($ExpDate){
        
        if(!is_numeric($ExpDate) || strlen((string) $ExpDate) != 6 ){
            throw new Exception("Invalid expiry date! date format - MMYYYY");
        }
        $this->ExpDate = intval($ExpDate);
    }
    
    /*
     * Iniates payment and does do capture
     * 
     * Paypal methods handled
     * @return array which is parsed from the response string
     * 
     */
    public function doPayment(){
        if(!empty($this->CreditCardNumber) && $this->CVV !== "" && !empty($this->CardType) && !empty($this->ExpDate)){
            $data = array();
            $data['VERSION'] = "86";
            $data['COUNTRYCODE'] = $this->CountryCode;            
            
            $directPaymentData = array_merge($this->DoDirectPayment(), $data);
            $directPaymentQuery = $this->buildQuery($directPaymentData);
            $directPaymentResponse = $this->parseResponse($this->connectPaypal($directPaymentQuery, 30));
            
            if(strtolower($directPaymentResponse['ACK']) == "success"){
                
                //ON success DOCapture paypal method initiated
                
                $data['AUTHORIZATIONID'] = $directPaymentResponse['TRANSACTIONID'];
                $data['METHOD'] = "DoCapture";
                $data['COMPLETETYPE'] = "Complete";
                
                $doCaptureQuery = $this->buildQuery($data);
                $doCaptureResponse = $this->parseResponse($this->connectPaypal($doCaptureQuery, 30));
                if($doCaptureResponse['ACK'] == "Success"){
                    return $doCaptureResponse;
                }
                else{
                    $this->PaypalError($doCaptureResponse);
                }
            }
            else{
                $this->PaypalError($directPaymentResponse);
            }
        }
        else{
            throw new Exception("Credit card number, CVV, ExpDate and Card type are required!");
        }
    }
    
    /*
     * Returns array for DirectPayment method => DoDirectPayment
     */
    private function DoDirectPayment(){
        $directPaymentData['METHOD'] = "DoDirectPayment";
        $directPaymentData['IPADDRESS'] = $_SERVER['REMOTE_ADDR'];
        $directPaymentData['PAYMENTACTION'] = "Authorization";        
        $directPaymentData['ACCT'] = $this->CreditCardNumber;
        $directPaymentData['CREDITCARDTYPE'] = $this->CardType;
        $directPaymentData['EXPDATE'] = $this->ExpDate;
        $directPaymentData['CVV2'] = $this->CVV;
        $directPaymentData['FIRSTNAME'] = $this->FirstName;
        $directPaymentData['LASTNAME'] = $this->LastName;
        $directPaymentData['STREET'] = $this->street;
        $directPaymentData['CITY'] = $this->city;
        $directPaymentData['STATE'] = $this->state;
        $directPaymentData['ZIP'] = $this->zip;
        return $directPaymentData;
    }
    
}
