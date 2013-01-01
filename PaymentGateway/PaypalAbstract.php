<?php

/**
 * Paypal Integration kit
 * for direct payment and express checkout
 *
 * @author     Ravi Shanker
 * @copyright  Copyright (c) Ravi Shanker
 * @license    http://www.opensource.org/licenses/mit-license.html  MIT License
 */

namespace PaymentGateway;

use \Exception;

abstract class PaypalAbstract {
    
    //Merchant credentials
    const USER_NAME = "sdk-three_api1.sdk.com";    
    const PWD = "QFZCWN5HZM8VBG7Q";    
    const SIGNATURE = "A-IzJhZZjhg29XQ2qnhapuwxIDzyAZQ92FRP5dqBzVesOkzbdUONzmOU";
    
    const CURRENCY_CODE = "USD";
    
    //Not required for DirectpaymentMethod
    const RETURN_URI = "http://localhost/testExpressCheckout.php?result=success";    
    const CANCEL_URI = "http://localhost/testExpressCheckout.php?result=cancelled";
    
    //paypal nvp
    const PAYPAL_NVP = "https://api-3t.sandbox.paypal.com/nvp"; //Sandbox to be used only for testing
    
    //TOKEN will be concatenated after setting checkout
    const PAYPAL_LINK = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token="; //Sandbox to be used only for testing
    
    protected $amount;
    
    /*
     * @param amount => Amount as integer or float
     */
    function __construct($amount) {
        $this->setAmount($amount);
    }

    private function setAmount($amount){

        if(!is_numeric($amount)){
            throw new Exception("Amount given in invalid! Only numeric values are acccepted!");
        }
        $this->amount = $amount;
    }
    
    private function getAmount(){
        return $this->amount;
    }

    /*
     * Method used to post values to paypal
     * 
     * @param string postValues => values to be posted
     * @param int connectionTimeout => maximum timeout
     * 
     * @return response string
     */
    protected function connectPaypal($post, $connectionTimeout = 15){
        
        if(!extension_loaded("curl")){
            throw new Exception("Curl not installed!");
            return;
        }

        $ch = curl_init(self::PAYPAL_NVP);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    //use when ssl is not available
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);    //use when ssl is not available
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectionTimeout);
        $content = curl_exec($ch);
        curl_close($ch);
        if($content !== FALSE){
            return $content;
        }
        else{
            $error = "Curl error: ". curl_error($ch);
            throw new Exception($error);
            return;
        }
    }
    
    /*
     * Method used to parse response text into array
     * 
     * @param string response => ResponseText
     * @return response array
     */
    protected function parseResponse($response){
        $a  = explode("&", $response);
        $response = array();
        foreach ($a as $v) {
            $k = strpos($v, '=');
            if ($k) {
                $key = trim(substr($v, 0, $k));
                $value = trim(substr($v, $k + 1));
                if (!$key)
                    continue;
                $response[$key] = urldecode($value);
            }
            else {
                $response[] = $v;
            }
        }
        return $response;
    }
    
    /*
     * @param data => array key and Value
     * 
     * @return CURL string to post
     */
    protected function buildQuery(array $postVal){
        $postVal['USER'] = self::USER_NAME;
        $postVal['PWD'] = self::PWD;
        $postVal['SIGNATURE'] = self::SIGNATURE;
        $postVal['AMT'] = $this->getAmount();
        
        return http_build_query($postVal);
    }
    
    /*
     * Throws custom error on paypal failure
     */
    protected function PaypalError(array $directPaymentResponse){
        $error = "L_ERRORCODE0 - ".$directPaymentResponse['L_ERRORCODE0']
                ."<br/>L_LONGMESSAGE0 - ".$directPaymentResponse['L_LONGMESSAGE0'];
        throw new Exception($error);
    }

    
    /*
     * Iniates payment      * 
     * Paypal methods handled
     */
    abstract function doPayment();

}
